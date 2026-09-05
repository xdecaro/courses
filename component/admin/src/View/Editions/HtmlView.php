<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Editions;

defined('_JEXEC') or die;

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

        ToolbarHelper::title(Text::_('COM_DECAROCOURSES_EDITIONS'), 'calendar');
        ToolbarHelper::addNew('edition.add');
        ToolbarHelper::editList('edition.edit');
        ToolbarHelper::publish('editions.publish');
        ToolbarHelper::unpublish('editions.unpublish');
        ToolbarHelper::trash('editions.trash');

        parent::display($tpl);
    }
}
