# SG eFootball

[العربية](#العربية) | [English](#english)

## العربية

**SG eFootball** هو موقع محتوى ثنائي اللغة بالعربية والإنجليزية لأخبار eFootball والتشكيلات والتطويرات والتسريبات. يتضمن موقعاً عاماً للمقالات ومنطقة إدارة محمية لإدارة المنشورات المترجمة والوسائط.

### المزايا

- مسارات عربية وإنجليزية مع دعم اتجاه العرض من اليمين إلى اليسار للعربية.
- صفحة رئيسية وقوائم للفئات وصفحات للمنشورات وصفحة اتصال وصفحة 404 مخصصة.
- أربع فئات للمحتوى: الأخبار والتشكيلات والتطويرات والتسريبات.
- حقول منفصلة للعنوان والاسم اللطيف والمحتوى في كل لغة.
- قوائم بأحدث المنشورات وتقسيم صفحات الفئات وتصفية العناوين من جانب العميل.
- صور للمقالات ودعم اختياري لمقاطع فيديو YouTube.
- بيانات وصفية للعناوين الأساسية وOpen Graph واللغات البديلة.
- تسجيل دخول المسؤول وملفات تعريف ارتباط موقّعة للمصادقة وتسجيل الخروج وإعدادات الحساب.
- مسارات عمل للمسؤول لإنشاء المنشورات وتعديلها وحذفها.
- التحقق من الصور ورفعها إلى حاوية Supabase Storage عامة.
- توجيه الطلبات عبر Apache وموجّه PHP خاص بـ Vercel.

### التقنيات

- شفرة تطبيق بأسلوب PHP 8.2
- PostgreSQL عبر PDO
- Supabase PostgreSQL وSupabase Storage
- HTML وCSS مخصص وVanilla JavaScript
- Apache `mod_rewrite`
- ملفات إعداد Docker/Railway وVercel

لا يتطلب المشروع تبعيات Composer أو تثبيت حزم JavaScript.

### بنية المشروع

```text
.
├── admin/                 # المصادقة ولوحة التحكم وعمليات CRUD للمنشورات والرفع والإعدادات
├── api/index.php          # موجّه طلبات Vercel
├── ar/ and en/            # نقاط دخول اللغات والفئات
├── assets/                # الأنماط والبرامج النصية وصور الموقع والوسائط المرفوعة
├── database/schema.sql    # مخطط PostgreSQL والمحتوى الأولي وسياسات التخزين
├── includes/              # الإعدادات وقاعدة البيانات والمصادقة والدوال المساعدة والاستعلامات
├── lang/                  # ترجمات الواجهة العربية والإنجليزية
├── layouts/               # هياكل صفحات الموقع العامة والإدارة
├── pages/                 # قوالب الصفحات المشتركة وبطاقات المنشورات
├── .env.example           # قالب متغيرات البيئة
├── .htaccess              # توجيه Apache وحماية الملفات
├── Dockerfile             # تعريف حاوية Apache/PHP
└── vercel.json            # بيئة تشغيل PHP ومسارات Vercel
```

### المتطلبات

- PHP 8.2 أو أحدث.
- امتدادات PHP: PDO PostgreSQL وcURL وFileinfo وMbstring وGD.
- PostgreSQL، إما محلياً أو عبر Supabase.
- Apache مع `mod_rewrite` و`AllowOverride All`، أو موجّه Vercel المضمّن.
- حاوية Supabase Storage عامة إذا كان رفع الصور من لوحة الإدارة مفعّلاً.

### الإعداد

انسخ قالب متغيرات البيئة:

```bash
cp .env.example .env
```

عيّن مجموعات المتغيرات الآتية في `.env` أو في منصة النشر:

| المتغيرات | الغرض |
| --- | --- |
| `DATABASE_URL` | سلسلة اتصال Supabase/PostgreSQL، ويوصى بها للنشر دون خادم |
| `DB_HOST`، `DB_PORT`، `DB_NAME`، `DB_USER`، `DB_PASS` | اتصال PostgreSQL محلي احتياطي |
| `SUPABASE_URL`، `SUPABASE_SERVICE_ROLE_KEY`، `SUPABASE_BUCKET` | رفع الصور من جانب الخادم |
| `SITE_URL`، `SITE_NAME`، `CREATOR_NAME` | هوية الموقع العامة والعناوين الأساسية |
| `LOCAL_AUTH`، `ADMIN_EMAIL`، `ADMIN_PASSWORD` | تسجيل دخول اختياري للمسؤول يعتمد على متغيرات البيئة |
| `APP_SECRET` | مفتاح توقيع ملفات تعريف ارتباط المصادقة |

استخدم قيمة `APP_SECRET` عشوائية طويلة. أبقِ `.env` وكلمات مرور قاعدة البيانات ومفتاح service-role الخاص بـ Supabase خارج نظام التحكم في الإصدارات.

### إعداد قاعدة البيانات

لإجراء الإعداد الكامل، شغّل `database/schema.sql` في Supabase SQL Editor للمشروع المستهدف. يستخدم القسم الأخير جدولي `storage.buckets` و`storage.objects` الخاصين بـ Supabase؛ وعند استخدام خادم PostgreSQL عادي، نفّذ الجزء الخاص بجداول التطبيق فقط، واحذف قسم التخزين أو استبدله.

> **تحذير:** يبدأ البرنامج النصي بحذف جداول التطبيق، ثم ينشئها مجدداً ويدرج محتوى نموذجياً ومسؤولاً افتراضياً. راجع بيانات المسؤول الأولية وغيّرها قبل تشغيله، ولا تشغّل أبداً برنامج إعادة الضبط هذا على بيانات تحتاج إلى الاحتفاظ بها.

ينشئ البرنامج النصي نفسه حاوية `uploads` عامة وسياسات للتخزين. راجع هذه السياسات وفق نموذج التهديد المقصود لبيئة الإنتاج قبل تطبيقها.

### التشغيل باستخدام Apache

1. وجّه جذر مستندات Apache إلى مجلد هذا المشروع.
2. فعّل `mod_rewrite` واسمح بتجاوزات `.htaccess`.
3. ثبّت امتدادات PHP المطلوبة وأعد تشغيل Apache.
4. أنشئ `.env` واضبط قاعدة البيانات والتخزين واستورد المخطط.
5. افتح `SITE_URL` المضبوط. يعيد المسار الجذري التوجيه إلى العربية أو الإنجليزية استناداً إلى لغة المتصفح.

على Vercel يعالج الموجّه المضمّن المسار `/admin/`. أما إعداد Apache الحالي فيمرّر مجلد `admin` قبل قاعدة المسار النظيف، لذلك افتح `/admin/login.php` مباشرةً، أو أصلح ترتيب قواعد `.htaccess` قبل الاعتماد على `/admin/`.

### ملفات النشر

- يوجّه `vercel.json` الطلبات الديناميكية عبر `api/index.php` ويقدّم الأصول الثابتة مباشرة.
- يضبط `Dockerfile` و`docker-entrypoint.sh` خادم Apache لاستخدام `PORT` الذي توفره المنصة، وهو مناسب للاستضافة بالحاويات على غرار Railway.

يثبّت Dockerfile الحالي امتداد PHP الخاص بـ MySQL، بينما يتصل التطبيق عبر PDO PostgreSQL، كما أنه لا يثبّت PHP cURL. أضف امتدادي PostgreSQL وcURL قبل الاعتماد على الحاوية للوصول إلى قاعدة البيانات ورفع الملفات إلى Supabase.

### الحالة الحالية

يحتوي المستودع على تنفيذ كامل لنظام إدارة محتوى في مرحلة ما قبل الإنتاج ومحتوى نموذجي، لكنه لا يتضمن اختبارات آلية أو ملف قفل للتبعيات. قبل النشر العام، راجع بيانات الاعتماد الأولية ومفتاح ملفات تعريف الارتباط وسياسات التخزين وصلاحيات قاعدة البيانات ومعالجة الأخطاء وعدم تطابق امتدادات Docker الموضح أعلاه.

## English

**SG eFootball** is a bilingual Arabic and English content website for eFootball news, formations, upgrades, and leaks. It includes a public article site and a protected administration area for managing translated posts and media.

### Features

- Arabic and English routes with right-to-left support for Arabic.
- Homepage, category listings, individual posts, contact page, and custom 404 page.
- Four content categories: news, formations, upgrades, and leaks.
- Separate title, slug, and content fields for each language.
- Latest-post lists, category pagination, and client-side title filtering.
- Article images and optional YouTube video support.
- Canonical, Open Graph, and alternate-language metadata.
- Admin login, signed authentication cookies, logout, and account settings.
- Admin create, edit, and delete workflows for posts.
- Image validation and upload to a public Supabase Storage bucket.
- Routing for Apache and a PHP router for Vercel.

### Technology

- PHP 8.2-style application code
- PostgreSQL through PDO
- Supabase PostgreSQL and Supabase Storage
- HTML, custom CSS, and vanilla JavaScript
- Apache `mod_rewrite`
- Docker/Railway and Vercel configuration files

No Composer dependencies or JavaScript package installation are required.

### Project Structure

```text
.
├── admin/                 # Authentication, dashboard, post CRUD, uploads, settings
├── api/index.php          # Vercel request router
├── ar/ and en/            # Locale and category entry points
├── assets/                # Styles, scripts, site images, and uploaded media
├── database/schema.sql    # PostgreSQL schema, seed content, and storage policies
├── includes/              # Configuration, database, auth, helpers, and queries
├── lang/                  # Arabic and English interface translations
├── layouts/               # Public and admin page shells
├── pages/                 # Shared page templates and post cards
├── .env.example           # Environment-variable template
├── .htaccess              # Apache routing and file protection
├── Dockerfile             # Apache/PHP container definition
└── vercel.json            # Vercel PHP runtime and routes
```

### Requirements

- PHP 8.2 or newer.
- PHP extensions: PDO PostgreSQL, cURL, Fileinfo, Mbstring, and GD.
- PostgreSQL, either locally or through Supabase.
- Apache with `mod_rewrite` and `AllowOverride All`, or the included Vercel router.
- A public Supabase Storage bucket if admin image uploads are enabled.

### Configuration

Copy the environment template:

```bash
cp .env.example .env
```

Set these groups of variables in `.env` or in the deployment platform:

| Variables | Purpose |
| --- | --- |
| `DATABASE_URL` | Supabase/PostgreSQL connection string, recommended for serverless deployment |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` | Local PostgreSQL connection fallback |
| `SUPABASE_URL`, `SUPABASE_SERVICE_ROLE_KEY`, `SUPABASE_BUCKET` | Server-side image uploads |
| `SITE_URL`, `SITE_NAME`, `CREATOR_NAME` | Public site identity and canonical URLs |
| `LOCAL_AUTH`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Optional environment-based administrator login |
| `APP_SECRET` | Signature key for authentication cookies |

Use a long random `APP_SECRET`. Keep `.env`, database passwords, and the Supabase service-role key out of version control.

### Database Setup

For the complete setup, run `database/schema.sql` in the target Supabase SQL Editor. The final section uses Supabase-specific `storage.buckets` and `storage.objects` tables; when using a plain PostgreSQL server, execute only the application-table portion and omit or replace the storage section.

> **Warning:** the script starts by dropping the application tables, then creates them again and inserts sample content and a default administrator. Review and change the seed administrator before running it, and never run this reset script against data you need to preserve.

The same script creates a public `uploads` bucket and storage policies. Review those policies for the intended production threat model before applying them.

### Run with Apache

1. Point the Apache document root to this project directory.
2. Enable `mod_rewrite` and allow `.htaccess` overrides.
3. Install the required PHP extensions and restart Apache.
4. Create `.env`, configure the database and storage, and import the schema.
5. Open the configured `SITE_URL`. The root route redirects to Arabic or English from the browser language.

On Vercel, the included router handles `/admin/`. The current Apache rules pass the `admin` directory through before the clean-route rule, so open `/admin/login.php` directly or fix the `.htaccess` rule order before relying on `/admin/`.

### Deployment Files

- `vercel.json` routes dynamic requests through `api/index.php` and serves static assets directly.
- `Dockerfile` and `docker-entrypoint.sh` configure Apache to use the platform-provided `PORT`, which is suitable for Railway-style container hosting.

The current Dockerfile installs the PHP MySQL extension, while the application connects through PDO PostgreSQL, and it does not install PHP cURL. Add the PostgreSQL and cURL extensions before relying on the container for database access and Supabase uploads.

### Current Status

The repository contains a complete pre-production CMS implementation and sample content, but it has no automated tests or dependency lockfile. Before a public deployment, review the seed credentials, cookie secret, storage policies, database privileges, error handling, and the Docker extension mismatch described above.
