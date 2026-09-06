<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Editions;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Xdecaro\Component\Decarocourses\Administrator\Helper\AdminToolbarHelper;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

class HtmlView extends BaseHtmlView
{
    public $items;
    public $pagination;
    public $state;
    public $stats;
    public string $selectedCourseTitle = '';
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
        $this->selectedCourseTitle = $this->getModel()->getSelectedCourseTitle();

        $identity = Factory::getApplication()->getIdentity();
        $this->canCreate = $identity->authorise('core.create', 'com_decarocourses');
        $this->canEdit = $identity->authorise('core.edit', 'com_decarocourses');
        $this->canEditState = $identity->authorise('core.edit.state', 'com_decarocourses');
        $this->canDelete = $identity->authorise('core.delete', 'com_decarocourses');

        AdminToolbarHelper::editions(
            $this->canCreate,
            $this->canEditState,
            $this->canDelete,
            (string) $this->state->get('filter.state', '') === '-2'
        );

        parent::display($tpl);
    }
}
