<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$info = $this->info;
$diagnostics = (array) ($info['diagnostics'] ?? []);
$allCriticalOk = !in_array(false, [
    $diagnostics['coursesTable'] ?? false,
    $diagnostics['editionsTable'] ?? false,
    $diagnostics['schemaAligned'] ?? false,
], true);

$diagnosticPayload = [
    'Courses component' => (string) ($info['componentVersion'] ?? ''),
    'Courses package' => (string) ($info['packageVersion'] ?? ''),
    'Joomla' => (string) ($info['joomlaVersion'] ?? ''),
    'PHP' => (string) ($info['phpVersion'] ?? ''),
    'DB schema' => (string) ($info['schemaVersion'] ?? ''),
    'Courses table' => !empty($diagnostics['coursesTable']) ? 'OK' : 'MISSING',
    'Editions table' => !empty($diagnostics['editionsTable']) ? 'OK' : 'MISSING',
    'Schema aligned' => !empty($diagnostics['schemaAligned']) ? 'OK' : 'CHECK',
    'Forms installed' => !empty($info['formsInstalled']) ? 'YES' : 'NO',
    'Forms version' => (string) ($info['formsVersion'] ?? ''),
    'Forms count' => (int) ($info['formsCount'] ?? 0),
    'Update site configured' => !empty($info['updateSite']['configured']) ? 'YES' : 'NO',
    'Update site enabled' => !empty($info['updateSite']['enabled']) ? 'YES' : 'NO',
    'Available update' => (string) ($info['availableVersion'] ?? ''),
];
?>
<div class="dc-app dc-information-page">
  <header class="dc-page-head">
    <div>
      <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_EYEBROW'); ?></span>
      <h1><?php echo Text::_('COM_DECAROCOURSES_INFO_TITLE'); ?></h1>
      <p><?php echo Text::_('COM_DECAROCOURSES_INFO_DESCRIPTION'); ?></p>
    </div>
  </header>

  <div class="dc-information-grid">
    <section class="dc-card dc-information-card">
      <div class="dc-card-head">
        <div>
          <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_PRODUCT'); ?></span>
          <h2><?php echo Text::_('COM_DECAROCOURSES_INFO_VERSIONS'); ?></h2>
        </div>
      </div>
      <dl class="dc-information-list">
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_COMPONENT_VERSION'); ?></dt><dd><?php echo $escape($info['componentVersion'] ?? '—'); ?></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_PACKAGE_VERSION'); ?></dt><dd><?php echo $escape($info['packageVersion'] ?? '—'); ?></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_SCHEMA_VERSION'); ?></dt><dd><?php echo $escape(($info['schemaVersion'] ?? '') !== '' ? $info['schemaVersion'] : Text::_('COM_DECAROCOURSES_INFO_NOT_DETECTED')); ?></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_DEVELOPER'); ?></dt><dd>Luca De Caro</dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_REPOSITORY'); ?></dt><dd><a href="https://github.com/xdecaro/courses" target="_blank" rel="noopener noreferrer">xdecaro/courses</a></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_LICENSE'); ?></dt><dd>GNU GPL v2 or later</dd></div>
      </dl>
    </section>

    <section class="dc-card dc-information-card">
      <div class="dc-card-head">
        <div>
          <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_ENVIRONMENT'); ?></span>
          <h2><?php echo Text::_('COM_DECAROCOURSES_INFO_SYSTEM'); ?></h2>
        </div>
      </div>
      <dl class="dc-information-list">
        <div class="dc-information-row"><dt>Joomla</dt><dd><?php echo $escape($info['joomlaVersion'] ?? '—'); ?></dd></div>
        <div class="dc-information-row"><dt>PHP</dt><dd><?php echo $escape($info['phpVersion'] ?? '—'); ?></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_COURSES_TABLE'); ?></dt><dd><span class="dc-badge <?php echo !empty($diagnostics['coursesTable']) ? 'is-success' : 'is-danger'; ?>"><?php echo !empty($diagnostics['coursesTable']) ? Text::_('COM_DECAROCOURSES_INFO_OK') : Text::_('COM_DECAROCOURSES_INFO_MISSING'); ?></span></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_EDITIONS_TABLE'); ?></dt><dd><span class="dc-badge <?php echo !empty($diagnostics['editionsTable']) ? 'is-success' : 'is-danger'; ?>"><?php echo !empty($diagnostics['editionsTable']) ? Text::_('COM_DECAROCOURSES_INFO_OK') : Text::_('COM_DECAROCOURSES_INFO_MISSING'); ?></span></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_SCHEMA_STATUS'); ?></dt><dd><span class="dc-badge <?php echo !empty($diagnostics['schemaAligned']) ? 'is-success' : 'is-danger'; ?>"><?php echo !empty($diagnostics['schemaAligned']) ? Text::_('COM_DECAROCOURSES_INFO_ALIGNED') : Text::_('COM_DECAROCOURSES_INFO_CHECK'); ?></span></dd></div>
      </dl>
    </section>

    <section class="dc-card dc-information-card">
      <div class="dc-card-head">
        <div>
          <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_INTEGRATIONS'); ?></span>
          <h2>Forms by xdecaro</h2>
        </div>
      </div>
      <dl class="dc-information-list">
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_STATUS'); ?></dt><dd><span class="dc-badge <?php echo !empty($info['formsInstalled']) ? 'is-success' : 'is-muted'; ?>"><?php echo !empty($info['formsInstalled']) ? Text::_('COM_DECAROCOURSES_INFO_INSTALLED') : Text::_('COM_DECAROCOURSES_INFO_NOT_INSTALLED'); ?></span></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_VERSION'); ?></dt><dd><?php echo $escape(($info['formsVersion'] ?? '') !== '' ? $info['formsVersion'] : '—'); ?></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_FORMS_COUNT'); ?></dt><dd><?php echo (int) ($info['formsCount'] ?? 0); ?></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_LIVE_REFRESH'); ?></dt><dd><span class="dc-badge <?php echo !empty($info['formsInstalled']) ? 'is-success' : 'is-muted'; ?>"><?php echo !empty($info['formsInstalled']) ? Text::_('COM_DECAROCOURSES_INFO_AVAILABLE') : Text::_('COM_DECAROCOURSES_INFO_NOT_AVAILABLE'); ?></span></dd></div>
      </dl>
    </section>

    <section class="dc-card dc-information-card">
      <div class="dc-card-head">
        <div>
          <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_UPDATES'); ?></span>
          <h2><?php echo Text::_('COM_DECAROCOURSES_INFO_UPDATE_CHANNEL'); ?></h2>
        </div>
      </div>
      <dl class="dc-information-list">
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_UPDATE_SITE'); ?></dt><dd><span class="dc-badge <?php echo !empty($info['updateSite']['configured']) ? 'is-success' : 'is-danger'; ?>"><?php echo !empty($info['updateSite']['configured']) ? Text::_('COM_DECAROCOURSES_INFO_CONFIGURED') : Text::_('COM_DECAROCOURSES_INFO_NOT_CONFIGURED'); ?></span></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_UPDATE_SITE_STATUS'); ?></dt><dd><span class="dc-badge <?php echo !empty($info['updateSite']['enabled']) ? 'is-success' : 'is-muted'; ?>"><?php echo !empty($info['updateSite']['enabled']) ? Text::_('COM_DECAROCOURSES_INFO_ENABLED') : Text::_('COM_DECAROCOURSES_INFO_DISABLED'); ?></span></dd></div>
        <div class="dc-information-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_AVAILABLE_VERSION'); ?></dt><dd><?php echo $escape(($info['availableVersion'] ?? '') !== '' ? $info['availableVersion'] : Text::_('COM_DECAROCOURSES_INFO_NO_CACHED_UPDATE')); ?></dd></div>
      </dl>
    </section>

    <section class="dc-card dc-information-card dc-field-span-2">
      <div class="dc-card-head">
        <div>
          <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_DIAGNOSTICS'); ?></span>
          <h2><?php echo Text::_('COM_DECAROCOURSES_INFO_HEALTH'); ?></h2>
        </div>
        <span class="dc-badge <?php echo $allCriticalOk ? 'is-success' : 'is-danger'; ?>"><?php echo $allCriticalOk ? Text::_('COM_DECAROCOURSES_INFO_NO_CRITICAL_ISSUES') : Text::_('COM_DECAROCOURSES_INFO_ATTENTION_REQUIRED'); ?></span>
      </div>
      <p><?php echo Text::_('COM_DECAROCOURSES_INFO_DIAGNOSTICS_HELP'); ?></p>
      <div class="dc-information-actions">
        <button class="btn dc-btn dc-btn-secondary" type="button" data-dc-copy-diagnostics><?php echo Text::_('COM_DECAROCOURSES_INFO_COPY_DIAGNOSTICS'); ?></button>
        <span class="dc-information-copy-status" data-dc-copy-status aria-live="polite"></span>
      </div>
    </section>
  </div>
</div>

<script>
(() => {
  'use strict';
  const button = document.querySelector('[data-dc-copy-diagnostics]');
  const status = document.querySelector('[data-dc-copy-status]');
  const payload = <?php echo json_encode($diagnosticPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

  if (!(button instanceof HTMLButtonElement)) {
    return;
  }

  button.addEventListener('click', async () => {
    const text = Object.entries(payload).map(([key, value]) => `${key}: ${value}`).join('\n');

    try {
      await navigator.clipboard.writeText(text);
      if (status instanceof HTMLElement) {
        status.textContent = <?php echo json_encode(Text::_('COM_DECAROCOURSES_INFO_COPIED'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
      }
    } catch (error) {
      if (status instanceof HTMLElement) {
        status.textContent = <?php echo json_encode(Text::_('COM_DECAROCOURSES_INFO_COPY_FAILED'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
      }
    }
  });
})();
</script>
