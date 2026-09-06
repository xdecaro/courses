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

  const initEditionPeriodBuilder = () => {
    const builder = document.querySelector('[data-dc-period-builder]');
    const hidden = document.getElementById('jform_academic_year');
    const yearSelect = document.querySelector('[data-dc-period-year]');
    const typeInputs = [...document.querySelectorAll('input[name="dc_period_type"]')];
    const toggle = document.querySelector('[data-dc-period-new-toggle]');
    const panel = document.querySelector('[data-dc-period-new]');
    const newYear = document.querySelector('[data-dc-period-new-year]');
    const addButton = document.querySelector('[data-dc-period-add]');
    const cancelButton = document.querySelector('[data-dc-period-new-cancel]');
    const help = document.querySelector('[data-dc-period-new-help]');
    const form = builder?.closest('form');

    if (!(builder instanceof HTMLElement)
      || !(hidden instanceof HTMLInputElement)
      || !(yearSelect instanceof HTMLSelectElement)
      || !typeInputs.length) {
      return;
    }

    const getType = () => typeInputs.find((input) => input.checked)?.value === 'academic'
      ? 'academic'
      : 'single';

    const formatPeriod = (year, type = getType()) => type === 'academic'
      ? `${year}/${year + 1}`
      : String(year);

    const syncHidden = () => {
      const year = Number.parseInt(yearSelect.value, 10);

      if (Number.isInteger(year) && year >= 1900 && year <= 2200) {
        hidden.value = formatPeriod(year);
      }
    };

    const refreshYearLabels = () => {
      const type = getType();

      [...yearSelect.options].forEach((option) => {
        const year = Number.parseInt(option.value, 10);

        if (Number.isInteger(year)) {
          option.textContent = formatPeriod(year, type);
        }
      });

      syncHidden();
      refreshNewYearHelp();
    };

    const refreshNewYearHelp = () => {
      if (!(newYear instanceof HTMLInputElement) || !(help instanceof HTMLElement)) {
        return;
      }

      const year = Number.parseInt(newYear.value, 10);
      help.textContent = Number.isInteger(year) && year >= 1900 && year <= 2200
        ? formatPeriod(year)
        : '';
    };

    const setPanelOpen = (open) => {
      if (!(panel instanceof HTMLElement) || !(toggle instanceof HTMLButtonElement)) {
        return;
      }

      panel.hidden = !open;
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

      if (open && newYear instanceof HTMLInputElement) {
        window.requestAnimationFrame(() => newYear.focus());
      }
    };

    typeInputs.forEach((input) => {
      input.addEventListener('change', refreshYearLabels);
    });

    yearSelect.addEventListener('change', syncHidden);

    if (toggle instanceof HTMLButtonElement) {
      toggle.addEventListener('click', () => {
        setPanelOpen(panel instanceof HTMLElement ? panel.hidden : false);
      });
    }

    if (newYear instanceof HTMLInputElement) {
      newYear.addEventListener('input', refreshNewYearHelp);
    }

    if (cancelButton instanceof HTMLButtonElement) {
      cancelButton.addEventListener('click', () => {
        if (newYear instanceof HTMLInputElement) {
          newYear.value = '';
          newYear.setCustomValidity('');
        }

        if (help instanceof HTMLElement) {
          help.textContent = '';
        }

        setPanelOpen(false);
      });
    }

    if (addButton instanceof HTMLButtonElement && newYear instanceof HTMLInputElement) {
      addButton.addEventListener('click', () => {
        newYear.required = true;

        if (!newYear.reportValidity()) {
          newYear.required = false;
          return;
        }

        newYear.required = false;
        const year = Number.parseInt(newYear.value, 10);

        if (!Number.isInteger(year) || year < 1900 || year > 2200) {
          return;
        }

        let option = [...yearSelect.options].find((item) => Number.parseInt(item.value, 10) === year);

        if (!(option instanceof HTMLOptionElement)) {
          option = document.createElement('option');
          option.value = String(year);
          yearSelect.append(option);

          const sorted = [...yearSelect.options].sort(
            (a, b) => Number.parseInt(a.value, 10) - Number.parseInt(b.value, 10)
          );
          sorted.forEach((item) => yearSelect.append(item));
        }

        option.selected = true;
        refreshYearLabels();
        newYear.value = '';
        newYear.setCustomValidity('');

        if (help instanceof HTMLElement) {
          help.textContent = '';
        }

        setPanelOpen(false);
        yearSelect.focus();
      });
    }

    if (form instanceof HTMLFormElement) {
      form.addEventListener('submit', syncHidden, { capture: true });
    }

    refreshYearLabels();
  };

  const init = () => {
    initBulkActions();
    initEditionCustomFormat();
    initEditionPeriodBuilder();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();
