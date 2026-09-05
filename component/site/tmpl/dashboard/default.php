<?php
defined('_JEXEC') or die;
?>
<div class="dc-app">
  <header class="dc-page-head"><div><span class="dc-eyebrow">SCUOLA</span><h1>Courses</h1><p>Panoramica dei corsi e delle edizioni disponibili.</p></div></header>
  <section class="dc-stats"><div class="dc-stat"><span>Corsi disponibili</span><strong><?php echo (int) $this->summary['courses']; ?></strong></div><div class="dc-stat is-active"><span>In corso</span><strong><?php echo (int) $this->summary['active']; ?></strong></div><div class="dc-stat is-open"><span>Iscrizioni aperte</span><strong><?php echo (int) $this->summary['registrations_open']; ?></strong></div></section>
  <section class="dc-card"><span class="dc-eyebrow">VERSIONE 1.0.0</span><h2>Centro gestione corsi</h2><p>Questa è la base del nuovo sistema Courses. Le aree Studenti, Docenti, Coordinatori, Calendario, Presenze e Valutazioni verranno collegate progressivamente senza duplicare i dati.</p></section>
</div>
