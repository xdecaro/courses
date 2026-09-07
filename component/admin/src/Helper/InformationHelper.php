<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Throwable;

final class InformationHelper
{
    public const VERSION = '1.0.31';

    public static function getData(): array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $tables = $db->getTableList();
        $prefix = $db->getPrefix();

        $coursesTable = $prefix . 'decarocourses_courses';
        $editionsTable = $prefix . 'decarocourses_editions';
        $formsTable = $prefix . 'decaroforms_forms';

        $component = self::getExtension($db, 'component', 'com_decarocourses');
        $package = self::getExtension($db, 'package', 'pkg_decarocourses')
            ?? self::getExtension($db, 'package', 'decarocourses');
        $formsExtension = self::getExtension($db, 'component', 'com_decaroforms');

        $componentVersion = self::manifestVersion($component) ?: self::VERSION;
        $packageVersion = self::manifestVersion($package) ?: self::VERSION;
        $formsInstalled = $formsExtension !== null || in_array($formsTable, $tables, true);
        $formsCount = 0;

        if (in_array($formsTable, $tables, true)) {
            try {
                $formsCount = (int) $db->setQuery(
                    $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from($db->quoteName('#__decaroforms_forms'))
                )->loadResult();
            } catch (Throwable) {
                $formsCount = 0;
            }
        }

        $schemaVersion = self::getSchemaVersion($db, (int) ($component->extension_id ?? 0));
        $updateSite = self::getUpdateSiteStatus($db, (int) ($package->extension_id ?? 0));
        $availableVersion = self::getAvailableUpdate($db);

        $diagnostics = [
            'coursesTable' => in_array($coursesTable, $tables, true),
            'editionsTable' => in_array($editionsTable, $tables, true),
            'schemaAligned' => $schemaVersion === '' || version_compare($schemaVersion, $componentVersion, '>='),
            'formsAvailable' => $formsInstalled,
            'updateSiteEnabled' => $updateSite['configured'] && $updateSite['enabled'],
        ];

        return [
            'componentVersion' => $componentVersion,
            'packageVersion' => $packageVersion,
            'joomlaVersion' => defined('JVERSION') ? JVERSION : '',
            'phpVersion' => PHP_VERSION,
            'schemaVersion' => $schemaVersion,
            'formsInstalled' => $formsInstalled,
            'formsVersion' => self::manifestVersion($formsExtension),
            'formsCount' => $formsCount,
            'updateSite' => $updateSite,
            'availableVersion' => $availableVersion,
            'diagnostics' => $diagnostics,
        ];
    }

    private static function getExtension(DatabaseInterface $db, string $type, string $element): ?object
    {
        try {
            $query = $db->getQuery(true)
                ->select([
                    $db->quoteName('extension_id'),
                    $db->quoteName('manifest_cache'),
                ])
                ->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('type') . ' = :type')
                ->where($db->quoteName('element') . ' = :element')
                ->bind(':type', $type)
                ->bind(':element', $element);

            $record = $db->setQuery($query, 0, 1)->loadObject();

            return $record ?: null;
        } catch (Throwable) {
            return null;
        }
    }

    private static function manifestVersion(?object $extension): string
    {
        if (!$extension || empty($extension->manifest_cache)) {
            return '';
        }

        $manifest = json_decode((string) $extension->manifest_cache, true);

        return is_array($manifest) ? trim((string) ($manifest['version'] ?? '')) : '';
    }

    private static function getSchemaVersion(DatabaseInterface $db, int $extensionId): string
    {
        if ($extensionId <= 0) {
            return '';
        }

        try {
            $query = $db->getQuery(true)
                ->select($db->quoteName('version_id'))
                ->from($db->quoteName('#__schemas'))
                ->where($db->quoteName('extension_id') . ' = :extensionId')
                ->bind(':extensionId', $extensionId, ParameterType::INTEGER);

            return trim((string) ($db->setQuery($query, 0, 1)->loadResult() ?? ''));
        } catch (Throwable) {
            return '';
        }
    }

    private static function getUpdateSiteStatus(DatabaseInterface $db, int $extensionId): array
    {
        $result = [
            'configured' => false,
            'enabled' => false,
            'location' => '',
        ];

        if ($extensionId <= 0) {
            return $result;
        }

        try {
            $query = $db->getQuery(true)
                ->select([
                    $db->quoteName('s.location'),
                    $db->quoteName('s.enabled'),
                ])
                ->from($db->quoteName('#__update_sites_extensions', 'm'))
                ->innerJoin(
                    $db->quoteName('#__update_sites', 's')
                    . ' ON ' . $db->quoteName('s.update_site_id') . ' = ' . $db->quoteName('m.update_site_id')
                )
                ->where($db->quoteName('m.extension_id') . ' = :extensionId')
                ->bind(':extensionId', $extensionId, ParameterType::INTEGER);

            $site = $db->setQuery($query, 0, 1)->loadObject();

            if ($site) {
                $result['configured'] = true;
                $result['enabled'] = (int) $site->enabled === 1;
                $result['location'] = trim((string) $site->location);
            }
        } catch (Throwable) {
            return $result;
        }

        return $result;
    }

    private static function getAvailableUpdate(DatabaseInterface $db): string
    {
        try {
            $element = 'pkg_decarocourses';
            $query = $db->getQuery(true)
                ->select($db->quoteName('version'))
                ->from($db->quoteName('#__updates'))
                ->where($db->quoteName('element') . ' = :element')
                ->bind(':element', $element)
                ->order($db->quoteName('update_id') . ' DESC');

            return trim((string) ($db->setQuery($query, 0, 1)->loadResult() ?? ''));
        } catch (Throwable) {
            return '';
        }
    }
}
