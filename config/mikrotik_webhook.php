<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration Mikrotik Webhook
    |--------------------------------------------------------------------------
    |
    | Configuration pour les webhooks Mikrotik Hotspot
    | URL: https://votre-domaine.com/api/hotspot/event?secret=VOTRE_SECRET
    |
    */

    // URL du webhook Wifipay
    'webhook_url' => env('APP_URL') . '/api/hotspot/event',

    // Token secret pour sécuriser le webhook
    'secret_token' => env('MIKROTIK_WEBHOOK_SECRET', 'votre_secret_webhook'),

    // Script RouterOS pour Mikrotik
    'routeros_script' => [
        'name' => 'notify-wifipay',
        'source' => '
            :local webhook_url "' . env('APP_URL') . '/api/hotspot/event?secret=' . env('MIKROTIK_WEBHOOK_SECRET') . '"
            :local payload ""

            :set payload ("event=" . $event . \\
                          "&user=" . $user . \\
                          "&mac=" . $mac . \\
                          "&ip=" . $ip . \\
                          "&cause=" . $cause . \\
                          "&error=" . $error . \\
                          "&server=" . $server)

            /tool fetch \\
                url=$webhook_url \\
                http-method=post \\
                http-data=$payload \\
                output=none \\
                keep-result=no
        ',
    ],

    // Configuration des hooks hotspot
    'hotspot_hooks' => [
        'on-login' => '/system script run notify-wifipay \\"event=login&user=$user&mac=$mac&ip=$ip\\""',
        'on-logout' => '/system script run notify-wifipay \\"event=logout&user=$user&mac=$mac&ip=$ip&cause=$cause\\""',
        'on-login-fail' => '/system script run notify-wifipay \\"event=login-fail&user=$user&mac=$mac&ip=$ip&error=$error\\""',
        'on-status-page' => '/system script run notify-wifipay \\"event=status-page&user=$user&mac=$mac&ip=$ip\\""',
    ],

    // Causes de déconnexion et leurs traitements
    'logout_causes' => [
        'session-timeout' => [
            'label' => 'Temps de connexion épuisé',
            'action' => 'mark_exhausted',
            'message' => 'Votre temps de connexion est épuisé. Rechargez sur Wifipay.',
        ],
        'validity-timeout' => [
            'label' => 'Forfait expiré',
            'action' => 'mark_expired',
            'message' => 'Votre forfait a expiré. Renouvelez sur Wifipay.',
        ],
        'idle-timeout' => [
            'label' => 'Inactivité prolongée',
            'action' => 'mark_idle',
            'message' => 'Session terminée pour inactivité.',
        ],
        'lost-carrier' => [
            'label' => 'Signal WiFi perdu',
            'action' => 'mark_paused',
            'message' => 'Connexion interrompue - Signal perdu.',
        ],
        'link-logout' => [
            'label' => 'Déconnexion manuelle',
            'action' => 'mark_disconnected',
            'message' => 'Vous vous êtes déconnecté manuellement.',
        ],
        'manual' => [
            'label' => 'Déconnecté par admin',
            'action' => 'mark_disconnected',
            'message' => 'Déconnecté par l\'administrateur.',
        ],
    ],
];
