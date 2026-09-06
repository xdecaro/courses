<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Information;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Xdecaro\Component\Decarocourses\Administrator\Helper\InformationHelper;
use Xdecaro\Component\Decarocourses\Administrator\Helper\UiHelper;

class HtmlView extends BaseHtmlView
{
    public array $info = [];

    public function display($tpl = null): void
    {
        UiHelper::loadAssets();

        Factory::getApplication()->getLanguage()->load(
            'com_decarocourses.information',
            JPATH_ADMINISTRATOR,
            null,
            true
        );

        $this->info = InformationHelper::getData();

        parent::display($tpl);
    }
}
