<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&view=courses'); ?>" method="post" name="adminForm" id="adminForm">
<div class="dc-app">
  <header class="dc-page-head"><div><span class="dc-eyebrow">AREA SEGRETERIA</span><h1>Gestione corsi</h1><p>Catalogo generale dei corsi. Le singole annualità o sessioni vengono gestite come edizioni separate.</p></div></header>
  <section class="dc-card">
    <div class="dc-toolbar"><input class="form-control" type="search" name="filter_search" value="<?php echo htmlspecialchars((string) $this->state->get('filter.search'), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Cerca corso o codice…"><button class="btn btn-primary" type="submit">Cerca</button><a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses'); ?>">Azzera</a></div>
    <?php if (!$this->items) : ?><div class="dc-empty">Nessun corso presente. Usa “Nuovo” per creare il primo corso.</div><?php else : ?>
    <div class="dc-table-wrap"><table class="dc-table"><thead><tr><th class="dc-check"><?php echo HTMLHelper::_('grid.checkall'); ?></th><th>Titolo</th><th>Codice</th><th>Stato</th><th>ID</th></tr></thead><tbody>
    <?php foreach ($this->items as $i => $item) : ?>
      <tr><td class="dc-check"><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td><td><a class="dc-title-link" href="<?php echo Route::_('index.php?option=com_decarocourses&task=course.edit&id=' . (int) $item->id); ?>"><?php echo htmlspecialchars((string) $item->title, ENT_QUOTES, 'UTF-8'); ?></a></td><td><span class="dc-code"><?php echo htmlspecialchars((string) $item->code, ENT_QUOTES, 'UTF-8'); ?></span></td><td><span class="dc-badge <?php echo (int) $item->state === 1 ? 'is-success' : 'is-muted'; ?>"><?php echo (int) $item->state === 1 ? 'Attivo' : 'Disattivato'; ?></span></td><td><?php echo (int) $item->id; ?></td></tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <?php echo $this->pagination->getListFooter(); ?>
    <?php endif; ?>
  </section>
</div>
<input type="hidden" name="task" value="">
<input type="hidden" name="boxchecked" value="0">
<?php echo HTMLHelper::_('form.token'); ?>
</form>
