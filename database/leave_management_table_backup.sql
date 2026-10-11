-- Backup of leave_management table
-- Generated: 2026-10-11 13:03:55

DROP TABLE IF EXISTS `leave_management`;
CREATE TABLE `leave_management` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `user_type` varchar(20) DEFAULT NULL,
  `attendance_unique_id` varchar(255) DEFAULT NULL,
  `branch_id` int(11) NOT NULL DEFAULT '1',
  `session_id` int(11) NOT NULL DEFAULT '1',
  `admission_id` varchar(255) DEFAULT NULL,
  `class_type_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `reason` text,
  `status` varchar(10) DEFAULT NULL COMMENT '0=>deny,1=>approve,2=>pending',
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL,
  `deleted_at` timestamp(6) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `leave_management` (`id`, `user_id`, `user_type`, `attendance_unique_id`, `branch_id`, `session_id`, `admission_id`, `class_type_id`, `subject`, `from_date`, `to_date`, `reason`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('1', '1', 'user', 'ua1', '1', '5', NULL, NULL, 'Self Attendance Leave', '2026-05-30', '2026-06-10', '', '1', '2026-05-30 11:06:59.000000', '2026-05-30 11:07:53.000000', NULL);
INSERT INTO `leave_management` (`id`, `user_id`, `user_type`, `attendance_unique_id`, `branch_id`, `session_id`, `admission_id`, `class_type_id`, `subject`, `from_date`, `to_date`, `reason`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES ('2', '1', NULL, NULL, '1', '5', '1437', '32', 'Politics', '2026-07-04', '2026-07-04', 'TEST', '1', '2026-07-04 08:31:24.000000', '2026-07-04 08:31:38.000000', NULL);
