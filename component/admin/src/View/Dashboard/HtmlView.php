<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Dashboard;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Xdecaro\Component\Decarocourses\Administrator\Helper\AdminToolbarHelper;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

class HtmlView extends BaseHtmlView
{
    public array $summary = [];
    public array $recentEditions = [];
    public bool $canConfigure = false;

    public function display($tpl = null): void
    {
        UiHelper::loadAssets();
        $this->summary = $this->getModel()->getSummary();
        $this->recentEditions = $this->getModel()->getRecentEditions();

        $identity = Factory::getApplication()->getIdentity();
        $this->canConfigure = $identity->authorise('core.admin', 'com_decarocourses')
            || $identity->authorise('core.options', 'com_decarocourses');

        AdminToolbarHelper::dashboard($this->canConfigure);

        parent::display($tpl);
    }
}
