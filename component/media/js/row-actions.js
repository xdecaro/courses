(() => {
  'use strict';

  const allowedTask = /^(courses|editions)\.(publish|unpublish|trash|restore|checkin|delete|featured|unfeatured)$/;

  const submitRowTask = (button) => {
    const form = document.getElementById('adminForm');
    const task = String(button.dataset.dcRowTask || '');
    const itemId = String(button.dataset.dcItemId || '');

    if (!(form instanceof HTMLFormElement) || !allowedTask.test(task) || !/^\d+$/.test(itemId)) {
      return;
    }

    const confirmation = String(button.dataset.dcConfirm || '');

    if (confirmation && !window.confirm(confirmation)) {
      return;
    }

    const checkboxes = [...form.querySelectorAll('input[name="cid[]"]')]
      .filter((element) => element instanceof HTMLInputElement);
    const target = checkboxes.find((checkbox) => checkbox.value === itemId);

    if (!(target instanceof HTMLInputElement)) {
      return;
    }

    checkboxes.forEach((checkbox) => {
      checkbox.checked = checkbox === target;
    });

    const checkAll = form.querySelector('input[name="checkall-toggle"]');

    if (checkAll instanceof HTMLInputElement) {
      checkAll.checked = false;
    }

    const boxchecked = form.querySelector('input[name="boxchecked"]');

    if (boxchecked instanceof HTMLInputElement) {
      boxchecked.value = '1';
    }

    if (window.Joomla && typeof window.Joomla.submitbutton === 'function') {
      window.Joomla.submitbutton(task);
      return;
    }

    const taskInput = form.querySelector('input[name="task"]');

    if (taskInput instanceof HTMLInputElement) {
      taskInput.value = task;
    }

    form.requestSubmit();
  };

  document.addEventListener('click', (event) => {
    const target = event.target;

    if (!(target instanceof Element)) {
      return;
    }

    const button = target.closest('[data-dc-row-task]');

    if (!(button instanceof HTMLButtonElement)) {
      return;
    }

    event.preventDefault();
    submitRowTask(button);
  });
})();
