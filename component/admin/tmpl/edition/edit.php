<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$isNew = empty($this->item->id);
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="dc-app">
  <header class="dc-page-head">
    <div>
      <span class="dc-eyebrow">EDIZIONE</span>
      <h1><?php echo $isNew ? 'Nuova edizione' : $escape($this->item->title); ?></h1>
      <p>Una edizione rappresenta una specifica annualità o sessione del corso e può essere collegata a Forms by xdecaro.</p>
    </div>
    <div class="dc-page-actions">
      <a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_decarocourses&view=editions'); ?>">← Elenco edizioni</a>
    </div>
  </header>

  <section class="dc-card dc-form-card">
    <div class="dc-section-head">
      <div><span class="dc-eyebrow">DATI EDIZIONE</span><h2>Configurazione</h2></div>
      <p>Definisci corso, anno accademico, date, capienza, stato operativo e modulo di iscrizione.</p>
    </div>
    <?php echo $this->form->renderFieldset('details'); ?>
  </section>

  <div class="dc-form-actions" aria-label="Azioni edizione">
    <?php if ($this->canSave) : ?>
      <button class="btn btn-primary" type="submit" onclick="document.getElementById('dc-task').value='edition.apply'">Salva</button>
      <button class="btn btn-outline-primary" type="submit" onclick="document.getElementById('dc-task').value='edition.save'">Salva e chiudi</button>
    <?php endif; ?>
    <button class="btn btn-outline-secondary" type="submit" formnovalidate onclick="document.getElementById('dc-task').value='edition.cancel'">Annulla</button>
  </div>
</div>
<input type="hidden" name="task" id="dc-task" value="edition.apply">
<?php echo HTMLHelper::_('form.token'); ?>
</form>
