-- Himalayan Water Science — Field Service schema
-- Matches packages/Hws/FieldService/src/Database/Migrations exactly.
-- Run top to bottom (later tables have FKs into earlier ones).
-- Assumes Bagisto's own `admins` table already exists — this doesn't create it.

CREATE TABLE `hws_tasks` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `task_no` VARCHAR(255) NOT NULL,
  `type` ENUM('installation','amc_service','complaint','service','sales_visit','site_survey') NOT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_phone` VARCHAR(255) NOT NULL,
  `customer_address` TEXT NOT NULL,
  `priority` ENUM('urgent','high','normal','low') NOT NULL DEFAULT 'normal',
  `step` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `scheduled_at` DATETIME NULL,
  `assigned_to` BIGINT UNSIGNED NULL,
  `work_description` TEXT NULL,
  `signature_path` VARCHAR(255) NULL,
  `rating` TINYINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `hws_tasks_task_no_unique` (`task_no`),
  KEY `hws_tasks_type_step_index` (`type`, `step`),
  CONSTRAINT `hws_tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hws_task_materials` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `task_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `hws_task_materials_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `hws_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hws_task_photos` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `task_id` BIGINT UNSIGNED NOT NULL,
  `type` ENUM('before','after','survey_site') NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  KEY `hws_task_photos_task_id_type_index` (`task_id`, `type`),
  CONSTRAINT `hws_task_photos_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `hws_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hws_site_surveys` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `task_id` BIGINT UNSIGNED NOT NULL,
  `property_type` ENUM('hotel','hospital','bungalow','other') NOT NULL DEFAULT 'other',
  `floors` INT UNSIGNED NULL,
  `built_up_area_sqft` INT UNSIGNED NULL,
  `rooms_units` INT UNSIGNED NULL,
  `water_use_kld` DECIMAL(8,2) NULL,
  `water_source` ENUM('municipal','borewell','tanker','river') NULL,
  `wastewater_disposal` ENUM('septic_tank','open_drain','existing_stp','none') NULL,
  `space_available` ENUM('open_area','limited','basement_only','not_sure') NULL,
  `notes` TEXT NULL,
  `follow_up_date` DATE NULL,
  `status` ENUM('draft','submitted') NOT NULL DEFAULT 'draft',
  `latitude` DECIMAL(10,7) NULL,
  `longitude` DECIMAL(10,7) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `hws_site_surveys_task_id_unique` (`task_id`),
  CONSTRAINT `hws_site_surveys_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `hws_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hws_survey_inquiry_types` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `survey_id` BIGINT UNSIGNED NOT NULL,
  `inquiry_type` ENUM('stp','wtp','etp','ro_plant','softener','amc_only') NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `hws_survey_inquiry_types_survey_id_inquiry_type_unique` (`survey_id`, `inquiry_type`),
  CONSTRAINT `hws_survey_inquiry_types_survey_id_foreign` FOREIGN KEY (`survey_id`) REFERENCES `hws_site_surveys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hws_attendance` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `date` DATE NOT NULL,
  `check_in_time` DATETIME NULL,
  `check_in_lat` DECIMAL(10,7) NULL,
  `check_in_lng` DECIMAL(10,7) NULL,
  `check_in_selfie_path` VARCHAR(255) NULL,
  `check_out_time` DATETIME NULL,
  `check_out_lat` DECIMAL(10,7) NULL,
  `check_out_lng` DECIMAL(10,7) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `hws_attendance_employee_id_date_unique` (`employee_id`, `date`),
  CONSTRAINT `hws_attendance_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hws_leave_requests` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `reason` VARCHAR(255) NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `hws_leave_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hws_leave_requests_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `hws_expense_claims` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `category` VARCHAR(255) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `description` TEXT NULL,
  `receipt_path` VARCHAR(255) NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `hws_expense_claims_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hws_expense_claims_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
