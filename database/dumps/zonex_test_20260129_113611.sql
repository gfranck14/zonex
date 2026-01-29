-- Export de la base de données zonex_test
-- Date: 2026-01-29 11:36:11

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom_complet` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mac_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_depense` int(11) NOT NULL DEFAULT 0,
  `derniere_zone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_telephone_unique` (`telephone`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clients` (`id`, `nom_complet`, `telephone`, `mac_address`, `total_depense`, `derniere_zone`, `is_blocked`, `created_at`, `updated_at`) VALUES
(2, ''Franck A.'', ''+229 0160000000'', NULL, 0, ''Manuel'', 0, ''2026-01-28 22:55:10'', ''2026-01-28 22:55:10'');

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `forfaits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wifizones_id` bigint(20) unsigned NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix` int(11) NOT NULL,
  `validite` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_mikrotik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bg-brand-blue',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forfaits_wifizones_id_foreign` (`wifizones_id`),
  CONSTRAINT `forfaits_wifizones_id_foreign` FOREIGN KEY (`wifizones_id`) REFERENCES `wifizones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `forfaits` (`id`, `wifizones_id`, `nom`, `prix`, `validite`, `profile_mikrotik`, `description`, `color_class`, `created_at`, `updated_at`) VALUES
(1, 1, ''Forfait de 4h'', 200, ''24h'', ''4H-24H'', ''Un petit forfait pour Tiktok'', ''bg-red-500'', ''2026-01-27 20:27:13'', ''2026-01-28 10:24:16''),
(3, 2, ''Forfait de 1 Jour'', 500, ''48h'', ''4H-24H'', ''Internet illimités pour 24heure'', ''bg-purple-500'', ''2026-01-28 14:05:07'', ''2026-01-29 08:52:42'');

CREATE TABLE `import_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proprio_id` bigint(20) unsigned NOT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone_nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forfait_nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantite` int(11) NOT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_histories_proprio_id_foreign` (`proprio_id`),
  CONSTRAINT `import_histories_proprio_id_foreign` FOREIGN KEY (`proprio_id`) REFERENCES `proprio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `import_histories` (`id`, `proprio_id`, `nom_fichier`, `zone_nom`, `forfait_nom`, `quantite`, `statut`, `observation`, `created_at`, `updated_at`) VALUES
(1, 1, ''export-ticket de 4h.csv'', ''Cardy WIFIZONE 2'', ''Forfait de 4h'', 6, ''succes'', ''Importé 6 tickets.'', ''2026-01-29 02:10:47'', ''2026-01-29 02:10:47''),
(2, 1, ''export-ticket de 4h.csv'', ''UAC WIFIZONE'', ''Forfait de 1 Jour'', 0, ''echec'', ''Aucun ticket importé. 6 tickets avec profil Mikrotik incorrect.'', ''2026-01-29 08:47:58'', ''2026-01-29 08:47:58''),
(3, 1, ''export-ticket de 4h.csv'', ''Cardy WIFIZONE 2'', ''Forfait de 4h'', 1, ''partiel'', ''Importé 1 tickets. 5 doublons ignorés. '', ''2026-01-29 08:48:44'', ''2026-01-29 08:48:44''),
(4, 1, ''export-ticket de 4h.csv'', ''UAC WIFIZONE'', ''Forfait de 1 Jour'', 0, ''echec'', ''Aucun ticket importé. 6 doublons trouvés. '', ''2026-01-29 08:53:02'', ''2026-01-29 08:53:02'');

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, ''0001_01_01_000000_create_users_table'', 1),
(2, ''0001_01_01_000001_create_cache_table'', 1),
(3, ''0001_01_01_000002_create_jobs_table'', 1),
(4, ''2026_01_22_192957_create_proprio'', 1),
(5, ''2026_01_23_140912_create_wifizones_table'', 1),
(7, ''2026_01_27_165531_create_clients_table'', 1),
(8, ''2026_01_24_230317_create_forfaits_table'', 2),
(9, ''2024_01_28_000000_create_tickets_table'', 3),
(10, ''2026_01_28_000000_create_import_histories_table'', 3),
(11, ''2026_01_28_000001_add_observation_to_import_histories_table'', 3),
(12, ''2026_01_29_000000_create_transactions_table'', 3),
(13, ''2026_01_29_000001_create_withdrawals_table'', 3),
(14, ''2026_01_29_001415_add_settings_fields_to_proprio_table'', 4),
(15, ''2026_01_29_003302_increase_proprio_numero_size'', 5),
(16, ''2026_01_29_004248_add_active_status_to_proprio_table'', 6);

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `proprio` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wa_numero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wa_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `wa_alert_threshold` int(11) NOT NULL DEFAULT 15,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deactivation_reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `proprio` (`id`, `nom`, `prenom`, `email`, `numero`, `wa_numero`, `wa_notifications_enabled`, `wa_alert_threshold`, `password`, `is_active`, `deactivation_reason`, `created_at`, `updated_at`) VALUES
(1, ''CODJO'', ''Caleb'', NULL, ''+229 62800782'', NULL, 1, 15, ''$2y$12$IAyWa3W73YF.y7p52SjDb.c0JbcJ0pu2BVG51BuLOhP27kTIo.rpu'', 1, NULL, ''2026-01-27 18:47:40'', ''2026-01-29 10:09:21'');

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
(''erIWBD76wvCLQyauJt8TDziLBN4dqFuu6EGlTDDy'', 1, ''127.0.0.1'', ''Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0'', ''YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZTFScFd0TndkVE1LNDdod09JOEd4QlNONEJqTkdXdEdGc2lRU3BVUCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMy9zZXR0aW5ncyI7czo1OiJyb3V0ZSI7czo4OiJzZXR0aW5ncyI7fXM6NTQ6ImxvZ2luX3Byb3ByaW9fNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30='', 1769685656);

CREATE TABLE `tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `forfaits_id` bigint(20) unsigned NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'libre',
  `client_id` bigint(20) unsigned DEFAULT NULL,
  `date_vente` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_username_unique` (`username`),
  KEY `tickets_forfaits_id_foreign` (`forfaits_id`),
  KEY `tickets_client_id_foreign` (`client_id`),
  CONSTRAINT `tickets_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `tickets_forfaits_id_foreign` FOREIGN KEY (`forfaits_id`) REFERENCES `forfaits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tickets` (`id`, `forfaits_id`, `username`, `password`, `statut`, `client_id`, `date_vente`, `created_at`, `updated_at`) VALUES
(1, 1, ''azerty'', ''1234'', ''libre'', NULL, NULL, ''2026-01-29 02:10:47'', ''2026-01-29 02:10:47''),
(2, 1, ''RBRMT'', ''93372'', ''libre'', NULL, NULL, ''2026-01-29 02:10:47'', ''2026-01-29 02:10:47''),
(3, 1, ''Y5P64'', ''67333'', ''libre'', NULL, NULL, ''2026-01-29 02:10:47'', ''2026-01-29 02:10:47''),
(4, 1, ''3LD9K'', ''53662'', ''libre'', NULL, NULL, ''2026-01-29 02:10:47'', ''2026-01-29 02:10:47''),
(5, 1, ''T62HR'', ''93329'', ''libre'', NULL, NULL, ''2026-01-29 02:10:47'', ''2026-01-29 02:10:47''),
(7, 1, ''U8J5D'', ''92574'', ''libre'', NULL, NULL, ''2026-01-29 08:48:44'', ''2026-01-29 08:48:44'');

CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `wifizone_id` bigint(20) unsigned DEFAULT NULL,
  `ticket_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('purchase','deposit','refund') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'purchase',
  `operator` enum('mtn','moov','celtiis','cash') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','success','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_reference_unique` (`reference`),
  KEY `transactions_client_id_foreign` (`client_id`),
  KEY `transactions_wifizone_id_foreign` (`wifizone_id`),
  KEY `transactions_ticket_id_foreign` (`ticket_id`),
  KEY `transactions_reference_index` (`reference`),
  KEY `transactions_status_index` (`status`),
  KEY `transactions_created_at_index` (`created_at`),
  CONSTRAINT `transactions_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transactions_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_wifizone_id_foreign` FOREIGN KEY (`wifizone_id`) REFERENCES `wifizones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wifizones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proprio_id` bigint(20) unsigned NOT NULL,
  `nom_zone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wifizones_token_unique` (`token`),
  KEY `wifizones_proprio_id_foreign` (`proprio_id`),
  CONSTRAINT `wifizones_proprio_id_foreign` FOREIGN KEY (`proprio_id`) REFERENCES `proprio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wifizones` (`id`, `proprio_id`, `nom_zone`, `adresse`, `token`, `created_at`, `updated_at`) VALUES
(1, 1, ''Cardy WIFIZONE 2'', ''Menontin'', ''zone_GzDrkU3xXv'', ''2026-01-27 18:51:11'', ''2026-01-29 01:34:29''),
(2, 1, ''UAC WIFIZONE'', ''Abomey Calavi'', ''zone_YnKskYubhv'', ''2026-01-28 10:25:13'', ''2026-01-28 10:25:13'');

CREATE TABLE `withdrawals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `operator` enum('mtn','moov','celtiis') COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','processing','completed','cancelled','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `mobile_money_ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `withdrawals_reference_unique` (`reference`),
  KEY `withdrawals_user_id_foreign` (`user_id`),
  KEY `withdrawals_reference_index` (`reference`),
  KEY `withdrawals_status_index` (`status`),
  KEY `withdrawals_requested_at_index` (`requested_at`),
  CONSTRAINT `withdrawals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

