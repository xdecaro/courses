(() => {
  'use strict';

  const app = document.querySelector('.dc-app');

  if (!(app instanceof HTMLElement)) {
    return;
  }

  const editionPage = app.classList.contains('dc-edition-page') ? app : null;
  const actions = document.querySelector('[data-dc-edition-actions]');
  const mobileQuery = window.matchMedia('(max-width: 760px)');
  let frame = 0;

  const getBottomCandidates = () => [...new Set(document.querySelectorAll('#header .header-items, .header-items'))]
    .filter((element) => element instanceof HTMLElement);

  const getBottomOffset = () => {
    let bottomOffset = 0;

    getBottomCandidates().forEach((element) => {
      const style = window.getComputedStyle(element);

      if (style.position !== 'fixed'
        || style.display === 'none'
        || style.visibility === 'hidden') {
        return;
      }

      const rect = element.getBoundingClientRect();

      if (rect.height <= 0
        || rect.top >= window.innerHeight
        || rect.bottom < window.innerHeight - 2) {
        return;
      }

      bottomOffset = Math.max(bottomOffset, window.innerHeight - rect.top);
    });

    return Math.ceil(bottomOffset);
  };

  const updateSafeArea = () => {
    frame = 0;

    const bottomOffset = getBottomOffset();
    app.style.setProperty('--dc-joomla-bottom-offset', `${bottomOffset}px`);

    if (!mobileQuery.matches) {
      document.documentElement.style.removeProperty('scroll-padding-bottom');
      return;
    }

    if (actions instanceof HTMLElement && editionPage instanceof HTMLElement) {
      const actionHeight = Math.ceil(actions.getBoundingClientRect().height);
      const safeArea = bottomOffset + actionHeight + 40;

      editionPage.style.setProperty('--dc-edition-actions-height', `${actionHeight}px`);
      editionPage.style.setProperty('--dc-edition-mobile-safe', `${safeArea}px`);
      document.documentElement.style.scrollPaddingBottom = `${safeArea}px`;
    } else {
      document.documentElement.style.scrollPaddingBottom = `${bottomOffset + 48}px`;
    }
  };

  const queueUpdate = () => {
    if (frame) {
      return;
    }

    frame = window.requestAnimationFrame(updateSafeArea);
  };

  const keepFocusedControlVisible = (target) => {
    if (!mobileQuery.matches
      || !(editionPage instanceof HTMLElement)
      || !(actions instanceof HTMLElement)
      || !(target instanceof HTMLElement)
      || actions.contains(target)) {
      return;
    }

    window.requestAnimationFrame(() => {
      const targetRect = target.getBoundingClientRect();
      const actionsRect = actions.getBoundingClientRect();
      const clearance = 14;

      if (targetRect.bottom > actionsRect.top - clearance
        && targetRect.top < actionsRect.bottom) {
        const delta = targetRect.bottom - actionsRect.top + clearance;
        window.scrollBy({ top: delta, behavior: 'auto' });
      }
    });
  };

  if (editionPage instanceof HTMLElement && actions instanceof HTMLElement) {
    editionPage.addEventListener('focusin', (event) => {
      keepFocusedControlVisible(event.target);
    });
  }

  window.addEventListener('resize', queueUpdate, { passive: true });
  window.addEventListener('orientationchange', queueUpdate, { passive: true });
  window.addEventListener('load', queueUpdate, { once: true });

  if (typeof mobileQuery.addEventListener === 'function') {
    mobileQuery.addEventListener('change', queueUpdate);
  }

  if ('ResizeObserver' in window) {
    const observer = new ResizeObserver(queueUpdate);
    [app, actions, ...getBottomCandidates()]
      .filter((element) => element instanceof HTMLElement)
      .forEach((element) => observer.observe(element));
  }

  const header = document.getElementById('header');

  if ('MutationObserver' in window && header instanceof HTMLElement) {
    const observer = new MutationObserver(queueUpdate);
    observer.observe(header, {
      childList: true,
      subtree: true,
      attributes: true,
      attributeFilter: ['class', 'style', 'hidden']
    });
  }

  updateSafeArea();
})();
