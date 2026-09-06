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

  const getBottomOffset = () => {
    const value = Number.parseFloat(
      window.getComputedStyle(app).getPropertyValue('--dc-joomla-bottom-offset')
    );

    return Number.isFinite(value) ? Math.max(0, value) : 0;
  };

  const updateSafeArea = () => {
    frame = 0;

    if (!mobileQuery.matches) {
      document.documentElement.style.removeProperty('scroll-padding-bottom');
      return;
    }

    const bottomOffset = getBottomOffset();

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

  document.addEventListener('decarocourses:layoutoffsets', queueUpdate);
  window.addEventListener('resize', queueUpdate, { passive: true });
  window.addEventListener('orientationchange', queueUpdate, { passive: true });

  if (typeof mobileQuery.addEventListener === 'function') {
    mobileQuery.addEventListener('change', queueUpdate);
  }

  if ('ResizeObserver' in window) {
    const observer = new ResizeObserver(queueUpdate);
    [app, actions]
      .filter((element) => element instanceof HTMLElement)
      .forEach((element) => observer.observe(element));
  }

  updateSafeArea();
})();
