<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$isNew = empty($this->item->id);
$courseId = (int) ($this->form->getValue('course_id') ?: 0);
$backUrl = Route::_('index.php?option=com_decarocourses&view=editions&filter_search=&filter_status=&filter_course_id=' . $courseId);

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

// Keep the selector intentionally compact: for a new edition Joomla's current
// year is the only initial option; when editing, preserve the saved year.
$periodYears = [$selectedYear];
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="dc-app dc-edition-page">
  <header class="dc-page-head">
    <div>
      <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_EDITION_EYEBROW'); ?></span>
      <h1><?php echo $isNew ? Text::_('COM_DECAROCOURSES_EDITION_NEW') : $escape($this->item->title); ?></h1>
      <p><?php echo Text::_('COM_DECAROCOURSES_EDITION_FORM_DESCRIPTION'); ?></p>
    </div>
    <div class="dc-page-actions">
      <a class="btn dc-btn dc-btn-secondary" href="<?php echo $backUrl; ?>"><?php echo Text::_('COM_DECAROCOURSES_BACK_TO_EDITIONS'); ?></a>
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
          <div class="dc-field-span-2"><?php echo $this->form->renderField('course_id'); ?></div>

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

          <div><?php echo $this->form->renderField('format'); ?></div>
          <div class="dc-field-span-2 dc-format-custom-wrap" data-dc-format-custom><?php echo $this->form->renderField('format_custom'); ?></div>
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
          <div><?php echo $this->form->renderField('forms_form_id'); ?></div>
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

  <div class="dc-form-actions dc-edition-form-actions" data-dc-edition-actions aria-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_EDITION_ACTIONS_ARIA')); ?>">
    <?php if ($this->canSave) : ?>
      <button class="btn dc-btn dc-btn-primary" type="submit" onclick="document.getElementById('dc-task').value='edition.apply'"><?php echo Text::_('COM_DECAROCOURSES_ACTION_SAVE'); ?></button>
      <button class="btn dc-btn dc-btn-secondary" type="submit" onclick="document.getElementById('dc-task').value='edition.save'"><?php echo Text::_('COM_DECAROCOURSES_ACTION_SAVE_CLOSE'); ?></button>
    <?php endif; ?>
    <button class="btn dc-btn dc-btn-neutral" type="submit" formnovalidate onclick="document.getElementById('dc-task').value='edition.cancel'"><?php echo Text::_('COM_DECAROCOURSES_ACTION_CANCEL'); ?></button>
  </div>
</div>
<?php echo $this->form->getInput('id'); ?>
<input type="hidden" name="task" id="dc-task" value="edition.apply">
<?php echo HTMLHelper::_('form.token'); ?>
</form>
