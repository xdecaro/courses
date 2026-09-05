<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Courses;

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
    public $stats;
    public bool $canCreate = false;
    public bool $canEdit = false;
    public bool $canEditState = false;
    public bool $canDelete = false;

    public function display($tpl = null): void
    {
        UiHelper::loadAssets();
        HTMLHelper::_('behavior.multiselect');

        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->stats = $this->get('Stats');

        $identity = Factory::getApplication()->getIdentity();
        $this->canCreate = $identity->authorise('core.create', 'com_decarocourses');
        $this->canEdit = $identity->authorise('core.edit', 'com_decarocourses');
        $this->canEditState = $identity->authorise('core.edit.state', 'com_decarocourses');
        $this->canDelete = $identity->authorise('core.delete', 'com_decarocourses');
        $hasItems = !empty($this->items);

        ToolbarHelper::title(Text::_('COM_DECAROCOURSES_COURSES'), 'stack');

        if ($this->canCreate) {
            ToolbarHelper::addNew('course.add');
        }

        if ($hasItems && $this->canEdit) {
            ToolbarHelper::editList('course.edit');
        }

        if ($hasItems && $this->canEditState) {
            ToolbarHelper::publish('courses.publish');
            ToolbarHelper::unpublish('courses.unpublish');

            if ((string) $this->state->get('filter.state') !== '-2') {
                ToolbarHelper::trash('courses.trash');
            }
        }

        if ($hasItems && $this->canDelete && (string) $this->state->get('filter.state') === '-2') {
            ToolbarHelper::deleteList('', 'courses.delete');
        }

        parent::display($tpl);
    }
}
