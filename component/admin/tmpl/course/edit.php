<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$isNew = empty($this->item->id);
$coursesUrl = Route::_('index.php?option=com_decarocourses&view=courses');
$editionsUrl = Route::_('index.php?option=com_decarocourses&view=editions&filter_search=&filter_course_id=' . (int) $this->item->id);
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="dc-app">
  <header class="dc-page-head">
    <div>
      <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_COURSE_EYEBROW'); ?></span>
      <h1><?php echo $isNew ? Text::_('COM_DECAROCOURSES_COURSE_NEW') : $escape($this->item->title); ?></h1>
      <p><?php echo Text::_('COM_DECAROCOURSES_COURSE_FORM_DESCRIPTION'); ?></p>
    </div>
    <div class="dc-page-actions">
      <a class="btn dc-btn dc-btn-secondary" href="<?php echo $coursesUrl; ?>"><?php echo Text::_('COM_DECAROCOURSES_BACK_TO_COURSES'); ?></a>
      <?php if (!$isNew) : ?>
        <a class="btn dc-btn dc-btn-primary" href="<?php echo $editionsUrl; ?>"><?php echo Text::_('COM_DECAROCOURSES_OPEN_EDITIONS'); ?></a>
      <?php endif; ?>
    </div>
  </header>

  <div class="dc-editor-layout">
    <main class="dc-editor-main">
      <section class="dc-card dc-form-section">
        <div class="dc-section-head">
          <div><span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_SECTION_MAIN_DATA'); ?></span><h2><?php echo Text::_('COM_DECAROCOURSES_COURSE_IDENTITY'); ?></h2></div>
          <p><?php echo Text::_('COM_DECAROCOURSES_COURSE_IDENTITY_HELP'); ?></p>
        </div>
        <div class="dc-form-grid dc-form-grid-2">
          <div class="dc-field-span-2"><?php echo $this->form->renderField('title'); ?></div>
          <div><?php echo $this->form->renderField('code'); ?></div>
          <div><?php echo $this->form->renderField('alias'); ?></div>
        </div>
      </section>

      <section class="dc-card dc-form-section">
        <div class="dc-section-head">
          <div><span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_SECTION_DESCRIPTION'); ?></span><h2><?php echo Text::_('COM_DECAROCOURSES_GENERAL_INFORMATION'); ?></h2></div>
        </div>
        <?php echo $this->form->renderField('description'); ?>
      </section>
    </main>

    <aside class="dc-editor-side">
      <section class="dc-card dc-form-section">
        <div class="dc-section-head"><div><span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_SECTION_VISIBILITY'); ?></span><h2><?php echo Text::_('COM_DECAROCOURSES_PUBLICATION'); ?></h2></div></div>
        <?php echo $this->form->renderField('state'); ?>
        <?php echo $this->form->renderField('ordering'); ?>
      </section>

      <section class="dc-help-box">
        <strong><?php echo Text::_('COM_DECAROCOURSES_HOW_IT_WORKS'); ?></strong>
        <p><?php echo Text::_('COM_DECAROCOURSES_COURSE_HELP_TEXT'); ?></p>
      </section>
    </aside>
  </div>

  <div class="dc-form-actions" aria-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COURSE_ACTIONS_ARIA')); ?>">
    <?php if ($this->canSave) : ?>
      <button class="btn dc-btn dc-btn-primary" type="submit" onclick="document.getElementById('dc-task').value='course.apply'"><?php echo Text::_('COM_DECAROCOURSES_ACTION_SAVE'); ?></button>
      <button class="btn dc-btn dc-btn-secondary" type="submit" onclick="document.getElementById('dc-task').value='course.save'"><?php echo Text::_('COM_DECAROCOURSES_ACTION_SAVE_CLOSE'); ?></button>
    <?php endif; ?>
    <button class="btn dc-btn dc-btn-neutral" type="submit" formnovalidate onclick="document.getElementById('dc-task').value='course.cancel'"><?php echo Text::_('COM_DECAROCOURSES_ACTION_CANCEL'); ?></button>
  </div>
</div>
<?php echo $this->form->getInput('id'); ?>
<input type="hidden" name="task" id="dc-task" value="course.apply">
<?php echo HTMLHelper::_('form.token'); ?>
</form>
