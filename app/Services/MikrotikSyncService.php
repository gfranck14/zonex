<?php

namespace App\Services;

use App\Models\WifiZone;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;

class MikrotikSyncService
{
    /**
     * Timeout global pour toutes les connexions au MikroTik (en secondes)
     */
    const MIKROTIK_TIMEOUT = 3;

    /**
     * Cache des connexions actives par Zone ID
     */
    private array $clients = [];

    /**
     * Initialise la connexion à un routeur MikroTik
     * 
     * @param WifiZone $zone
     * @return Client
     * @throws \Exception
     */
    public function connect(WifiZone $zone): Client
    {
        if (isset($this->clients[$zone->id])) {
            return $this->clients[$zone->id];
        }

        if (!$zone->api_host || !$zone->api_user || !$zone->api_password) {
            throw new \Exception("Les identifiants API (IP, User, Mot de passe) ne sont pas configurés pour la zone {$zone->nom_zone}.");
        }

        try {
            $config = new Config([
                'host'    => $zone->api_host,
                'user'    => $zone->api_user,
                'pass'    => Crypt::decryptString($zone->api_password),
                'port'    => (int)($zone->api_port ?: 8728),
                'timeout' => self::MIKROTIK_TIMEOUT,
            ]);

            $client = new Client($config);
            $this->clients[$zone->id] = $client;
            
            return $client;
        } catch (\Exception $e) {
            Log::error("❌ MIKROTIK CONN EXEC FAILURE", [
                'zone' => $zone->nom_zone,
                'host' => $zone->api_host,
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Impossible de se connecter au routeur MikroTik ({$zone->api_host}). Vérifiez qu'il est allumé, connecté à Internet et que les accès API sont corrects.");
        }
    }

    /**
     * Teste la connexion au routeur
     */
    public function testConnection(WifiZone $zone): bool
    {
        try {
            $client = $this->connect($zone);
            // Test simple en récupérant l'identité du routeur
            $client->query('/system/identity/print')->read();
            return true;
        } catch (\Exception $e) {
            Log::error("❌ TEST CONNEXION ÉCHOUÉ : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Teste une connexion avec des identifiants non sauvegardés
     */
    public function testCustomConnection(string $host, string $user, string $pass, int $port = 8728): array
    {
        try {
            $config = new Config([
                'host'    => $host,
                'user'    => $user,
                'pass'    => $pass,
                'port'    => $port,
                'timeout' => self::MIKROTIK_TIMEOUT,
            ]);

            $client = new Client($config);
            
            // Récupérer l'identité
            $response = $client->query('/system/identity/print')->read();
            $identity = $response[0]['name'] ?? 'Inconnu';

            // Récupérer la version
            $resource = $client->query('/system/resource/print')->read();
            $version = $resource[0]['version'] ?? '';

            return [
                'success' => true,
                'identity' => $identity,
                'version' => $version
            ];
        } catch (\Exception $e) {
            Log::error("❌ TEST CUSTOM CONNEXION ÉCHOUÉ ({$host}) : " . $e->getMessage());
            throw new \Exception("Impossible de se connecter au routeur ({$host}). Vérifiez qu'il est allumé et connecté.");
        }
    }

    /**
     * Récupère la liste des profils Hotspot
     */
    public function getProfiles(WifiZone $zone): array
    {
        $client = $this->connect($zone);
        return $client->query('/ip/hotspot/user/profile/print')->read();
    }

    /**
     * Récupère la liste des utilisateurs Hotspot (tickets)
     */
    public function getUsers(WifiZone $zone): array
    {
        $client = $this->connect($zone);
        return $client->query('/ip/hotspot/user/print')->read();
    }

    /**
     * Crée un profil utilisateur sur le hotspot
     */
    public function createProfile(WifiZone $zone, string $name, string $limitUptime, bool $addMacCookie = true): array
    {
        $client = $this->connect($zone);
        
        $query = new Query('/ip/hotspot/user/profile/add');
        $query->equal('name', $name);
        $query->equal('session-timeout', $limitUptime);
        
        if ($addMacCookie) {
            $query->equal('add-mac-cookie', 'yes');
        }

        return $client->query($query)->read();
    }

    /**
     * Récupère les sessions actives (/ip/hotspot/active) liées à un profil donné.
     */
    public function getActiveSessionsByProfile(WifiZone $zone, string $profileName): array
    {
        try {
            $client = $this->connect($zone);
            
            // 1. Récupérer tous les noms d'utilisateurs rattachés à ce profil
            $queryUsers = (new Query('/ip/hotspot/user/print'))->where('profile', $profileName);
            $users = $client->query($queryUsers)->read();
            $usernames = array_column($users, 'name');

            if (empty($usernames)) return [];

            // 2. Récupérer les sessions actives et filtrer par ces noms
            $active = $client->query('/ip/hotspot/active/print')->read();
            $filteredActive = [];
            foreach ($active as $session) {
                if (in_array($session['user'], $usernames)) {
                    $filteredActive[] = $session;
                }
            }

            return $filteredActive;
        } catch (\Exception $e) {
            Log::error("❌ getActiveSessionsByProfile Failure : " . $e->getMessage());
            return [];
        }
    }

    /**
     * Supprime des sessions actives par leurs IDs.
     */
    public function removeActiveSessions(WifiZone $zone, array $sessionIds): void
    {
        if (empty($sessionIds)) return;
        
        try {
            $client = $this->connect($zone);
            foreach ($sessionIds as $id) {
                $query = (new Query('/ip/hotspot/active/remove'))->equal('.id', $id);
                $client->query($query)->read();
            }
            Log::info("🗑️ " . count($sessionIds) . " sessions actives déconnectées sur MikroTik.");
        } catch (\Exception $e) {
            Log::error("❌ removeActiveSessions Failure : " . $e->getMessage());
        }
    }

    /**
     * Supprime un profil Hotspot par son nom.
     */
    public function removeProfileByName(WifiZone $zone, string $profileName): bool
    {
        try {
            $client = $this->connect($zone);
            $query = (new Query('/ip/hotspot/user/profile/print'))->where('name', $profileName);
            $profiles = $client->query($query)->read();

            if (!empty($profiles)) {
                $query = (new Query('/ip/hotspot/user/profile/remove'))->equal('.id', $profiles[0]['.id']);
                $client->query($query)->read();
                Log::info("🗑️ Profil MikroTik '{$profileName}' supprimé.");
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("❌ removeProfileByName Failure : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime tous les utilisateurs d'un profil donné.
     * Utile avant de supprimer le profil lui-même pour éviter les erreurs de contrainte.
     */
    public function removeUsersByProfile(WifiZone $zone, string $profileName): void
    {
        try {
            $client = $this->connect($zone);
            $queryFind = (new Query('/ip/hotspot/user/print'))->where('profile', $profileName);
            $users = $client->query($queryFind)->read();

            if (!empty($users)) {
                foreach ($users as $u) {
                    $queryRemove = (new Query('/ip/hotspot/user/remove'))->equal('.id', $u['.id']);
                    $client->query($queryRemove)->read();
                }
                Log::info("🗑️ Nettoyage MikroTik : " . count($users) . " utilisateur(s) du profil '{$profileName}' supprimé(s).");
            }
        } catch (\Exception $e) {
            Log::error("❌ removeUsersByProfile Failure : " . $e->getMessage());
        }
    }

    /**
     * Supprime des utilisateurs Hotspot par leurs usernames.
     */
    public function removeUsersByUsernames(WifiZone $zone, array $usernames): void
    {
        if (empty($usernames)) return;

        try {
            $client = $this->connect($zone);
            
            // Optimization: If only 1 username, use a direct where query
            if (count($usernames) === 1) {
                $queryFind = (new Query('/ip/hotspot/user/print'))->where('name', $usernames[0]);
                $user = $client->query($queryFind)->read();
                if (!empty($user)) {
                    $queryRemove = (new Query('/ip/hotspot/user/remove'))->equal('.id', $user[0]['.id']);
                    $client->query($queryRemove)->read();
                }
                return;
            }

            // For multiple, fetch once and filter (consistent with original logic but safer)
            $allUsers = $client->query('/ip/hotspot/user/print')->read();
            
            $usernamesToIds = [];
            foreach ($allUsers as $u) {
                if (in_array($u['name'], $usernames)) {
                    $usernamesToIds[] = $u['.id'];
                }
            }

            if (!empty($usernamesToIds)) {
                foreach ($usernamesToIds as $id) {
                    $query = (new Query('/ip/hotspot/user/remove'))->equal('.id', $id);
                    $client->query($query)->read();
                }
                Log::info("🗑️ " . count($usernamesToIds) . " utilisateur(s) supprimé(s) sur MikroTik.");
            }
        } catch (\Exception $e) {
            Log::error("❌ removeUsersByUsernames Failure : " . $e->getMessage());
        }
    }

    /**
     * Crée un utilisateur (ticket) sur le hotspot
     */
    public function createUser(WifiZone $zone, string $name, string $password, string $profile, ?string $limitUptime = null, string $comment = ""): array
    {
        $client = $this->connect($zone);
        
        $query = new Query('/ip/hotspot/user/add');
        $query->equal('name', $name);
        $query->equal('password', $password);
        $query->equal('profile', $profile);
        
        if ($limitUptime) {
            $query->equal('limit-uptime', $limitUptime);
        }
        
        if (!empty($comment)) {
            $query->equal('comment', $comment);
        }

        return $client->query($query)->read();
    }

    /**
     * Configure le script global de notification (Webhook) sur le MikroTik.
     * 
     * @param WifiZone $zone
     * @return bool
     */
    public function setupWebhookScript(WifiZone $zone): bool
    {
        try {
            $client = $this->connect($zone);
            $appUrl = url('/api/hotspot/logout');
            $token = $zone->token;

            // Script RouterOS optimisé pour variables globales et sécurité
            $scriptSource = <<<ROUTEROS
:global wfUser; :global wfMac; :global wfEvent; :global wfCause; :global wfIp;
:local webhookUrl "$appUrl";
:local zoneToken "$token";
:local payload ("event=" . \$wfEvent . "&user=" . \$wfUser . "&mac=" . \$wfMac . "&ip=" . \$wfIp . "&cause=" . \$wfCause . "&zone=" . \$zoneToken);
/tool fetch url=\$webhookUrl http-method=post http-data=\$payload output=none keep-result=no;
:set wfUser ""; :set wfMac ""; :set wfEvent ""; :set wfCause ""; :set wfIp "";
ROUTEROS;

            // 1. Vérifier si le script existe déjà
            $queryCheck = (new Query('/system/script/print'))->where('name', 'notify-wifipay');
            $scripts = $client->query($queryCheck)->read();

            if (empty($scripts)) {
                // Création
                $query = (new Query('/system/script/add'))
                    ->equal('name', 'notify-wifipay')
                    ->equal('source', $scriptSource);
                $client->query($query)->read();
                Log::info("✅ Script 'notify-wifipay' créé sur {$zone->nom_zone}");
            } else {
                // Mise à jour (au cas où l'URL ou le Token ont changé)
                $query = (new Query('/system/script/set'))
                    ->equal('.id', $scripts[0]['.id'])
                    ->equal('source', $scriptSource);
                $client->query($query)->read();
                Log::info("🔄 Script 'notify-wifipay' mis à jour sur {$zone->nom_zone}");
            }

            return true;
        } catch (\Exception $e) {
            Log::error("❌ setupWebhookScript Failure : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Injecte les hooks on-login et on-logout dans un profil Hotspot.
     * 
     * @param WifiZone $zone
     * @param string $profileName
     * @return bool
     */
    public function patchProfileHooks(WifiZone $zone, string $profileName): bool
    {
        try {
            $client = $this->connect($zone);
            
            // Chercher le profil par son nom
            $queryFind = (new Query('/ip/hotspot/user/profile/print'))->where('name', $profileName);
            $profiles = $client->query($queryFind)->read();
            
            if (empty($profiles)) {
                Log::warning("⚠️ Profil '{$profileName}' non trouvé sur MikroTik pour injection hooks.");
                return false;
            }

            // Définir les scripts d'appel (utilisation de variables globales et :execute pour l'asynchronisme)
            $onLogin = ":global wfUser \$user; :global wfMac \$mac; :global wfIp \$ip; :global wfEvent \"login\"; :execute script=\"/system script run notify-wifipay\"";
            $onLogout = ":global wfUser \$user; :global wfMac \$mac; :global wfIp \$ip; :global wfCause \$cause; :global wfEvent \"logout\"; :execute script=\"/system script run notify-wifipay\"";

            $query = (new Query('/ip/hotspot/user/profile/set'))
                ->equal('.id', $profiles[0]['.id'])
                ->equal('on-login', $onLogin)
                ->equal('on-logout', $onLogout);
            
            $client->query($query)->read();
            Log::info("🔧 Hooks injectés dans le profil '{$profileName}' sur {$zone->nom_zone}");

            return true;
        } catch (\Exception $e) {
            Log::error("❌ patchProfileHooks Failure : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime des utilisateurs (tickets) par batch
     */
    public function removeUsers(WifiZone $zone, array $userIds): void
    {
        if (empty($userIds)) return;
        
        $client = $this->connect($zone);
        foreach ($userIds as $id) {
            try {
                $query = new Query('/ip/hotspot/user/remove');
                $query->equal('.id', $id);
                $client->query($query)->read();
            } catch (\Exception $e) {
                Log::warning("⚠️ Échec suppression user Mikrotik {$id}: " . $e->getMessage());
            }
        }
    }
}
