# 🔒 Système de Session Unique pour Client

Ce système empêche un client d'être connecté sur plusieurs appareils simultanément.

## 📋 Fonctionnalités

- **Session unique** : Un client ne peut être connecté que sur un seul device à la fois
- **Blocage automatique** : Les tentatives de connexion multiples sont bloquées
- **Nettoyage automatique** : Les sessions expirées sont nettoyées toutes les 5 minutes
- **Monitoring** : Commandes pour diagnostiquer et gérer les sessions

## 🏗️ Architecture

### 1. Base de données
- **Table `client_sessions`** : Stocke les sessions actives des clients
- **Relations** : `client_sessions.client_id → clients.id`

### 2. Modèles
- **`ClientSession`** : Modèle pour gérer les sessions
- **`ClientSessionService`** : Service pour la logique métier

### 3. Middleware
- **`TrackClientSession`** : Suit l'activité des sessions authentifiées

## 🚀 Utilisation

### Installation
```bash
# Exécuter la migration
php artisan migrate

# Le middleware est déjà configuré dans bootstrap/app.php
# Le scheduler est déjà configuré dans routes/console.php
```

### Commandes disponibles

#### Nettoyer les sessions expirées
```bash
# Nettoyer avec timeout par défaut (30 minutes)
php artisan sessions:cleanup

# Nettoyer avec timeout personnalisé
php artisan sessions:cleanup --timeout=60
```

#### Diagnostiquer les sessions actives
```bash
# Voir toutes les sessions actives
php artisan sessions:diagnose

# Voir les sessions d'un client spécifique
php artisan sessions:diagnose --client-id=123
```

## 📊 Flux utilisateur

### Connexion normale
1. **Client se connecte** ✅
2. **Vérification session active** → Aucune session trouvée
3. **Création session** → Enregistrée dans `client_sessions`
4. **Accès autorisé** → Redirection vers shop/ticket

### Connexion multiple
1. **Client déjà connecté** sur Device A
2. **Tente connexion** sur Device B → 🚫 **BLOQUÉ**
3. **Message d'erreur** affiché avec toast **ORANGE** :
   > "Ce compte est déjà connecté sur un autre appareil (192.168.1.100) depuis 2 heures. Veuillez vous déconnecter de l'autre appareil avant de vous reconnecter."
4. **Réponse HTTP** : 409 Conflict avec JSON :
   ```json
   {
     "success": false,
     "message": "Ce compte est déjà connecté sur un autre appareil...",
     "error_type": "session_multiple"
   }
   ```
5. **Doit se déconnecter** de Device A d'abord

### Déconnexion
1. **Client se déconnecte** → Toutes ses sessions sont invalidées
2. **Nettoyage complet** → Sessions Laravel + `client_sessions`
3. **Message de succès** → "Vous êtes bien déconnecté."

## 🔧 Configuration

### Timeout de session (minutes)
Par défaut : 30 minutes

Pour modifier :
```php
// Dans ClientSessionService
private $timeout = 60; // 1 heure
```

### Scheduler
Nettoyage toutes les 5 minutes (configuré dans `routes/console.php`)

Pour modifier :
```php
// Toutes les heures
Schedule::command('sessions:cleanup')->hourly();

// Toutes les minutes
Schedule::command('sessions:cleanup')->everyMinute();
```

## 📝 Logs

### Connexion réussie
```
INFO: Nouvelle session client créée
{
    "client_id": 123,
    "session_id": "abc123...",
    "ip_address": "192.168.1.100"
}
```

### Connexion multiple bloquée
```
WARNING: Tentative de connexion multiple bloquée
{
    "client_id": 123,
    "client_pseudo": "john_doe",
    "current_ip": "192.168.1.101",
    "existing_ip": "192.168.1.100",
    "existing_duration": "2 heures ago"
}
```

### Déconnexion
```
INFO: Client déconnecté et sessions nettoyées
{
    "client_id": 123,
    "ip": "192.168.1.100"
}
```

## 🛠️ Maintenance

### Vérifier l'état du système
```bash
# Sessions actives
php artisan sessions:diagnose

# Nettoyer manuellement
php artisan sessions:cleanup

# Vérifier les logs récents
tail -f storage/logs/laravel.log | grep "session"

# Tester le système de session unique
php artisan test:single-session

# Tester spécifiquement le blocage de connexion multiple
php artisan test:session-multiple
```

### Résoudre les problèmes

#### Client ne peut pas se connecter
```bash
# Vérifier s'il a une session active
php artisan sessions:diagnose --client-id=CLIENT_ID

# Si oui, nettoyer manuellement ses sessions
php artisan tinker
>>> \App\Services\ClientSessionService::invalidateAllClientSessions(CLIENT_ID);
```

#### Sessions qui ne s'expirent pas
```bash
# Forcer le nettoyage avec un timeout plus court
php artisan sessions:cleanup --timeout=5

# Vérifier le scheduler
php artisan schedule:list
```

## 🎯 Sécurité

- **Isolation** : Chaque client a sa propre session
- **Tracking IP** : Les adresses IP sont enregistrées
- **Auto-cleanup** : Les sessions expirées sont automatiquement supprimées
- **Logs complets** : Toutes les actions sont tracées

## 📈 Monitoring

### Métriques à surveiller
- Nombre de sessions actives
- Fréquence des blocages de connexions multiples
- Durée moyenne des sessions
- Taux d'échec de connexion

### Alertes recommandées
- Plus de 1000 sessions actives
- Taux de blocage > 5%
- Sessions qui durent > 24 heures

---

**Le système garantit maintenant qu'un client ne peut être connecté que sur un seul device !** 🔒🚀
