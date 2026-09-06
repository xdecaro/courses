(() => {
  'use strict';

  const initBulkActions = () => {
    const form = document.querySelector('.dc-app')?.closest('form#adminForm')
      || document.querySelector('form#adminForm');

    if (!form) {
      return;
    }

    const bulkButtons = [...form.querySelectorAll('[data-dc-bulk-action]')];

    if (!bulkButtons.length) {
      return;
    }

    const getRowCheckboxes = () => [...form.querySelectorAll('input[name="cid[]"]')];
    const boxchecked = form.querySelector('input[name="boxchecked"]');
    let updateQueued = false;

    const updateState = () => {
      const selectedCount = getRowCheckboxes().filter((checkbox) => checkbox.checked).length;
      const hasSelection = selectedCount > 0;

      if (boxchecked) {
        boxchecked.value = String(selectedCount);
      }

      bulkButtons.forEach((button) => {
        button.disabled = !hasSelection;
        button.setAttribute('aria-disabled', hasSelection ? 'false' : 'true');
        button.classList.toggle('is-disabled', !hasSelection);
      });
    };

    const queueUpdate = () => {
      if (updateQueued) {
        return;
      }

      updateQueued = true;
      window.requestAnimationFrame(() => {
        updateQueued = false;
        updateState();
      });
    };

    form.addEventListener('change', (event) => {
      const target = event.target;

      if (target instanceof HTMLInputElement && target.type === 'checkbox') {
        queueUpdate();
      }
    });

    updateState();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBulkActions, { once: true });
  } else {
    initBulkActions();
  }
})();
