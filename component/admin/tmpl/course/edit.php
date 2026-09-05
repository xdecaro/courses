<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$isNew = empty($this->item->id);
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">
<div class="dc-app">
  <header class="dc-page-head">
    <div>
      <span class="dc-eyebrow">CORSO</span>
      <h1><?php echo $isNew ? 'Nuovo corso' : $escape($this->item->title); ?></h1>
      <p>Qui salvi le informazioni generali del corso. Anno accademico, date, capienza e iscrizioni vengono gestiti nelle singole edizioni.</p>
    </div>
    <div class="dc-page-actions">
      <a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses'); ?>">← Elenco corsi</a>
      <?php if (!$isNew) : ?>
        <a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_decarocourses&view=editions&filter_search=&filter_course_id=' . (int) $this->item->id); ?>">Apri edizioni</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="dc-editor-layout">
    <main class="dc-editor-main">
      <section class="dc-card dc-form-section">
        <div class="dc-section-head">
          <div><span class="dc-eyebrow">DATI PRINCIPALI</span><h2>Identità del corso</h2></div>
          <p>Titolo e codice identificano il corso nel catalogo.</p>
        </div>
        <div class="dc-form-grid dc-form-grid-2">
          <div class="dc-field-span-2"><?php echo $this->form->renderField('title'); ?></div>
          <div><?php echo $this->form->renderField('code'); ?></div>
          <div><?php echo $this->form->renderField('alias'); ?></div>
        </div>
      </section>

      <section class="dc-card dc-form-section">
        <div class="dc-section-head">
          <div><span class="dc-eyebrow">DESCRIZIONE</span><h2>Informazioni generali</h2></div>
        </div>
        <?php echo $this->form->renderField('description'); ?>
      </section>
    </main>

    <aside class="dc-editor-side">
      <section class="dc-card dc-form-section">
        <div class="dc-section-head"><div><span class="dc-eyebrow">PUBBLICAZIONE</span><h2>Stato</h2></div></div>
        <?php echo $this->form->renderField('state'); ?>
        <?php echo $this->form->renderField('ordering'); ?>
      </section>

      <section class="dc-help-box">
        <strong>Come funziona</strong>
        <p>Il corso è la scheda principale. Per ogni anno o sessione crea un’<b>edizione</b> collegata, così eviti duplicati e mantieni uno storico pulito.</p>
      </section>
    </aside>
  </div>
</div>
<?php echo $this->form->getInput('id'); ?>
<input type="hidden" name="task" value="">
<?php echo HTMLHelper::_('form.token'); ?>
</form>
