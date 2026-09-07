(() => {
  'use strict';

  const dataNode = document.getElementById('dci-diagnostics-data');
  const copyButton = document.querySelector('[data-dci-copy]');
  const downloadButton = document.querySelector('[data-dci-download]');
  const statusNode = document.querySelector('[data-dci-status]');

  if (!(dataNode instanceof HTMLScriptElement)) {
    return;
  }

  let payload = {};

  try {
    payload = JSON.parse(dataNode.textContent || '{}');
  } catch (error) {
    payload = {};
  }

  const diagnosticText = Object.entries(payload)
    .map(([key, value]) => `${key}: ${value}`)
    .join('\n');

  const text = (key, fallback) => {
    if (window.Joomla?.Text?._) {
      const translated = Joomla.Text._(key);

      if (translated && translated !== key) {
        return translated;
      }
    }

    return fallback;
  };

  const setStatus = (message) => {
    if (statusNode instanceof HTMLElement) {
      statusNode.textContent = message;
    }
  };

  const copyWithFallback = async (value) => {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(value);
      return;
    }

    const area = document.createElement('textarea');
    area.value = value;
    area.setAttribute('readonly', '');
    area.style.position = 'fixed';
    area.style.opacity = '0';
    document.body.appendChild(area);
    area.select();

    const copied = document.execCommand('copy');
    area.remove();

    if (!copied) {
      throw new Error('copy_failed');
    }
  };

  if (copyButton instanceof HTMLButtonElement) {
    copyButton.addEventListener('click', async () => {
      try {
        await copyWithFallback(diagnosticText);
        setStatus(text('COM_DECAROCOURSES_INFO_COPIED', 'Diagnostica copiata.'));
      } catch (error) {
        setStatus(text('COM_DECAROCOURSES_INFO_COPY_FAILED', 'Impossibile copiare la diagnostica.'));
      }
    });
  }

  if (downloadButton instanceof HTMLButtonElement) {
    downloadButton.addEventListener('click', () => {
      const version = String(payload.Courses || 'current').replace(/[^0-9A-Za-z._-]/g, '_');
      const blob = new Blob([`${diagnosticText}\n`], { type: 'text/plain;charset=utf-8' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');

      link.href = url;
      link.download = `courses-diagnostics-${version}.txt`;
      document.body.appendChild(link);
      link.click();
      link.remove();
      URL.revokeObjectURL(url);

      setStatus(text('COM_DECAROCOURSES_INFO_DOWNLOADED', 'Diagnostica scaricata.'));
    });
  }
})();
