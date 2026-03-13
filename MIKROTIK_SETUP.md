# Configuration Mikrotik Webhook pour Wifipay

## 📋 Prérequis

- RouterOS v6.40 ou supérieur
- Accès SSH/Terminal au Mikrotik
- URL du webhook Wifipay accessible depuis Internet

## 🔧 Étape 1 - Créer le script de notification

```routeros
# Script principal pour tous les événements Wifipay
/system script add name="notify-wifipay" source={
    :local webhook_url "https://votre-domaine.com/api/hotspot/event?secret=VOTRE_SECRET"
    :local payload ""

    # Construire le payload selon l'événement
    :set payload ("event=" . $event . \\
                  "&user=" . $user . \\
                  "&mac=" . $mac . \\
                  "&ip=" . $ip . \\
                  "&cause=" . $cause . \\
                  "&error=" . $error . \\
                  "&server=" . $server)

    # Envoyer à Wifipay
    /tool fetch \\
        url=$webhook_url \\
        http-method=post \\
        http-data=$payload \\
        output=none \\
        keep-result=no
}
```

## 🔧 Étape 2 - Configurer les hooks du Hotspot Server

```routeros
# Configuration du Hotspot Server avec tous les hooks
/ip hotspot set [find] \\
    on-login="/system script run notify-wifipay \\
              \\"event=login&user=$user&mac=$mac&ip=$ip\\"" \\
    \\
    on-logout="/system script run notify-wifipay \\
               \\"event=logout&user=$user&mac=$mac&ip=$ip&cause=$cause\\"" \\
    \\
    on-login-fail="/system script run notify-wifipay \\
                   \\"event=login-fail&user=$user&mac=$mac&ip=$ip&error=$error\\"" \\
    \\
    on-status-page="/system script run notify-wifipay \\
                    \\"event=status-page&user=$user&mac=$mac&ip=$ip\\""
```

## 🔧 Étape 3 - Configurer les profils de temps

```routeros
# Profils de temps selon les forfaits Wifipay
/ip hotspot user profile add name="1HOUR" \\
    session-timeout=1h \\
    idle-timeout=30m \\
    keepalive-timeout=2m \\
    status-autorefresh=1m

/ip hotspot user profile add name="4HOURS" \\
    session-timeout=4h \\
    idle-timeout=1h \\
    keepalive-timeout=2m \\
    status-autorefresh=1m

/ip hotspot user profile add name="WEEK" \\
    session-timeout=1w \\
    idle-timeout=12h \\
    keepalive-timeout=5m \\
    status-autorefresh=5m
```

## 🔧 Étape 4 - Test des webhooks

### Test de connexion
```routeros
# Simuler un événement de connexion
/system script run notify-wifipay \\
    \\"event=login&user=TEST123&mac=AA:BB:CC:DD:EE:FF&ip=192.168.1.100\\"
```

### Test de déconnexion
```routeros
# Simuler un événement de déconnexion avec cause
/system script run notify-wifipay \\
    \\"event=logout&user=TEST123&mac=AA:BB:CC:DD:EE:FF&ip=192.168.1.100&cause=session-timeout\\"
```

## 📊 Causes de déconnexion gérées

| Cause | Signification | Action Wifipay |
|-------|---------------|----------------|
| `session-timeout` | Temps de connexion épuisé | `statut='epuise'` |
| `validity-timeout` | Forfait expiré (semaine/mois) | `statut='expire'` |
| `idle-timeout` | Inactivité prolongée | `statut='idle'` |
| `lost-carrier` | Signal WiFi perdu | `statut='paused'` |
| `link-logout` | Déconnexion manuelle | `statut='disconnected'` |
| `manual` | Admin déconnecté manuellement | `statut='disconnected'` |

## 🔍 Vérification du fonctionnement

### 1. Vérifier les logs du script
```routeros
# Voir les logs du système
/log print where topics~"script"

# Activer le debug si nécessaire
/system logging set 0 action=memory topics="script"
```

### 2. Vérifier les appels HTTP
```routeros
# Vérifier les requêtes HTTP sortantes
/tool fetch print
```

### 3. Tester avec un utilisateur réel
```routeros
# Créer un utilisateur test
/ip hotspot user add name=TEST123 password=456789 profile="1HOUR"

# Se connecter avec cet utilisateur et vérifier les webhooks
```

## 🚨 Dépannage

### Problème: Aucun webhook reçu
1. **Vérifier l'URL**: Assurez-vous que l'URL est accessible depuis Internet
2. **Vérifier le secret**: Le token secret doit correspondre à celui configuré dans `.env`
3. **Vérifier les logs**: `/log print` sur le Mikrotik

### Problème: Webhook reçu mais cause vide
1. **Vérifier la version RouterOS**: Certaines versions n'envoient pas toutes les variables
2. **Adapter le script**: Ajouter des valeurs par défaut dans le script RouterOS

### Problème: Erreur 403 sur Wifipay
1. **Vérifier le token secret**: Doit correspondre exactement
2. **Vérifier l'URL**: Doit être exactement celle configurée

## 📝 Configuration complète en une seule commande

```routeros
# Script de notification
/system script add name="notify-wifipay" source={
    :local webhook_url "https://votre-domaine.com/api/hotspot/event?secret=VOTRE_SECRET"
    :local payload ""
    :set payload ("event=" . $event . "&user=" . $user . "&mac=" . $mac . "&ip=" . $ip . "&cause=" . $cause . "&error=" . $error . "&server=" . $server)
    /tool fetch url=$webhook_url http-method=post http-data=$payload output=none keep-result=no
}

# Configuration des hooks
/ip hotspot set [find] on-login="/system script run notify-wifipay \\"event=login&user=$user&mac=$mac&ip=$ip\\"" on-logout="/system script run notify-wifipay \\"event=logout&user=$user&mac=$mac&ip=$ip&cause=$cause\\"" on-login-fail="/system script run notify-wifipay \\"event=login-fail&user=$user&mac=$mac&ip=$ip&error=$error\\"" on-status-page="/system script run notify-wifipay \\"event=status-page&user=$user&mac=$mac&ip=$ip\\""
```

## ✅ Validation finale

Après configuration, testez avec un utilisateur réel:

1. **Créez un ticket** dans Wifipay
2. **Connectez-vous** avec ce ticket sur le hotspot
3. **Vérifiez** que le webhook `login` est reçu
4. **Attendez** l'expiration ou déconnectez manuellement
5. **Vérifiez** que le webhook `logout` est reçu avec la bonne cause
6. **Vérifiez** que le statut du ticket est mis à jour dans Wifipay

Le système est maintenant prêt pour suivre en temps réel l'état des tickets ! 🎯
