<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;
?>
<div class="dc-app">
  <header class="dc-page-head">
    <div>
      <span class="dc-eyebrow">AREA SEGRETERIA</span>
      <h1>Courses</h1>
      <p>Gestione centralizzata di corsi, edizioni, anni accademici e moduli di iscrizione.</p>
    </div>
    <?php if ($this->canConfigure) : ?>
      <div class="dc-page-actions">
        <a class="btn dc-btn dc-btn-secondary" href="<?php echo Route::_('index.php?option=com_config&view=component&component=com_decarocourses'); ?>">Impostazioni</a>
      </div>
    <?php endif; ?>
  </header>

  <section class="dc-stats" aria-label="Riepilogo Courses">
    <a class="dc-stat" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses'); ?>">
      <span>Corsi</span><strong><?php echo (int) $this->summary['courses']; ?></strong>
    </a>
    <a class="dc-stat" href="<?php echo Route::_('index.php?option=com_decarocourses&view=editions'); ?>">
      <span>Edizioni</span><strong><?php echo (int) $this->summary['editions']; ?></strong>
    </a>
    <a class="dc-stat is-active" href="<?php echo Route::_('index.php?option=com_decarocourses&view=editions'); ?>">
      <span>In corso</span><strong><?php echo (int) $this->summary['active']; ?></strong>
    </a>
    <a class="dc-stat is-open" href="<?php echo Route::_('index.php?option=com_decarocourses&view=editions'); ?>">
      <span>Iscrizioni aperte</span><strong><?php echo (int) $this->summary['registrations_open']; ?></strong>
    </a>
  </section>

  <div class="dc-grid-2">
    <section class="dc-card">
      <div class="dc-card-head">
        <div><span class="dc-eyebrow">GESTIONE</span><h2>Corsi ed edizioni</h2></div>
      </div>
      <div class="dc-actions-grid">
        <a class="dc-action" href="<?php echo Route::_('index.php?option=com_decarocourses&view=courses'); ?>"><strong>Gestione corsi</strong><span>Catalogo e struttura dei corsi</span></a>
        <a class="dc-action" href="<?php echo Route::_('index.php?option=com_decarocourses&view=editions'); ?>"><strong>Edizioni</strong><span>Anno accademico, date, stato e iscrizioni</span></a>
      </div>
    </section>

    <section class="dc-card">
      <div class="dc-card-head">
        <div><span class="dc-eyebrow">INTEGRAZIONE</span><h2>Forms by xdecaro</h2></div>
        <span class="dc-badge <?php echo $this->summary['forms_available'] ? 'is-success' : 'is-muted'; ?>">
          <?php echo $this->summary['forms_available'] ? 'Disponibile' : 'Non installato'; ?>
        </span>
      </div>
      <p>Ogni edizione può essere collegata a un modulo Forms senza rendere Courses dipendente dal componente Forms.</p>
      <?php if ($this->summary['forms_available']) : ?>
        <p class="dc-meta"><?php echo (int) $this->summary['forms_count']; ?> moduli rilevati.</p>
      <?php endif; ?>
    </section>
  </div>

  <section class="dc-card dc-mt">
    <div class="dc-card-head"><div><span class="dc-eyebrow">ULTIME ATTIVITÀ</span><h2>Edizioni recenti</h2></div></div>
    <?php if (!$this->recentEditions) : ?>
      <div class="dc-empty">Nessuna edizione ancora creata.</div>
    <?php else : ?>
      <div class="dc-table-wrap"><table class="dc-table"><thead><tr><th>Corso</th><th>Edizione</th><th>Anno</th><th>Stato</th></tr></thead><tbody>
      <?php foreach ($this->recentEditions as $edition) : ?>
        <tr>
          <td><?php echo htmlspecialchars((string) $edition->course_title, ENT_QUOTES, 'UTF-8'); ?></td>
          <td><a href="<?php echo Route::_('index.php?option=com_decarocourses&task=edition.edit&id=' . (int) $edition->id); ?>"><?php echo htmlspecialchars((string) $edition->title, ENT_QUOTES, 'UTF-8'); ?></a></td>
          <td><?php echo htmlspecialchars((string) $edition->academic_year, ENT_QUOTES, 'UTF-8'); ?></td>
          <td><span class="dc-status <?php echo UiHelper::statusClass((string) $edition->status); ?>"><?php echo UiHelper::statusLabel((string) $edition->status); ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody></table></div>
    <?php endif; ?>
  </section>
</div>
