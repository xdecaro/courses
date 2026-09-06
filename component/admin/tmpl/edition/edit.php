<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$isNew = empty($this->item->id);

$periodValue = trim((string) $this->form->getValue('academic_year'));
$periodType = preg_match('/^\d{4}\/\d{4}$/', $periodValue) === 1 ? 'academic' : 'single';
$currentYear = (int) Factory::getDate()->format('Y');
$selectedYear = $currentYear;

if (preg_match('/^(\d{4})/', $periodValue, $periodMatches) === 1) {
    $candidateYear = (int) $periodMatches[1];

    if ($candidateYear >= 1900 && $candidateYear <= 2200) {
        $selectedYear = $candidateYear;
    }
}

$periodYears = [$selectedYear];

$renderLiveSelect = function (string $fieldName, string $source) use ($escape): void {
    if (!$this->canSave) {
        echo $this->form->renderField($fieldName);
        return;
    }

    $field = $this->form->getField($fieldName);

    if (!$field) {
        return;
    }

    $description = trim((string) ($field->description ?? ''));
    ?>
    <div class="control-group dc-live-field" data-dc-live-refresh data-dc-source="<?php echo $escape($source); ?>">
      <div class="control-label"><?php echo $this->form->getLabel($fieldName); ?></div>
      <div class="controls">
        <div class="dc-live-select-row">
          <?php echo $this->form->getInput($fieldName); ?>
          <button
            class="btn dc-btn dc-btn-secondary dc-live-refresh-button"
            type="button"
            data-dc-live-refresh-button
            aria-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_ACTION_REFRESH')); ?>"
            title="<?php echo $escape(Text::_('COM_DECAROCOURSES_ACTION_REFRESH')); ?>"
          >
            <span class="icon-refresh" aria-hidden="true"></span>
            <span><?php echo Text::_('COM_DECAROCOURSES_ACTION_REFRESH'); ?></span>
          </button>
        </div>
        <?php if ($description !== '') : ?>
          <div class="form-text"><?php echo Text::_($description); ?></div>
        <?php endif; ?>
        <div class="dc-live-status" data-dc-live-status aria-live="polite" hidden></div>
      </div>
    </div>
    <?php
};
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="dc-app dc-edition-page">
  <header class="dc-page-head">
    <div>
      <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_EDITION_EYEBROW'); ?></span>
      <h1><?php echo $isNew ? Text::_('COM_DECAROCOURSES_EDITION_NEW') : $escape($this->item->title); ?></h1>
      <p><?php echo Text::_('COM_DECAROCOURSES_EDITION_FORM_DESCRIPTION'); ?></p>
    </div>
  </header>

  <div class="dc-editor-layout dc-edition-editor">
    <main class="dc-editor-main">
      <section class="dc-card dc-form-section">
        <div class="dc-section-head">
          <div>
            <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_SECTION_EDITION_DATA'); ?></span>
            <h2><?php echo Text::_('COM_DECAROCOURSES_EDITION_CONFIGURATION'); ?></h2>
          </div>
          <p><?php echo Text::_('COM_DECAROCOURSES_EDITION_CONFIGURATION_HELP'); ?></p>
        </div>
        <div class="dc-form-grid dc-form-grid-2">
          <div class="dc-field-span-2"><?php $renderLiveSelect('course_id', 'courses'); ?></div>

          <div class="dc-edition-config-columns dc-field-span-2">
            <div class="dc-period-builder" data-dc-period-builder>
              <fieldset class="dc-period-type">
                <legend><?php echo Text::_('COM_DECAROCOURSES_FIELD_PERIOD_TYPE'); ?> <span class="star" aria-hidden="true">*</span></legend>
                <div class="dc-period-type-options">
                  <label class="dc-choice-pill">
                    <input type="radio" name="dc_period_type" value="single"<?php echo $periodType === 'single' ? ' checked' : ''; ?>>
                    <span><?php echo Text::_('COM_DECAROCOURSES_PERIOD_TYPE_SINGLE'); ?></span>
                  </label>
                  <label class="dc-choice-pill">
                    <input type="radio" name="dc_period_type" value="academic"<?php echo $periodType === 'academic' ? ' checked' : ''; ?>>
                    <span><?php echo Text::_('COM_DECAROCOURSES_PERIOD_TYPE_ACADEMIC'); ?></span>
                  </label>
                </div>
              </fieldset>

              <div class="dc-period-year-group">
                <label for="dc-period-year"><?php echo Text::_('COM_DECAROCOURSES_FIELD_PERIOD_YEAR'); ?> <span class="star" aria-hidden="true">*</span></label>
                <div class="dc-period-year-row">
                  <select id="dc-period-year" class="form-select" data-dc-period-year required>
                    <?php foreach ($periodYears as $year) :
                        $label = $periodType === 'academic' ? $year . '/' . ($year + 1) : (string) $year;
                    ?>
                      <option value="<?php echo (int) $year; ?>"<?php echo $year === $selectedYear ? ' selected' : ''; ?>><?php echo $escape($label); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn dc-btn dc-btn-secondary dc-period-new-toggle" type="button" data-dc-period-new-toggle>
                    <?php echo Text::_('COM_DECAROCOURSES_ACTION_NEW_YEAR'); ?>
                  </button>
                </div>
              </div>

              <?php echo $this->form->getInput('academic_year'); ?>
            </div>

            <div class="dc-edition-format-column">
              <?php echo $this->form->renderField('format'); ?>
              <div class="dc-format-custom-wrap" data-dc-format-custom><?php echo $this->form->renderField('format_custom'); ?></div>
            </div>
          </div>
        </div>
      </section>

      <section class="dc-card dc-form-section">
        <div class="dc-section-head">
          <div>
            <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_SECTION_CALENDAR'); ?></span>
            <h2><?php echo Text::_('COM_DECAROCOURSES_EDITION_DATES'); ?></h2>
          </div>
        </div>
        <div class="dc-form-grid dc-form-grid-2">
          <div><?php echo $this->form->renderField('start_date'); ?></div>
          <div><?php echo $this->form->renderField('end_date'); ?></div>
          <div><?php echo $this->form->renderField('registration_start'); ?></div>
          <div><?php echo $this->form->renderField('registration_end'); ?></div>
        </div>
      </section>

      <section class="dc-card dc-form-section">
        <div class="dc-section-head">
          <div>
            <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_SECTION_REGISTRATIONS'); ?></span>
            <h2><?php echo Text::_('COM_DECAROCOURSES_EDITION_REGISTRATIONS'); ?></h2>
          </div>
        </div>
        <div class="dc-form-grid dc-form-grid-2">
          <div><?php echo $this->form->renderField('capacity'); ?></div>
          <div><?php $renderLiveSelect('forms_form_id', 'forms'); ?></div>
        </div>
      </section>

      <section class="dc-card dc-form-section">
        <div class="dc-section-head">
          <div>
            <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_SECTION_DETAILS'); ?></span>
            <h2><?php echo Text::_('COM_DECAROCOURSES_ADDITIONAL_INFORMATION'); ?></h2>
          </div>
        </div>
        <?php echo $this->form->renderField('notes'); ?>
      </section>
    </main>

    <aside class="dc-editor-side dc-edition-sticky-side">
      <section class="dc-card dc-form-section dc-edition-status-card">
        <div class="dc-section-head">
          <div>
            <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_SECTION_EDITION_STATUS'); ?></span>
            <h2><?php echo Text::_('COM_DECAROCOURSES_STATUS_MANAGEMENT'); ?></h2>
          </div>
        </div>
        <div class="dc-edition-status-fields">
          <?php echo $this->form->renderField('status'); ?>
          <?php echo $this->form->renderField('state'); ?>
        </div>
      </section>

      <section class="dc-help-box">
        <strong><?php echo Text::_('COM_DECAROCOURSES_EDITION_AUTO_TITLE_TITLE'); ?></strong>
        <p><?php echo Text::_('COM_DECAROCOURSES_EDITION_AUTO_TITLE_HELP'); ?></p>
      </section>
    </aside>
  </div>

  <dialog class="dc-period-modal" data-dc-period-modal aria-labelledby="dc-period-modal-title">
    <div class="dc-period-modal-card">
      <div class="dc-period-modal-head">
        <div>
          <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_FIELD_PERIOD'); ?></span>
          <h2 id="dc-period-modal-title"><?php echo Text::_('COM_DECAROCOURSES_FIELD_NEW_YEAR'); ?></h2>
        </div>
        <button class="dc-period-modal-close" type="button" data-dc-period-modal-close aria-label="<?php echo $escape(Text::_('JCLOSE')); ?>">×</button>
      </div>
      <div class="dc-period-modal-body">
        <label for="dc-period-new-year"><?php echo Text::_('COM_DECAROCOURSES_FIELD_PERIOD_YEAR'); ?></label>
        <input id="dc-period-new-year" class="form-control" type="number" min="1900" max="2200" step="1" inputmode="numeric" placeholder="<?php echo $currentYear + 1; ?>" data-dc-period-new-year>
        <p class="dc-period-modal-preview" aria-live="polite">
          <span><?php echo Text::_('COM_DECAROCOURSES_FIELD_PERIOD'); ?>:</span>
          <strong data-dc-period-preview></strong>
        </p>
      </div>
      <div class="dc-period-modal-actions">
        <button class="btn dc-btn dc-btn-neutral" type="button" data-dc-period-new-cancel><?php echo Text::_('COM_DECAROCOURSES_ACTION_CANCEL'); ?></button>
        <button class="btn dc-btn dc-btn-primary" type="button" data-dc-period-add><?php echo Text::_('COM_DECAROCOURSES_ACTION_ADD'); ?></button>
      </div>
    </div>
  </dialog>
</div>
<?php echo $this->form->getInput('id'); ?>
<input type="hidden" name="task" value="">
<?php echo HTMLHelper::_('form.token'); ?>
</form>
