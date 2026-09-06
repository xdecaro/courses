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

  const initEditionCustomFormat = () => {
    const format = document.getElementById('jform_format');
    const custom = document.getElementById('jform_format_custom');
    const wrapper = document.querySelector('[data-dc-format-custom]');

    if (!(format instanceof HTMLSelectElement)
      || !(custom instanceof HTMLInputElement)
      || !(wrapper instanceof HTMLElement)) {
      return;
    }

    const label = wrapper.querySelector('label');
    let requiredMarker = label?.querySelector('.dc-dynamic-required') || null;

    if (label && !requiredMarker) {
      requiredMarker = document.createElement('span');
      requiredMarker.className = 'star dc-dynamic-required';
      requiredMarker.setAttribute('aria-hidden', 'true');
      requiredMarker.textContent = ' *';
      label.append(requiredMarker);
    }

    const updateState = () => {
      const isCustom = format.value === 'custom';

      wrapper.hidden = !isCustom;
      wrapper.setAttribute('aria-hidden', isCustom ? 'false' : 'true');
      custom.required = isCustom;
      custom.classList.toggle('required', isCustom);

      if (requiredMarker instanceof HTMLElement) {
        requiredMarker.hidden = !isCustom;
      }

      if (isCustom) {
        custom.setAttribute('aria-required', 'true');
      } else {
        custom.removeAttribute('aria-required');
      }
    };

    format.addEventListener('change', updateState);
    updateState();
  };

  const init = () => {
    initBulkActions();
    initEditionCustomFormat();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();
