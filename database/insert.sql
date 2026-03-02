-- ═══════════════════════════════════════════════════════════════════════
--  SG_Efootball1 — إدراج البيانات فقط
--  قبل تشغيل هذا الكود: تأكد أن الجداول موجودة من خلال phpMyAdmin
--
--  التعليمات:
--   1. افتح phpMyAdmin → اختر قاعدة sg_efootball
--   2. تبويب SQL ← الصق الكود ← Go
-- ═══════════════════════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET foreign_key_checks = 0;

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

SET foreign_key_checks = 1;
