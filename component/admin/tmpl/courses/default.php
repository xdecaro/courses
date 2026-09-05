<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$filterState = (string) $this->state->get('filter.state', '');
$stats = $this->stats ?: (object) ['total' => 0, 'active' => 0, 'inactive' => 0, 'trashed' => 0];

$stateMeta = [
    1 => ['label' => 'Attivo', 'class' => 'is-success'],
    0 => ['label' => 'Disattivato', 'class' => 'is-muted'],
    -2 => ['label' => 'Cestinato', 'class' => 'is-danger'],
];
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&view=courses'); ?>" method="post" name="adminForm" id="adminForm">
<div class="dc-app">
  <header class="dc-page-head">
    <div>
      <span class="dc-eyebrow">AREA SEGRETERIA</span>
      <h1>Gestione corsi</h1>
      <p>Catalogo generale dei corsi. Ogni corso può avere più edizioni, annualità o sessioni senza duplicare i dati di base.</p>
    </div>
    <?php if ($this->canCreate) : ?>
      <div class="dc-page-actions">
        <a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_decarocourses&task=course.add'); ?>">+ Nuovo corso</a>
      </div>
    <?php endif; ?>
  </header>

  <nav class="dc-stats" aria-label="Filtri rapidi corsi">
    <a class="dc-stat<?php echo $filterState === '' ? ' is-selected' : ''; ?>" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses&filter_search=&filter_state='); ?>">
      <span>Totale corsi</span><strong><?php echo (int) $stats->total; ?></strong>
    </a>
    <a class="dc-stat is-active<?php echo $filterState === '1' ? ' is-selected' : ''; ?>" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses&filter_search=&filter_state=1'); ?>">
      <span>Attivi</span><strong><?php echo (int) $stats->active; ?></strong>
    </a>
    <a class="dc-stat<?php echo $filterState === '0' ? ' is-selected' : ''; ?>" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses&filter_search=&filter_state=0'); ?>">
      <span>Disattivati</span><strong><?php echo (int) $stats->inactive; ?></strong>
    </a>
    <a class="dc-stat is-trash<?php echo $filterState === '-2' ? ' is-selected' : ''; ?>" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses&filter_search=&filter_state=-2'); ?>">
      <span>Cestino</span><strong><?php echo (int) $stats->trashed; ?></strong>
    </a>
  </nav>

  <section class="dc-card">
    <div class="dc-toolbar" role="search">
      <input
        class="form-control"
        type="search"
        name="filter_search"
        value="<?php echo $escape($this->state->get('filter.search')); ?>"
        placeholder="Cerca per titolo, codice o alias…"
        aria-label="Cerca corsi"
      >
      <select class="form-select" name="filter_state" aria-label="Filtra corsi per stato">
        <option value=""<?php echo $filterState === '' ? ' selected' : ''; ?>>Tutti gli stati</option>
        <option value="1"<?php echo $filterState === '1' ? ' selected' : ''; ?>>Attivi</option>
        <option value="0"<?php echo $filterState === '0' ? ' selected' : ''; ?>>Disattivati</option>
        <option value="-2"<?php echo $filterState === '-2' ? ' selected' : ''; ?>>Cestino</option>
      </select>
      <button class="btn btn-primary" type="submit">Cerca</button>
      <a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses&filter_search=&filter_state='); ?>">Azzera</a>
    </div>

    <?php if (!$this->items) : ?>
      <div class="dc-empty">
        <strong>Nessun corso trovato.</strong>
        <span>Modifica i filtri oppure crea un nuovo corso.</span>
      </div>
    <?php else : ?>
      <?php if ($this->canEditState || ($filterState === '-2' && $this->canDelete)) : ?>
        <div class="dc-bulk-actions" aria-label="Azioni sui corsi selezionati">
          <span class="dc-bulk-label">Selezionati</span>
          <?php if ($this->canEditState) : ?>
            <button class="btn btn-sm btn-outline-success" type="button" onclick="Joomla.submitbutton('courses.publish')">Pubblica</button>
            <?php if ($filterState !== '-2') : ?>
              <button class="btn btn-sm btn-outline-secondary" type="button" onclick="Joomla.submitbutton('courses.unpublish')">Sospendi</button>
              <button class="btn btn-sm btn-outline-danger" type="button" onclick="Joomla.submitbutton('courses.trash')">Cestino</button>
            <?php endif; ?>
          <?php endif; ?>
          <?php if ($filterState === '-2' && $this->canDelete) : ?>
            <button class="btn btn-sm btn-danger" type="button" onclick="Joomla.submitbutton('courses.delete')">Elimina definitivamente</button>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <div class="dc-table-wrap">
        <table class="dc-table dc-responsive-table dc-courses-table">
          <thead>
            <tr>
              <th class="dc-check"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
              <th class="dc-col-title">Titolo</th>
              <th class="dc-col-code">Codice</th>
              <th class="dc-col-editions">Edizioni</th>
              <th class="dc-col-state">Stato</th>
              <th class="dc-col-updated">Aggiornato</th>
              <th class="dc-actions-col">Azioni</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($this->items as $i => $item) :
              $itemState = (int) $item->state;
              $meta = $stateMeta[$itemState] ?? ['label' => 'Sconosciuto', 'class' => 'is-muted'];
              $editUrl = Route::_('index.php?option=com_decarocourses&task=course.edit&id=' . (int) $item->id);
              $editionsUrl = Route::_('index.php?option=com_decarocourses&view=editions&filter_search=&filter_course_id=' . (int) $item->id);
              $modified = !empty($item->modified) ? (string) $item->modified : (string) $item->created;
              $modifiedLabel = $modified !== '' ? HTMLHelper::_('date', $modified, 'd/m/Y H:i') : '—';
          ?>
            <tr>
              <td class="dc-check" data-label="Seleziona"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
              <td class="dc-col-title" data-label="Titolo">
                <?php if ($this->canEdit) : ?>
                  <a class="dc-title-link" href="<?php echo $editUrl; ?>"><?php echo $escape($item->title); ?></a>
                <?php else : ?>
                  <strong><?php echo $escape($item->title); ?></strong>
                <?php endif; ?>
                <small class="dc-row-subtitle">ID <?php echo (int) $item->id; ?></small>
                <small class="dc-tablet-updated">Aggiornato: <?php echo $modifiedLabel; ?></small>
              </td>
              <td class="dc-col-code" data-label="Codice">
                <?php if ((string) $item->code !== '') : ?>
                  <span class="dc-code"><?php echo $escape($item->code); ?></span>
                <?php else : ?>—<?php endif; ?>
              </td>
              <td class="dc-col-editions" data-label="Edizioni">
                <a class="dc-count-link" href="<?php echo $editionsUrl; ?>" aria-label="Apri le edizioni di <?php echo $escape($item->title); ?>">
                  <?php echo (int) $item->editions_count; ?>
                </a>
              </td>
              <td class="dc-col-state" data-label="Stato"><span class="dc-badge <?php echo $meta['class']; ?>"><?php echo $meta['label']; ?></span></td>
              <td class="dc-meta dc-col-updated" data-label="Aggiornato"><?php echo $modifiedLabel; ?></td>
              <td class="dc-col-actions" data-label="Azioni">
                <div class="dc-row-actions">
                  <?php if ($this->canEdit) : ?>
                    <a class="btn btn-sm btn-outline-primary dc-row-action-edit" href="<?php echo $editUrl; ?>">Modifica</a>
                  <?php endif; ?>
                  <a class="btn btn-sm btn-outline-secondary dc-row-action-related" href="<?php echo $editionsUrl; ?>">Edizioni</a>
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
