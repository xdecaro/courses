<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

final class UiHelper
{
    public static function loadAssets(): void
    {
        $document = Factory::getApplication()->getDocument();
        $wa = $document->getWebAssetManager();
        $styleUri = rtrim(Uri::root(true), '/') . '/media/com_decarocourses/css/design-system.css';

        if (!$wa->assetExists('style', 'com_decarocourses.design')) {
            $wa->registerStyle(
                'com_decarocourses.design',
                $styleUri,
                [],
                ['version' => '1.0.4']
            );
        }

        $wa->useStyle('com_decarocourses.design');
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'registrations_open' => 'Iscrizioni aperte',
            'scheduled' => 'Programmato',
            'active' => 'In corso',
            'completed' => 'Concluso',
            'archived' => 'Archiviato',
            default => 'Bozza',
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
}
