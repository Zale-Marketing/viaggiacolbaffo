/**
 * filters.js — Trip catalog filter engine
 * Reads initial state from window.FILTERS_INIT (set inline by viaggi.php)
 */
(function () {
  'use strict';

  var cfg = window.FILTERS_INIT || {};

  // ─── State ─────────────────────────────────────────────────────────────────
  var state = {
    search:    cfg.search    || '',
    continent: cfg.continent || '',
    tipo:      cfg.tipo      || [],
    mese:      cfg.mese      || [],
    per:       cfg.per       || [],
    sort:      cfg.sort      || 'date-asc'
  };

  var MESE_MAP = cfg.meseMap || {}; // { "gennaio": 1, "febbraio": 2, ... }

  // ─── DOM refs ───────────────────────────────────────────────────────────────
  var searchInput  = document.getElementById('search-trips');
  var searchWrap   = document.getElementById('filter-search-wrap');
  var searchClear  = document.getElementById('search-clear');
  var gridEl       = document.getElementById('trips-grid');
  var emptyEl      = document.getElementById('empty-state');
  var countEl      = document.getElementById('trip-count');
  var resetBtn     = document.getElementById('filter-reset');
  var resultsCount = countEl ? countEl.closest('.results-bar__count') : null;

  var wrappers     = Array.from(document.querySelectorAll('.trip-card-wrapper'));

  // Dropdown toggle buttons
  var toggleContinent = document.getElementById('toggle-continent');
  var toggleTipo      = document.getElementById('toggle-tipo');
  var toggleMese      = document.getElementById('toggle-mese');
  var togglePer       = document.getElementById('toggle-per');

  // ─── Debounce ───────────────────────────────────────────────────────────────
  function debounce(fn, ms) {
    var t;
    return function () {
      clearTimeout(t);
      t = setTimeout(fn, ms);
    };
  }

  // ─── Core filter function ───────────────────────────────────────────────────
  function applyFilters() {
    var search    = state.search.toLowerCase().trim();
    var continent = state.continent;
    var tipo      = state.tipo;
    var mese      = state.mese;
    var per       = state.per;

    // Build set of month numbers from selected mese slugs
    var meseNums = mese.map(function (slug) { return MESE_MAP[slug]; }).filter(Boolean);

    var visible = 0;

    wrappers.forEach(function (w) {
      var wContinent = w.dataset.continent || '';
      var wTags      = w.dataset.tags ? w.dataset.tags.split(' ') : [];
      var wMonth     = parseInt(w.dataset.month, 10);
      var wSearch    = w.dataset.search || '';

      var mContinent = !continent || wContinent === continent;
      var mTipo      = tipo.length === 0 || tipo.every(function (t) { return wTags.indexOf(t) !== -1; });
      var mMese      = meseNums.length === 0 || meseNums.indexOf(wMonth) !== -1;
      var mPer       = per.length === 0  || per.every(function (p)  { return wTags.indexOf(p) !== -1; });
      var mSearch    = !search || wSearch.indexOf(search) !== -1;

      if (mContinent && mTipo && mMese && mPer && mSearch) {
        w.style.display = '';
        visible++;
      } else {
        w.style.display = 'none';
      }
    });

    // Count fade
    if (countEl) {
      countEl.classList.add('count-fade');
      setTimeout(function () {
        countEl.textContent = visible;
        countEl.classList.remove('count-fade');
      }, 150);
    }

    // Grid / empty state toggle
    var hasResults = visible > 0;
    if (gridEl)  gridEl.style.display  = hasResults ? '' : 'none';
    if (emptyEl) emptyEl.style.display = hasResults ? 'none' : 'block';

    // Hide count row when empty
    if (resultsCount) resultsCount.style.display = hasResults ? '' : 'none';

    // Reset button visibility
    var anyActive = state.search || state.continent ||
                    state.tipo.length || state.mese.length || state.per.length;
    if (resetBtn) resetBtn.classList.toggle('is-visible', !!anyActive);

    // Sort visible cards
    sortGrid();

    // Sync URL
    syncURL();
  }

  // ─── Sort ──────────────────────────────────────────────────────────────────
  function sortGrid() {
    if (!gridEl) return;

    var visible = wrappers.filter(function (w) { return w.style.display !== 'none'; });

    visible.sort(function (a, b) {
      switch (state.sort) {
        case 'price-asc':
          return parseInt(a.dataset.price, 10) - parseInt(b.dataset.price, 10);
        case 'price-desc':
          return parseInt(b.dataset.price, 10) - parseInt(a.dataset.price, 10);
        case 'date-desc':
          return a.dataset.date < b.dataset.date ? 1 : -1;
        case 'newest':
          return parseInt(b.dataset.index, 10) - parseInt(a.dataset.index, 10);
        case 'date-asc':
        default:
          return a.dataset.date > b.dataset.date ? 1 : -1;
      }
    });

    // Re-append in sorted order (hidden cards stay at end, invisible)
    visible.forEach(function (w) { gridEl.appendChild(w); });
  }

  // ─── URL sync ──────────────────────────────────────────────────────────────
  function syncURL() {
    var params = new URLSearchParams();
    if (state.search)              params.set('search',    state.search);
    if (state.continent)           params.set('continent', state.continent);
    if (state.tipo.length)         params.set('tipo',      state.tipo.join(','));
    if (state.mese.length)         params.set('mese',      state.mese.join(','));
    if (state.per.length)          params.set('per',       state.per.join(','));
    if (state.sort !== 'date-asc') params.set('sort',      state.sort);
    var newUrl = params.toString() ? '?' + params.toString() : window.location.pathname;
    history.replaceState(null, '', newUrl);
  }

  // ─── Toggle label helpers ───────────────────────────────────────────────────
  function updateToggle(btn, defaultLabel, selected) {
    if (!btn) return;
    if (!selected || (Array.isArray(selected) ? selected.length === 0 : !selected)) {
      btn.textContent = defaultLabel;
      btn.classList.remove('has-selection');
      btn.appendChild(document.createTextNode(''));
    } else {
      var count = Array.isArray(selected) ? selected.length : null;
      btn.textContent = count !== null
        ? defaultLabel.split(' ')[0] + ' (' + count + ')'
        : selected;
      btn.classList.add('has-selection');
    }
  }

  function updateContinentToggle() {
    if (!toggleContinent) return;
    if (!state.continent) {
      toggleContinent.textContent = 'Continente';
      toggleContinent.classList.remove('has-selection');
    } else {
      var radio = document.querySelector('input[name="continent"][value="' + state.continent + '"]');
      var label = radio ? radio.parentElement.textContent.trim() : state.continent;
      toggleContinent.textContent = label;
      toggleContinent.classList.add('has-selection');
    }
  }

  // ─── Dropdown open/close ───────────────────────────────────────────────────
  var ddIds = ['dd-continent', 'dd-tipo', 'dd-mese', 'dd-per'];

  function closeAll(except) {
    ddIds.forEach(function (id) {
      if (id !== except) {
        var el = document.getElementById(id);
        if (el) el.classList.remove('is-open');
      }
    });
  }

  ddIds.forEach(function (id) {
    var dd = document.getElementById(id);
    if (!dd) return;
    var toggle = dd.querySelector('.filter-dropdown__toggle');
    if (toggle) {
      toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        var wasOpen = dd.classList.contains('is-open');
        closeAll(null);
        if (!wasOpen) dd.classList.add('is-open');
      });
    }
  });

  // Click outside closes all dropdowns
  document.addEventListener('click', function () { closeAll(null); });

  // Panels don't propagate clicks outward
  ddIds.forEach(function (id) {
    var panel = document.getElementById('panel-' + id.replace('dd-', ''));
    if (panel) panel.addEventListener('click', function (e) { e.stopPropagation(); });
  });

  // ESC closes all
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAll(null);
  });

  // ─── Continent radio ───────────────────────────────────────────────────────
  document.querySelectorAll('input[name="continent"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
      state.continent = this.value;
      updateContinentToggle();
      applyFilters();
    });
  });

  // ─── Tipo checkboxes ───────────────────────────────────────────────────────
  document.querySelectorAll('input[name="tipo"]').forEach(function (cb) {
    cb.addEventListener('change', function () {
      var val = this.value;
      if (this.checked) {
        if (state.tipo.indexOf(val) === -1) state.tipo.push(val);
      } else {
        state.tipo = state.tipo.filter(function (v) { return v !== val; });
      }
      updateToggle(toggleTipo, 'Tipo viaggio', state.tipo);
      applyFilters();
    });
  });

  // ─── Mese checkboxes ───────────────────────────────────────────────────────
  document.querySelectorAll('input[name="mese"]').forEach(function (cb) {
    cb.addEventListener('change', function () {
      var val = this.value;
      if (this.checked) {
        if (state.mese.indexOf(val) === -1) state.mese.push(val);
      } else {
        state.mese = state.mese.filter(function (v) { return v !== val; });
      }
      updateToggle(toggleMese, 'Mese', state.mese);
      applyFilters();
    });
  });

  // ─── Per chi checkboxes ────────────────────────────────────────────────────
  document.querySelectorAll('input[name="per"]').forEach(function (cb) {
    cb.addEventListener('change', function () {
      var val = this.value;
      if (this.checked) {
        if (state.per.indexOf(val) === -1) state.per.push(val);
      } else {
        state.per = state.per.filter(function (v) { return v !== val; });
      }
      updateToggle(togglePer, 'Per chi', state.per);
      applyFilters();
    });
  });

  // ─── Search ────────────────────────────────────────────────────────────────
  if (searchInput) {
    var debouncedSearch = debounce(function () {
      state.search = searchInput.value;
      searchWrap.classList.toggle('has-text', !!state.search);
      applyFilters();
    }, 300);

    searchInput.addEventListener('keyup', debouncedSearch);
    searchInput.addEventListener('input', debouncedSearch);

    if (state.search) searchWrap.classList.add('has-text');
  }

  if (searchClear) {
    searchClear.addEventListener('click', function () {
      if (searchInput) searchInput.value = '';
      state.search = '';
      if (searchWrap) searchWrap.classList.remove('has-text');
      applyFilters();
    });
  }

  // ─── Sort pills ────────────────────────────────────────────────────────────
  document.querySelectorAll('.sort-pill').forEach(function (pill) {
    pill.addEventListener('click', function () {
      var newSort = this.dataset.sort;
      // Toggle direction for date sort
      if (newSort === 'date-asc' && state.sort === 'date-asc') {
        newSort = 'date-desc';
      } else if (newSort === 'date-desc' && state.sort === 'date-desc') {
        newSort = 'date-asc';
      }
      state.sort = newSort;
      document.querySelectorAll('.sort-pill').forEach(function (p) {
        p.classList.toggle('sort-pill--active', p.dataset.sort === state.sort);
      });
      // Update text on date-asc/desc pill if needed
      var dateAscPill = document.querySelector('[data-sort="date-asc"]');
      var dateDescPill = document.querySelector('[data-sort="date-desc"]');
      if (dateAscPill)  dateAscPill.classList.toggle('sort-pill--active',  state.sort === 'date-asc');
      if (dateDescPill) dateDescPill.classList.toggle('sort-pill--active', state.sort === 'date-desc');
      sortGrid();
      syncURL();
    });
  });

  // ─── Reset button ──────────────────────────────────────────────────────────
  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      // Clear state
      state.search    = '';
      state.continent = '';
      state.tipo      = [];
      state.mese      = [];
      state.per       = [];

      // Reset inputs
      if (searchInput) searchInput.value = '';
      if (searchWrap)  searchWrap.classList.remove('has-text');

      document.querySelectorAll('input[name="continent"]').forEach(function (r) {
        r.checked = r.value === '';
      });
      document.querySelectorAll('input[name="tipo"], input[name="mese"], input[name="per"]').forEach(function (cb) {
        cb.checked = false;
      });

      // Reset toggle labels
      if (toggleContinent) { toggleContinent.textContent = 'Continente';  toggleContinent.classList.remove('has-selection'); }
      if (toggleTipo)      { toggleTipo.textContent      = 'Tipo viaggio'; toggleTipo.classList.remove('has-selection'); }
      if (toggleMese)      { toggleMese.textContent      = 'Mese';         toggleMese.classList.remove('has-selection'); }
      if (togglePer)       { togglePer.textContent       = 'Per chi';      togglePer.classList.remove('has-selection'); }

      applyFilters();
    });
  }

  // ─── Initialize from URL state ─────────────────────────────────────────────
  // Pre-check all checkboxes matching init state (PHP already sets checked attr
  // but toggles need correct text + has-selection class set from state).
  updateContinentToggle();
  if (state.tipo.length) updateToggle(toggleTipo, 'Tipo viaggio', state.tipo);
  if (state.mese.length) updateToggle(toggleMese, 'Mese', state.mese);
  if (state.per.length)  updateToggle(togglePer,  'Per chi', state.per);

  applyFilters();

})();
