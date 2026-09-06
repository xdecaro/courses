<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

final class UiHelper
{
    public static function loadAssets(): void
    {
        $document = Factory::getApplication()->getDocument();
        $wa = $document->getWebAssetManager();

        if (!$wa->assetExists('style', 'com_decarocourses.design')) {
            $wa->getRegistry()->addExtensionRegistryFile('com_decarocourses');
        }

        if (!$wa->assetExists('style', 'com_decarocourses.design')) {
            $wa->registerStyle(
                'com_decarocourses.design',
                'com_decarocourses/design-system.css',
                ['version' => '1.0.14']
            );
        }

        if (!$wa->assetExists('style', 'com_decarocourses.responsive')) {
            $wa->registerStyle(
                'com_decarocourses.responsive',
                'com_decarocourses/responsive.css',
                ['version' => '1.0.14']
            );
        }

        if (!$wa->assetExists('style', 'com_decarocourses.editions')) {
            $wa->registerStyle(
                'com_decarocourses.editions',
                'com_decarocourses/editions.css',
                ['version' => '1.0.14']
            );
        }

        if (!$wa->assetExists('script', 'com_decarocourses.admin-ui')) {
            $wa->registerScript(
                'com_decarocourses.admin-ui',
                'com_decarocourses/admin-ui.js',
                ['version' => '1.0.14'],
                ['defer' => true]
            );
        }

        $wa->useStyle('com_decarocourses.design');
        $wa->useStyle('com_decarocourses.responsive');
        $wa->useStyle('com_decarocourses.editions');
        $wa->useScript('com_decarocourses.admin-ui');
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

    public static function formatLabel(string $format, string $custom = ''): string
    {
        return match ($format) {
            'intensive' => Text::_('COM_DECAROCOURSES_FORMAT_INTENSIVE'),
            'evening' => Text::_('COM_DECAROCOURSES_FORMAT_EVENING'),
            'weekend' => Text::_('COM_DECAROCOURSES_FORMAT_WEEKEND'),
            'custom' => Text::_('COM_DECAROCOURSES_FORMAT_CUSTOM') . ($custom !== '' ? ' — ' . $custom : ''),
            default => Text::_('COM_DECAROCOURSES_FORMAT_ANNUAL'),
        };
    }
}
