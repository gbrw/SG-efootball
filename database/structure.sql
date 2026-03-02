-- ═══════════════════════════════════════════════════════════════════════
--  SG_Efootball1 — هيكل الجداول فقط (بدون بيانات)
--  شغّل هذا أولاً إن كانت الجداول غير موجودة
-- ═══════════════════════════════════════════════════════════════════════

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `admins` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`      VARCHAR(191) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category`   ENUM('news','formations','upgrades','leaks') NOT NULL DEFAULT 'news',
  `image_url`  TEXT         NOT NULL,
  `video_url`  TEXT         NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category`   (`category`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `post_translations` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `post_id`    INT UNSIGNED    NOT NULL,
  `language`   ENUM('ar','en') NOT NULL,
  `title`      VARCHAR(500)    NOT NULL,
  `slug`       VARCHAR(500)    NOT NULL,
  `content`    LONGTEXT        NOT NULL,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_post_lang` (`post_id`, `language`),
  UNIQUE KEY `uq_lang_slug` (`language`, `slug`(191)),
  KEY `idx_slug` (`slug`(191)),
  CONSTRAINT `fk_pt_post`
    FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
