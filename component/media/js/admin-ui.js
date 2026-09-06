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
    const modal = document.querySelector('[data-dc-period-modal]');
    const modalClose = document.querySelector('[data-dc-period-modal-close]');
    const newYear = document.querySelector('[data-dc-period-new-year]');
    const addButton = document.querySelector('[data-dc-period-add]');
    const cancelButton = document.querySelector('[data-dc-period-new-cancel]');
    const preview = document.querySelector('[data-dc-period-preview]');
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

    const refreshPreview = () => {
      if (!(newYear instanceof HTMLInputElement) || !(preview instanceof HTMLElement)) {
        return;
      }

      const year = Number.parseInt(newYear.value, 10);
      preview.textContent = Number.isInteger(year) && year >= 1900 && year <= 2200
        ? formatPeriod(year)
        : '';
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
      refreshPreview();
    };

    const resetModal = () => {
      if (newYear instanceof HTMLInputElement) {
        newYear.value = '';
        newYear.required = false;
        newYear.setCustomValidity('');
      }

      if (preview instanceof HTMLElement) {
        preview.textContent = '';
      }
    };

    const openModal = () => {
      if (!(modal instanceof HTMLDialogElement)) {
        return;
      }

      resetModal();
      modal.showModal();
      window.requestAnimationFrame(() => newYear instanceof HTMLInputElement && newYear.focus());
    };

    const closeModal = () => {
      if (modal instanceof HTMLDialogElement && modal.open) {
        modal.close();
      }

      resetModal();
      toggle instanceof HTMLButtonElement && toggle.focus();
    };

    typeInputs.forEach((input) => {
      input.addEventListener('change', refreshYearLabels);
    });

    yearSelect.addEventListener('change', syncHidden);

    if (toggle instanceof HTMLButtonElement) {
      toggle.addEventListener('click', openModal);
    }

    if (modalClose instanceof HTMLButtonElement) {
      modalClose.addEventListener('click', closeModal);
    }

    if (newYear instanceof HTMLInputElement) {
      newYear.addEventListener('input', refreshPreview);
      newYear.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
          event.preventDefault();
          addButton instanceof HTMLButtonElement && addButton.click();
        }
      });
    }

    if (cancelButton instanceof HTMLButtonElement) {
      cancelButton.addEventListener('click', closeModal);
    }

    if (modal instanceof HTMLDialogElement) {
      modal.addEventListener('cancel', () => {
        resetModal();
      });

      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          closeModal();
        }
      });
    }

    if (addButton instanceof HTMLButtonElement && newYear instanceof HTMLInputElement) {
      addButton.addEventListener('click', () => {
        newYear.required = true;

        if (!newYear.reportValidity()) {
          return;
        }

        const year = Number.parseInt(newYear.value, 10);

        if (!Number.isInteger(year) || year < 1900 || year > 2200) {
          return;
        }

        let option = [...yearSelect.options].find((item) => Number.parseInt(item.value, 10) === year);

        if (!(option instanceof HTMLOptionElement)) {
          option = document.createElement('option');
          option.value = String(year);
          yearSelect.append(option);

          [...yearSelect.options]
            .sort((a, b) => Number.parseInt(a.value, 10) - Number.parseInt(b.value, 10))
            .forEach((item) => yearSelect.append(item));
        }

        option.selected = true;
        refreshYearLabels();
        closeModal();
        yearSelect.focus();
      });
    }

    if (form instanceof HTMLFormElement) {
      form.addEventListener('submit', syncHidden, { capture: true });
    }

    refreshYearLabels();
  };

  const initJoomlaLayoutOffsets = () => {
    const app = document.querySelector('.dc-app');
    const stickySide = document.querySelector('.dc-edition-sticky-side');
    const actions = document.querySelector('[data-dc-edition-actions]');

    if (!(app instanceof HTMLElement)) {
      return;
    }

    const getTopCandidates = () => [...new Set(document.querySelectorAll('#subhead-container, .subhead, #header, .header'))]
      .filter((element) => element instanceof HTMLElement);

    const getBottomCandidates = () => [...new Set(document.querySelectorAll('#header .header-items, .header-items'))]
      .filter((element) => element instanceof HTMLElement);

    let frame = 0;

    const updateLayout = () => {
      frame = 0;
      let topOffset = 0;
      let bottomOffset = 0;

      getTopCandidates().forEach((element) => {
        const style = window.getComputedStyle(element);

        if (!['fixed', 'sticky'].includes(style.position)
          || style.display === 'none'
          || style.visibility === 'hidden') {
          return;
        }

        const rect = element.getBoundingClientRect();

        if (rect.height <= 0) {
          return;
        }

        const parsedTop = Number.parseFloat(style.top);
        const top = Number.isFinite(parsedTop) ? Math.max(0, parsedTop) : 0;
        topOffset = Math.max(topOffset, top + rect.height);
      });

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

      const roundedTop = Math.ceil(topOffset);
      const roundedBottom = Math.ceil(bottomOffset);

      app.style.setProperty('--dc-joomla-sticky-offset', `${roundedTop}px`);
      app.style.setProperty('--dc-joomla-bottom-offset', `${roundedBottom}px`);

      if (actions instanceof HTMLElement) {
        const appRect = app.getBoundingClientRect();
        const actionHeight = Math.ceil(actions.getBoundingClientRect().height);

        app.style.setProperty('--dc-edition-actions-left', `${Math.max(0, Math.round(appRect.left))}px`);
        app.style.setProperty('--dc-edition-actions-width', `${Math.max(0, Math.round(appRect.width))}px`);
        app.style.setProperty('--dc-edition-actions-height', `${actionHeight}px`);
      }

      document.dispatchEvent(new CustomEvent('decarocourses:layoutoffsets', {
        detail: {
          top: roundedTop,
          bottom: roundedBottom
        }
      }));
    };

    const queueUpdate = () => {
      if (frame) {
        return;
      }

      frame = window.requestAnimationFrame(updateLayout);
    };

    window.addEventListener('resize', queueUpdate, { passive: true });
    window.addEventListener('orientationchange', queueUpdate, { passive: true });
    window.addEventListener('load', queueUpdate, { once: true });

    if ('ResizeObserver' in window) {
      const observer = new ResizeObserver(queueUpdate);
      [app, stickySide, actions, ...getTopCandidates(), ...getBottomCandidates()]
        .filter((element) => element instanceof HTMLElement)
        .forEach((element) => observer.observe(element));
    }

    const header = document.getElementById('header');
    const subhead = document.getElementById('subhead-container');

    if ('MutationObserver' in window) {
      const observer = new MutationObserver(queueUpdate);

      [header, subhead]
        .filter((element) => element instanceof HTMLElement)
        .forEach((element) => observer.observe(element, {
          childList: true,
          subtree: true,
          attributes: true,
          attributeFilter: ['class', 'style', 'hidden']
        }));
    }

    updateLayout();
  };

  const init = () => {
    initBulkActions();
    initEditionCustomFormat();
    initEditionPeriodBuilder();
    initJoomlaLayoutOffsets();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();
