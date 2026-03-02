-- ═══════════════════════════════════════════════════════════════════════════
--  SG_Efootball1 — Complete Database Import
--  صاحب الموقع: سيف جبار | SG_Efootball1
--
--  طريقة الاستيراد:
--   phpMyAdmin → Import → اختر هذا الملف → Go
--   أو: phpMyAdmin → SQL → الصق الكود → Go
-- ═══════════════════════════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET time_zone = '+03:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

-- ─── إنشاء قاعدة البيانات ────────────────────────────────────────────────────
CREATE DATABASE IF NOT EXISTS `sg_efootball`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `sg_efootball`;

-- ─── جدول المشرفين ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admins` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`      VARCHAR(191) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── جدول المنشورات ──────────────────────────────────────────────────────────
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

-- ─── جدول الترجمات ───────────────────────────────────────────────────────────
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

SET foreign_key_checks = 1;

-- ─── مشرف افتراضي ────────────────────────────────────────────────────────────
-- البريد    : admin@sg-efootball.com
-- كلمة السر : SG@Admin2026
INSERT IGNORE INTO `admins` (`email`, `password`) VALUES
('admin@sg-efootball.com', '$2y$12$IGIei/5nYxJP1hcng.LE6.diu/F1OrO2ssFylIIqPBzo1kU6zsqfO');

-- ─── منشورات تجريبية ─────────────────────────────────────────────────────────
INSERT IGNORE INTO `posts` (`id`, `category`, `image_url`, `video_url`) VALUES
(1, 'news',       '/assets/img/sg-banner.jpg', NULL),
(2, 'formations', '/assets/img/sg-banner2.jpg', NULL),
(3, 'leaks',      '/assets/img/sg-banner3.png', NULL);

INSERT IGNORE INTO `post_translations` (`post_id`, `language`, `title`, `slug`, `content`) VALUES
(1, 'ar', 'آخر أخبار eFootball 2026', 'akhir-akhbar-efootball-2026',
 '<p>أهلاً بكم في الموقع الرسمي لـ سيف جبار — SG_Efootball1. متابعة لكل جديد في عالم eFootball.</p>'),
(1, 'en', 'Latest eFootball 2026 News', 'latest-efootball-2026-news',
 '<p>Welcome to the official website of Saif Jabbar — SG_Efootball1. Your source for eFootball news.</p>'),
(2, 'ar', 'أفضل تشكيلة 4-3-3 مع سيف جبار', 'afdal-tashkila-433-sg',
 '<p>شرح مفصّل لتشكيلة 4-3-3 التكتيكية مع نقاط القوة والضعف من خبير تكتيكي GS Team.</p>'),
(2, 'en', 'Best 4-3-3 Formation by SG', 'best-433-formation-sg',
 '<p>Full tactical breakdown of the 4-3-3 formation by GS Team coach Saif Jabbar.</p>'),
(3, 'ar', 'تسريبات اللاعبين القادمين', 'tasribat-laabeen-qadimin',
 '<p>أحدث التسريبات حول اللاعبين القادمين في تحديثات eFootball القادمة.</p>'),
(3, 'en', 'Upcoming Player Leaks', 'upcoming-player-leaks',
 '<p>Latest leaks about players coming in upcoming eFootball updates.</p>');
