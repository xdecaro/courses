<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Editions;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

class HtmlView extends BaseHtmlView
{
    public $items;
    public $pagination;
    public $state;

    public function display($tpl = null): void
    {
        UiHelper::loadAssets();
        HTMLHelper::_('behavior.multiselect');

        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');

        $identity = Factory::getApplication()->getIdentity();
        $canCreate = $identity->authorise('core.create', 'com_decarocourses');
        $canEdit = $identity->authorise('core.edit', 'com_decarocourses');
        $canEditState = $identity->authorise('core.edit.state', 'com_decarocourses');
        $hasItems = !empty($this->items);

        ToolbarHelper::title(Text::_('COM_DECAROCOURSES_EDITIONS'), 'calendar');

        if ($canCreate) {
            ToolbarHelper::addNew('edition.add');
        }

        if ($hasItems && $canEdit) {
            ToolbarHelper::editList('edition.edit');
        }

        if ($hasItems && $canEditState) {
            ToolbarHelper::publish('editions.publish');
            ToolbarHelper::unpublish('editions.unpublish');
            ToolbarHelper::trash('editions.trash');
        }

        parent::display($tpl);
    }
}
