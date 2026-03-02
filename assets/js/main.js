/* ──────────────────────────────────────────────────────────────────────────
   SG_Efootball1 — main.js
────────────────────────────────────────────────────────────────────────── */

(function () {
  'use strict';

  // ── Dark / Light mode ────────────────────────────────────────────────────
  const html        = document.documentElement;
  const themeBtn    = document.getElementById('theme-toggle');
  const THEME_KEY   = 'sg-theme';
  // Apply saved or system preference
  const savedTheme  = localStorage.getItem(THEME_KEY);
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const initTheme   = savedTheme || (prefersDark ? 'dark' : 'light');
  html.setAttribute('data-theme', initTheme);

  function applyTheme(t) {
    html.setAttribute('data-theme', t);
    localStorage.setItem(THEME_KEY, t);
    if (themeBtn) {
      // Update only the <i> icon — don't overwrite sibling label spans
      const icon = themeBtn.querySelector('i');
      if (icon) {
        icon.className = t === 'dark' ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
      } else {
        themeBtn.innerHTML = t === 'dark'
          ? '<i class="fa-solid fa-moon"></i>'
          : '<i class="fa-solid fa-sun"></i>';
      }
      // Update optional label span (admin sidebar)
      const lbl = themeBtn.querySelector('[data-theme-label]');
      if (lbl) lbl.textContent = t === 'dark' ? 'الوضع الليلي' : 'الوضع النهاري';
      themeBtn.setAttribute('aria-label', t === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
    }
  }
  applyTheme(initTheme);

  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const current = html.getAttribute('data-theme') ?? 'dark';
      applyTheme(current === 'dark' ? 'light' : 'dark');
    });
  }

  // ── Admin mobile sidebar ─────────────────────────────────────────────────
  const adminMenuBtn = document.getElementById('admin-menu-btn');
  const adminSidebar = document.getElementById('admin-sidebar');
  const adminOverlay = document.getElementById('admin-overlay');

  if (adminMenuBtn && adminSidebar && adminOverlay) {
    function openAdminSidebar() {
      adminSidebar.classList.add('open');
      adminOverlay.classList.add('open');
    }
    function closeAdminSidebar() {
      adminSidebar.classList.remove('open');
      adminOverlay.classList.remove('open');
    }
    adminMenuBtn.addEventListener('click', openAdminSidebar);
    adminOverlay.addEventListener('click', closeAdminSidebar);
    // Close on nav link click (mobile)
    adminSidebar.querySelectorAll('nav a').forEach((a) => {
      a.addEventListener('click', closeAdminSidebar);
    });
  }

  // ── Mobile nav toggle ────────────────────────────────────────────────────
  const navToggle  = document.getElementById('nav-toggle');
  const mobileMenu = document.getElementById('mobile-menu');

  if (navToggle && mobileMenu) {
    navToggle.addEventListener('click', () => {
      const isOpen = mobileMenu.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', isOpen);
    });
    // Close when clicking outside
    document.addEventListener('click', (e) => {
      if (!navToggle.contains(e.target) && !mobileMenu.contains(e.target)) {
        mobileMenu.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ── Image upload preview (admin) ─────────────────────────────────────────
  const fileInput     = document.getElementById('image-file');
  const uploadZone    = document.getElementById('upload-zone');
  const uploadPreview = document.getElementById('upload-preview');
  const previewImg    = document.getElementById('preview-img');
  const removeBtn     = document.getElementById('remove-image');
  const imageUrlInput = document.getElementById('image_url');

  if (fileInput && uploadZone) {
    // Click zone → open file picker
    uploadZone.addEventListener('click', () => fileInput.click());

    // Drag & drop
    uploadZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadZone.style.borderColor = 'var(--green)';
    });
    uploadZone.addEventListener('dragleave', () => {
      uploadZone.style.borderColor = '';
    });
    uploadZone.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadZone.style.borderColor = '';
      const file = e.dataTransfer?.files?.[0];
      if (file) handleImageFile(file);
    });

    fileInput.addEventListener('change', () => {
      const file = fileInput.files?.[0];
      if (file) handleImageFile(file);
    });
  }

  function handleImageFile(file) {
    if (!file.type.startsWith('image/')) { showToast('Please select an image file', 'error'); return; }
    if (file.size > 5 * 1024 * 1024)    { showToast('Image must be under 5 MB', 'error'); return; }

    const reader = new FileReader();
    reader.onload = (e) => {
      if (previewImg)    previewImg.src = e.target.result;
      if (uploadZone)    uploadZone.style.display    = 'none';
      if (uploadPreview) uploadPreview.style.display = 'block';
    };
    reader.readAsDataURL(file);

    // Upload via AJAX
    uploadImageAjax(file);
  }

  function uploadImageAjax(file) {
    const formData = new FormData();
    formData.append('image', file);

    showToast('Uploading image…', 'info');

    fetch('/admin/upload.php', { method: 'POST', body: formData })
      .then((r) => r.json())
      .then((data) => {
        if (data.url) {
          if (imageUrlInput) imageUrlInput.value = data.url;
          showToast('Image uploaded ✓', 'success');
        } else {
          showToast(data.error ?? 'Upload failed', 'error');
          resetUpload();
        }
      })
      .catch(() => {
        showToast('Upload failed — check connection', 'error');
        resetUpload();
      });
  }

  if (removeBtn) {
    removeBtn.addEventListener('click', resetUpload);
  }

  function resetUpload() {
    if (fileInput)     fileInput.value      = '';
    if (imageUrlInput) imageUrlInput.value  = '';
    if (uploadZone)    uploadZone.style.display    = 'block';
    if (uploadPreview) uploadPreview.style.display = 'none';
    if (previewImg)    previewImg.src = '';
  }

  // ── Auto-slug from title (admin create) ─────────────────────────────────
  function slugify(str) {
    return str.toLowerCase().trim()
      .replace(/[\s_]+/g, '-')
      .replace(/[^\u0600-\u06FFa-z0-9\-]/g, '')
      .replace(/--+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  const arTitle = document.getElementById('ar_title');
  const arSlug  = document.getElementById('ar_slug');
  const enTitle = document.getElementById('en_title');
  const enSlug  = document.getElementById('en_slug');

  // Only auto-fill slugs in create mode (data-mode="create")
  const isCreate = document.body.dataset.mode === 'create';

  if (arTitle && arSlug && isCreate) {
    arTitle.addEventListener('input', () => { arSlug.value = slugify(arTitle.value); });
  }
  if (enTitle && enSlug && isCreate) {
    enTitle.addEventListener('input', () => { enSlug.value = slugify(enTitle.value); });
  }

  // ── Delete confirm ───────────────────────────────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach((el) => {
    el.addEventListener('click', (e) => {
      if (!confirm(el.dataset.confirm ?? 'Are you sure?')) e.preventDefault();
    });
  });

  // ── Toast notifications ──────────────────────────────────────────────────
  function showToast(msg, type = 'info') {
    const colors = { success: '#4ade80', error: '#f87171', info: '#60a5fa' };
    const toast  = document.createElement('div');
    toast.textContent = msg;
    Object.assign(toast.style, {
      position: 'fixed',
      bottom: '1.5rem',
      insetInlineEnd: '1.5rem',
      background: 'var(--bg-card)',
      border: `1px solid ${colors[type] ?? colors.info}`,
      color: colors[type] ?? colors.info,
      padding: '.7rem 1.2rem',
      borderRadius: '.65rem',
      fontSize: '.875rem',
      fontWeight: '600',
      zIndex: '9999',
      animation: 'slideUp .3s ease-out',
      maxWidth: '320px',
      boxShadow: '0 4px 20px rgba(0,0,0,.5)',
    });
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
  }

  window.showToast = showToast;

  // ── Reading progress bar ─────────────────────────────────────────────────
  const progressBar = document.getElementById('reading-progress');
  if (progressBar) {
    const updateProgress = () => {
      const scrollTop  = window.scrollY;
      const docHeight  = document.documentElement.scrollHeight - window.innerHeight;
      const pct        = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
      progressBar.style.width = pct.toFixed(2) + '%';
    };
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();
  }

  // ── Back-to-top button ───────────────────────────────────────────────────
  const backToTop = document.getElementById('back-to-top');
  if (backToTop) {
    window.addEventListener('scroll', () => {
      backToTop.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });
    backToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ── Scroll: sticky header effect ────────────────────────────────────────
  const siteHeader = document.querySelector('.site-header');
  if (siteHeader) {
    const onScroll = () => {
      siteHeader.classList.toggle('scrolled', window.scrollY > 20);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ── Lazy-load images (with fade-in) ──────────────────────────────────────
  if ('IntersectionObserver' in window) {
    const imgs = document.querySelectorAll('img[data-src]');
    const io   = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const img = entry.target;
        img.src   = img.dataset.src;
        img.removeAttribute('data-src');
        img.addEventListener('load',  () => img.classList.add('loaded'), { once: true });
        img.addEventListener('error', () => img.classList.add('loaded'), { once: true });
        io.unobserve(img);
      });
    }, { rootMargin: '300px 0px' });
    imgs.forEach((img) => io.observe(img));

    // Images already loaded (no data-src) still need .loaded
    document.querySelectorAll('.post-card .thumb img:not([data-src])').forEach((img) => {
      if (img.complete) img.classList.add('loaded');
      else img.addEventListener('load', () => img.classList.add('loaded'), { once: true });
    });
  } else {
    // Fallback: no IO support — just swap all data-src
    document.querySelectorAll('img[data-src]').forEach((img) => {
      img.src = img.dataset.src;
      img.removeAttribute('data-src');
      img.classList.add('loaded');
    });
  }

  // ── Scroll reveal ─────────────────────────────────────────────────────────
  if ('IntersectionObserver' in window) {
    const reveals = document.querySelectorAll('.reveal');
    if (reveals.length) {
      const revealIO = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            revealIO.unobserve(entry.target);
          }
        });
      }, { threshold: 0.1 });
      reveals.forEach((el) => revealIO.observe(el));
    }
  }

  // ── Active nav link ──────────────────────────────────────────────────────
  const currentPath = window.location.pathname.replace(/\/$/, '');
  document.querySelectorAll('.nav-links a, .mobile-menu a').forEach((a) => {
    const href = a.getAttribute('href')?.replace(/\/$/, '');
    if (!href) return;
    if (currentPath === href) {
      a.classList.add('active');
    } else if (href.length > 4 && currentPath.startsWith(href)) {
      a.classList.add('active');
    }
  });

})();
