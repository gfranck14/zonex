<?php

namespace App\Services;

use Exception;

/**
 * Service pour communiquer avec l'API RouterOS de Mikrotik
 */
class RouterosAPI
{
    private $connection;
    private $connected = false;

    /**
     * Connexion au routeur Mikrotik
     */
    public function connect($host, $username, $password)
    {
        try {
            $this->connection = @fsockopen($host, 8728, $errno, $errstr, 10);
            
            if (!$this->connection) {
                throw new Exception("Impossible de se connecter à $host: $errstr");
            }

            // Envoyer les identifiants
            $this->write('/login');
            $this->write('=name=' . $username);
            $this->write('=password=' . $password);

            $response = $this->read();
            
            if (strpos($response, '!done') === false) {
                throw new Exception("Échec de l'authentification");
            }

            $this->connected = true;
            return true;

        } catch (Exception $e) {
            $this->connected = false;
            throw $e;
        }
    }

    /**
     * Déconnexion du routeur
     */
    public function disconnect()
    {
        if ($this->connection) {
            $this->write('/quit');
            fclose($this->connection);
            $this->connected = false;
        }
    }

    /**
     * Exécuter une commande sur le routeur
     */
    public function comm($command, $params = [])
    {
        if (!$this->connected) {
            throw new Exception("Non connecté au routeur");
        }

        try {
            // Envoyer la commande
            $this->write($command);

            // Envoyer les paramètres
            foreach ($params as $key => $value) {
                $this->write('=' . $key . '=' . $value);
            }

            // Marquer la fin de la commande
            $this->write('.tag=' . uniqid());

            // Lire la réponse
            $response = $this->read();

            return $this->parseResponse($response);

        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'exécution de la commande $command: " . $e->getMessage());
        }
    }

    /**
     * Écrire des données dans la connexion
     */
    private function write($data)
    {
        $length = strlen($data);
        fwrite($this->connection, pack('N', $length) . $data);
    }

    /**
     * Lire les données depuis la connexion
     */
    private function read()
    {
        $response = '';
        while (true) {
            $header = fread($this->connection, 4);
            if (strlen($header) < 4) break;

            $length = unpack('N', $header)[1];
            $response .= fread($this->connection, $length);

            if (strpos($response, '!done') !== false || strpos($response, '!trap') !== false) {
                break;
            }
        }
        return $response;
    }

    /**
     * Parser la réponse du routeur
     */
    private function parseResponse($response)
    {
        $lines = explode("\n", $response);
        $result = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '!') continue;

            if ($line[0] === '=') {
                parse_str(substr($line, 1), $item);
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Vérifier si la connexion est active
     */
    public function isConnected()
    {
        return $this->connected;
    }
}
