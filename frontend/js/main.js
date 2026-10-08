/* ============================================================
   DIREKTORAT BANDAR UDARA — SHARED SITE BEHAVIOR
   ============================================================ */
(function(){
  'use strict';

  /* ---------- Kirim <form> ke backend, kembalikan {ok, message} ---------- */
  function postForm(form){
    return fetch(form.getAttribute('action'), {
      method: 'POST',
      body: new FormData(form),
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(function(r){ return r.json(); })
      .catch(function(){ return { ok: false, message: 'Terjadi kesalahan jaringan. Coba lagi.' }; });
  }

  document.addEventListener('DOMContentLoaded', function(){

    /* ---------- Sticky header shadow ---------- */
    var header = document.querySelector('.sticky-stack') || document.querySelector('.header');
    if(header){
      window.addEventListener('scroll', function(){
        header.classList.toggle('is-stuck', window.scrollY > 8);
      });
    }

    /* ---------- Mobile nav toggle ---------- */
    var burger = document.querySelector('.burger');
    var navbar = document.querySelector('.navbar');
    if(burger && navbar){
      burger.addEventListener('click', function(){
        navbar.classList.toggle('mobile-open');
        burger.querySelector('i').className = navbar.classList.contains('mobile-open') ? 'bi bi-x-lg' : 'bi bi-list';
      });
    }

    /* ---------- Mega menu: dibuka dengan diklik ----------
       Label maupun panahnya sama-sama membuka panel. Tautan induknya
       tetap ditulis sebagai <a href> supaya tanpa JS halamannya masih
       bisa dibuka — dan lewat klik kanan / buka di tab baru. */
    var menuItems = document.querySelectorAll('.navlist > li');
    function tutupSemuaMenu(kecuali){
      menuItems.forEach(function(li){
        if(li === kecuali) return;
        li.classList.remove('is-open');
        var t = li.querySelector('.nav-toggle');
        if(t) t.setAttribute('aria-expanded', 'false');
      });
    }
    menuItems.forEach(function(li){
      var toggle = li.querySelector('.nav-toggle');
      if(!toggle) return;
      var label = li.querySelector(':scope > a');
      [label, toggle].forEach(function(pemicu){
        if(!pemicu) return;
        pemicu.addEventListener('click', function(e){
          e.preventDefault();
          e.stopPropagation();
          var buka = !li.classList.contains('is-open');
          tutupSemuaMenu(li);
          li.classList.toggle('is-open', buka);
          toggle.setAttribute('aria-expanded', buka ? 'true' : 'false');
        });
      });
    });
    /* Klik di luar navbar, atau tombol Esc, menutup panel yang terbuka. */
    document.addEventListener('click', function(e){
      if(!e.target.closest('.navlist')) tutupSemuaMenu(null);
    });
    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape') tutupSemuaMenu(null);
    });

    /* ---------- Search box toggle ---------- */
    var searchToggle = document.querySelector('[data-search-toggle]');
    var searchBox = document.querySelector('.search-box');
    if(searchToggle && searchBox){
      searchToggle.addEventListener('click', function(){
        searchBox.classList.toggle('open');
        if(searchBox.classList.contains('open')) searchBox.querySelector('input').focus();
      });
    }

    /* ---------- Language switch (cosmetic) ---------- */
    document.querySelectorAll('.lang-switch button').forEach(function(btn){
      btn.addEventListener('click', function(){
        document.querySelectorAll('.lang-switch button').forEach(function(b){ b.classList.remove('active'); });
        btn.classList.add('active');
      });
    });

    /* ---------- Hero carousel ---------- */
    var heroSlides = document.querySelectorAll('.hero-slide');
    if(heroSlides.length){
      var heroIdx = 0, heroTimer;
      var dotsWrap = document.querySelector('.hero-dots');
      heroSlides.forEach(function(_, i){
        var b = document.createElement('button');
        if(i===0) b.classList.add('active');
        b.addEventListener('click', function(){ goHero(i); });
        dotsWrap && dotsWrap.appendChild(b);
      });
      function goHero(i){
        heroSlides[heroIdx].classList.remove('active');
        dotsWrap && dotsWrap.children[heroIdx].classList.remove('active');
        heroIdx = (i + heroSlides.length) % heroSlides.length;
        heroSlides[heroIdx].classList.add('active');
        dotsWrap && dotsWrap.children[heroIdx].classList.add('active');
      }
      function nextHero(){ goHero(heroIdx+1); }
      function resetTimer(){ clearInterval(heroTimer); heroTimer = setInterval(nextHero, 6000); }
      resetTimer();
      var prevBtn = document.querySelector('.hero-arrow.prev');
      var nextBtn = document.querySelector('.hero-arrow.next');
      prevBtn && prevBtn.addEventListener('click', function(){ goHero(heroIdx-1); resetTimer(); });
      nextBtn && nextBtn.addEventListener('click', function(){ goHero(heroIdx+1); resetTimer(); });
    }

    /* ---------- Animated counters ---------- */
    var counters = document.querySelectorAll('[data-count]');
    if(counters.length && 'IntersectionObserver' in window){
      var obs = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            animateCount(entry.target);
            obs.unobserve(entry.target);
          }
        });
      }, {threshold:.4});
      counters.forEach(function(c){ obs.observe(c); });
    }
    function animateCount(el){
      var target = parseInt(el.getAttribute('data-count'), 10) || 0;
      var suffix = el.getAttribute('data-suffix') || '';
      var dur = 1400, start = null;
      function step(ts){
        if(!start) start = ts;
        var p = Math.min((ts - start) / dur, 1);
        var eased = 1 - Math.pow(1-p, 3);
        el.textContent = Math.round(eased * target).toLocaleString('id-ID') + suffix;
        if(p < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    }

    /* ---------- Tabs ---------- */
    document.querySelectorAll('[data-tabs]').forEach(function(group){
      var buttons = group.querySelectorAll('[data-tab]');
      var id = group.getAttribute('data-tabs');
      var panels = document.querySelectorAll('[data-tab-panel="'+id+'"]');
      buttons.forEach(function(btn){
        btn.addEventListener('click', function(){
          buttons.forEach(function(b){ b.classList.remove('active'); });
          panels.forEach(function(p){ p.classList.remove('active'); });
          btn.classList.add('active');
          var target = document.querySelector('[data-tab-panel="'+id+'"][data-panel-id="'+btn.getAttribute('data-tab')+'"]');
          target && target.classList.add('active');
        });
      });
    });

    /* ---------- Accordion ---------- */
    document.querySelectorAll('.accordion-head').forEach(function(head){
      head.addEventListener('click', function(){
        var item = head.closest('.accordion-item');
        var group = item.closest('[data-accordion-group]');
        if(group){
          group.querySelectorAll('.accordion-item').forEach(function(i){ if(i!==item) i.classList.remove('open'); });
        }
        item.classList.toggle('open');
      });
    });

    /* ---------- FAQ ---------- */
    document.querySelectorAll('.faq-q').forEach(function(q){
      q.addEventListener('click', function(){
        q.closest('.faq-item').classList.toggle('open');
      });
    });

    /* ---------- Org chart interactive units ---------- */
    document.querySelectorAll('.org-unit').forEach(function(unit){
      unit.addEventListener('click', function(){
        document.querySelectorAll('.org-unit').forEach(function(u){ u.classList.remove('active'); });
        document.querySelectorAll('.org-detail').forEach(function(d){ d.classList.remove('show'); });
        unit.classList.add('active');
        var key = unit.getAttribute('data-unit');
        var detail = document.querySelector('.org-detail[data-detail="'+key+'"]');
        if(detail){
          detail.classList.add('show');
          detail.scrollIntoView({behavior:'smooth', block:'nearest'});
        }
      });
    });

    /* ---------- Lightbox gallery ---------- */
    var lightbox = document.querySelector('.lightbox');
    if(lightbox){
      var lbMedia = lightbox.querySelector('.lb-media');
      var lbCap = lightbox.querySelector('.lb-cap');
      var items = Array.prototype.slice.call(document.querySelectorAll('[data-lightbox]'));
      var lbIdx = 0;
      function openLb(i){
        lbIdx = i;
        render();
        lightbox.classList.add('open');
      }
      function render(){
        var item = items[lbIdx];
        var artEl = item.querySelector('.art');
        if(artEl){
          var clone = artEl.cloneNode(true);
          clone.style.height = '480px';
          clone.style.borderRadius = 'var(--r-md)';
          lbMedia.innerHTML = '';
          lbMedia.appendChild(clone);
        } else {
          lbMedia.innerHTML = item.innerHTML;
        }
        lbCap.textContent = item.getAttribute('data-caption') || '';
      }
      items.forEach(function(item, i){
        item.addEventListener('click', function(){ openLb(i); });
      });
      var closeBtn = lightbox.querySelector('.lb-close');
      closeBtn && closeBtn.addEventListener('click', function(){ lightbox.classList.remove('open'); });
      lightbox.addEventListener('click', function(e){ if(e.target === lightbox) lightbox.classList.remove('open'); });
      var prev = lightbox.querySelector('.lb-nav.prev');
      var next = lightbox.querySelector('.lb-nav.next');
      prev && prev.addEventListener('click', function(){ lbIdx = (lbIdx - 1 + items.length) % items.length; render(); });
      next && next.addEventListener('click', function(){ lbIdx = (lbIdx + 1) % items.length; render(); });
      document.addEventListener('keydown', function(e){
        if(!lightbox.classList.contains('open')) return;
        if(e.key === 'Escape') lightbox.classList.remove('open');
        if(e.key === 'ArrowLeft') prev && prev.click();
        if(e.key === 'ArrowRight') next && next.click();
      });
    }

    /* ---------- Regulasi table filter ---------- */
    var regTable = document.querySelector('[data-reg-table]');
    if(regTable){
      var yearSel = document.querySelector('[data-reg-year]');
      var catSel = document.querySelector('[data-reg-cat]');
      var searchInp = document.querySelector('[data-reg-search]');
      var rows = Array.prototype.slice.call(regTable.querySelectorAll('tbody tr'));
      var emptyRow = document.querySelector('[data-reg-empty]');
      function filterRows(){
        var y = yearSel ? yearSel.value : 'all';
        var c = catSel ? catSel.value : 'all';
        var s = searchInp ? searchInp.value.trim().toLowerCase() : '';
        var visibleCount = 0;
        rows.forEach(function(row){
          var matchY = (y === 'all') || row.getAttribute('data-year') === y;
          var matchC = (c === 'all') || row.getAttribute('data-cat') === c;
          var matchS = !s || row.textContent.toLowerCase().indexOf(s) !== -1;
          var show = matchY && matchC && matchS;
          row.style.display = show ? '' : 'none';
          if(show) visibleCount++;
        });
        if(emptyRow) emptyRow.style.display = visibleCount === 0 ? '' : 'none';
      }
      yearSel && yearSel.addEventListener('change', filterRows);
      catSel && catSel.addEventListener('change', filterRows);
      searchInp && searchInp.addEventListener('input', filterRows);
    }

    /* ---------- Layanan table filter ---------- */
    var svcTable = document.querySelector('[data-svc-table]');
    if(svcTable){
      var svcCatSel = document.querySelector('[data-svc-cat]');
      var svcSearchInp = document.querySelector('[data-svc-search]');
      var svcRows = Array.prototype.slice.call(svcTable.querySelectorAll('tbody tr'));
      var svcEmptyRow = document.querySelector('[data-svc-empty]');
      function filterSvcRows(){
        var c = svcCatSel ? svcCatSel.value : 'all';
        var s = svcSearchInp ? svcSearchInp.value.trim().toLowerCase() : '';
        var visibleCount = 0;
        svcRows.forEach(function(row){
          var matchC = (c === 'all') || row.getAttribute('data-cat') === c;
          var matchS = !s || row.textContent.toLowerCase().indexOf(s) !== -1;
          var show = matchC && matchS;
          row.style.display = show ? '' : 'none';
          if(show) visibleCount++;
        });
        if(svcEmptyRow) svcEmptyRow.style.display = visibleCount === 0 ? '' : 'none';
      }
      svcCatSel && svcCatSel.addEventListener('change', filterSvcRows);
      svcSearchInp && svcSearchInp.addEventListener('input', filterSvcRows);
    }

    /* ---------- Register Bandar Udara table ---------- */
    var abuTable = document.querySelector('[data-abu-table]');
    if(abuTable){
      var abuRows = Array.prototype.slice.call(abuTable.querySelectorAll('tbody tr'));
      var abuNow = new Date();
      abuRows.forEach(function(row){
        var expiry = new Date(row.getAttribute('data-expiry'));
        var days = Math.floor((expiry - abuNow) / (1000 * 60 * 60 * 24));
        row.classList.remove('status-expired', 'status-soon');
        if(days < 0) row.classList.add('status-expired');
        else if(days <= 90) row.classList.add('status-soon');
      });

      var abuBase = document.querySelector('[data-abu-base]');
      var abuCert = document.querySelector('[data-abu-cert]');
      var abuSearch = document.querySelector('[data-abu-search]');
      var abuEmpty = document.querySelector('[data-abu-empty]');

      function filterAbu(){
        var base = abuBase ? abuBase.value : '';
        var cert = abuCert ? abuCert.value : '';
        var s = abuSearch ? abuSearch.value.trim().toLowerCase() : '';
        var visible = 0;
        abuRows.forEach(function(row){
          var matchBase = row.getAttribute('data-base') === base;
          var matchCert = row.getAttribute('data-cert') === cert;
          var matchS = !s || row.textContent.toLowerCase().indexOf(s) !== -1;
          var show = matchBase && matchCert && matchS;
          row.style.display = show ? '' : 'none';
          if(show){
            visible++;
            var noCell = row.querySelector('[data-abu-no]');
            if(noCell) noCell.textContent = visible;
          }
        });
        if(abuEmpty) abuEmpty.style.display = visible === 0 ? '' : 'none';
      }

      abuBase && abuBase.addEventListener('change', filterAbu);
      abuCert && abuCert.addEventListener('change', filterAbu);
      abuSearch && abuSearch.addEventListener('input', filterAbu);
      filterAbu();
    }

    /* ---------- Ajukan Layanan modal (front-end only demo) ---------- */
    var applyModal = document.querySelector('[data-apply-modal]');
    if(applyModal){
      var applyForm = applyModal.querySelector('[data-apply-form]');
      var applyServiceLabel = applyModal.querySelector('[data-apply-service]');
      var applyServiceInput = applyModal.querySelector('[data-apply-service-input]');
      var applyNote = applyModal.querySelector('[data-apply-note]');

      function openApplyModal(serviceName){
        applyServiceLabel.textContent = serviceName;
        applyServiceInput.value = serviceName;
        applyModal.classList.add('open');
        document.body.style.overflow = 'hidden';
      }
      function closeApplyModal(){
        applyModal.classList.remove('open');
        document.body.style.overflow = '';
      }

      document.querySelectorAll('[data-svc-apply]').forEach(function(btn){
        btn.addEventListener('click', function(){
          openApplyModal(btn.getAttribute('data-svc-apply'));
        });
      });

      applyModal.querySelectorAll('[data-apply-close]').forEach(function(el){
        el.addEventListener('click', closeApplyModal);
      });
      applyModal.addEventListener('click', function(e){
        if(e.target === applyModal) closeApplyModal();
      });
      document.addEventListener('keydown', function(e){
        if(e.key === 'Escape' && applyModal.classList.contains('open')) closeApplyModal();
      });

      applyForm && applyForm.addEventListener('submit', function(e){
        e.preventDefault();
        var submitBtn = applyForm.querySelector('button[type="submit"]');
        submitBtn && (submitBtn.disabled = true);
        postForm(applyForm).then(function(res){
          if(applyNote){
            applyNote.innerHTML = '<i class="bi bi-' + (res.ok ? 'check-circle-fill' : 'exclamation-circle-fill') + '"></i> ' + res.message;
            applyNote.classList.add('show');
          }
          submitBtn && (submitBtn.disabled = false);
          if(res.ok){
            setTimeout(function(){
              applyForm.reset();
              applyNote && applyNote.classList.remove('show');
              closeApplyModal();
            }, 2200);
          }
        });
      });
    }

    /* ---------- News archive: filter + client-side pagination ---------- */
    var newsGrid = document.querySelector('[data-news-grid]');
    if(newsGrid){
      var newsCards = Array.prototype.slice.call(newsGrid.querySelectorAll('[data-news-item]'));
      var perPage = 6;
      var newsCatSel = document.querySelector('[data-news-cat]');
      var newsSearch = document.querySelector('[data-news-search]');
      var pager = document.querySelector('[data-news-pager]');
      var page = 1;

      function getFiltered(){
        var c = newsCatSel ? newsCatSel.value : 'all';
        var s = newsSearch ? newsSearch.value.trim().toLowerCase() : '';
        return newsCards.filter(function(card){
          var matchC = (c === 'all') || card.getAttribute('data-cat') === c;
          var matchS = !s || card.textContent.toLowerCase().indexOf(s) !== -1;
          return matchC && matchS;
        });
      }
      function renderNews(){
        var filtered = getFiltered();
        var totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if(page > totalPages) page = totalPages;
        newsCards.forEach(function(c){ c.style.display = 'none'; });
        filtered.slice((page-1)*perPage, page*perPage).forEach(function(c){ c.style.display = ''; });
        if(pager){
          pager.innerHTML = '';
          for(var i=1;i<=totalPages;i++){
            var b = document.createElement('button');
            b.textContent = i;
            if(i===page) b.classList.add('active');
            (function(n){ b.addEventListener('click', function(){ page = n; renderNews(); window.scrollTo({top: newsGrid.offsetTop - 120, behavior:'smooth'}); }); })(i);
            pager.appendChild(b);
          }
        }
      }
      newsCatSel && newsCatSel.addEventListener('change', function(){ page = 1; renderNews(); });
      newsSearch && newsSearch.addEventListener('input', function(){ page = 1; renderNews(); });
      renderNews();
    }

    /* ---------- Gallery tabs (Photos/Video/Album) reuse data-tabs above ---------- */

    /* ---------- Floating buttons ---------- */
    var fabTop = document.querySelector('.fab-top');
    if(fabTop){
      window.addEventListener('scroll', function(){
        fabTop.classList.toggle('show', window.scrollY > 500);
      });
      fabTop.addEventListener('click', function(){ window.scrollTo({top:0, behavior:'smooth'}); });
    }

    /* ---------- Contact form (front-end only demo) ---------- */
    var contactForm = document.querySelector('[data-contact-form]');
    if(contactForm){
      contactForm.addEventListener('submit', function(e){
        e.preventDefault();
        var note = document.querySelector('[data-form-note]');
        var btn = contactForm.querySelector('button[type="submit"]');
        btn && (btn.disabled = true);
        postForm(contactForm).then(function(res){
          if(note){
            note.innerHTML = '<i class="bi bi-' + (res.ok ? 'check-circle-fill' : 'exclamation-circle-fill') + '"></i> ' + res.message;
            note.classList.add('show');
            setTimeout(function(){ note.classList.remove('show'); }, 5000);
          }
          btn && (btn.disabled = false);
          if(res.ok) contactForm.reset();
        });
      });
    }

    /* ---------- Peta wilayah Otoritas Bandar Udara (zoom, pan, hover) ---------- */
    var mapStage = document.querySelector('[data-map-stage]');
    if(mapStage){
      var zoomInner = mapStage.querySelector('[data-map-zoom-inner]');
      var scale = 1, panX = 0, panY = 0;
      var MIN_SCALE = 1, MAX_SCALE = 3.2, STEP = 0.4;

      function applyMapTransform(withTransition){
        zoomInner.style.transition = withTransition ? 'transform .25s ease' : 'none';
        zoomInner.style.transform = 'translate(' + panX + 'px,' + panY + 'px) scale(' + scale + ')';
        mapStage.classList.toggle('is-zoomed', scale > 1);
      }
      function clampMapPan(){
        var maxX = (scale - 1) * (mapStage.clientWidth / 2);
        var maxY = (scale - 1) * (mapStage.clientHeight / 2);
        panX = Math.max(-maxX, Math.min(maxX, panX));
        panY = Math.max(-maxY, Math.min(maxY, panY));
      }
      function setMapScale(next){
        scale = Math.max(MIN_SCALE, Math.min(MAX_SCALE, next));
        if(scale === MIN_SCALE){ panX = 0; panY = 0; }
        clampMapPan();
        applyMapTransform(true);
      }

      var zin = document.querySelector('[data-map-zoom-in]');
      var zout = document.querySelector('[data-map-zoom-out]');
      var zreset = document.querySelector('[data-map-zoom-reset]');
      zin && zin.addEventListener('click', function(){ setMapScale(scale + STEP); });
      zout && zout.addEventListener('click', function(){ setMapScale(scale - STEP); });
      zreset && zreset.addEventListener('click', function(){ setMapScale(1); });

      mapStage.addEventListener('wheel', function(e){
        e.preventDefault();
        setMapScale(scale + (e.deltaY > 0 ? -STEP/2 : STEP/2));
      }, { passive: false });

      var dragging = false, dragStartX = 0, dragStartY = 0, panStartX = 0, panStartY = 0, dragMoved = false;
      mapStage.addEventListener('mousedown', function(e){
        if(scale <= MIN_SCALE) return;
        dragging = true; dragMoved = false;
        mapStage.classList.add('is-dragging');
        dragStartX = e.clientX; dragStartY = e.clientY;
        panStartX = panX; panStartY = panY;
      });
      window.addEventListener('mousemove', function(e){
        if(!dragging) return;
        if(Math.abs(e.clientX - dragStartX) > 3 || Math.abs(e.clientY - dragStartY) > 3) dragMoved = true;
        panX = panStartX + (e.clientX - dragStartX);
        panY = panStartY + (e.clientY - dragStartY);
        clampMapPan();
        applyMapTransform(false);
      });
      window.addEventListener('mouseup', function(){
        dragging = false;
        mapStage.classList.remove('is-dragging');
      });

      /* ---- Pilih wilayah lewat klik (bukan hover) ---- */
      var tooltip = document.querySelector('[data-map-tooltip]');
      var ttNum = tooltip.querySelector('[data-tt-num]');
      var ttTitle = tooltip.querySelector('[data-tt-title]');
      var ttHq = tooltip.querySelector('[data-tt-hq]');
      var ttCoverage = tooltip.querySelector('[data-tt-coverage]');
      var ttCount = tooltip.querySelector('[data-tt-count]');
      var pinnedRegion = null;

      /* ---- Panel daftar bandar udara di bawah peta ---- */
      var airportsWrap  = document.querySelector('[data-map-airports]');
      var airportsEmpty = airportsWrap && airportsWrap.querySelector('[data-airports-empty]');
      var airportPanels = airportsWrap
        ? Array.prototype.slice.call(airportsWrap.querySelectorAll('[data-airports-region]'))
        : [];

      function showAirports(code){
        if(!airportsWrap) return;
        var found = false;
        airportPanels.forEach(function(panel){
          var match = panel.getAttribute('data-airports-region') === code;
          panel.hidden = !match;
          if(match) found = true;
        });
        if(airportsEmpty) airportsEmpty.hidden = found;
      }

      function positionTooltip(clientX, clientY){
        var rect = mapStage.getBoundingClientRect();
        var left = clientX - rect.left + 18;
        var top = clientY - rect.top + 18;
        var ttRect = tooltip.getBoundingClientRect();
        if(left + ttRect.width > rect.width) left = clientX - rect.left - ttRect.width - 18;
        if(top + ttRect.height > rect.height) top = clientY - rect.top - ttRect.height - 18;
        tooltip.style.left = Math.max(8, left) + 'px';
        tooltip.style.top = Math.max(8, top) + 'px';
      }
      function fillTooltip(region){
        ttNum.textContent = region.getAttribute('data-num');
        ttTitle.textContent = 'Otoritas Bandar Udara ' + region.getAttribute('data-title');
        ttHq.textContent = region.getAttribute('data-hq');
        ttCoverage.textContent = region.getAttribute('data-coverage');
        if(ttCount){
          var jml = region.getAttribute('data-airports');
          ttCount.textContent = jml ? jml + ' bandar udara' : '';
        }
      }

      var regions = Array.prototype.slice.call(mapStage.querySelectorAll('.region'));
      var legendItems = Array.prototype.slice.call(document.querySelectorAll('[data-legend-region]'));

      /** Satu-satunya jalan mengubah pilihan: klik peta, klik legenda, atau Enter/Spasi. */
      function selectRegion(region, clientX, clientY){
        if(pinnedRegion && pinnedRegion !== region) pinnedRegion.classList.remove('is-active');
        pinnedRegion = region;
        region.classList.add('is-active');

        var code = region.getAttribute('data-region');
        legendItems.forEach(function(item){
          item.classList.toggle('is-active', item.getAttribute('data-legend-region') === code);
        });

        fillTooltip(region);
        if(clientX === undefined){
          var box = region.getBoundingClientRect();
          clientX = box.left + box.width / 2;
          clientY = box.top + box.height / 2;
        }
        tooltip.classList.add('show');
        positionTooltip(clientX, clientY);
        showAirports(code);
      }

      function clearRegion(){
        if(pinnedRegion) pinnedRegion.classList.remove('is-active');
        pinnedRegion = null;
        legendItems.forEach(function(item){ item.classList.remove('is-active'); });
        tooltip.classList.remove('show');
        showAirports(null);
      }

      function toggleRegion(region, clientX, clientY){
        if(pinnedRegion === region) clearRegion();
        else selectRegion(region, clientX, clientY);
      }

      regions.forEach(function(region){
        region.addEventListener('click', function(e){
          if(dragMoved){ dragMoved = false; return; }
          toggleRegion(region, e.clientX, e.clientY);
          e.stopPropagation();
        });
        region.addEventListener('keydown', function(e){
          if(e.key !== 'Enter' && e.key !== ' ') return;
          e.preventDefault();
          toggleRegion(region);
          e.stopPropagation();
        });
      });

      legendItems.forEach(function(item){
        var region = mapStage.querySelector('.region[data-region="' + item.getAttribute('data-legend-region') + '"]');
        if(!region) return;
        item.addEventListener('click', function(e){
          toggleRegion(region);
          e.stopPropagation();
        });
      });

      document.addEventListener('click', function(){
        if(pinnedRegion) clearRegion();
      });
    }

    /* ---------- Korsel indikasi pembangunan bandar udara (RPJMN 2025–2029) ---------- */
    var rpjmn = document.querySelector('[data-rpjmn]');
    if(rpjmn){
      var rPanes   = Array.prototype.slice.call(rpjmn.querySelectorAll('[data-rpjmn-pane]'));
      var rDots    = Array.prototype.slice.call(rpjmn.querySelectorAll('[data-rpjmn-dot]'));
      var rMap     = rpjmn.querySelector('[data-rpjmn-map]');
      var rTooltip = rpjmn.querySelector('[data-rpjmn-tooltip]');
      var rShapes  = Array.prototype.slice.call(rMap.querySelectorAll('.region'));
      var rIndex   = 0;
      var rTimer   = null;
      var rDelay   = parseInt(rpjmn.getAttribute('data-rpjmn-interval'), 10) || 3000;

      var rtNum      = rTooltip.querySelector('[data-rt-num]');
      var rtTitle    = rTooltip.querySelector('[data-rt-title]');
      var rtCoverage = rTooltip.querySelector('[data-rt-coverage]');
      var rtAirports = rTooltip.querySelector('[data-rt-airports]');

      function rShape(code){
        for(var i = 0; i < rShapes.length; i++){
          if(rShapes[i].getAttribute('data-region') === code) return rShapes[i];
        }
        return null;
      }

      /* Sorot wilayah milik bagian yang sedang aktif; sisanya kembali pasif. */
      function rSyncMap(){
        rShapes.forEach(function(shape){
          shape.classList.remove('is-on', 'is-active');
          shape.classList.add('is-muted');
          shape.removeAttribute('data-airports');
          shape.setAttribute('tabindex', '-1');
        });
        var areas = rPanes[rIndex].querySelectorAll('[data-rpjmn-area]');
        Array.prototype.forEach.call(areas, function(area){
          var shape = rShape(area.getAttribute('data-rpjmn-area'));
          if(!shape) return;
          shape.classList.remove('is-muted');
          shape.classList.add('is-on');
          shape.setAttribute('data-airports', area.getAttribute('data-airports') || '');
          shape.setAttribute('tabindex', '0');
        });
        rTooltip.classList.remove('show');
      }

      /* Tinggi panel mengikuti pane aktif — pane lain diposisikan absolut. */
      var rPaneWrap = rpjmn.querySelector('.rpjmn-panes');
      function rSyncHeight(){
        var style = window.getComputedStyle(rPaneWrap);
        var pad = parseFloat(style.paddingTop) + parseFloat(style.paddingBottom);
        rPaneWrap.style.height = (rPanes[rIndex].offsetHeight + pad) + 'px';
      }
      window.addEventListener('resize', rSyncHeight);

      function rShow(next){
        rIndex = (next + rPanes.length) % rPanes.length;
        rPanes.forEach(function(pane, i){
          pane.classList.toggle('active', i === rIndex);
          if(i === rIndex){ pane.removeAttribute('aria-hidden'); }
          else { pane.setAttribute('aria-hidden', 'true'); }
        });
        rDots.forEach(function(dot, i){
          dot.classList.toggle('active', i === rIndex);
          if(i === rIndex){ dot.setAttribute('aria-selected', 'true'); }
          else { dot.removeAttribute('aria-selected'); }
        });
        rSyncHeight();
        rSyncMap();
      }

      function rStart(){
        rStop();
        if(rPanes.length > 1) rTimer = setInterval(function(){ rShow(rIndex + 1); }, rDelay);
      }
      function rStop(){
        if(rTimer){ clearInterval(rTimer); rTimer = null; }
      }
      function rGoto(next){ rShow(next); rStart(); }

      rDots.forEach(function(dot, i){ dot.addEventListener('click', function(){ rGoto(i); }); });
      var rPrev = rpjmn.querySelector('[data-rpjmn-prev]');
      var rNext = rpjmn.querySelector('[data-rpjmn-next]');
      rPrev && rPrev.addEventListener('click', function(){ rGoto(rIndex - 1); });
      rNext && rNext.addEventListener('click', function(){ rGoto(rIndex + 1); });

      /* Berhenti otomatis selagi pengunjung membaca atau menelusuri peta. */
      rpjmn.addEventListener('mouseenter', rStop);
      rpjmn.addEventListener('mouseleave', rStart);
      rpjmn.addEventListener('focusin', rStop);
      rpjmn.addEventListener('focusout', function(e){
        if(!rpjmn.contains(e.relatedTarget)) rStart();
      });

      /* ---- Tooltip peta ---- */
      function rPlaceTooltip(clientX, clientY){
        var rect = rMap.getBoundingClientRect();
        var box  = rTooltip.getBoundingClientRect();
        var left = clientX - rect.left + 18;
        var top  = clientY - rect.top + 18;
        if(left + box.width > rect.width) left = clientX - rect.left - box.width - 18;
        if(top + box.height > rect.height) top = clientY - rect.top - box.height - 18;
        rTooltip.style.left = Math.max(8, left) + 'px';
        rTooltip.style.top  = Math.max(8, top) + 'px';
      }
      function rShowTooltip(shape, clientX, clientY){
        if(!shape.classList.contains('is-on')) return;
        rtNum.textContent      = shape.getAttribute('data-num') || '';
        rtTitle.textContent    = shape.getAttribute('data-title') || '';
        rtCoverage.textContent = shape.getAttribute('data-coverage') || '';
        rtAirports.textContent = shape.getAttribute('data-airports') || '';
        rPlaceTooltip(clientX, clientY);
        rTooltip.classList.add('show');
      }

      rShapes.forEach(function(shape){
        shape.addEventListener('mouseenter', function(e){ rShowTooltip(shape, e.clientX, e.clientY); });
        shape.addEventListener('mousemove', function(e){
          if(shape.classList.contains('is-on')) rPlaceTooltip(e.clientX, e.clientY);
        });
        shape.addEventListener('mouseleave', function(){ rTooltip.classList.remove('show'); });
        shape.addEventListener('focus', function(){
          var box = shape.getBoundingClientRect();
          rShowTooltip(shape, box.left + box.width / 2, box.top + box.height / 2);
        });
        shape.addEventListener('blur', function(){ rTooltip.classList.remove('show'); });
      });

      /* ---- Sinkron daftar wilayah pada panel teks dengan peta ---- */
      rpjmn.addEventListener('mouseover', function(e){
        var area = e.target.closest ? e.target.closest('[data-rpjmn-area]') : null;
        if(!area) return;
        var shape = rShape(area.getAttribute('data-rpjmn-area'));
        if(shape) shape.classList.add('is-active');
      });
      rpjmn.addEventListener('mouseout', function(e){
        var area = e.target.closest ? e.target.closest('[data-rpjmn-area]') : null;
        if(!area) return;
        var shape = rShape(area.getAttribute('data-rpjmn-area'));
        if(shape) shape.classList.remove('is-active');
      });

      rSyncHeight();
      rSyncMap();
      rStart();
      /* Ukur ulang setelah webfont & ikon selesai dimuat. */
      window.addEventListener('load', rSyncHeight);
    }

    /* ---------- Emissions dashboard (SVG bar charts, hover tooltips) ---------- */
    var emisiScopeWrap = document.querySelector('[data-emisi-chart="scope"]');
    var emisiQuarterlyWrap = document.querySelector('[data-emisi-chart="quarterly"]');
    if(emisiScopeWrap && emisiQuarterlyWrap){
      var SVG_NS = 'http://www.w3.org/2000/svg';
      var SCOPE_COLORS = { s1: '#00396B', s2: '#005BAC', s3: '#2E86D8' };
      var YEAR_COLORS = { 2023: '#a8440f', 2024: '#e07830', 2025: '#eb9a63' };
      var QUARTERS = ['Q1', 'Q2', 'Q3', 'Q4'];
      var YEARS = [2023, 2024, 2025];

      var scopeData = [
        { q: 'Q1', year: 2023, s1: 115, s2: 128, s3: 305 },
        { q: 'Q2', year: 2023, s1: 122, s2: 135, s3: 313 },
        { q: 'Q3', year: 2023, s1: 128, s2: 142, s3: 320 },
        { q: 'Q4', year: 2023, s1: 138, s2: 155, s3: 337 },
        { q: 'Q1', year: 2024, s1: 110, s2: 122, s3: 303 },
        { q: 'Q2', year: 2024, s1: 124, s2: 137, s3: 310 },
        { q: 'Q3', year: 2024, s1: 130, s2: 145, s3: 317 },
        { q: 'Q4', year: 2024, s1: 140, s2: 158, s3: 335 },
        { q: 'Q1', year: 2025, s1: 108, s2: 118, s3: 302 },
        { q: 'Q2', year: 2025, s1: 118, s2: 130, s3: 308 },
        { q: 'Q3', year: 2025, s1: 126, s2: 140, s3: 316 },
        { q: 'Q4', year: 2025, s1: 136, s2: 152, s3: 332 }
      ];

      function fmtInt(n){ return Math.round(n).toLocaleString('id-ID'); }

      function svgEl(tag, attrs){
        var el = document.createElementNS(SVG_NS, tag);
        for(var k in attrs){ el.setAttribute(k, attrs[k]); }
        return el;
      }

      function roundedTopPath(x, y, w, h, r){
        if(h <= 0) return '';
        r = Math.min(r, w / 2, h);
        return 'M' + x + ',' + (y + h) +
          ' V' + (y + r) +
          ' Q' + x + ',' + y + ' ' + (x + r) + ',' + y +
          ' H' + (x + w - r) +
          ' Q' + (x + w) + ',' + y + ' ' + (x + w) + ',' + (y + r) +
          ' V' + (y + h) + ' Z';
      }

      /* ---- Shared tooltip ---- */
      var emisiTooltip = document.createElement('div');
      emisiTooltip.className = 'emisi-tooltip';
      document.body.appendChild(emisiTooltip);
      var emisiTooltipHost = null;

      function showEmisiTooltip(hostWrap, clientX, clientY, titleText, rows){
        emisiTooltipHost = hostWrap;
        while(emisiTooltip.firstChild) emisiTooltip.removeChild(emisiTooltip.firstChild);
        var title = document.createElement('div');
        title.className = 'emisi-tooltip-title';
        title.textContent = titleText;
        emisiTooltip.appendChild(title);
        rows.forEach(function(r){
          var row = document.createElement('div');
          row.className = 'emisi-tooltip-row' + (r.total ? ' total' : '');
          if(r.color){
            var key = document.createElement('span');
            key.className = 'key';
            key.style.background = r.color;
            row.appendChild(key);
          }
          var lbl = document.createElement('span');
          lbl.className = 'lbl';
          lbl.textContent = r.label;
          var val = document.createElement('span');
          val.className = 'val';
          val.textContent = r.value;
          row.appendChild(lbl);
          row.appendChild(val);
          emisiTooltip.appendChild(row);
        });
        positionEmisiTooltip(clientX, clientY);
        emisiTooltip.classList.add('show');
      }

      function positionEmisiTooltip(clientX, clientY){
        if(!emisiTooltipHost) return;
        var rect = emisiTooltipHost.getBoundingClientRect();
        var ttRect = emisiTooltip.getBoundingClientRect();
        var left = clientX - rect.left + 16;
        var top = clientY - rect.top - ttRect.height - 14;
        if(left + ttRect.width > rect.width) left = clientX - rect.left - ttRect.width - 16;
        if(top < 0) top = clientY - rect.top + 16;
        emisiTooltip.style.left = (rect.left + left + window.scrollX) + 'px';
        emisiTooltip.style.top = (rect.top + top + window.scrollY) + 'px';
      }

      function hideEmisiTooltip(){
        emisiTooltip.classList.remove('show');
        emisiTooltipHost = null;
      }

      /* ---- Chart 1: stacked bars, Emissions by Scope ---- */
      function renderScopeChart(){
        var width = 380, height = 280;
        var padL = 42, padR = 8, padT = 10, padB = 46;
        var plotW = width - padL - padR, plotH = height - padT - padB;
        var maxY = 700, step = 100;
        var baseline = padT + plotH;

        var svg = svgEl('svg', { viewBox: '0 0 ' + width + ' ' + height, preserveAspectRatio: 'xMidYMid meet' });

        for(var v = 0; v <= maxY; v += step){
          var gy = baseline - (v / maxY) * plotH;
          svg.appendChild(svgEl('line', { class: 'axis-line', x1: padL, x2: padL + plotW, y1: gy, y2: gy }));
          var lbl = svgEl('text', { class: 'axis-label', x: padL - 8, y: gy + 3, 'text-anchor': 'end' });
          lbl.textContent = v;
          svg.appendChild(lbl);
        }

        var clusterGap = 14, barGap = 3, maxBarW = 22;
        var clusterW = (plotW - (YEARS.length - 1) * clusterGap) / YEARS.length;
        var barSlot = clusterW / QUARTERS.length;
        var barW = Math.min(maxBarW, barSlot - barGap);

        YEARS.forEach(function(year, ci){
          var clusterX = padL + ci * (clusterW + clusterGap);
          QUARTERS.forEach(function(q, qi){
            var d = scopeData.filter(function(r){ return r.year === year && r.q === q; })[0];
            var x = clusterX + qi * barSlot + (barSlot - barW) / 2;

            var h1 = (d.s1 / maxY) * plotH, h2 = (d.s2 / maxY) * plotH, h3 = (d.s3 / maxY) * plotH;
            var yTop1 = baseline - h1, yTop2 = yTop1 - h2, yTop3 = yTop2 - h3;

            var g = svgEl('g', { class: 'emisi-bar-group' });

            var seg1 = svgEl('rect', { class: 'emisi-seg', x: x, y: yTop1 + 1, width: barW, height: Math.max(h1 - 1, 0), fill: SCOPE_COLORS.s1 });
            var seg2 = svgEl('rect', { class: 'emisi-seg', x: x, y: yTop2 + 1, width: barW, height: Math.max(h2 - 2, 0), fill: SCOPE_COLORS.s2 });
            var seg3 = svgEl('path', { class: 'emisi-seg', d: roundedTopPath(x, yTop3, barW, Math.max(h3 - 1, 0), 4), fill: SCOPE_COLORS.s3 });
            g.appendChild(seg1); g.appendChild(seg2); g.appendChild(seg3);

            var hit = svgEl('rect', { class: 'emisi-bar-hit', x: x - barGap / 2, y: yTop3 - 6, width: barW + barGap, height: baseline - yTop3 + 6 });
            g.appendChild(hit);

            var qLbl = svgEl('text', { class: 'q-label', x: x + barW / 2, y: baseline + 16 });
            qLbl.textContent = q;
            svg.appendChild(qLbl);

            hit.addEventListener('pointerenter', function(){ g.classList.add('is-hover'); });
            hit.addEventListener('pointerleave', function(){ g.classList.remove('is-hover'); hideEmisiTooltip(); });
            hit.addEventListener('pointermove', function(e){
              showEmisiTooltip(emisiScopeWrap, e.clientX, e.clientY, q + ' ' + year, [
                { color: SCOPE_COLORS.s1, label: 'Scope 1', value: fmtInt(d.s1) + ' tCO2e' },
                { color: SCOPE_COLORS.s2, label: 'Scope 2', value: fmtInt(d.s2) + ' tCO2e' },
                { color: SCOPE_COLORS.s3, label: 'Scope 3', value: fmtInt(d.s3) + ' tCO2e' },
                { label: 'Total', value: fmtInt(d.s1 + d.s2 + d.s3) + ' tCO2e', total: true }
              ]);
            });

            svg.appendChild(g);
          });

          var yearLbl = svgEl('text', { class: 'year-label', x: clusterX + clusterW / 2, y: baseline + 34 });
          yearLbl.textContent = year;
          svg.appendChild(yearLbl);
        });

        emisiScopeWrap.appendChild(svg);
      }

      /* ---- Chart 2: grouped bars, Quarterly Comparison ---- */
      function renderQuarterlyChart(){
        var width = 330, height = 280;
        var padL = 42, padR = 8, padT = 10, padB = 34;
        var plotW = width - padL - padR, plotH = height - padT - padB;
        var maxY = 700, step = 100;
        var baseline = padT + plotH;

        var svg = svgEl('svg', { viewBox: '0 0 ' + width + ' ' + height, preserveAspectRatio: 'xMidYMid meet' });

        for(var v = 0; v <= maxY; v += step){
          var gy = baseline - (v / maxY) * plotH;
          svg.appendChild(svgEl('line', { class: 'axis-line', x1: padL, x2: padL + plotW, y1: gy, y2: gy }));
          var lbl = svgEl('text', { class: 'axis-label', x: padL - 8, y: gy + 3, 'text-anchor': 'end' });
          lbl.textContent = v;
          svg.appendChild(lbl);
        }

        var clusterGap = 18, barGap = 3, maxBarW = 20;
        var clusterW = (plotW - (QUARTERS.length - 1) * clusterGap) / QUARTERS.length;
        var barSlot = clusterW / YEARS.length;
        var barW = Math.min(maxBarW, barSlot - barGap);

        QUARTERS.forEach(function(q, qi){
          var clusterX = padL + qi * (clusterW + clusterGap);
          var g = svgEl('g', { class: 'emisi-bar-group' });
          var rowsForTooltip = [];
          var maxTop = baseline;

          YEARS.forEach(function(year, yi){
            var d = scopeData.filter(function(r){ return r.year === year && r.q === q; })[0];
            var total = d.s1 + d.s2 + d.s3;
            var x = clusterX + yi * barSlot + (barSlot - barW) / 2;
            var h = (total / maxY) * plotH;
            var yTop = baseline - h;
            if(yTop < maxTop) maxTop = yTop;

            var bar = svgEl('path', { class: 'emisi-bar', d: roundedTopPath(x, yTop, barW, h, 4), fill: YEAR_COLORS[year] });
            g.appendChild(bar);

            rowsForTooltip.push({ color: YEAR_COLORS[year], label: String(year), value: fmtInt(total) + ' tCO2e' });
          });

          var hit = svgEl('rect', { class: 'emisi-bar-hit', x: clusterX - barGap, y: maxTop - 6, width: clusterW + barGap * 2, height: baseline - maxTop + 6 });
          hit.addEventListener('pointerenter', function(){ g.classList.add('is-hover'); });
          hit.addEventListener('pointerleave', function(){ g.classList.remove('is-hover'); hideEmisiTooltip(); });
          hit.addEventListener('pointermove', function(e){
            showEmisiTooltip(emisiQuarterlyWrap, e.clientX, e.clientY, q, rowsForTooltip);
          });
          g.appendChild(hit);
          svg.appendChild(g);

          var qLbl = svgEl('text', { class: 'q-label', x: clusterX + clusterW / 2, y: baseline + 20 });
          qLbl.textContent = q;
          svg.appendChild(qLbl);
        });

        emisiQuarterlyWrap.appendChild(svg);
      }

      renderScopeChart();
      renderQuarterlyChart();
    }

    /* ---------- Fasilitas sisi udara (pilih bandara: runway/taxiway/apron) ---------- */
    var specAirportSelect = document.querySelector('[data-spec-airport]');
    if(specAirportSelect){
      var AIRPORT_FACILITIES = {
        'ngurah-rai': {
          name: 'I Gusti Ngurah Rai',
          runway: { count: 2, nomor: '09 - 27', panjang: '1.500 m', lebar: '150 m', tipePermukaan: 'Non-Flexible', pcr: 40, skidResistance: '0.6 (Moventor)', roughnessIndex: '2 mm', bbi: '0.8', pci: 86 },
          taxiway: { count: 3, kode: 'Taxiway 1E', panjang: '94 m', lebar: '26 m', pcr: 61, pci: 87 },
          apron: { count: 3, kode: 'Apron 1C', panjang: '72 m', lebar: '60 m', pcr: 40, pci: 86, parkingStand: 72, info: [
            { label: 'Sisi Selatan', aircraft: 'B738', count: 40 },
            { label: 'Sisi Utara', aircraft: 'A321', count: 30 },
            { label: 'Utama', aircraft: 'Critical Aircraft', count: 2 }
          ]}
        },
        'juanda': {
          name: 'Juanda',
          runway: { count: 1, nomor: '10 - 28', panjang: '3.000 m', lebar: '45 m', tipePermukaan: 'Flexible', pcr: 65, skidResistance: '0.7 (Grip Tester)', roughnessIndex: '1.5 mm', bbi: '0.6', pci: 90 },
          taxiway: { count: 4, kode: 'Taxiway A', panjang: '120 m', lebar: '23 m', pcr: 58, pci: 84 },
          apron: { count: 2, kode: 'Apron Domestik', panjang: '250 m', lebar: '120 m', pcr: 55, pci: 82, parkingStand: 18, info: [
            { label: 'Sisi Barat', aircraft: 'B737', count: 10 },
            { label: 'Sisi Timur', aircraft: 'A320', count: 6 },
            { label: 'Remote', aircraft: 'Wide Body', count: 2 }
          ]}
        },
        'kualanamu': {
          name: 'Kualanamu',
          runway: { count: 1, nomor: '05 - 23', panjang: '3.750 m', lebar: '60 m', tipePermukaan: 'Flexible', pcr: 70, skidResistance: '0.75 (Grip Tester)', roughnessIndex: '1.2 mm', bbi: '0.5', pci: 92 },
          taxiway: { count: 5, kode: 'Taxiway B2', panjang: '150 m', lebar: '23 m', pcr: 62, pci: 88 },
          apron: { count: 3, kode: 'Apron 2A', panjang: '280 m', lebar: '100 m', pcr: 58, pci: 85, parkingStand: 14, info: [
            { label: 'Sisi Utara', aircraft: 'B738', count: 8 },
            { label: 'Sisi Selatan', aircraft: 'A330', count: 4 },
            { label: 'Cargo', aircraft: 'B777F', count: 2 }
          ]}
        },
        'hasanuddin': {
          name: 'Sultan Hasanuddin',
          runway: { count: 1, nomor: '03 - 21', panjang: '3.100 m', lebar: '45 m', tipePermukaan: 'Non-Flexible', pcr: 55, skidResistance: '0.65 (Moventor)', roughnessIndex: '1.8 mm', bbi: '0.7', pci: 80 },
          taxiway: { count: 3, kode: 'Taxiway C1', panjang: '100 m', lebar: '23 m', pcr: 50, pci: 78 },
          apron: { count: 2, kode: 'Apron 1', panjang: '200 m', lebar: '90 m', pcr: 45, pci: 76, parkingStand: 12, info: [
            { label: 'Sisi Utara', aircraft: 'B738', count: 7 },
            { label: 'Sisi Selatan', aircraft: 'A320', count: 3 },
            { label: 'VIP', aircraft: 'CRJ1000', count: 2 }
          ]}
        },
        'sepinggan': {
          name: 'Sultan Aji Muhammad Sulaiman (Sepinggan)',
          runway: { count: 1, nomor: '07 - 25', panjang: '2.500 m', lebar: '45 m', tipePermukaan: 'Non-Flexible', pcr: 48, skidResistance: '0.6 (Moventor)', roughnessIndex: '2.2 mm', bbi: '0.75', pci: 74 },
          taxiway: { count: 2, kode: 'Taxiway D', panjang: '80 m', lebar: '23 m', pcr: 46, pci: 72 },
          apron: { count: 2, kode: 'Apron Utama', panjang: '180 m', lebar: '80 m', pcr: 42, pci: 70, parkingStand: 10, info: [
            { label: 'Sisi Barat', aircraft: 'B738', count: 6 },
            { label: 'Sisi Timur', aircraft: 'A320', count: 3 },
            { label: 'VIP', aircraft: 'ATR72', count: 1 }
          ]}
        }
      };

      Object.keys(AIRPORT_FACILITIES).forEach(function(key){
        var opt = document.createElement('option');
        opt.value = key;
        opt.textContent = AIRPORT_FACILITIES[key].name;
        specAirportSelect.appendChild(opt);
      });

      function pciBadgeClass(pci){
        if(pci >= 85) return 'badge-success';
        if(pci >= 75) return 'badge-accent';
        return 'badge-danger';
      }

      var specBodies = {
        runway: document.querySelector('[data-spec-body="runway"]'),
        taxiway: document.querySelector('[data-spec-body="taxiway"]'),
        apron: document.querySelector('[data-spec-body="apron"]')
      };
      var specCounts = {
        runway: document.querySelector('[data-spec-count="runway"]'),
        taxiway: document.querySelector('[data-spec-count="taxiway"]'),
        apron: document.querySelector('[data-spec-count="apron"]')
      };

      function renderFacilities(key){
        var d = AIRPORT_FACILITIES[key];
        if(!d) return;

        specCounts.runway.textContent = d.runway.count + ' Unit';
        specBodies.runway.innerHTML =
          '<div class="spec-row"><span>Nomor Runway</span><b>' + d.runway.nomor + '</b></div>' +
          '<div class="spec-group"><div class="spec-group-label">Dimensi</div>' +
          '<div class="spec-row sub"><span>Panjang</span><b>' + d.runway.panjang + '</b></div>' +
          '<div class="spec-row sub"><span>Lebar</span><b>' + d.runway.lebar + '</b></div></div>' +
          '<div class="spec-row"><span>Tipe Permukaan</span><b>' + d.runway.tipePermukaan + '</b></div>' +
          '<div class="spec-row"><span>Kekuatan (PCR)</span><b>' + d.runway.pcr + '</b></div>' +
          '<div class="spec-row"><span>Skid Resistance</span><b>' + d.runway.skidResistance + '</b></div>' +
          '<div class="spec-row"><span>Roughness Index</span><b>' + d.runway.roughnessIndex + '</b></div>' +
          '<div class="spec-row"><span>Boeing Bump Index (BBI)</span><b>' + d.runway.bbi + '</b></div>' +
          '<div class="spec-row highlight"><span>Pavement Condition Index (PCI)</span><span class="badge ' + pciBadgeClass(d.runway.pci) + '">' + d.runway.pci + '</span></div>';

        specCounts.taxiway.textContent = d.taxiway.count + ' Unit';
        specBodies.taxiway.innerHTML =
          '<div class="spec-row"><span>Kode Taxiway</span><b>' + d.taxiway.kode + '</b></div>' +
          '<div class="spec-group"><div class="spec-group-label">Dimensi</div>' +
          '<div class="spec-row sub"><span>Panjang</span><b>' + d.taxiway.panjang + '</b></div>' +
          '<div class="spec-row sub"><span>Lebar</span><b>' + d.taxiway.lebar + '</b></div></div>' +
          '<div class="spec-row"><span>Kekuatan (PCR)</span><b>' + d.taxiway.pcr + '</b></div>' +
          '<div class="spec-row highlight"><span>Pavement Condition Index (PCI)</span><span class="badge ' + pciBadgeClass(d.taxiway.pci) + '">' + d.taxiway.pci + '</span></div>';

        var infoHtml = d.apron.info.map(function(row){
          return '<div class="spec-stand-item"><span>' + row.label + ' <b>' + row.aircraft + '</b></span><b>' + row.count + '</b></div>';
        }).join('');
        specCounts.apron.textContent = d.apron.count + ' Unit';
        specBodies.apron.innerHTML =
          '<div class="spec-row"><span>Kode Apron</span><b>' + d.apron.kode + '</b></div>' +
          '<div class="spec-group"><div class="spec-group-label">Dimensi</div>' +
          '<div class="spec-row sub"><span>Panjang</span><b>' + d.apron.panjang + '</b></div>' +
          '<div class="spec-row sub"><span>Lebar</span><b>' + d.apron.lebar + '</b></div></div>' +
          '<div class="spec-row"><span>Kekuatan (PCR)</span><b>' + d.apron.pcr + '</b></div>' +
          '<div class="spec-row highlight"><span>Pavement Condition Index (PCI)</span><span class="badge ' + pciBadgeClass(d.apron.pci) + '">' + d.apron.pci + '</span></div>' +
          '<div class="spec-row"><span>Parking Stand</span><b>' + d.apron.parkingStand + '</b></div>' +
          '<div class="spec-group"><div class="spec-group-label">Parking Stand Info</div>' + infoHtml + '</div>';
      }

      specAirportSelect.addEventListener('change', function(){ renderFacilities(specAirportSelect.value); });
      renderFacilities(specAirportSelect.value);
    }

    /* ---------- Peralatan & Pelayanan Darurat (donut/pie/bar, hover tooltips) ---------- */
    var pkpSection = document.querySelector('[data-pkp-section]');
    if(pkpSection){
      var PKP_NS = 'http://www.w3.org/2000/svg';
      function pkpEl(tag, attrs){
        var el = document.createElementNS(PKP_NS, tag);
        for(var k in attrs){ el.setAttribute(k, attrs[k]); }
        return el;
      }
      function pkpFmt(n){ return Math.round(n).toLocaleString('id-ID'); }
      function pkpPolar(cx, cy, r, deg){
        var rad = (deg - 90) * Math.PI / 180;
        return [cx + r * Math.cos(rad), cy + r * Math.sin(rad)];
      }

      /* Shared tooltip (mirrors .emisi-tooltip styling) */
      var pkpTooltip = document.createElement('div');
      pkpTooltip.className = 'emisi-tooltip';
      document.body.appendChild(pkpTooltip);
      var pkpTooltipHost = null;

      function pkpShowTooltip(host, clientX, clientY, title, rows){
        pkpTooltipHost = host;
        while(pkpTooltip.firstChild) pkpTooltip.removeChild(pkpTooltip.firstChild);
        var t = document.createElement('div');
        t.className = 'emisi-tooltip-title';
        t.textContent = title;
        pkpTooltip.appendChild(t);
        rows.forEach(function(r){
          var row = document.createElement('div');
          row.className = 'emisi-tooltip-row';
          if(r.color){
            var key = document.createElement('span');
            key.className = 'key';
            key.style.background = r.color;
            row.appendChild(key);
          }
          var lbl = document.createElement('span');
          lbl.className = 'lbl'; lbl.textContent = r.label;
          var val = document.createElement('span');
          val.className = 'val'; val.textContent = r.value;
          row.appendChild(lbl); row.appendChild(val);
          pkpTooltip.appendChild(row);
        });
        pkpPositionTooltip(clientX, clientY);
        pkpTooltip.classList.add('show');
      }
      function pkpPositionTooltip(clientX, clientY){
        if(!pkpTooltipHost) return;
        var rect = pkpTooltipHost.getBoundingClientRect();
        var ttRect = pkpTooltip.getBoundingClientRect();
        var left = clientX - rect.left + 16;
        var top = clientY - rect.top - ttRect.height - 14;
        if(left + ttRect.width > rect.width) left = clientX - rect.left - ttRect.width - 16;
        if(top < 0) top = clientY - rect.top + 16;
        pkpTooltip.style.left = (rect.left + left + window.scrollX) + 'px';
        pkpTooltip.style.top = (rect.top + top + window.scrollY) + 'px';
      }
      function pkpHideTooltip(){ pkpTooltip.classList.remove('show'); pkpTooltipHost = null; }

      function pkpRenderLegend(container, data){
        var wrap = document.createElement('div');
        wrap.className = 'pkp-legend';
        data.forEach(function(d){
          var row = document.createElement('div');
          row.className = 'pkp-legend-row';
          var dot = document.createElement('span');
          dot.className = 'pkp-legend-dot';
          dot.style.background = d.color;
          var lbl = document.createElement('span');
          lbl.className = 'pkp-legend-lbl';
          lbl.textContent = d.label;
          var val = document.createElement('span');
          val.className = 'pkp-legend-val';
          val.textContent = pkpFmt(d.value);
          row.appendChild(dot); row.appendChild(lbl); row.appendChild(val);
          wrap.appendChild(row);
        });
        container.appendChild(wrap);
      }

      /* ---- Donut (ring) chart via stroke-dasharray ---- */
      function pkpRenderDonut(wrap, data, opts){
        opts = opts || {};
        var size = opts.size || 208, thickness = opts.thickness || 30;
        var r = (size - thickness) / 2, cx = size / 2, cy = size / 2;
        var circumference = 2 * Math.PI * r;
        var total = data.reduce(function(s, d){ return s + d.value; }, 0);

        var svg = pkpEl('svg', { viewBox: '0 0 ' + size + ' ' + size, width: size, height: size });
        var g = pkpEl('g', { transform: 'rotate(-90 ' + cx + ' ' + cy + ')' });
        var offset = 0;
        data.forEach(function(d){
          var frac = d.value / total;
          var len = Math.max(frac * circumference - 2, 0);
          var seg = pkpEl('circle', {
            cx: cx, cy: cy, r: r, fill: 'none', stroke: d.color, 'stroke-width': thickness,
            'stroke-dasharray': len + ' ' + (circumference - len), 'stroke-dashoffset': -offset,
            'stroke-linecap': 'round', class: 'pkp-donut-seg'
          });
          g.appendChild(seg);
          seg.addEventListener('pointerenter', function(){ seg.setAttribute('stroke-width', thickness + 5); });
          seg.addEventListener('pointerleave', function(){ seg.setAttribute('stroke-width', thickness); pkpHideTooltip(); });
          seg.addEventListener('pointermove', function(e){
            pkpShowTooltip(wrap, e.clientX, e.clientY, d.label, [
              { color: d.color, label: 'Jumlah', value: pkpFmt(d.value) },
              { label: 'Persentase', value: Math.round(d.value / total * 100) + '%' }
            ]);
          });
          offset += frac * circumference;
        });
        svg.appendChild(g);

        if(opts.centerValue != null){
          var t1 = pkpEl('text', { x: cx, y: cy - 3, class: 'pkp-donut-total' });
          t1.textContent = pkpFmt(opts.centerValue);
          var t2 = pkpEl('text', { x: cx, y: cy + 16, class: 'pkp-donut-sub' });
          t2.textContent = opts.centerLabel || 'TOTAL';
          svg.appendChild(t1); svg.appendChild(t2);
        }
        wrap.appendChild(svg);
        pkpRenderLegend(wrap.parentNode, data);
      }

      /* ---- Pie (filled) chart via arc paths ---- */
      function pkpRenderPie(wrap, data, opts){
        opts = opts || {};
        var size = opts.size || 190;
        var r = size / 2 - 6, cx = size / 2, cy = size / 2;
        var total = data.reduce(function(s, d){ return s + d.value; }, 0);
        var svg = pkpEl('svg', { viewBox: '0 0 ' + size + ' ' + size, width: size, height: size });
        var angle = 0;
        data.forEach(function(d){
          var frac = d.value / total, sweep = frac * 360;
          var p1 = pkpPolar(cx, cy, r, angle), p2 = pkpPolar(cx, cy, r, angle + sweep);
          var large = sweep > 180 ? 1 : 0;
          var d1 = 'M' + cx + ',' + cy + ' L' + p1[0] + ',' + p1[1] +
            ' A' + r + ',' + r + ' 0 ' + large + ' 1 ' + p2[0] + ',' + p2[1] + ' Z';
          var seg = pkpEl('path', { d: d1, fill: d.color, stroke: '#fff', 'stroke-width': 2, class: 'pkp-pie-seg' });
          svg.appendChild(seg);
          seg.addEventListener('pointerenter', function(){ seg.classList.add('is-hover'); });
          seg.addEventListener('pointerleave', function(){ pkpHideTooltip(); });
          seg.addEventListener('pointermove', function(e){
            pkpShowTooltip(wrap, e.clientX, e.clientY, d.label, [
              { color: d.color, label: 'Jumlah', value: pkpFmt(d.value) },
              { label: 'Persentase', value: Math.round(d.value / total * 100) + '%' }
            ]);
          });
          angle += sweep;
        });
        wrap.appendChild(svg);
        pkpRenderLegend(wrap.parentNode, data);
      }

      /* ---- Simple grouped bar (2 categories x 2 series) ---- */

      /* ---- Single-series categorical bar (own color per bar) ---- */
      function pkpRenderCategoryBar(wrap, data){
        var width = 300, height = 210, padL = 22, padR = 6, padT = 16, padB = 30;
        var plotW = width - padL - padR, plotH = height - padT - padB;
        var maxY = Math.max.apply(null, data.map(function(d){ return d.value; })) * 1.25;
        var baseline = padT + plotH;
        var svg = pkpEl('svg', { viewBox: '0 0 ' + width + ' ' + height, width: '100%' });

        svg.appendChild(pkpEl('line', { class: 'pkp-axis-line', x1: padL, x2: padL + plotW, y1: baseline, y2: baseline }));

        var gap = 10, maxBarW = 34;
        var slot = plotW / data.length;
        var barW = Math.min(maxBarW, slot - gap);

        data.forEach(function(d, i){
          var x = padL + i * slot + (slot - barW) / 2;
          var h = (d.value / maxY) * plotH;
          var y = baseline - h;
          var bar = pkpEl('rect', { class: 'pkp-bar', x: x, y: y, width: barW, height: h, rx: 4, fill: d.color });
          svg.appendChild(bar);
          var vLbl = pkpEl('text', { class: 'pkp-val-label', x: x + barW / 2, y: y - 6 });
          vLbl.textContent = d.value;
          svg.appendChild(vLbl);
          var cLbl = pkpEl('text', { class: 'pkp-cat-label', x: x + barW / 2, y: baseline + 16, style: 'font-size:8.5px' });
          cLbl.textContent = d.shortLabel || d.label;
          svg.appendChild(cLbl);

          bar.addEventListener('pointerenter', function(){ bar.classList.add('is-hover'); });
          bar.addEventListener('pointerleave', function(){ pkpHideTooltip(); });
          bar.addEventListener('pointermove', function(e){
            pkpShowTooltip(wrap, e.clientX, e.clientY, d.label, [
              { color: d.color, label: 'Jumlah', value: pkpFmt(d.value) }
            ]);
          });
        });

        wrap.appendChild(svg);
      }

      /* ---- Palette (validated categorical order — see dataviz skill) ---- */
      var CAT = { blue: '#2a78d6', orange: '#eb6834', aqua: '#1baf7a', yellow: '#eda100', magenta: '#e87ba4', green: '#008300', violet: '#4a3aa7', red: '#e34948' };

      /* ---- Data ---- */
      pkpRenderDonut(document.querySelector('[data-pkp-chart="kategori"]'), [
        { label: 'Kategori 2', value: 23, color: CAT.blue },
        { label: 'Kategori 3', value: 35, color: CAT.orange },
        { label: 'Kategori 4', value: 70, color: CAT.aqua },
        { label: 'Kategori 5', value: 37, color: CAT.yellow },
        { label: 'Kategori 6', value: 24, color: CAT.magenta },
        { label: 'Kategori 7', value: 35, color: CAT.green },
        { label: 'Kategori 8', value: 18, color: CAT.violet },
        { label: 'Kategori 9', value: 5, color: CAT.red }
      ], { centerValue: 247, centerLabel: 'TOTAL' });

      pkpRenderPie(document.querySelector('[data-pkp-chart="vasis-breakdown"]'), [
        { label: 'PAPI', value: 75, color: CAT.violet },
        { label: 'A-PAPI', value: 25, color: CAT.aqua }
      ]);

      pkpRenderCategoryBar(document.querySelector('[data-pkp-chart="approach-light"]'), [
        { label: 'SALS', value: 4, color: CAT.blue },
        { label: 'MALS', value: 7, color: CAT.orange },
        { label: 'PALS CAT I', shortLabel: 'CAT I', value: 8, color: CAT.aqua },
        { label: 'PALS CAT II', shortLabel: 'CAT II', value: 5, color: CAT.yellow },
        { label: 'PALS CAT III', shortLabel: 'CAT III', value: 8, color: CAT.magenta }
      ]);
    }

    /* ---------- Penyelenggaraan & Pengusahaan (LOS charts, inspection list, DSP) ---------- */
    var svcSection = document.querySelector('[data-svc-section]');
    if(svcSection){
      var SVC_NS = 'http://www.w3.org/2000/svg';
      function svcEl(tag, attrs){
        var el = document.createElementNS(SVC_NS, tag);
        for(var k in attrs){ el.setAttribute(k, attrs[k]); }
        return el;
      }

      var svcTooltip = document.createElement('div');
      svcTooltip.className = 'emisi-tooltip';
      document.body.appendChild(svcTooltip);
      var svcTooltipHost = null;

      function svcShowTooltip(host, clientX, clientY, title, rows){
        svcTooltipHost = host;
        while(svcTooltip.firstChild) svcTooltip.removeChild(svcTooltip.firstChild);
        var t = document.createElement('div');
        t.className = 'emisi-tooltip-title';
        t.textContent = title;
        svcTooltip.appendChild(t);
        rows.forEach(function(r){
          var row = document.createElement('div');
          row.className = 'emisi-tooltip-row';
          if(r.color){
            var key = document.createElement('span');
            key.className = 'key'; key.style.background = r.color;
            row.appendChild(key);
          }
          var lbl = document.createElement('span');
          lbl.className = 'lbl'; lbl.textContent = r.label;
          var val = document.createElement('span');
          val.className = 'val'; val.textContent = r.value;
          row.appendChild(lbl); row.appendChild(val);
          svcTooltip.appendChild(row);
        });
        svcPositionTooltip(clientX, clientY);
        svcTooltip.classList.add('show');
      }
      function svcPositionTooltip(clientX, clientY){
        if(!svcTooltipHost) return;
        var rect = svcTooltipHost.getBoundingClientRect();
        var ttRect = svcTooltip.getBoundingClientRect();
        var left = clientX - rect.left + 16;
        var top = clientY - rect.top - ttRect.height - 14;
        if(left + ttRect.width > rect.width) left = clientX - rect.left - ttRect.width - 16;
        if(top < 0) top = clientY - rect.top + 16;
        svcTooltip.style.left = (rect.left + left + window.scrollX) + 'px';
        svcTooltip.style.top = (rect.top + top + window.scrollY) + 'px';
      }
      function svcHideTooltip(){ svcTooltip.classList.remove('show'); svcTooltipHost = null; }

      var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

      /* ---- Line chart (multi-series) ---- */
      function svcRenderLineChart(wrap, categories, series){
        var width = 560, height = 260, padL = 34, padR = 10, padT = 14, padB = 30;
        var plotW = width - padL - padR, plotH = height - padT - padB;
        var maxY = 100, step = 10;
        var baseline = padT + plotH;
        var svg = svcEl('svg', { viewBox: '0 0 ' + width + ' ' + height, width: '100%' });

        for(var v = 0; v <= maxY; v += step){
          var gy = baseline - (v / maxY) * plotH;
          svg.appendChild(svcEl('line', { class: 'pkp-axis-line', x1: padL, x2: padL + plotW, y1: gy, y2: gy }));
          var lbl = svcEl('text', { class: 'pkp-axis-label', x: padL - 6, y: gy + 3, 'text-anchor': 'end' });
          lbl.textContent = v;
          svg.appendChild(lbl);
        }

        var n = categories.length;
        var xStep = plotW / (n - 1);
        function xAt(i){ return padL + i * xStep; }
        function yAt(val){ return baseline - (val / maxY) * plotH; }

        categories.forEach(function(c, i){
          if(i % 2 === 1){
            var cl = svcEl('text', { class: 'pkp-cat-label', x: xAt(i), y: baseline + 18 });
            cl.textContent = c;
            svg.appendChild(cl);
          }
        });

        series.forEach(function(s){
          var d = s.values.map(function(val, i){ return (i === 0 ? 'M' : 'L') + xAt(i) + ',' + yAt(val); }).join('');
          svg.appendChild(svcEl('path', { d: d, fill: 'none', stroke: s.color, 'stroke-width': 2, 'stroke-linejoin': 'round', 'stroke-linecap': 'round' }));
          s.values.forEach(function(val, i){
            svg.appendChild(svcEl('circle', { cx: xAt(i), cy: yAt(val), r: 3.5, fill: s.color, stroke: '#fff', 'stroke-width': 1.4 }));
          });
        });

        var crosshair = svcEl('line', { x1: 0, x2: 0, y1: padT, y2: baseline, stroke: 'var(--primary)', 'stroke-width': 1, 'stroke-dasharray': '3 3', visibility: 'hidden', style: 'pointer-events:none' });
        svg.appendChild(crosshair);

        categories.forEach(function(c, i){
          var hit = svcEl('rect', { x: xAt(i) - xStep / 2, y: padT, width: xStep, height: plotH, fill: 'transparent', style: 'cursor:pointer' });
          hit.addEventListener('pointerenter', function(){
            crosshair.setAttribute('x1', xAt(i)); crosshair.setAttribute('x2', xAt(i));
            crosshair.setAttribute('visibility', 'visible');
          });
          hit.addEventListener('pointerleave', function(){ crosshair.setAttribute('visibility', 'hidden'); svcHideTooltip(); });
          hit.addEventListener('pointermove', function(e){
            svcShowTooltip(wrap, e.clientX, e.clientY, c, series.map(function(s){
              return { color: s.color, label: s.name, value: s.values[i] };
            }));
          });
          svg.appendChild(hit);
        });

        wrap.appendChild(svg);
      }

      /* ---- Single-color bar chart ---- */
      function svcRenderBarChart(wrap, categories, values, color){
        var width = 560, height = 260, padL = 34, padR = 10, padT = 14, padB = 30;
        var plotW = width - padL - padR, plotH = height - padT - padB;
        var maxY = 100, step = 10;
        var baseline = padT + plotH;
        var svg = svcEl('svg', { viewBox: '0 0 ' + width + ' ' + height, width: '100%' });

        for(var v = 0; v <= maxY; v += step){
          var gy = baseline - (v / maxY) * plotH;
          svg.appendChild(svcEl('line', { class: 'pkp-axis-line', x1: padL, x2: padL + plotW, y1: gy, y2: gy }));
          var lbl = svcEl('text', { class: 'pkp-axis-label', x: padL - 6, y: gy + 3, 'text-anchor': 'end' });
          lbl.textContent = v;
          svg.appendChild(lbl);
        }

        var n = categories.length;
        var slot = plotW / n, gap = 8, maxBarW = 30;
        var barW = Math.min(maxBarW, slot - gap);

        categories.forEach(function(c, i){
          var val = values[i];
          var x = padL + i * slot + (slot - barW) / 2;
          var h = (val / maxY) * plotH;
          var y = baseline - h;
          var bar = svcEl('rect', { class: 'pkp-bar', x: x, y: y, width: barW, height: h, rx: 4, fill: color });
          svg.appendChild(bar);
          bar.addEventListener('pointerenter', function(){ bar.classList.add('is-hover'); });
          bar.addEventListener('pointerleave', function(){ svcHideTooltip(); });
          bar.addEventListener('pointermove', function(e){
            svcShowTooltip(wrap, e.clientX, e.clientY, c, [{ color: color, label: 'Skor LOS', value: val }]);
          });
          if(i % 2 === 1){
            var cl = svcEl('text', { class: 'pkp-cat-label', x: x + barW / 2, y: baseline + 18 });
            cl.textContent = c;
            svg.appendChild(cl);
          }
        });

        wrap.appendChild(svg);
      }
      svcRenderLineChart(document.querySelector('[data-pkp-chart="los-trend"]'), MONTHS, [
        { name: 'Sisi Udara', color: '#4a3aa7', values: [86, 88, 92, 90, 87, 93, 89, 91, 86, 90, 88, 94] },
        { name: 'Sisi Darat', color: '#1baf7a', values: [84, 85, 83, 86, 84, 88, 85, 87, 90, 86, 89, 84] },
        { name: 'Terminal', color: '#eb6834', values: [80, 82, 78, 72, 80, 74, 71, 85, 79, 90, 83, 92] }
      ]);

      svcRenderBarChart(document.querySelector('[data-pkp-chart="los-aggregate"]'), MONTHS,
        [58, 62, 76, 86, 64, 83, 78, 92, 74, 88, 80, 93], '#2E86D8');

      /* ---- Checklist toggle ---- */
      var svcChecklistToggle = document.querySelector('[data-svc-checklist]');
      if(svcChecklistToggle){
        svcChecklistToggle.addEventListener('change', function(){
          var on = svcChecklistToggle.checked;
          document.querySelectorAll('[data-svc-nilai]').forEach(function(cell){
            var nilai = cell.getAttribute('data-svc-nilai');
            cell.innerHTML = on
              ? '<span class="svc-nilai-badge pass"><i class="bi bi-check-circle-fill"></i> Terpenuhi</span>'
              : '<span class="svc-nilai-badge">' + nilai + '</span>';
          });
        });
      }

      /* ---- Dokumen Standar Pelayanan (view-only) ---- */
      var dspTable = document.querySelector('[data-dsp-table]');
      if(dspTable){
        var DSP_DOCS = [
          { bandara: 'Soekarno-Hatta', type: 'Main', tahun: '2024', lembar: 'SBU/DSP-0061/TEST', nomor: 'Surat Keputusan General Manager PT Angkasa Pura Indonesia tentang Penetapan Dokumen Standar Pelayanan' },
          { bandara: 'Juanda', type: 'Main', tahun: '2024', lembar: 'SBU/DSP-0032/JUA', nomor: 'Surat Keputusan General Manager PT Angkasa Pura Indonesia tentang Penetapan Dokumen Standar Pelayanan' },
          { bandara: 'Kualanamu', type: 'Addendum', tahun: '2025', lembar: 'SBU/DSP-0018/KNO', nomor: 'Surat Keputusan General Manager tentang Perubahan (Addendum) Dokumen Standar Pelayanan' },
          { bandara: 'Sultan Hasanuddin', type: 'Main', tahun: '2023', lembar: 'SBU/DSP-0045/UPG', nomor: 'Surat Keputusan General Manager PT Angkasa Pura Indonesia tentang Penetapan Dokumen Standar Pelayanan' },
          { bandara: 'Sultan Aji Muhammad Sulaiman (Sepinggan)', type: 'Addendum', tahun: '2026', lembar: 'SBU/DSP-0027/BPN', nomor: 'Surat Keputusan General Manager tentang Perubahan (Addendum) Dokumen Standar Pelayanan' }
        ];

        var dspAirportSelect = document.querySelector('[data-dsp-airport]');
        Array.from(new Set(DSP_DOCS.map(function(d){ return d.bandara; }))).forEach(function(name){
          var opt = document.createElement('option');
          opt.value = name; opt.textContent = name;
          dspAirportSelect.appendChild(opt);
        });

        var dspTypeSelect = document.querySelector('[data-dsp-type]');
        var dspYearSelect = document.querySelector('[data-dsp-year]');
        var dspEmpty = document.querySelector('[data-dsp-empty]');
        var dspTbody = dspTable.querySelector('tbody');

        function renderDsp(){
          var airport = dspAirportSelect.value, type = dspTypeSelect.value, year = dspYearSelect.value;
          var rows = DSP_DOCS.filter(function(d){
            return (airport === 'all' || d.bandara === airport) &&
              (type === 'all' || d.type === type) &&
              (year === 'all' || d.tahun === year);
          });
          while(dspTbody.firstChild) dspTbody.removeChild(dspTbody.firstChild);
          rows.forEach(function(d){
            var tr = document.createElement('tr');
            var typeBadge = d.type === 'Main' ? 'badge-primary' : 'badge-accent';
            tr.innerHTML =
              '<td>' + d.bandara + '</td>' +
              '<td><span class="badge ' + typeBadge + '">' + d.type + '</span></td>' +
              '<td>' + d.tahun + '</td>' +
              '<td>' + d.lembar + '</td>' +
              '<td>' + d.nomor + '</td>' +
              '<td><a href="#" class="dl-link"><i class="bi bi-eye"></i> Lihat</a></td>';
            dspTbody.appendChild(tr);
          });
          if(dspEmpty) dspEmpty.style.display = rows.length === 0 ? '' : 'none';
        }

        dspAirportSelect.addEventListener('change', renderDsp);
        dspTypeSelect.addEventListener('change', renderDsp);
        dspYearSelect.addEventListener('change', renderDsp);
        var dspSearchBtn = document.querySelector('[data-dsp-search]');
        dspSearchBtn && dspSearchBtn.addEventListener('click', renderDsp);
        renderDsp();
      }
    }

    /* ---------- Dashboard carousel (tabs + arrows + dots) ---------- */
    var dashCarousel = document.querySelector('[data-dash-carousel]');
    if(dashCarousel){
      var dashTabs = Array.prototype.slice.call(dashCarousel.querySelectorAll('[data-dash-tab]'));
      var dashSlides = Array.prototype.slice.call(dashCarousel.querySelectorAll('[data-dash-slide]'));
      var dashDots = dashCarousel.querySelector('[data-dash-dots]');
      var dashIndex = 0;

      dashSlides.forEach(function(_, i){
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.setAttribute('aria-label', 'Dasbor ' + (i + 1));
        if(i === 0) dot.classList.add('active');
        dot.addEventListener('click', function(){ showDash(i); });
        dashDots.appendChild(dot);
      });
      var dashDotBtns = Array.prototype.slice.call(dashDots.children);

      function showDash(i){
        dashIndex = (i + dashSlides.length) % dashSlides.length;
        dashSlides.forEach(function(s, n){
          var on = n === dashIndex;
          s.hidden = !on;
          s.classList.toggle('active', on);
        });
        dashTabs.forEach(function(t, n){
          var on = n === dashIndex;
          t.classList.toggle('active', on);
          t.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        dashDotBtns.forEach(function(d, n){ d.classList.toggle('active', n === dashIndex); });
      }

      dashTabs.forEach(function(t, i){ t.addEventListener('click', function(){ showDash(i); }); });
      var dashPrev = dashCarousel.querySelector('[data-dash-prev]');
      var dashNext = dashCarousel.querySelector('[data-dash-next]');
      dashPrev && dashPrev.addEventListener('click', function(){ showDash(dashIndex - 1); });
      dashNext && dashNext.addEventListener('click', function(){ showDash(dashIndex + 1); });
    }

    /* ---------- Newsletter form (demo) ---------- */
    var newsletterForm = document.querySelector('[data-newsletter-form]');
    if(newsletterForm){
      newsletterForm.addEventListener('submit', function(e){
        e.preventDefault();
        var btn = newsletterForm.querySelector('button');
        var original = btn.textContent;
        btn.disabled = true;
        postForm(newsletterForm).then(function(res){
          btn.textContent = res.message;
          setTimeout(function(){
            btn.textContent = original;
            btn.disabled = false;
            if(res.ok) newsletterForm.reset();
          }, 2500);
        });
      });
    }

  });
})();
