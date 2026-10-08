/* ============================================================
   CMS Direktorat Bandar Udara — perilaku panel admin
   ============================================================ */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {

    /* ---------- Sidebar seluler ---------- */
    var toggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('sidebar');
    if (toggle && sidebar) {
      toggle.addEventListener('click', function () { sidebar.classList.toggle('open'); });
      document.addEventListener('click', function (e) {
        if (window.innerWidth <= 860 && sidebar.classList.contains('open') &&
            !sidebar.contains(e.target) && !toggle.contains(e.target)) {
          sidebar.classList.remove('open');
        }
      });
    }

    /* ---------- Pratinjau ikon Bootstrap ---------- */
    document.querySelectorAll('[data-icon-input]').forEach(function (input) {
      var preview = input.parentNode.querySelector('.icon-preview i');
      if (!preview) return;
      input.addEventListener('input', function () {
        preview.className = 'bi ' + (input.value.trim() || 'bi-app');
      });
    });

    /* ---------- Slug otomatis dari kolom sumber ---------- */
    function slugify(text) {
      return text.toLowerCase().trim()
        .replace(/&/g, ' dan ').replace(/\//g, '-')
        .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }
    document.querySelectorAll('[data-slug-from]').forEach(function (slugInput) {
      var sourceName = slugInput.getAttribute('data-slug-from');
      if (!sourceName) return;
      var source = document.querySelector('[name="' + sourceName + '"]');
      if (!source) return;
      var touched = slugInput.value.trim() !== '';
      slugInput.addEventListener('input', function () { touched = true; });
      source.addEventListener('input', function () {
        if (!touched) slugInput.value = slugify(source.value);
      });
    });

    /* ---------- Repeater baris anak ---------- */
    document.querySelectorAll('[data-repeater]').forEach(function (repeater) {
      var rows = repeater.querySelector('[data-repeater-rows]');
      var template = repeater.querySelector('[data-repeater-template]');
      var addBtn = repeater.querySelector('[data-repeater-add]');
      var counter = rows.children.length;

      function bindRemove(row) {
        var btn = row.querySelector('[data-repeater-remove]');
        if (btn) {
          btn.addEventListener('click', function () {
            row.remove();
          });
        }
        row.querySelectorAll('[data-icon-input]').forEach(function (input) {
          var preview = input.parentNode.querySelector('.icon-preview i');
          if (!preview) return;
          input.addEventListener('input', function () {
            preview.className = 'bi ' + (input.value.trim() || 'bi-app');
          });
        });
      }

      Array.prototype.forEach.call(rows.children, bindRemove);

      addBtn && addBtn.addEventListener('click', function () {
        var html = template.innerHTML.replace(/__INDEX__/g, 'new' + counter);
        counter++;
        var wrap = document.createElement('div');
        wrap.innerHTML = html.trim();
        var row = wrap.firstElementChild;
        rows.appendChild(row);
        bindRemove(row);
        var firstInput = row.querySelector('input[type=text], textarea');
        firstInput && firstInput.focus();
      });
    });

    /* ---------- Peringatan perubahan belum disimpan ---------- */
    var form = document.querySelector('form.form, form.card.form');
    if (form) {
      var dirty = false;
      form.addEventListener('input', function () { dirty = true; });
      form.addEventListener('submit', function () { dirty = false; });
      window.addEventListener('beforeunload', function (e) {
        if (dirty) { e.preventDefault(); e.returnValue = ''; }
      });
    }

  });
})();
