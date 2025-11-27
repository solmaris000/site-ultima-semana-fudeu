// Simple i18n system: load language JSON and provide translation function
(function(){
  let currentLang = 'en';
  let translations = {};

  async function loadLanguage(lang) {
    if (!['en', 'pt'].includes(lang)) lang = 'en';
    try {
      const response = await fetch(`../lang/${lang}.json`);
      translations = await response.json();
      currentLang = lang;
      localStorage.setItem('app:language', lang);
      applyTranslations();
      return translations;
    } catch (e) {
      console.error('Error loading language:', e);
      return {};
    }
  }

  function t(key) {
    return translations[key] || key;
  }

  function applyTranslations() {
    // Replace all elements with data-i18n attribute
    document.querySelectorAll('[data-i18n]').forEach(el => {
      const key = el.getAttribute('data-i18n');
      const translated = t(key);
      
      // Check if it's an input/button with placeholder
      if (el.hasAttribute('placeholder')) {
        el.setAttribute('placeholder', translated);
      } else if (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA') {
        // Don't replace content of form elements
        return;
      } else if (el.querySelector('input, select, textarea, button')) {
        // If element contains form elements, only replace the first text node
        const firstTextNode = Array.from(el.childNodes).find(node => node.nodeType === 3 && node.textContent.trim());
        if (firstTextNode) {
          firstTextNode.textContent = translated;
        }
      } else {
        // Safe to replace textContent
        el.textContent = translated;
      }
    });
  }

  async function initialize() {
    // Load language from localStorage, default to 'en'
    const saved = localStorage.getItem('app:language') || 'en';
    await loadLanguage(saved);
  }

  window.i18n = {
    loadLanguage,
    t,
    getCurrentLang: () => currentLang,
    initialize,
    applyTranslations
  };
})();
