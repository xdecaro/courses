<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Information;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Xdecaro\Component\Decarocourses\Administrator\Helper\InformationHelper;

class HtmlView extends BaseHtmlView
{
    public array $info = [];
    public bool $canManageInstaller = false;

    public function display($tpl = null): void
    {
        $app = Factory::getApplication();
        $user = $app->getIdentity();

        if (!$user->authorise('core.manage', 'com_decarocourses')) {
            throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $app->getLanguage()->load(
            'com_decarocourses.information',
            JPATH_ADMINISTRATOR,
            null,
            true
        );

        $document = $app->getDocument();
        $wa = $document->getWebAssetManager();

        $wa->getRegistry()->addExtensionRegistryFile('com_decarocourses');
        $wa->useStyle('com_decarocourses.design');
        $wa->useStyle('com_decarocourses.information');
        $wa->useScript('com_decarocourses.information');

        foreach ([
            'COM_DECAROCOURSES_INFO_COPIED',
            'COM_DECAROCOURSES_INFO_COPY_FAILED',
            'COM_DECAROCOURSES_INFO_DOWNLOADED',
        ] as $key) {
            Text::script($key);
        }

        $this->info = InformationHelper::getData();
        $this->canManageInstaller = $user->authorise('core.manage', 'com_installer');

        parent::display($tpl);
    }
}
