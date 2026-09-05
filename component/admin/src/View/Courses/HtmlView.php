<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Courses;

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

        ToolbarHelper::title(Text::_('COM_DECAROCOURSES_COURSES'), 'stack');
        ToolbarHelper::addNew('course.add');
        ToolbarHelper::editList('course.edit');
        ToolbarHelper::publish('courses.publish');
        ToolbarHelper::unpublish('courses.unpublish');
        ToolbarHelper::trash('courses.trash');

        parent::display($tpl);
    }
}
