  </main><!-- /admin-main -->
</div><!-- /admin-wrap -->

<script src="/assets/js/main.js"></script>
<script>
/* ── Editor toolbar ────────────────────────────────────────────────────────
   Inserts HTML snippets / images at cursor position in content textareas
──────────────────────────────────────────────────────────────────────── */
(function () {

  // Insert text at textarea cursor, smart-wrap selected text if any
  function insertAtCursor(el, html) {
    const start    = el.selectionStart ?? el.value.length;
    const end      = el.selectionEnd   ?? el.value.length;
    const before   = el.value.substring(0, start);
    const selected = el.value.substring(start, end);
    const after    = el.value.substring(end);

    // Detect open+close pair like <h2></h2>
    const pair = html.match(/^(<[a-z0-9]+(?:\s[^>]*)?>)(<\/[a-z0-9]+>)$/i);
    let insert, cursor;
    if (pair) {
      insert = pair[1] + (selected || '') + pair[2];
      cursor = start + pair[1].length + (selected ? selected.length : 0) + pair[2].length;
      if (!selected) cursor = start + pair[1].length; // place inside
    } else {
      insert = html;
      cursor = start + html.length;
    }

    el.value = before + insert + after;
    el.focus();
    el.setSelectionRange(cursor, cursor);
    el.dispatchEvent(new Event('input'));
  }

  document.querySelectorAll('.editor-toolbar').forEach(function (toolbar) {
    const targetId = toolbar.dataset.target;
    const target   = targetId ? document.getElementById(targetId) : null;
    if (!target) return;

    // HTML snippet buttons
    toolbar.querySelectorAll('.tb-btn[data-insert]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        insertAtCursor(target, btn.dataset.insert);
      });
    });

    // Inline image upload button
    const imgBtn   = toolbar.querySelector('.tb-img-btn');
    const imgInput = toolbar.querySelector('.tb-img-input');
    if (imgBtn && imgInput) {
      imgBtn.addEventListener('click', function () { imgInput.click(); });
      imgInput.addEventListener('change', function () {
        const file = imgInput.files && imgInput.files[0];
        if (!file) return;
        if (!file.type.startsWith('image/'))           { window.showToast && showToast('اختر ملف صورة', 'error'); return; }
        if (file.size > 5 * 1024 * 1024)               { window.showToast && showToast('الصورة أكبر من 5 MB', 'error'); return; }

        const fd = new FormData();
        fd.append('image', file);
        window.showToast && showToast('جارٍ رفع الصورة…', 'info');

        fetch('/admin/upload', { method: 'POST', body: fd })
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (data.url) {
              insertAtCursor(target, '<img src="' + data.url + '" alt="" style="max-width:100%;border-radius:.6rem;margin:1rem 0;">');
              window.showToast && showToast('✓ تم رفع الصورة', 'success');
            } else {
              window.showToast && showToast(data.error || 'فشل الرفع', 'error');
            }
          })
          .catch(function () { window.showToast && showToast('فشل الرفع', 'error'); });

        imgInput.value = '';
      });
    }
  });

})();
</script>
</body>
</html>
