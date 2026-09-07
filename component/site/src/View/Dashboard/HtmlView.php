<?php
namespace Xdecaro\Component\Decarocourses\Site\View\Dashboard;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use RuntimeException;

class HtmlView extends BaseHtmlView
{
    public array $summary = [];

    public function display($tpl = null): void
    {
        $app = Factory::getApplication();
        if ($app->getIdentity()->guest) {
            throw new RuntimeException('Accesso riservato agli utenti autenticati.', 403);
        }

        $wa = $app->getDocument()->getWebAssetManager();
        if (!$wa->assetExists('style', 'com_decarocourses.design')) {
            $wa->registerStyle('com_decarocourses.design', 'com_decarocourses/css/design-system.css', [], ['version' => '1.0.33']);
        }
        $wa->useStyle('com_decarocourses.design');

        $this->summary = $this->getModel()->getSummary();
        parent::display($tpl);
    }
}
