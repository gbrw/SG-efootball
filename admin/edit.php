<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/functions.php';

requireAdmin();

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$postId) {
    header('Location: /admin/dashboard.php');
    exit;
}

// Load existing post with translations
$pdo  = db();
$stmt = $pdo->prepare(
    "SELECT p.*, pt.id AS t_id, pt.language, pt.title, pt.slug, pt.content
     FROM posts p
     LEFT JOIN post_translations pt ON pt.post_id = p.id
     WHERE p.id = :id"
);
$stmt->execute([':id' => $postId]);
$rows = $stmt->fetchAll();

if (!$rows) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'المنشور غير موجود.'];
    header('Location: /admin/dashboard.php');
    exit;
}

$post   = $rows[0];
$byLang = [];
foreach ($rows as $r) {
    if ($r['language']) {
        $byLang[$r['language']] = [
            'id'      => $r['t_id'],
            'title'   => $r['title'],
            'slug'    => $r['slug'],
            'content' => $r['content'],
        ];
    }
}

$errors = [];
$form = [
    'category'   => $post['category'] ?? 'news',
    'image_url'  => $post['image_url'] ?? '',
    'video_url'  => $post['video_url'] ?? '',
    'ar_title'   => $byLang['ar']['title']   ?? '',
    'ar_slug'    => $byLang['ar']['slug']    ?? '',
    'ar_content' => $byLang['ar']['content'] ?? '',
    'en_title'   => $byLang['en']['title']   ?? '',
    'en_slug'    => $byLang['en']['slug']    ?? '',
    'en_content' => $byLang['en']['content'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $key => $_) {
        $form[$key] = trim($_POST[$key] ?? '');
    }

    if (!in_array($form['category'], CATEGORIES)) $errors[] = 'التصنيف غير صحيح.';
    if (empty($form['image_url']))   $errors[] = 'صورة الغلاف مطلوبة.';
    if (empty($form['ar_title']))    $errors[] = 'العنوان بالعربية مطلوب.';
    if (empty($form['ar_slug']))     $errors[] = 'الرابط المختصر العربي مطلوب.';
    if (empty($form['ar_content']))  $errors[] = 'المحتوى بالعربية مطلوب.';
    if (empty($form['en_title']))    $errors[] = 'العنوان بالإنجليزية مطلوب.';
    if (empty($form['en_slug']))     $errors[] = 'الرابط المختصر الإنجليزي مطلوب.';
    if (empty($form['en_content']))  $errors[] = 'المحتوى بالإنجليزية مطلوب.';

    if (empty($errors)) {
        try {
            $pdo = db();

            // Update post
            $pdo->prepare(
                "UPDATE posts SET category=:cat, image_url=:img, video_url=:vid WHERE id=:id"
            )->execute([
                ':cat' => $form['category'],
                ':img' => $form['image_url'],
                ':vid' => $form['video_url'] ?: null,
                ':id'  => $postId,
            ]);

            // Upsert translations
            $sql = "INSERT INTO post_translations (post_id, language, title, slug, content)
                    VALUES (:pid, :lang, :title, :slug, :content)
                    ON DUPLICATE KEY UPDATE title=VALUES(title), slug=VALUES(slug), content=VALUES(content)";
            $stmtT = $pdo->prepare($sql);
            foreach (['ar', 'en'] as $lang) {
                $stmtT->execute([
                    ':pid'     => $postId,
                    ':lang'    => $lang,
                    ':title'   => $form["{$lang}_title"],
                    ':slug'    => $form["{$lang}_slug"],
                    ':content' => $form["{$lang}_content"],
                ]);
            }

            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'تم تحديث المنشور بنجاح'];
            header('Location: /admin/dashboard.php');
            exit;

        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }
    }
}

$pageTitle = 'تعديل المنشور';
$bodyMode  = 'edit';
include dirname(__DIR__) . '/layouts/admin-header.php';
?>

<div class="page-header">
  <div>
    <h1><i class="fa-solid fa-pen-to-square" style="color:var(--purple);font-size:1.2rem;"></i> تعديل المنشور <span style="color:var(--text-faint);font-size:.75em;">#<?= $postId ?></span></h1>
    <p>تحديث المحتوى بالعربية والإنجليزية</p>
  </div>
  <a href="/admin/dashboard.php" class="btn btn-ghost btn-sm"><i class="fa-solid fa-arrow-right"></i> رجوع</a>
</div>

<?php if (!empty($errors)): ?>
<div class="flash flash-error">
  <?php foreach ($errors as $err): ?><div><i class="fa-solid fa-circle-exclamation"></i> <?= h($err) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<form method="POST" action="" id="post-form">

  <!-- ── Settings ──────────────────────────────────────────────── -->
  <div class="form-card">
    <div class="form-section-title"><i class="fa-solid fa-sliders"></i> إعدادات المنشور</div>
    <div class="form-grid">
      <div class="form-group">
        <label class="field-label" for="category">التصنيف</label>
        <select name="category" id="category" class="field-input">
          <?php foreach (CATEGORIES as $cat): ?>
          <option value="<?= $cat ?>" <?= $form['category'] === $cat ? 'selected' : '' ?>>
            <?= categoryIcon($cat) ?> <?= h(categoryLabel($cat, 'ar')) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="field-label" for="video_url">رابط الفيديو — يوتيوب / تيك توك <span style="color:var(--text-faint);font-weight:400;">(اختياري)</span></label>
        <input type="url" name="video_url" id="video_url" class="field-input"
               value="<?= h($form['video_url']) ?>" placeholder="https://youtube.com/watch?v=...">
      </div>
    </div>
  </div>

  <!-- ── Cover Image ────────────────────────────────────────────── -->
  <div class="form-card">
    <div class="form-section-title"><i class="fa-solid fa-image"></i> صورة الغلاف</div>

    <input type="hidden" name="image_url" id="image_url" value="<?= h($form['image_url']) ?>">

    <?php if ($form['image_url']): ?>
    <div class="upload-preview" id="upload-preview">
      <img id="preview-img" src="<?= h($form['image_url']) ?>" alt="preview">
      <button type="button" class="remove-btn" id="remove-image">✕</button>
    </div>
    <div id="upload-zone" class="upload-zone" style="display:none;">
    <?php else: ?>
    <div id="upload-preview" class="upload-preview" style="display:none;">
      <img id="preview-img" src="" alt="preview">
      <button type="button" class="remove-btn" id="remove-image">✕</button>
    </div>
    <div id="upload-zone" class="upload-zone">
    <?php endif; ?>
      <div class="upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
      <strong>اضغط أو اسحب لرفع صورة جديدة</strong>
      <span style="font-size:.8rem;color:var(--text-faint);">PNG أو JPG أو WebP — الحجم الأقصى 5 MB</span>
    </div>
    <input type="file" id="image-file" accept="image/*" style="display:none;">

    <p style="text-align:center;color:var(--text-faint);font-size:.8rem;margin:.75rem 0;">— أو الصق رابط الصورة مباشرة —</p>
    <input type="url" class="field-input" value="<?= h($form['image_url']) ?>"
           placeholder="https://example.com/image.jpg"
           oninput="document.getElementById('image_url').value=this.value;
                    if(this.value){document.getElementById('preview-img').src=this.value;
                    document.getElementById('upload-zone').style.display='none';
                    document.getElementById('upload-preview').style.display='block';}">
  </div>

  <!-- ── Arabic ─────────────────────────────────────────────────── -->
  <div class="form-card" dir="rtl">
    <div class="form-section-title"><i class="fa-solid fa-language"></i> العربية</div>
    <div class="form-grid">
      <div class="form-group">
        <label class="field-label" for="ar_title">العنوان</label>
        <input type="text" name="ar_title" id="ar_title" class="field-input"
               value="<?= h($form['ar_title']) ?>" placeholder="عنوان المنشور" required>
      </div>
      <div class="form-group">
        <label class="field-label" for="ar_slug">Slug</label>
        <input type="text" name="ar_slug" id="ar_slug" class="field-input"
               value="<?= h($form['ar_slug']) ?>" placeholder="عنوان-المنشور" dir="ltr" required>
      </div>
      <div class="form-group full">
        <label class="field-label" for="ar_content">المحتوى (HTML)</label>
        <div class="editor-toolbar" data-target="ar_content">
          <button type="button" class="tb-btn" data-insert="<h2></h2>" title="عنوان H2"><i class="fa-solid fa-heading"></i> H2</button>
          <button type="button" class="tb-btn" data-insert="<h3></h3>" title="عنوان H3"><i class="fa-solid fa-heading"></i> H3</button>
          <button type="button" class="tb-btn" data-insert="<p></p>" title="فقرة"><i class="fa-solid fa-paragraph"></i></button>
          <button type="button" class="tb-btn" data-insert="<strong></strong>" title="عريض"><i class="fa-solid fa-bold"></i></button>
          <button type="button" class="tb-btn" data-insert="<em></em>" title="مائل"><i class="fa-solid fa-italic"></i></button>
          <button type="button" class="tb-btn" data-insert='<a href=""></a>' title="رابط"><i class="fa-solid fa-link"></i></button>
          <button type="button" class="tb-btn" data-insert="<blockquote></blockquote>" title="اقتباس"><i class="fa-solid fa-quote-right"></i></button>
          <button type="button" class="tb-btn" data-insert="<ul>&#10;  <li></li>&#10;</ul>" title="قائمة"><i class="fa-solid fa-list-ul"></i></button>
          <div class="tb-sep"></div>
          <button type="button" class="tb-btn tb-img-btn" title="إدراج صورة داخل النص"><i class="fa-solid fa-image"></i> صورة</button>
          <input type="file" class="tb-img-input" accept="image/*" style="display:none;">
        </div>
        <textarea name="ar_content" id="ar_content" class="field-input"
                  placeholder="<p>اكتب المحتوى هنا...</p>" required><?= h($form['ar_content']) ?></textarea>
      </div>
    </div>
  </div>

  <!-- ── English ────────────────────────────────────────────────── -->
  <div class="form-card">
    <div class="form-section-title"><i class="fa-solid fa-globe"></i> النص الإنجليزي</div>
    <div class="form-grid">
      <div class="form-group">
        <label class="field-label" for="en_title">العنوان (إنجليزي)</label>
        <input type="text" name="en_title" id="en_title" class="field-input"
               value="<?= h($form['en_title']) ?>" placeholder="Post title" required>
      </div>
      <div class="form-group">
        <label class="field-label" for="en_slug">الرابط المختصر (إنجليزي)</label>
        <input type="text" name="en_slug" id="en_slug" class="field-input"
               value="<?= h($form['en_slug']) ?>" placeholder="post-slug" required>
      </div>
      <div class="form-group full">
        <label class="field-label" for="en_content">المحتوى (HTML إنجليزي)</label>
        <div class="editor-toolbar" data-target="en_content">
          <button type="button" class="tb-btn" data-insert="<h2></h2>" title="عنوان H2"><i class="fa-solid fa-heading"></i> H2</button>
          <button type="button" class="tb-btn" data-insert="<h3></h3>" title="عنوان H3"><i class="fa-solid fa-heading"></i> H3</button>
          <button type="button" class="tb-btn" data-insert="<p></p>" title="فقرة"><i class="fa-solid fa-paragraph"></i></button>
          <button type="button" class="tb-btn" data-insert="<strong></strong>" title="عريض"><i class="fa-solid fa-bold"></i></button>
          <button type="button" class="tb-btn" data-insert="<em></em>" title="مائل"><i class="fa-solid fa-italic"></i></button>
          <button type="button" class="tb-btn" data-insert='<a href=""></a>' title="رابط"><i class="fa-solid fa-link"></i></button>
          <button type="button" class="tb-btn" data-insert="<blockquote></blockquote>" title="اقتباس"><i class="fa-solid fa-quote-right"></i></button>
          <button type="button" class="tb-btn" data-insert="<ul>&#10;  <li></li>&#10;</ul>" title="قائمة"><i class="fa-solid fa-list-ul"></i></button>
          <div class="tb-sep"></div>
          <button type="button" class="tb-btn tb-img-btn" title="إدراج صورة"><i class="fa-solid fa-image"></i> صورة</button>
          <input type="file" class="tb-img-input" accept="image/*" style="display:none;">
        </div>
        <textarea name="en_content" id="en_content" class="field-input"
                  placeholder="<p>Write content here...</p>" required><?= h($form['en_content']) ?></textarea>
      </div>
    </div>
  </div>

  <!-- Submit -->
  <div style="display:flex;align-items:center;gap:1rem;">
    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> حفظ التغييرات</button>
    <a href="/admin/dashboard.php" class="btn btn-ghost">إلغاء</a>
  </div>

</form>

<?php include dirname(__DIR__) . '/layouts/admin-footer.php'; ?>
