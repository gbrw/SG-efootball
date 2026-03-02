-- ═══════════════════════════════════════════════════════════════════════
--  SG_Efootball1 — PostgreSQL Schema (Supabase)
--  الخطوات:
--   1. افتح Supabase Dashboard → SQL Editor
--   2. الصق هذا الكود كاملاً واضغط Run
-- ═══════════════════════════════════════════════════════════════════════

-- ─── حذف الجداول القديمة ─────────────────────────────────────────────────
DROP TABLE IF EXISTS post_translations CASCADE;
DROP TABLE IF EXISTS posts CASCADE;
DROP TABLE IF EXISTS admins CASCADE;

-- ─── trigger function لتحديث updated_at تلقائياً ─────────────────────────
CREATE OR REPLACE FUNCTION set_updated_at()
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
  created_at TIMESTAMP    NOT NULL DEFAULT NOW()
);

-- ─── جدول المنشورات ──────────────────────────────────────────────────────
CREATE TABLE posts (
  id         SERIAL      PRIMARY KEY,
  category   VARCHAR(20) NOT NULL DEFAULT 'news'
               CHECK (category IN ('news','formations','upgrades','leaks')),
  image_url  TEXT        NOT NULL,
  video_url  TEXT,
  created_at TIMESTAMP   NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMP   NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_posts_category   ON posts (category);
CREATE INDEX idx_posts_created_at ON posts (created_at DESC);

CREATE TRIGGER trg_posts_updated_at
  BEFORE UPDATE ON posts
  FOR EACH ROW EXECUTE FUNCTION set_updated_at();

-- ─── جدول الترجمات ───────────────────────────────────────────────────────
CREATE TABLE post_translations (
  id         SERIAL      PRIMARY KEY,
  post_id    INTEGER     NOT NULL REFERENCES posts (id) ON DELETE CASCADE ON UPDATE CASCADE,
  language   VARCHAR(2)  NOT NULL CHECK (language IN ('ar','en')),
  title      VARCHAR(500) NOT NULL,
  slug       VARCHAR(500) NOT NULL,
  content    TEXT         NOT NULL,
  created_at TIMESTAMP   NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMP   NOT NULL DEFAULT NOW(),
  UNIQUE (post_id, language),
  UNIQUE (language, slug)
);

CREATE INDEX idx_pt_slug ON post_translations (slug);

CREATE TRIGGER trg_pt_updated_at
  BEFORE UPDATE ON post_translations
  FOR EACH ROW EXECUTE FUNCTION set_updated_at();

-- ─── مشرف افتراضي ────────────────────────────────────────────────────────
-- البريد:  admin@sg-efootball.com
-- كلمة السر:  SG@Admin2026
INSERT INTO admins (email, password) VALUES
('admin@sg-efootball.com', '$2y$12$IGIei/5nYxJP1hcng.LE6.diu/F1OrO2ssFylIIqPBzo1kU6zsqfO');
