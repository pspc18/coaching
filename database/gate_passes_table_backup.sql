-- Backup of gate_passes table and sidebar_sub entry for Gate Pass
-- Created at 2026-10-11

CREATE TABLE IF NOT EXISTS `gate_passes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `session_id` int(11) NOT NULL DEFAULT '1',
  `branch_id` int(11) NOT NULL DEFAULT '1',
  `admissionNo` int(11) DEFAULT NULL,
  `student_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_mobile` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reciver_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reciver_mobile` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `relation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iessu_date` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gate_passes_branch_session` (`branch_id`,`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- sidebar_sub entry:
-- INSERT INTO `sidebar_sub` (`id`, `user_id`, `branch_id`, `session_id`, `sidebar_id`, `sidebar_name`, `name`, `bg_color`, `url`, `hindi_name`, `ican`, `orderBy`, `sub_sidebar`, `updated_at`, `created_at`, `deleted_at`) VALUES (46, 1, 1, 1, 9, 'Master Panel', 'Gate Pass', 'danger', 'gate_pass_view', NULL, 'ion-stats-bars', NULL, 'no', '2023-12-31 05:17:39.000000', '2023-12-17 05:17:39.000000', NULL);
