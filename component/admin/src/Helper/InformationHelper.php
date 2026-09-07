<?php
namespace Xdecaro\Component\Decarocourses\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Throwable;

final class InformationHelper
{
    public const VERSION = '1.0.34';
    public const MINIMUM_JOOMLA = '6.0.0';
    public const MINIMUM_PHP = '8.3.0';

    public static function getData(): array
    {
        /** @var DatabaseInterface $db */
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
        $packageVersion = self::manifestVersion($package);
        $schemaVersion = self::getSchemaVersion($db, (int) ($component->extension_id ?? 0));

        $coursesPresent = in_array($coursesTable, $tables, true);
        $editionsPresent = in_array($editionsTable, $tables, true);
        $schemaAligned = $schemaVersion !== '' && version_compare($schemaVersion, $componentVersion, '>=');
        $packageDetected = $package !== null && $packageVersion !== '';
        $installationConsistent = $packageDetected
            && version_compare($componentVersion, $packageVersion, '==')
            && $schemaAligned;

        $joomlaVersion = defined('JVERSION') ? (string) JVERSION : '';
        $phpVersion = PHP_VERSION;
        $environmentCompatible = version_compare($joomlaVersion, self::MINIMUM_JOOMLA, '>=')
            && version_compare($phpVersion, self::MINIMUM_PHP, '>=');

        $databaseType = '';
        $databaseVersion = '';

        try {
            $databaseType = method_exists($db, 'getServerType') ? (string) $db->getServerType() : '';
            $databaseVersion = method_exists($db, 'getVersion') ? (string) $db->getVersion() : '';
        } catch (Throwable) {
            $databaseType = '';
            $databaseVersion = '';
        }

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

        $updateSite = self::getUpdateSiteStatus($db, (int) ($package->extension_id ?? 0));
        $availableVersion = self::getAvailableUpdate($db);

        $updateAvailable = $availableVersion !== ''
            && version_compare($availableVersion, $componentVersion, '>');

        $updateState = !$updateSite['configured'] || !$updateSite['enabled']
            ? 'inactive'
            : ($updateAvailable ? 'available' : 'current');

        $criticalChecks = [
            'coursesTable' => $coursesPresent,
            'editionsTable' => $editionsPresent,
            'schemaAligned' => $schemaAligned,
            'packageDetected' => $packageDetected,
            'installationConsistent' => $installationConsistent,
            'environmentCompatible' => $environmentCompatible,
        ];

        $criticalCount = count(array_filter(
            $criticalChecks,
            static fn (bool $ok): bool => !$ok
        ));

        $diagnostics = [
            ...$criticalChecks,
            'tablesPresent' => $coursesPresent && $editionsPresent,
            'updateSiteEnabled' => $updateSite['configured'] && $updateSite['enabled'],
            'formsAvailable' => $formsInstalled,
        ];

        return [
            'componentVersion' => $componentVersion,
            'packageVersion' => $packageVersion,
            'schemaVersion' => $schemaVersion,
            'minimumJoomla' => self::MINIMUM_JOOMLA,
            'minimumPhp' => self::MINIMUM_PHP,
            'joomlaVersion' => $joomlaVersion,
            'phpVersion' => $phpVersion,
            'databaseType' => $databaseType,
            'databaseVersion' => $databaseVersion,
            'tablePresentCount' => (int) $coursesPresent + (int) $editionsPresent,
            'tableExpectedCount' => 2,
            'formsInstalled' => $formsInstalled,
            'formsEnabled' => (int) ($formsExtension->enabled ?? 0) === 1,
            'formsVersion' => self::manifestVersion($formsExtension),
            'formsCount' => $formsCount,
            'updateSite' => $updateSite,
            'availableVersion' => $availableVersion,
            'updateAvailable' => $updateAvailable,
            'updateState' => $updateState,
            'diagnostics' => $diagnostics,
            'criticalCount' => $criticalCount,
            'systemOk' => $criticalCount === 0,
        ];
    }

    private static function getExtension(DatabaseInterface $db, string $type, string $element): ?object
    {
        try {
            $query = $db->getQuery(true)
                ->select([
                    $db->quoteName('extension_id'),
                    $db->quoteName('manifest_cache'),
                    $db->quoteName('enabled'),
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
            'lastCheckTimestamp' => 0,
        ];

        if ($extensionId <= 0) {
            return $result;
        }

        try {
            $query = $db->getQuery(true)
                ->select([
                    $db->quoteName('s.location'),
                    $db->quoteName('s.enabled'),
                    $db->quoteName('s.last_check_timestamp'),
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
                $result['lastCheckTimestamp'] = max(0, (int) $site->last_check_timestamp);
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
