/**
 * Live preview helpers for Queen Al-Falah Customizer settings.
 * This file safely no-ops when it is loaded outside the Customizer preview.
 */

(function (api) {
  'use strict';

  if (!api) {
    return;
  }

  const root = document.documentElement;

  const hasSetting = (settingId) => {
    return typeof api.has !== 'function' || api.has(settingId);
  };

  const bindSetting = (settingId, callback) => {
    if (!hasSetting(settingId)) {
      return;
    }

    api(settingId, (setting) => {
      setting.bind(callback);
    });
  };

  const setText = (selector, value) => {
    document.querySelectorAll(selector).forEach((element) => {
      element.textContent = value;
    });
  };

  const setColor = (customProperty, value) => {
    if (typeof value !== 'string' || !value.trim()) {
      return;
    }

    if (window.CSS && typeof window.CSS.supports === 'function' && !window.CSS.supports('color', value)) {
      return;
    }

    root.style.setProperty(customProperty, value);
  };

  bindSetting('blogname', (value) => {
    setText('.brand__name, .site-title', value);
  });

  bindSetting('blogdescription', (value) => {
    setText('.brand__tagline, .site-description', value);
  });

  ['queen_primary_color', 'queen_alfalah_primary_color', 'qa_primary_color', 'primary_color'].forEach((settingId) => {
    bindSetting(settingId, (value) => {
      setColor('--color-primary', value);
      setColor('--color-primary-deep', value);
      setColor('--qa-emerald-700', value);
      setColor('--qa-emerald-800', value);
    });
  });

  ['queen_accent_color', 'queen_alfalah_accent_color', 'qa_accent_color', 'accent_color'].forEach((settingId) => {
    bindSetting(settingId, (value) => {
      setColor('--color-accent', value);
      setColor('--qa-gold-500', value);
    });
  });

  ['queen_alfalah_hero_title', 'qa_hero_title'].forEach((settingId) => {
    bindSetting(settingId, (value) => {
      setText('.hero__title', value);
    });
  });

  ['queen_alfalah_hero_description', 'qa_hero_description'].forEach((settingId) => {
    bindSetting(settingId, (value) => {
      setText('.hero__lead', value);
    });
  });

  ['queen_alfalah_announcement_text', 'qa_announcement_text'].forEach((settingId) => {
    bindSetting(settingId, (value) => {
      setText('.announcement-strip__content', value);
    });
  });
})(window.wp && window.wp.customize ? window.wp.customize : null);
