<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

final class UiHelper
{
    public static function loadLanguage(): void
    {
        Factory::getApplication()->getLanguage()->load(
            'com_decarocourses.actions',
            JPATH_ADMINISTRATOR,
            null,
            true
        );
    }

    public static function loadAssets(): void
    {
        self::loadLanguage();

        $app = Factory::getApplication();
        $document = $app->getDocument();
        $wa = $document->getWebAssetManager();

        if (!$wa->assetExists('style', 'com_decarocourses.design')) {
            $wa->getRegistry()->addExtensionRegistryFile('com_decarocourses');
        }

        if (!$wa->assetExists('style', 'com_decarocourses.design')) {
            $wa->registerStyle(
                'com_decarocourses.design',
                'com_decarocourses/design-system.css',
                ['version' => '1.0.33']
            );
        }

        if (!$wa->assetExists('style', 'com_decarocourses.responsive')) {
            $wa->registerStyle(
                'com_decarocourses.responsive',
                'com_decarocourses/responsive.css',
                ['version' => '1.0.33']
            );
        }

        if (!$wa->assetExists('style', 'com_decarocourses.live-refresh')) {
            $wa->registerStyle(
                'com_decarocourses.live-refresh',
                'com_decarocourses/live-refresh.css',
                ['version' => '1.0.33']
            );
        }

        if (!$wa->assetExists('style', 'com_decarocourses.editions')) {
            $wa->registerStyle(
                'com_decarocourses.editions',
                'com_decarocourses/editions.css',
                ['version' => '1.0.33']
            );
        }

        if (!$wa->assetExists('style', 'com_decarocourses.editions-mobile')) {
            $wa->registerStyle(
                'com_decarocourses.editions-mobile',
                'com_decarocourses/editions-mobile.css',
                ['version' => '1.0.33']
            );
        }

        if (!$wa->assetExists('style', 'com_decarocourses.adaptive')) {
            $wa->registerStyle(
                'com_decarocourses.adaptive',
                'com_decarocourses/adaptive.css',
                ['version' => '1.0.33']
            );
        }

        if (!$wa->assetExists('style', 'com_decarocourses.row-actions-style')) {
            $wa->registerStyle(
                'com_decarocourses.row-actions-style',
                'com_decarocourses/row-actions.css',
                ['version' => '1.0.33']
            );
        }

        if (!$wa->assetExists('script', 'com_decarocourses.admin-ui')) {
            $wa->registerScript(
                'com_decarocourses.admin-ui',
                'com_decarocourses/admin-ui.js',
                ['version' => '1.0.33'],
                ['defer' => true]
            );
        }

        if (!$wa->assetExists('script', 'com_decarocourses.row-actions')) {
            $wa->registerScript(
                'com_decarocourses.row-actions',
                'com_decarocourses/row-actions.js',
                ['version' => '1.0.33'],
                ['defer' => true]
            );
        }

        $document->addScriptOptions('com_decarocourses.liveRefresh', [
            'url' => Route::_('index.php?option=com_decarocourses&task=live.options&format=json', false),
            'token' => Session::getFormToken(),
            'labels' => [
                'refreshing' => Text::_('COM_DECAROCOURSES_LIVE_REFRESHING'),
                'updated' => Text::_('COM_DECAROCOURSES_LIVE_REFRESH_UPDATED'),
                'unchanged' => Text::_('COM_DECAROCOURSES_LIVE_REFRESH_UNCHANGED'),
                'error' => Text::_('COM_DECAROCOURSES_LIVE_REFRESH_ERROR'),
                'stale' => Text::_('COM_DECAROCOURSES_LIVE_REFRESH_STALE'),
                'staleSuffix' => Text::_('COM_DECAROCOURSES_LIVE_REFRESH_STALE_SUFFIX'),
            ],
        ]);

        $wa->useStyle('com_decarocourses.design');
        $wa->useStyle('com_decarocourses.responsive');
        $wa->useStyle('com_decarocourses.live-refresh');
        $wa->useStyle('com_decarocourses.editions');
        $wa->useStyle('com_decarocourses.editions-mobile');
        $wa->useStyle('com_decarocourses.adaptive');
        $wa->useStyle('com_decarocourses.row-actions-style');
        $wa->useScript('com_decarocourses.admin-ui');
        $wa->useScript('com_decarocourses.row-actions');
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'registrations_open' => Text::_('COM_DECAROCOURSES_STATUS_REGISTRATIONS_OPEN'),
            'scheduled' => Text::_('COM_DECAROCOURSES_STATUS_SCHEDULED'),
            'active' => Text::_('COM_DECAROCOURSES_STATUS_ACTIVE'),
            'completed' => Text::_('COM_DECAROCOURSES_STATUS_COMPLETED'),
            'archived' => Text::_('COM_DECAROCOURSES_STATUS_ARCHIVED'),
            default => Text::_('COM_DECAROCOURSES_STATUS_DRAFT'),
        };
    }

    public static function statusClass(string $status): string
    {
        return match ($status) {
            'registrations_open' => 'is-open',
            'scheduled' => 'is-scheduled',
            'active' => 'is-active',
            'completed' => 'is-completed',
            'archived' => 'is-archived',
            default => 'is-draft',
        };
    }

    public static function publicationLabel(int $state): string
    {
        return match ($state) {
            1 => Text::_('JPUBLISHED'),
            -2 => Text::_('JTRASHED'),
            default => Text::_('JUNPUBLISHED'),
        };
    }

    public static function publicationClass(int $state): string
    {
        return match ($state) {
            1 => 'is-success',
            -2 => 'is-danger',
            default => 'is-muted',
        };
    }

    public static function formatLabel(string $format, string $custom = ''): string
    {
        $custom = trim($custom);

        return match ($format) {
            'intensive' => Text::_('COM_DECAROCOURSES_FORMAT_INTENSIVE'),
            'evening' => Text::_('COM_DECAROCOURSES_FORMAT_EVENING'),
            'weekend' => Text::_('COM_DECAROCOURSES_FORMAT_WEEKEND'),
            'custom' => $custom !== '' ? $custom : Text::_('COM_DECAROCOURSES_FORMAT_CUSTOM'),
            default => Text::_('COM_DECAROCOURSES_FORMAT_ANNUAL'),
        };
    }
}
