<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$filterState = (string) $this->state->get('filter.state', '');
$isTrashFilter = $filterState === '-2';
$stats = $this->stats ?: (object) ['total' => 0, 'active' => 0, 'inactive' => 0, 'trashed' => 0];

$listUrl = 'index.php?option=com_decarocourses&view=courses';
$resetUrl = Route::_($listUrl . '&filter_search=&filter_state=');

$stateMeta = [
    1 => ['label' => Text::_('COM_DECAROCOURSES_STATE_ACTIVE'), 'class' => 'is-success'],
    0 => ['label' => Text::_('COM_DECAROCOURSES_STATE_INACTIVE'), 'class' => 'is-muted'],
    -2 => ['label' => Text::_('COM_DECAROCOURSES_STATE_TRASHED'), 'class' => 'is-danger'],
];
?>
<form action="<?php echo Route::_($listUrl); ?>" method="post" name="adminForm" id="adminForm">
<div class="dc-app">
  <header class="dc-page-head">
    <div>
      <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_AREA_OFFICE'); ?></span>
      <h1><?php echo Text::_('COM_DECAROCOURSES_COURSES_TITLE'); ?></h1>
      <p><?php echo Text::_('COM_DECAROCOURSES_COURSES_DESCRIPTION'); ?></p>
    </div>
  </header>

  <nav class="dc-stats" aria-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_STATS_ARIA')); ?>">
    <a class="dc-stat<?php echo $filterState === '' ? ' is-selected' : ''; ?>" href="<?php echo $resetUrl; ?>">
      <span><?php echo Text::_('COM_DECAROCOURSES_STAT_TOTAL_COURSES'); ?></span><strong><?php echo (int) $stats->total; ?></strong>
    </a>
    <a class="dc-stat is-active<?php echo $filterState === '1' ? ' is-selected' : ''; ?>" href="<?php echo Route::_($listUrl . '&filter_search=&filter_state=1'); ?>">
      <span><?php echo Text::_('COM_DECAROCOURSES_STAT_ACTIVE'); ?></span><strong><?php echo (int) $stats->active; ?></strong>
    </a>
    <a class="dc-stat<?php echo $filterState === '0' ? ' is-selected' : ''; ?>" href="<?php echo Route::_($listUrl . '&filter_search=&filter_state=0'); ?>">
      <span><?php echo Text::_('COM_DECAROCOURSES_STAT_INACTIVE'); ?></span><strong><?php echo (int) $stats->inactive; ?></strong>
    </a>
    <a class="dc-stat is-trash<?php echo $isTrashFilter ? ' is-selected' : ''; ?>" href="<?php echo Route::_($listUrl . '&filter_search=&filter_state=-2'); ?>">
      <span><?php echo Text::_('COM_DECAROCOURSES_STAT_TRASH'); ?></span><strong><?php echo (int) $stats->trashed; ?></strong>
    </a>
  </nav>

  <section class="dc-card">
    <div class="dc-toolbar" role="search">
      <input
        class="form-control"
        type="search"
        name="filter_search"
        value="<?php echo $escape($this->state->get('filter.search')); ?>"
        placeholder="<?php echo $escape(Text::_('COM_DECAROCOURSES_FILTER_SEARCH_PLACEHOLDER')); ?>"
        aria-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_FILTER_SEARCH_ARIA')); ?>"
      >
      <select class="form-select" name="filter_state" aria-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_FILTER_STATE_ARIA')); ?>">
        <option value=""<?php echo $filterState === '' ? ' selected' : ''; ?>><?php echo Text::_('COM_DECAROCOURSES_FILTER_ALL_STATES'); ?></option>
        <option value="1"<?php echo $filterState === '1' ? ' selected' : ''; ?>><?php echo Text::_('COM_DECAROCOURSES_STAT_ACTIVE'); ?></option>
        <option value="0"<?php echo $filterState === '0' ? ' selected' : ''; ?>><?php echo Text::_('COM_DECAROCOURSES_STAT_INACTIVE'); ?></option>
        <option value="-2"<?php echo $isTrashFilter ? ' selected' : ''; ?>><?php echo Text::_('COM_DECAROCOURSES_STAT_TRASH'); ?></option>
      </select>
      <button class="btn dc-btn dc-btn-primary" type="submit"><?php echo Text::_('COM_DECAROCOURSES_ACTION_SEARCH'); ?></button>
      <a class="btn dc-btn dc-btn-secondary" href="<?php echo $resetUrl; ?>"><?php echo Text::_('COM_DECAROCOURSES_ACTION_RESET'); ?></a>
    </div>

    <?php if (!$this->items) : ?>
      <div class="dc-empty">
        <strong><?php echo Text::_('COM_DECAROCOURSES_EMPTY_COURSES_TITLE'); ?></strong>
        <span><?php echo Text::_('COM_DECAROCOURSES_EMPTY_COURSES_HELP'); ?></span>
      </div>
    <?php else : ?>
      <div class="dc-table-wrap dc-courses-table-wrap">
        <table class="dc-table dc-responsive-table dc-courses-table">
          <thead>
            <tr>
              <th class="dc-check" scope="col"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
              <th class="dc-col-title" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_TITLE'); ?></th>
              <th class="dc-col-code" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_CODE'); ?></th>
              <th class="dc-col-editions" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_EDITIONS'); ?></th>
              <th class="dc-col-state" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_STATE'); ?></th>
              <th class="dc-col-updated" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_UPDATED'); ?></th>
              <th class="dc-actions-col" scope="col"><?php echo Text::_('COM_DECAROCOURSES_COLUMN_ACTIONS'); ?></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($this->items as $i => $item) :
              $itemState = (int) $item->state;
              $meta = $stateMeta[$itemState] ?? ['label' => Text::_('COM_DECAROCOURSES_STATE_UNKNOWN'), 'class' => 'is-muted'];
              $editUrl = Route::_('index.php?option=com_decarocourses&task=course.edit&id=' . (int) $item->id);
              $editionsUrl = Route::_('index.php?option=com_decarocourses&view=editions&filter_search=&filter_course_id=' . (int) $item->id);
              $modified = !empty($item->modified) ? (string) $item->modified : (string) $item->created;
              $modifiedLabel = $modified !== '' ? HTMLHelper::_('date', $modified, 'd/m/Y H:i') : '—';
          ?>
            <tr>
              <td class="dc-check" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_ROW_SELECT')); ?>"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
              <td class="dc-col-title" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_TITLE')); ?>">
                <?php if ($this->canEdit) : ?>
                  <a class="dc-title-link" href="<?php echo $editUrl; ?>"><?php echo $escape($item->title); ?></a>
                <?php else : ?>
                  <strong><?php echo $escape($item->title); ?></strong>
                <?php endif; ?>
                <small class="dc-row-subtitle">ID <?php echo (int) $item->id; ?></small>
                <small class="dc-tablet-updated"><?php echo $escape(Text::sprintf('COM_DECAROCOURSES_UPDATED_PREFIX', $modifiedLabel)); ?></small>
              </td>
              <td class="dc-col-code" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_CODE')); ?>">
                <?php if ((string) $item->code !== '') : ?>
                  <span class="dc-code"><?php echo $escape($item->code); ?></span>
                <?php else : ?>—<?php endif; ?>
              </td>
              <td class="dc-col-editions" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_EDITIONS')); ?>">
                <a class="dc-count-link" href="<?php echo $editionsUrl; ?>" aria-label="<?php echo $escape(Text::sprintf('COM_DECAROCOURSES_OPEN_EDITIONS_ARIA', $item->title)); ?>">
                  <?php echo (int) $item->editions_count; ?>
                </a>
              </td>
              <td class="dc-col-state" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_STATE')); ?>"><span class="dc-badge <?php echo $meta['class']; ?>"><?php echo $escape($meta['label']); ?></span></td>
              <td class="dc-meta dc-col-updated" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_UPDATED')); ?>"><?php echo $escape($modifiedLabel); ?></td>
              <td class="dc-col-actions" data-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_COLUMN_ACTIONS')); ?>">
                <div class="dc-row-actions">
                  <?php if ($this->canEdit) : ?>
                    <a class="btn dc-btn dc-btn-primary" href="<?php echo $editUrl; ?>"><?php echo Text::_('COM_DECAROCOURSES_ACTION_EDIT'); ?></a>
                  <?php endif; ?>
                  <a class="btn dc-btn dc-btn-secondary" href="<?php echo $editionsUrl; ?>"><?php echo Text::_('COM_DECAROCOURSES_ACTION_EDITIONS'); ?></a>
                </div>
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
<input type="hidden" name="task" value="">
<input type="hidden" name="boxchecked" value="0">
<?php echo HTMLHelper::_('form.token'); ?>
</form>
