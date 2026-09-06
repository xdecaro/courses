<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$isNew = empty($this->item->id);
$courseId = (int) ($this->form->getValue('course_id') ?: 0);
$backUrl = Route::_('index.php?option=com_decarocourses&view=editions&filter_search=&filter_status=&filter_course_id=' . $courseId);
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="dc-app">
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
          <div><?php echo $this->form->renderField('academic_year'); ?></div>
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
            <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_SECTION_NOTES'); ?></span>
            <h2><?php echo Text::_('COM_DECAROCOURSES_FIELD_NOTES'); ?></h2>
          </div>
        </div>
        <?php echo $this->form->renderField('notes'); ?>
      </section>
    </main>

    <aside class="dc-editor-side">
      <section class="dc-card dc-form-section">
        <div class="dc-section-head">
          <div>
            <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_SECTION_EDITION_STATUS'); ?></span>
            <h2><?php echo Text::_('COM_DECAROCOURSES_FIELD_STATUS'); ?></h2>
          </div>
        </div>
        <?php echo $this->form->renderField('status'); ?>
      </section>

      <section class="dc-card dc-form-section">
        <div class="dc-section-head">
          <div>
            <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_SECTION_VISIBILITY'); ?></span>
            <h2><?php echo Text::_('COM_DECAROCOURSES_PUBLICATION'); ?></h2>
          </div>
        </div>
        <?php echo $this->form->renderField('state'); ?>
      </section>

      <section class="dc-help-box">
        <strong><?php echo Text::_('COM_DECAROCOURSES_EDITION_AUTO_TITLE_TITLE'); ?></strong>
        <p><?php echo Text::_('COM_DECAROCOURSES_EDITION_AUTO_TITLE_HELP'); ?></p>
      </section>
    </aside>
  </div>

  <div class="dc-form-actions" aria-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_EDITION_ACTIONS_ARIA')); ?>">
    <?php if ($this->canSave) : ?>
      <button class="btn dc-btn dc-btn-primary" type="submit" onclick="document.getElementById('dc-task').value='edition.apply'\"><?php echo Text::_('COM_DECAROCOURSES_ACTION_SAVE'); ?></button>
      <button class="btn dc-btn dc-btn-secondary" type="submit" onclick="document.getElementById('dc-task').value='edition.save'\"><?php echo Text::_('COM_DECAROCOURSES_ACTION_SAVE_CLOSE'); ?></button>
    <?php endif; ?>
    <button class="btn dc-btn dc-btn-neutral" type="submit" formnovalidate onclick="document.getElementById('dc-task').value='edition.cancel'\"><?php echo Text::_('COM_DECAROCOURSES_ACTION_CANCEL'); ?></button>
  </div>
</div>
<?php echo $this->form->getInput('id'); ?>
<input type="hidden" name="task" id="dc-task" value="edition.apply">
<?php echo HTMLHelper::_('form.token'); ?>
</form>
