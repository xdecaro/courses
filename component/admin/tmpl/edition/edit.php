<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
?>
<form action="<?php echo Route::_('index.php?option=com_decarocourses&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">
<div class="dc-app"><header class="dc-page-head"><div><span class="dc-eyebrow">EDIZIONE</span><h1><?php echo empty($this->item->id) ? 'Nuova edizione' : htmlspecialchars((string) $this->item->title, ENT_QUOTES, 'UTF-8'); ?></h1><p>Una edizione rappresenta una specifica annualità/sessione del corso e può essere collegata a Forms by xdecaro.</p></div></header><section class="dc-card dc-form-card"><?php echo $this->form->renderFieldset('details'); ?></section></div>
<input type="hidden" name="task" value=""><?php echo HTMLHelper::_('form.token'); ?>
</form>
