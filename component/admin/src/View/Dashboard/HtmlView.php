<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Dashboard;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

class HtmlView extends BaseHtmlView
{
    public array $summary = [];
    public array $recentEditions = [];

    public function display($tpl = null): void
    {
        UiHelper::loadAssets();
        $this->summary = $this->getModel()->getSummary();
        $this->recentEditions = $this->getModel()->getRecentEditions();

        ToolbarHelper::title(Text::_('COM_DECAROCOURSES_DASHBOARD'), 'grid-2');
        ToolbarHelper::preferences('com_decarocourses');

        parent::display($tpl);
    }
}
