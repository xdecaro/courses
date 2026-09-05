<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

$courseId = (int) $this->state->get('filter.course_id', 0);
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&view=editions'); ?>" method="post" name="adminForm" id="adminForm">
<div class="dc-app">
  <header class="dc-page-head">
    <div><span class="dc-eyebrow">AREA SEGRETERIA</span><h1>Edizioni dei corsi</h1><p>Gestisci anno accademico, periodo, capienza, stato e modulo di iscrizione associato.</p></div>
    <div class="dc-page-actions"><a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses'); ?>">← Corsi</a></div>
  </header>

  <?php if ($courseId > 0) : ?>
    <div class="dc-filter-notice">
      <span>Stai visualizzando solo le edizioni del corso <strong>#<?php echo $courseId; ?></strong>.</span>
      <a href="<?php echo Route::_('index.php?option=com_decarocourses&view=editions&filter_search=&filter_course_id=0'); ?>">Mostra tutte</a>
    </div>
  <?php endif; ?>

  <section class="dc-card">
    <div class="dc-toolbar">
      <input class="form-control" type="search" name="filter_search" value="<?php echo htmlspecialchars((string) $this->state->get('filter.search'), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Cerca corso, edizione o anno…" aria-label="Cerca edizioni">
      <button class="btn btn-primary" type="submit">Cerca</button>
      <a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_decarocourses&view=editions&filter_search=&filter_course_id=0'); ?>">Azzera</a>
    </div>
    <?php if (!$this->items) : ?><div class="dc-empty"><strong>Nessuna edizione trovata.</strong><span>Crea una nuova edizione oppure modifica i filtri.</span></div><?php else : ?>
    <div class="dc-table-wrap"><table class="dc-table"><thead><tr><th class="dc-check"><?php echo HTMLHelper::_('grid.checkall'); ?></th><th>Corso</th><th>Edizione</th><th>Anno</th><th>Stato</th><th>Forms</th><th>ID</th></tr></thead><tbody>
    <?php foreach ($this->items as $i => $item) : ?>
      <tr><td class="dc-check"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td><td><?php echo htmlspecialchars((string) $item->course_title, ENT_QUOTES, 'UTF-8'); ?></td><td><a class="dc-title-link" href="<?php echo Route::_('index.php?option=com_decarocourses&task=edition.edit&id=' . (int) $item->id); ?>"><?php echo htmlspecialchars((string) $item->title, ENT_QUOTES, 'UTF-8'); ?></a></td><td><?php echo htmlspecialchars((string) $item->academic_year, ENT_QUOTES, 'UTF-8'); ?></td><td><span class="dc-status <?php echo UiHelper::statusClass((string) $item->status); ?>"><?php echo UiHelper::statusLabel((string) $item->status); ?></span></td><td><?php echo (int) $item->forms_form_id > 0 ? '<span class="dc-badge is-info">#' . (int) $item->forms_form_id . '</span>' : '—'; ?></td><td><?php echo (int) $item->id; ?></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <?php echo $this->pagination->getListFooter(); ?>
    <?php endif; ?>
  </section>
</div>
<input type="hidden" name="filter_course_id" value="<?php echo $courseId; ?>">
<input type="hidden" name="task" value=""><input type="hidden" name="boxchecked" value="0"><?php echo HTMLHelper::_('form.token'); ?>
</form>
