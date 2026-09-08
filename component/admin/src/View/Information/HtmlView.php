<?php
namespace Xdecaro\Component\Decarocourses\Administrator\View\Information;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\WebAsset\WebAssetManager;
use Xdecaro\Component\Decarocourses\Administrator\Helper\InformationHelper;

class HtmlView extends BaseHtmlView
{
    private const MINIMUM_CORE_UI_VERSION = '1.3.0';

    public array $info = [];
    public bool $canManageInstaller = false;
    public bool $coreUiActive = false;

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

        ToolbarHelper::title(Text::_('COM_DECAROCOURSES_INFORMATION'), 'info-circle');

        $document = $app->getDocument();
        $wa = $document->getWebAssetManager();

        $wa->getRegistry()->addExtensionRegistryFile('com_decarocourses');

        $this->coreUiActive = $this->enableCoreUi($wa);

        $wa->useStyle('com_decarocourses.design');

        if ($this->coreUiActive) {
            $wa->useStyle('com_decarocourses.core-bridge');
            $this->setLayout('core');
        }

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

    private function enableCoreUi(WebAssetManager $webAssets): bool
    {
        if (!class_exists(\xdecaro\Core\Version::class)
            || version_compare(\xdecaro\Core\Version::VERSION, self::MINIMUM_CORE_UI_VERSION, '<')
            || !class_exists(\xdecaro\Core\Asset\AssetService::class)) {
            return false;
        }

        try {
            $assetService = new \xdecaro\Core\Asset\AssetService();

            return $assetService->useComponents($webAssets);
        } catch (\Throwable $exception) {
            // Core remains optional: a UI integration failure must not block Courses.
            return false;
        }
    }
}
