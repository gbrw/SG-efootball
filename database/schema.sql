-- ═══════════════════════════════════════════════════════════════════════
--  SG_Efootball1 — PostgreSQL Schema (Supabase)
--  التعليمات:
--   1. افتح Supabase Dashboard → SQL Editor
--   2. الصق الكود كاملاً واضغط Run
-- ═══════════════════════════════════════════════════════════════════════

-- ─── حذف الجداول القديمة ──────────────────────────────────────────────────
DROP TABLE IF EXISTS post_translations CASCADE;
DROP TABLE IF EXISTS posts CASCADE;
DROP TABLE IF EXISTS admins CASCADE;

-- ─── دالة تحديث updated_at تلقائياً ──────────────────────────────────────
CREATE OR REPLACE FUNCTION update_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ─── جدول المشرفين ───────────────────────────────────────────────────────
CREATE TABLE admins (
  id         SERIAL       PRIMARY KEY,
  email      VARCHAR(191) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  created_at TIMESTAMPTZ  NOT NULL DEFAULT NOW()
);

-- ─── جدول المنشورات ──────────────────────────────────────────────────────
CREATE TABLE posts (
  id         SERIAL      PRIMARY KEY,
  category   VARCHAR(20) NOT NULL DEFAULT 'news'
               CHECK (category IN ('news','formations','upgrades','leaks')),
  image_url  TEXT        NOT NULL,
  video_url  TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_posts_category   ON posts (category);
CREATE INDEX idx_posts_created_at ON posts (created_at);

CREATE TRIGGER trg_posts_updated_at
  BEFORE UPDATE ON posts
  FOR EACH ROW EXECUTE FUNCTION update_updated_at();

-- ─── جدول الترجمات ───────────────────────────────────────────────────────
CREATE TABLE post_translations (
  id         SERIAL      PRIMARY KEY,
  post_id    INT         NOT NULL REFERENCES posts(id) ON DELETE CASCADE ON UPDATE CASCADE,
  language   VARCHAR(2)  NOT NULL CHECK (language IN ('ar','en')),
  title      VARCHAR(500) NOT NULL,
  slug       VARCHAR(500) NOT NULL,
  content    TEXT        NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  UNIQUE (post_id, language),
  UNIQUE (language, slug)
);

CREATE INDEX idx_pt_slug ON post_translations (slug);

CREATE TRIGGER trg_pt_updated_at
  BEFORE UPDATE ON post_translations
  FOR EACH ROW EXECUTE FUNCTION update_updated_at();

-- ─── مشرف افتراضي ────────────────────────────────────────────────────────
-- البريد:  admin@sg-efootball.com
-- كلمة السر:  SG@Admin2026
INSERT INTO admins (email, password) VALUES
('admin@sg-efootball.com', '$2y$12$IGIei/5nYxJP1hcng.LE6.diu/F1OrO2ssFylIIqPBzo1kU6zsqfO');

-- ─── بيانات تجريبية ──────────────────────────────────────────────────────
INSERT INTO posts (category, image_url, video_url) VALUES
('news',       '/assets/img/placeholder.svg', NULL),
('formations', '/assets/img/placeholder.svg', NULL),
('leaks',      '/assets/img/placeholder.svg', NULL);

INSERT INTO post_translations (post_id, language, title, slug, content) VALUES
(1, 'ar', 'آخر أخبار eFootball', 'akhir-akhbar-efootball', '<p>أهلاً بكم في موقع SG_Efootball1.</p>'),
(1, 'en', 'Latest eFootball News', 'latest-efootball-news', '<p>Welcome to SG_Efootball1.</p>'),
(2, 'ar', 'أفضل تشكيلة 4-3-3', 'afdal-tashkila-433', '<p>شرح تشكيلة 4-3-3 التكتيكية.</p>'),
(2, 'en', 'Best 4-3-3 Formation', 'best-433-formation', '<p>Breakdown of the 4-3-3 formation.</p>'),
(3, 'ar', 'تسريبات اللاعبين الجدد', 'tasribat-laabeen-judud', '<p>أحدث التسريبات.</p>'),
(3, 'en', 'New Player Leaks', 'new-player-leaks', '<p>Latest leaks about upcoming players.</p>');

-- ─── Supabase Storage — سياسات bucket "uploads" ──────────────────────────
-- شغّل هذا بعد إنشاء الـ bucket من Dashboard → Storage → New bucket (Public)

INSERT INTO storage.buckets (id, name, public)
VALUES ('uploads', 'uploads', true)
ON CONFLICT (id) DO UPDATE SET public = true;

CREATE POLICY "public read uploads"
  ON storage.objects FOR SELECT
  USING (bucket_id = 'uploads');

CREATE POLICY "anon upload to uploads"
  ON storage.objects FOR INSERT
  TO anon WITH CHECK (bucket_id = 'uploads');

CREATE POLICY "anon delete from uploads"
  ON storage.objects FOR DELETE
  TO anon USING (bucket_id = 'uploads');
