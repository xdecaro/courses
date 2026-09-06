<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$courseId = (int) $this->state->get('filter.course_id', 0);
$statusFilter = (string) $this->state->get('filter.status', '');
$courseLabel = trim((string) $this->selectedCourseTitle);
$resetUrl = Route::_('index.php?option=com_decarocourses&view=editions&filter_search=&filter_status=&filter_course_id=' . $courseId);
$newEditionUrl = Route::_('index.php?option=com_decarocourses&task=edition.add' . ($courseId > 0 ? '&course_id=' . $courseId : ''));
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&view=editions'); ?>" method="post" name="adminForm" id="adminForm">
<div class="dc-app">
  <header class="dc-page-head">
    <div>
      <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_AREA_OFFICE'); ?></span>
      <h1><?php echo Text::_('COM_DECAROCOURSES_EDITIONS_TITLE'); ?></h1>
      <p><?php echo Text::_('COM_DECAROCOURSES_EDITIONS_DESCRIPTION'); ?></p>
    </div>
    <div class="dc-page-actions">
      <a class="btn dc-btn dc-btn-secondary" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses'); ?>"><?php echo Text::_('COM_DECAROCOURSES_BACK_TO_COURSES_SHORT'); ?></a>
      <?php if ($this->canCreate) : ?>
        <a class="btn dc-btn dc-btn-primary" href="<?php echo $newEditionUrl; ?>"><?php echo Text::_('COM_DECAROCOURSES_ACTION_NEW_EDITION'); ?></a>
      <?php endif; ?>
    </div>
  </header>

  <?php if ($courseId > 0) : ?>
    <div class="dc-filter-notice">
      <span><?php echo Text::sprintf('COM_DECAROCOURSES_EDITIONS_FILTERED_BY_COURSE', $escape($courseLabel !== '' ? $courseLabel : Text::sprintf('COM_DECAROCOURSES_COURSE_NUMBER', $courseId))); ?></span>
      <a href="<?php echo Route::_('index.php?option=com_decarocourses&view=editions&filter_search=&filter_status=&filter_course_id=0'); ?>"><?php echo Text::_('COM_DECAROCOURSES_ACTION_SHOW_ALL'); ?></a>
    </div>
  <?php endif; ?>

  <section class="dc-card">
    <div class="dc-toolbar" role="search">
      <input
        class="form-control"
        type="search"
        name="filter_search"
        value="<?php echo $escape($this->state->get('filter.search')); ?>"
        placeholder="<?php echo $escape(Text::_('COM_DECAROCOURSES_EDITIONS_SEARCH_PLACEHOLDER')); ?>"
        aria-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_EDITIONS_SEARCH_ARIA')); ?>"
      >
      <select class="form-select" name="filter_status" aria-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_EDITIONS_STATUS_FILTER_ARIA')); ?>">
        <option value=""<?php echo $statusFilter === '' ? ' selected' : ''; ?>><?php echo Text::_('COM_DECAROCOURSES_FILTER_ALL_STATES'); ?></option>
        <option value="draft"<?php echo $statusFilter === 'draft' ? ' selected' : ''; ?>><?php echo Text::_('COM_DECAROCOURSES_STATUS_DRAFT'); ?></option>
        <option value="registrations_open"<?php echo $statusFilter === 'registrations_open' ? ' selected' : ''; ?>><?php echo Text::_('COM_DECAROCOURSES_STATUS_REGISTRATIONS_OPEN'); ?></option>
        <option value="scheduled"<?php echo $statusFilter === 'scheduled' ? ' selected' : ''; ?>><?php echo Text::_('COM_DECAROCOURSES_STATUS_SCHEDULED'); ?></option>
        <option value="active"<?php echo $statusFilter === 'active' ? ' selected' : ''; ?>><?php echo Text::_('COM_DECAROCOURSES_STATUS_ACTIVE'); ?></option>
        <option value="completed"<?php echo $statusFilter === 'completed' ? ' selected' : ''; ?>><?php echo Text::_('COM_DECAROCOURSES_STATUS_COMPLETED'); ?></option>
        <option value="archived"<?php echo $statusFilter === 'archived' ? ' selected' : ''; ?>><?php echo Text::_('COM_DECAROCOURSES_STATUS_ARCHIVED'); ?></option>
      </select>
      <button class="btn dc-btn dc-btn-primary" type="submit"><?php echo Text::_('COM_DECAROCOURSES_ACTION_SEARCH'); ?></button>
      <a class="btn dc-btn dc-btn-secondary" href="<?php echo $resetUrl; ?>"><?php echo Text::_('COM_DECAROCOURSES_ACTION_RESET'); ?></a>
    </div>

    <?php if (!$this->items) : ?>
      <div class="dc-empty">
        <strong><?php echo Text::_($courseId > 0 ? 'COM_DECAROCOURSES_EMPTY_EDITIONS_COURSE_TITLE' : 'COM_DECAROCOURSES_EMPTY_EDITIONS_TITLE'); ?></strong>
        <span><?php echo Text::_($courseId > 0 ? 'COM_DECAROCOURSES_EMPTY_EDITIONS_COURSE_HELP' : 'COM_DECAROCOURSES_EMPTY_EDITIONS_HELP'); ?></span>
      </div>
    <?php else : ?>
      <?php if ($this->canEditState) : ?>
        <div class="dc-bulk-actions" aria-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_EDITIONS_BULK_ARIA')); ?>">
          <span class="dc-bulk-label"><?php echo Text::_('COM_DECAROCOURSES_BULK_SELECTED'); ?></span>
          <button class="btn dc-btn dc-btn-success" type="button" data-dc-bulk-action disabled aria-disabled="true" onclick="Joomla.submitbutton('editions.publish')"><?php echo Text::_('COM_DECAROCOURSES_ACTION_PUBLISH'); ?></button>
          <button class="btn dc-btn dc-btn-neutral" type="button" data-dc-bulk-action disabled aria-disabled="true" onclick="Joomla.submitbutton('editions.unpublish')"><?php echo Text::_('COM_DECAROCOURSES_ACTION_SUSPEND'); ?></button>
          <button class="btn dc-btn dc-btn-danger" type="button" data-dc-bulk-action disabled aria-disabled="true" onclick="Joomla.submitbutton('editions.trash')"><?php echo Text::_('COM_DECAROCOURSES_ACTION_TRASH'); ?></button>
        </div>
      <?php endif; ?>

      <div class="dc-table-wrap dc-editions-table-wrap">
        <table class="dc-table dc-responsive-table dc-editions-table">
          <thead>
            <tr>
              <th class="dc-check" scope="col"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
              <th class="dc-col-edition-course" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_COURSE'); ?></th>
              <th class="dc-col-period" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_PERIOD'); ?></th>
              <th class="dc-col-format" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_FORMAT'); ?></th>
              <th class="dc-col-edition-status" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_STATE'); ?></th>
              <th class="dc-col-forms" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_FORMS'); ?></th>
              <th class="dc-actions-col" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_ACTIONS'); ?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($this->items as $i => $item) :
              $editUrl = Route::_('index.php?option=com_decarocourses&task=edition.edit&id=' . (int) $item->id);
              $formsLabel = (int) $item->forms_form_id > 0 ? '#' . (int) $item->forms_form_id : '—';
          ?>
            <tr>
              <td class="dc-check" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_ROW_SELECT')); ?>"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
              <td class="dc-col-edition-course" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_COURSE')); ?>">
                <strong class="dc-edition-course-title"><?php echo $escape($item->course_title); ?></strong>
                <small class="dc-row-subtitle">ID <?php echo (int) $item->id; ?></small>
                <small class="dc-edition-tablet-forms"><?php echo Text::sprintf('COM_DECAROCOURSES_FORMS_PREFIX', $escape($formsLabel)); ?></small>
              </td>
              <td class="dc-col-period" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_PERIOD')); ?>"><strong><?php echo $escape($item->academic_year); ?></strong></td>
              <td class="dc-col-format" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_FORMAT')); ?>"><?php echo $escape(UiHelper::formatLabel((string) $item->format, (string) $item->format_custom)); ?></td>
              <td class="dc-col-edition-status" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_STATE')); ?>"><span class="dc-status <?php echo UiHelper::statusClass((string) $item->status); ?>"><?php echo $escape(UiHelper::statusLabel((string) $item->status)); ?></span></td>
              <td class="dc-col-forms" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_FORMS')); ?>"><?php echo (int) $item->forms_form_id > 0 ? '<span class="dc-badge is-info">' . $escape($formsLabel) . '</span>' : '—'; ?></td>
              <td class="dc-col-actions" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_ACTIONS')); ?>">
                <?php if ($this->canEdit) : ?>
                  <div class="dc-row-actions"><a class="btn dc-btn dc-btn-primary" href="<?php echo $editUrl; ?>"><?php echo Text::_('COM_DECAROCOURSES_ACTION_EDIT'); ?></a></div>
                <?php else : ?>—<?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="dc-pagination"><?php echo $this->pagination->getListFooter(); ?></div>
    <?php endif; ?>
  </section>
</div>
<input type="hidden" name="filter_course_id" value="<?php echo $courseId; ?>">
<input type="hidden" name="task" value="">
<input type="hidden" name="boxchecked" value="0">
<?php echo HTMLHelper::_('form.token'); ?>
</form>
