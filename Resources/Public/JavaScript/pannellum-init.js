(function () {
  'use strict';

  function initElement(el) {
    if (!el || !el.id) return;
    var cfgJson = el.getAttribute('data-pannellum-config');
    if (!cfgJson) return;

    var config;
    try {
      config = JSON.parse(cfgJson);
    } catch (e) {
      if (window && window.console) {
        console.error('pannellum-init: invalid JSON in data-pannellum-config for #' + el.id, e);
      }
      return;
    }

    function tryInit() {
      if (window.pannellum && document.getElementById(el.id)) {
        try {
          window.pannellum.viewer(el.id, config);
        } catch (err) {
          if (window && window.console) {
            console.error('pannellum-init: failed to initialize viewer for #' + el.id, err);
          }
        }
      } else {
        setTimeout(tryInit, 50);
      }
    }

    tryInit();
  }

  function initAll() {
    var nodes = document.querySelectorAll('[data-pannellum-config]');
    nodes.forEach(initElement);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
