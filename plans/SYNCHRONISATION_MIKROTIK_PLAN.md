# PLAN DE SYNCHRONISATION ZONEX <-> MIKROTIK

## Objectif

Etablir une synchronisation bidirectionnelle entre la plateforme ZoneX et les routeurs MikroTik pour que la plateforme reflete fidelement l'etat reel des donnees sur les equipements reseau.

---

## 1. DIAGNOSTIC ACTUEL

### 1.1 Architecture existante

- **Fonction createMikrotikProfileSimple()** : Cree un profil Hotspot sur MikroTik lors de la creation d'un forfait
- **Fonction destroyTicket()** : Supprime utilisateur MikroTik lors de la suppression d'un ticket
- **MikrotikEventController** : Recoit webhooks login/logout
- **Import CSV** : Importe des tickets et les cree sur MikroTik

### 1.2 Configuration actuelle

- Configuration MikroTik hardcodee dans ForfaitController.php:203-209
- IP: 10.10.10.1, User: wifipay_api, Pass: wifipay_pass, Port: 8728
- Le modele WifiZone stocke les identifiants admin tickets mais pas la config MikroTik par zone

---

## 2. ARCHITECTURE CIBLE

### 2.1 Principe de synchronisation

**Scenario 1: Ajout zone sur ZoneX**
1. Utilisateur cree zone WiFi
2. ZoneX se connecte au MikroTik via API
3. Cree le Hotspot Server sur le routeur
4. Marque la zone comme synchronisee

**Scenario 2: Ajout forfait sur ZoneX**
1. Utilisateur cree forfait
2. ZoneX cree le User Profile sur MikroTik
3. Le profil est lie au forfait

**Scenario 3: Import tickets**
1. Utilisateur importe CSV tickets
2. ZoneX cree les utilisateurs Hotspot sur MikroTik
3. Les tickets sont synchronises

**Scenario 4: Synchronisation inverse**
1. Utilisateur clique sur "Synchroniser"
2. ZoneX recupere la liste des utilisateurs et profils MikroTik
3. Met a jour la base de donnees locale

---

## 3. PLAN D'IMPLEMENTATION

### PHASE 1: Infrastructure de synchronisation

#### 1.1 Ajout champs MikroTik dans WifiZone (Migration)

```php
Schema::table('wifizones', function (Blueprint $table) {
    // Configuration connexion MikroTik
    $table->string('mikrotik_ip')->nullable();
    $table->string('mikrotik_user')->nullable();
    $table->string('mikrotik_password')->nullable();
    $table->integer('mikrotik_port')->default(8728);
    
    // Configuration Hotspot
    $table->string('hotspot_name')->nullable();
    $table->string('hotspot_interface')->nullable();
    
    //Etat synchronisation
    $table->boolean('is_synced')->default(false);
    $table->timestamp('last_synced_at')->nullable();
});
```

#### 1.2 Creation du service MikrotikService

Fichier: app/Services/MikrotikService.php

Methodes principales:
- connect(WifiZone $zone) : connexion au routeur
- listProfiles() : liste les profils Hotspot
- createProfile($name, $params) : cree un profil
- listUsers() : liste les utilisateurs Hotspot
- createUser($username, $password, $profile) : cree un utilisateur
- deleteUser($username) : supprime un utilisateur
- setupHotspot($name, $interface) : configure le Hotspot Server
- getHotspotInfo() : recupere les infos Hotspot

---

### PHASE 2: Synchronisation zones

#### 2.1 Modification du WifizoneController::store()

Ajouter les champs MikroTik dans le formulaire et appeler le service lors de la creation.

#### 2.2 Interface de configuration zone

Ajouter dans le formulaire d'ajout de zone:
- Adresse IP MikroTik
- Port API (defaut 8728)
- Utilisateur API
- Mot de passe API
- Interface Hotspot (bridge/wlan1/ether1)

---

### PHASE 3: Synchronisation forfaits/profils

#### 3.1 Modification createMikrotikProfileSimple

Ameliorer pour utiliser le nouveau service MikrotikService avec les parametres de temps limites.

#### 3.2 Mapping temps_limit vers MikroKit

Convertir "2 heures" -> "2h", "1 jour" -> "1d" en parametres MikroTik:
- session-timeout
- idle-timeout
- keepalive-timeout

---

### PHASE 4: Synchronisation tickets/utilisateurs

#### 4.1 Import tickets vers MikroTik

Ameliorer la fonction import() pour utiliser le nouveau service.

#### 4.2 Suppression ticket vers MikroTik

Ameliorer destroyTicket() pour supprimer l'utilisateur sur MikroTik.

---

### PHASE 5: Synchronisation inverse (Pull)

#### 5.1 Bouton de synchronisation

Nouvelle route: POST /wifizones/{id}/sync

Implementer:
- sync() : recuperer profils et utilisateurs MikroTik
- syncProfiles() : creer/mettre a jour forfaits
- syncUsers() : creer/mettre a jour tickets

---

### PHASE 6: Interface utilisateur

#### 6.1 Indicateurs de synchronisation

- Badge "Synchronise" / "Non synchronise" sur chaque zone
- Bouton "Synchroniser" avec icone
- Date de derniere synchronisation
- Notifications de succes/echec

---

## 4. ROADMAP

### Etape 1: Preparation (1 jour)
- Creer migration pour nouveaux champs WifiZone
- Mettre a jour modele WifiZone
- Creer MikrotikService de base

### Etape 2: Connexion zone (2 jours)
- Modifier WifizoneController::store()
- Ajouter champs MikroTik dans le formulaire
- Methode setupHotspot() dans le service

### Etape 3: Synchronisation profils (2 jours)
- Ameliorer createMikrotikProfileSimple()
- Ajouter parseTimeLimit()
- Mapper temps_limit vers MikroTik

### Etape 4: Synchronisation tickets (2 jours)
- Ameliorer import()
- Ameliorer destroyTicket()
- Ajouter createUsers() batch

### Etape 5: Synchronisation inverse (2 jours)
- Ajouter route /sync
- Implementer syncProfiles()
- Implementer syncUsers()

### Etape 6: Interface (1 jour)
- Indicateurs visuel sync
- Bouton synchronisation
- Notifications

---

## 5. FICHIERS A MODIFIER

| Fichier | Action |
|---------|--------|
| database/migrations/ | Creer nouvelle migration |
| app/Models/WifiZone.php | Modifier - ajouter champs et relation |
| app/Services/MikrotikService.php | Creer - nouveau service |
| app/Http/Controllers/WifizoneController.php | Modifier - ajout sync |
| app/Http/Controllers/ForfaitController.php | Modifier - utiliser service |
| resources/views/proprio/wifizones.blade.php | Modifier - UI |
| routes/web.php | Ajouter routes sync |

---

## 6. RISQUES ET SOLUTIONS

| Risque | Solution |
|--------|----------|
| Connexion MikroTik echoue | Timeout + logging + fallback mode |
| Doublons utilisateurs | Verifier existence avant creation |
| Perte de donnees | Sauvegarde DB avant sync |
| Performances | Pagination pour gros imports |
| Authentification echoue | Gestion multi-methodes (API/DNS) |

---

Plan genere le 26 mars 2026