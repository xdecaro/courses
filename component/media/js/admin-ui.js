(() => {
  'use strict';

  const initBulkActions = () => {
    const form = document.querySelector('.dc-app')?.closest('form#adminForm') || document.querySelector('form#adminForm');

    if (!form) {
      return;
    }

    const bulkButtons = [...form.querySelectorAll('[data-dc-bulk-action]')];

    if (!bulkButtons.length) {
      return;
    }

    const rowCheckboxes = () => [...form.querySelectorAll('input[name="cid[]"]')];

    const updateState = () => {
      const selected = rowCheckboxes().some((checkbox) => checkbox.checked);

      bulkButtons.forEach((button) => {
        button.disabled = !selected;
        button.setAttribute('aria-disabled', selected ? 'false' : 'true');
        button.classList.toggle('is-disabled', !selected);
      });
    };

    form.addEventListener('change', (event) => {
      const target = event.target;

      if (!(target instanceof HTMLInputElement) || target.type !== 'checkbox') {
        return;
      }

      window.setTimeout(updateState, 0);
    });

    form.addEventListener('click', (event) => {
      const target = event.target;

      if (target instanceof HTMLInputElement && target.type === 'checkbox') {
        window.setTimeout(updateState, 0);
      }
    });

    bulkButtons.forEach((button) => {
      button.addEventListener('click', (event) => {
        if (!rowCheckboxes().some((checkbox) => checkbox.checked)) {
          event.preventDefault();
          event.stopImmediatePropagation();
          updateState();
        }
      }, true);
    });

    updateState();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBulkActions, { once: true });
  } else {
    initBulkActions();
  }
})();
