<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$courseId = (int) $this->state->get('filter.course_id', 0);
$statusFilter = (string) $this->state->get('filter.status', '');
$courseLabel = trim((string) $this->selectedCourseTitle);
$resetUrl = Route::_(
    'index.php?option=com_decarocourses&view=editions&filter_search=&filter_status=&filter_course_id=' . $courseId
);
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&view=editions'); ?>" method="post" name="adminForm" id="adminForm">
<div class="dc-app">
  <header class="dc-page-head">
    <div>
      <span class="dc-eyebrow">AREA SEGRETERIA</span>
      <h1>Edizioni dei corsi</h1>
      <p>Gestisci anno accademico, periodo, capienza, stato e modulo di iscrizione associato.</p>
    </div>
    <div class="dc-page-actions">
      <a class="btn dc-btn dc-btn-secondary" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses'); ?>">← Corsi</a>
      <?php if ($this->canCreate) : ?>
        <a class="btn dc-btn dc-btn-primary" href="<?php echo Route::_('index.php?option=com_decarocourses&task=edition.add'); ?>">+ Nuova edizione</a>
      <?php endif; ?>
    </div>
  </header>

  <?php if ($courseId > 0) : ?>
    <div class="dc-filter-notice">
      <span>
        Stai visualizzando le edizioni di
        <strong>“<?php echo $escape($courseLabel !== '' ? $courseLabel : 'Corso #' . $courseId); ?>”</strong>.
      </span>
      <a href="<?php echo Route::_('index.php?option=com_decarocourses&view=editions&filter_search=&filter_status=&filter_course_id=0'); ?>">Mostra tutte</a>
    </div>
  <?php endif; ?>

  <section class="dc-card">
    <div class="dc-toolbar" role="search">
      <input
        class="form-control"
        type="search"
        name="filter_search"
        value="<?php echo $escape($this->state->get('filter.search')); ?>"
        placeholder="Cerca corso, edizione o anno…"
        aria-label="Cerca edizioni"
      >
      <select class="form-select" name="filter_status" aria-label="Filtra edizioni per stato operativo">
        <option value=""<?php echo $statusFilter === '' ? ' selected' : ''; ?>>Tutti gli stati</option>
        <option value="draft"<?php echo $statusFilter === 'draft' ? ' selected' : ''; ?>>Bozza</option>
        <option value="registrations_open"<?php echo $statusFilter === 'registrations_open' ? ' selected' : ''; ?>>Iscrizioni aperte</option>
        <option value="scheduled"<?php echo $statusFilter === 'scheduled' ? ' selected' : ''; ?>>Programmato</option>
        <option value="active"<?php echo $statusFilter === 'active' ? ' selected' : ''; ?>>In corso</option>
        <option value="completed"<?php echo $statusFilter === 'completed' ? ' selected' : ''; ?>>Concluso</option>
        <option value="archived"<?php echo $statusFilter === 'archived' ? ' selected' : ''; ?>>Archiviato</option>
      </select>
      <button class="btn dc-btn dc-btn-primary" type="submit">Cerca</button>
      <a class="btn dc-btn dc-btn-secondary" href="<?php echo $resetUrl; ?>">Azzera</a>
    </div>

    <?php if (!$this->items) : ?>
      <div class="dc-empty"><strong>Nessuna edizione trovata.</strong><span>Crea una nuova edizione oppure modifica i filtri.</span></div>
    <?php else : ?>
      <?php if ($this->canEditState) : ?>
        <div class="dc-bulk-actions" aria-label="Azioni sulle edizioni selezionate">
          <span class="dc-bulk-label">Selezionati</span>
          <button class="btn dc-btn dc-btn-success" type="button" data-dc-bulk-action disabled aria-disabled="true" onclick="Joomla.submitbutton('editions.publish')">Pubblica</button>
          <button class="btn dc-btn dc-btn-neutral" type="button" data-dc-bulk-action disabled aria-disabled="true" onclick="Joomla.submitbutton('editions.unpublish')">Sospendi</button>
          <button class="btn dc-btn dc-btn-danger" type="button" data-dc-bulk-action disabled aria-disabled="true" onclick="Joomla.submitbutton('editions.trash')">Cestino</button>
        </div>
      <?php endif; ?>

      <div class="dc-table-wrap">
        <table class="dc-table dc-responsive-table">
          <thead>
            <tr>
              <th class="dc-check"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
              <th>Corso</th>
              <th>Edizione</th>
              <th>Anno</th>
              <th>Stato</th>
              <th>Forms</th>
              <th class="dc-actions-col">Azioni</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($this->items as $i => $item) :
              $editUrl = Route::_('index.php?option=com_decarocourses&task=edition.edit&id=' . (int) $item->id);
          ?>
            <tr>
              <td class="dc-check" data-label="Seleziona"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
              <td data-label="Corso"><?php echo $escape($item->course_title); ?></td>
              <td data-label="Edizione">
                <?php if ($this->canEdit) : ?>
                  <a class="dc-title-link" href="<?php echo $editUrl; ?>"><?php echo $escape($item->title); ?></a>
                <?php else : ?>
                  <strong><?php echo $escape($item->title); ?></strong>
                <?php endif; ?>
                <small class="dc-row-subtitle">ID <?php echo (int) $item->id; ?></small>
              </td>
              <td data-label="Anno"><?php echo $escape($item->academic_year); ?></td>
              <td data-label="Stato"><span class="dc-status <?php echo UiHelper::statusClass((string) $item->status); ?>"><?php echo UiHelper::statusLabel((string) $item->status); ?></span></td>
              <td data-label="Forms"><?php echo (int) $item->forms_form_id > 0 ? '<span class="dc-badge is-info">#' . (int) $item->forms_form_id . '</span>' : '—'; ?></td>
              <td data-label="Azioni">
                <?php if ($this->canEdit) : ?>
                  <div class="dc-row-actions"><a class="btn dc-btn dc-btn-primary" href="<?php echo $editUrl; ?>">Modifica</a></div>
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
