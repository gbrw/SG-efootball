-- ═══════════════════════════════════════════════════════════════════════
--  SG_Efootball1 — MySQL Schema
--  التعليمات:
--   1. افتح phpMyAdmin  ←  http://localhost/phpmyadmin
--   2. اضغط "New" وأنشئ قاعدة بيانات:  sg_efootball
--      Collation: utf8mb4_unicode_ci
--   3. اختر القاعدة ← تبويب SQL ← الصق الكود كاملاً ← Go
-- ═══════════════════════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET time_zone = '+03:00';
SET foreign_key_checks = 0;

-- ─── حذف الجداول القديمة ──────────────────────────────────────────────────
DROP TABLE IF EXISTS `post_translations`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `admins`;

-- ─── جدول المشرفين ───────────────────────────────────────────────────────
CREATE TABLE `admins` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`      VARCHAR(191) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── جدول المنشورات ──────────────────────────────────────────────────────
CREATE TABLE `posts` (
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

-- ─── جدول الترجمات ───────────────────────────────────────────────────────
CREATE TABLE `post_translations` (
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

SET foreign_key_checks = 1;

-- ─── مشرف افتراضي ────────────────────────────────────────────────────────
-- البريد:  admin@sg-efootball.com
-- كلمة السر:  SG@Admin2026
INSERT INTO `admins` (`email`, `password`) VALUES
('admin@sg-efootball.com', '$2y$12$IGIei/5nYxJP1hcng.LE6.diu/F1OrO2ssFylIIqPBzo1kU6zsqfO');

-- ─── بيانات تجريبية ──────────────────────────────────────────────────────
INSERT INTO `posts` (`category`, `image_url`, `video_url`) VALUES
('news',       '/assets/img/placeholder.svg', NULL),
('formations', '/assets/img/placeholder.svg', NULL),
('leaks',      '/assets/img/placeholder.svg', NULL);

INSERT INTO `post_translations` (`post_id`, `language`, `title`, `slug`, `content`) VALUES
(1, 'ar', 'آخر أخبار eFootball', 'akhir-akhbar-efootball', '<p>أهلاً بكم في موقع SG_Efootball1.</p>'),
(1, 'en', 'Latest eFootball News', 'latest-efootball-news', '<p>Welcome to SG_Efootball1.</p>'),
(2, 'ar', 'أفضل تشكيلة 4-3-3', 'afdal-tashkila-433', '<p>شرح تشكيلة 4-3-3 التكتيكية.</p>'),
(2, 'en', 'Best 4-3-3 Formation', 'best-433-formation', '<p>Breakdown of the 4-3-3 formation.</p>'),
(3, 'ar', 'تسريبات اللاعبين الجدد', 'tasribat-laabeen-judud', '<p>أحدث التسريبات.</p>'),
(3, 'en', 'New Player Leaks', 'new-player-leaks', '<p>Latest leaks about upcoming players.</p>');
