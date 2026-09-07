<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$info = (array) $this->info;
$diagnostics = (array) ($info['diagnostics'] ?? []);
$componentVersion = (string) ($info['componentVersion'] ?? '—');
$packageVersion = (string) ($info['packageVersion'] ?? '');
$schemaVersion = (string) ($info['schemaVersion'] ?? '');
$updateSite = (array) ($info['updateSite'] ?? []);
$availableVersion = (string) ($info['availableVersion'] ?? '');
$updateState = (string) ($info['updateState'] ?? 'inactive');
$systemOk = !empty($info['systemOk']);
$installationConsistent = !empty($diagnostics['installationConsistent']);
$environmentCompatible = !empty($diagnostics['environmentCompatible']);
$schemaAligned = !empty($diagnostics['schemaAligned']);
$tablesPresent = !empty($diagnostics['tablesPresent']);
$packageDetected = !empty($diagnostics['packageDetected']);
$updateSiteEnabled = !empty($diagnostics['updateSiteEnabled']);
$formsInstalled = !empty($info['formsInstalled']);
$formsEnabled = !empty($info['formsEnabled']);
$lastCheckTimestamp = (int) ($updateSite['lastCheckTimestamp'] ?? 0);

$updateLabelKey = match ($updateState) {
    'current' => 'COM_DECAROCOURSES_INFO_UP_TO_DATE',
    'available' => 'COM_DECAROCOURSES_INFO_UPDATE_AVAILABLE',
    default => 'COM_DECAROCOURSES_INFO_INACTIVE',
};

$updateBadgeClass = match ($updateState) {
    'current' => 'is-success',
    'available' => 'is-warning',
    default => 'is-danger',
};

$diagnosticPayload = [
    'Courses' => $componentVersion,
    'Component' => 'com_decarocourses',
    'Package' => 'pkg_decarocourses',
    'Package version' => $packageVersion !== '' ? $packageVersion : 'not detected',
    'Schema version' => $schemaVersion !== '' ? $schemaVersion : 'not detected',
    'Joomla' => (string) ($info['joomlaVersion'] ?? ''),
    'PHP' => (string) ($info['phpVersion'] ?? ''),
    'Database' => trim((string) ($info['databaseType'] ?? '') . ' ' . (string) ($info['databaseVersion'] ?? '')),
    'Tables' => (int) ($info['tablePresentCount'] ?? 0) . '/' . (int) ($info['tableExpectedCount'] ?? 2),
    'Update server configured' => !empty($updateSite['configured']) ? 'yes' : 'no',
    'Update server enabled' => !empty($updateSite['enabled']) ? 'yes' : 'no',
    'Update state' => $updateState,
    'Available version' => $availableVersion !== '' ? $availableVersion : 'none',
    'Forms installed' => $formsInstalled ? 'yes' : 'no',
    'Forms enabled' => $formsEnabled ? 'yes' : 'no',
    'Forms version' => (string) ($info['formsVersion'] ?? ''),
    'Forms available' => (int) ($info['formsCount'] ?? 0),
    'Critical issues' => (int) ($info['criticalCount'] ?? 0),
];

$diagnosticJson = json_encode(
    $diagnosticPayload,
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
?>
<div class="dc-app dci-page">
    <header class="dci-page-header">
        <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_EYEBROW'); ?></span>
        <h1><?php echo Text::_('COM_DECAROCOURSES_INFO_TITLE'); ?></h1>
        <p><?php echo Text::_('COM_DECAROCOURSES_INFO_DESCRIPTION'); ?></p>
    </header>

    <div class="dci-summary" aria-label="<?php echo $escape(Text::_('COM_DECAROCOURSES_INFO_QUICK_STATUS')); ?>">
        <strong>Courses <?php echo $escape($componentVersion); ?></strong>
        <span class="dc-badge <?php echo $updateBadgeClass; ?>"><?php echo Text::_($updateLabelKey); ?></span>
        <span class="dc-badge <?php echo $systemOk ? 'is-success' : 'is-danger'; ?>">
            <?php echo Text::_($systemOk ? 'COM_DECAROCOURSES_INFO_SYSTEM_OK' : 'COM_DECAROCOURSES_INFO_SYSTEM_CHECK'); ?>
        </span>
    </div>

    <div class="dci-grid">
        <section class="dc-card dci-card">
            <div class="dc-card-head">
                <div>
                    <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_PRODUCT'); ?></span>
                    <h2><?php echo Text::_('COM_DECAROCOURSES_INFO_VERSIONS'); ?></h2>
                </div>
                <span class="dc-badge <?php echo $installationConsistent ? 'is-success' : 'is-danger'; ?>">
                    <?php echo Text::_($installationConsistent ? 'COM_DECAROCOURSES_INFO_COHERENT' : 'COM_DECAROCOURSES_INFO_NOT_COHERENT'); ?>
                </span>
            </div>

            <dl class="dci-list">
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_COMPONENT_VERSION'); ?></dt>
                    <dd><span class="dc-badge is-success"><?php echo $escape($componentVersion); ?></span></dd>
                </div>
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_PACKAGE_VERSION'); ?></dt>
                    <dd>
                        <span class="dc-badge <?php echo $packageDetected && $packageVersion === $componentVersion ? 'is-success' : 'is-danger'; ?>">
                            <?php echo $escape($packageVersion !== '' ? $packageVersion : Text::_('COM_DECAROCOURSES_INFO_NOT_DETECTED')); ?>
                        </span>
                    </dd>
                </div>
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_DATABASE_SCHEMA'); ?></dt>
                    <dd><span class="dc-badge <?php echo $schemaAligned ? 'is-success' : 'is-danger'; ?>"><?php echo Text::_($schemaAligned ? 'COM_DECAROCOURSES_INFO_ALIGNED' : 'COM_DECAROCOURSES_INFO_CHECK'); ?></span></dd>
                </div>
                <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_COMPONENT_ID'); ?></dt><dd><code>com_decarocourses</code></dd></div>
                <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_PACKAGE_ID'); ?></dt><dd><code>pkg_decarocourses</code></dd></div>
                <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_DEVELOPER'); ?></dt><dd>Luca De Caro</dd></div>
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_REPOSITORY'); ?></dt>
                    <dd><a href="https://github.com/xdecaro/courses" target="_blank" rel="noopener noreferrer">xdecaro/courses <span aria-hidden="true">↗</span></a></dd>
                </div>
                <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_LICENSE'); ?></dt><dd>GNU GPL v2 or later</dd></div>
            </dl>
        </section>

        <section class="dc-card dci-card">
            <div class="dc-card-head">
                <div>
                    <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_ENVIRONMENT'); ?></span>
                    <h2><?php echo Text::_('COM_DECAROCOURSES_INFO_SYSTEM'); ?></h2>
                </div>
                <span class="dc-badge <?php echo $environmentCompatible ? 'is-success' : 'is-danger'; ?>">
                    <?php echo Text::_($environmentCompatible ? 'COM_DECAROCOURSES_INFO_COMPATIBLE' : 'COM_DECAROCOURSES_INFO_INCOMPATIBLE'); ?>
                </span>
            </div>

            <dl class="dci-list">
                <div class="dci-row"><dt>Joomla</dt><dd><?php echo $escape($info['joomlaVersion'] ?? '—'); ?></dd></div>
                <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_JOOMLA_MINIMUM'); ?></dt><dd><?php echo $escape($info['minimumJoomla'] ?? '—'); ?></dd></div>
                <div class="dci-row"><dt>PHP</dt><dd><?php echo $escape($info['phpVersion'] ?? '—'); ?></dd></div>
                <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_PHP_MINIMUM'); ?></dt><dd><?php echo $escape($info['minimumPhp'] ?? '—'); ?></dd></div>
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_DATABASE'); ?></dt>
                    <dd><?php echo $escape(trim((string) ($info['databaseType'] ?? '') . ' ' . (string) ($info['databaseVersion'] ?? '')) ?: '—'); ?></dd>
                </div>
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_TABLES'); ?></dt>
                    <dd>
                        <span class="dc-badge <?php echo $tablesPresent ? 'is-success' : 'is-danger'; ?>">
                            <?php echo (int) ($info['tablePresentCount'] ?? 0); ?>/<?php echo (int) ($info['tableExpectedCount'] ?? 2); ?>
                            <?php echo Text::_('COM_DECAROCOURSES_INFO_PRESENT'); ?>
                        </span>
                    </dd>
                </div>
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_DATABASE_SCHEMA'); ?></dt>
                    <dd><span class="dc-badge <?php echo $schemaAligned ? 'is-success' : 'is-danger'; ?>"><?php echo Text::_($schemaAligned ? 'COM_DECAROCOURSES_INFO_ALIGNED' : 'COM_DECAROCOURSES_INFO_CHECK'); ?></span></dd>
                </div>
            </dl>
        </section>

        <section class="dc-card dci-card">
            <div class="dc-card-head">
                <div>
                    <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_EXTENSIONS'); ?></span>
                    <h2><?php echo Text::_('COM_DECAROCOURSES_INFO_INCLUDED_EXTENSIONS'); ?></h2>
                </div>
            </div>

            <dl class="dci-list">
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_COMPONENT'); ?></dt>
                    <dd><span class="dc-badge is-success"><?php echo Text::_('COM_DECAROCOURSES_INFO_INSTALLED'); ?></span></dd>
                </div>
                <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_VERSION'); ?></dt><dd><span class="dc-badge is-success"><?php echo $escape($componentVersion); ?></span></dd></div>
                <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_EXTRA_PLUGINS'); ?></dt><dd><?php echo Text::_('COM_DECAROCOURSES_INFO_NONE'); ?></dd></div>
                <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_EXTRA_MODULES'); ?></dt><dd><?php echo Text::_('COM_DECAROCOURSES_INFO_NONE'); ?></dd></div>
            </dl>
        </section>

        <section class="dc-card dci-card">
            <div class="dc-card-head">
                <div>
                    <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_UPDATES'); ?></span>
                    <h2><?php echo Text::_('COM_DECAROCOURSES_INFO_UPDATE_CHANNEL'); ?></h2>
                </div>
                <span class="dc-badge <?php echo $updateSiteEnabled ? 'is-success' : 'is-danger'; ?>">
                    <?php echo Text::_($updateSiteEnabled ? 'COM_DECAROCOURSES_INFO_ACTIVE' : 'COM_DECAROCOURSES_INFO_INACTIVE'); ?>
                </span>
            </div>

            <dl class="dci-list">
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_UPDATE_SERVER'); ?></dt>
                    <dd><span class="dc-badge <?php echo !empty($updateSite['configured']) ? 'is-success' : 'is-danger'; ?>"><?php echo Text::_(!empty($updateSite['configured']) ? 'COM_DECAROCOURSES_INFO_CONFIGURED' : 'COM_DECAROCOURSES_INFO_NOT_CONFIGURED'); ?></span></dd>
                </div>
                <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_CHANNEL'); ?></dt><dd><?php echo Text::_('COM_DECAROCOURSES_INFO_STABLE'); ?></dd></div>
                <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_INSTALLED_VERSION'); ?></dt><dd><?php echo $escape($componentVersion); ?></dd></div>
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_LATEST_DETECTED'); ?></dt>
                    <dd><?php echo $availableVersion !== '' ? $escape($availableVersion) : Text::_('COM_DECAROCOURSES_INFO_NO_UPDATE_DETECTED'); ?></dd>
                </div>
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_LAST_CHECK'); ?></dt>
                    <dd><?php echo $lastCheckTimestamp > 0 ? $escape(date('d/m/Y H:i', $lastCheckTimestamp)) : Text::_('COM_DECAROCOURSES_INFO_NEVER'); ?></dd>
                </div>
                <div class="dci-row">
                    <dt><?php echo Text::_('COM_DECAROCOURSES_INFO_STATUS'); ?></dt>
                    <dd><span class="dc-badge <?php echo $updateBadgeClass; ?>"><?php echo Text::_($updateLabelKey); ?></span></dd>
                </div>
            </dl>

            <?php if ($this->canManageInstaller) : ?>
                <div class="dci-actions">
                    <a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_installer&view=update'); ?>"><?php echo Text::_('COM_DECAROCOURSES_INFO_OPEN_UPDATES'); ?></a>
                    <a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_installer&view=updatesites'); ?>"><?php echo Text::_('COM_DECAROCOURSES_INFO_OPEN_UPDATE_SITES'); ?></a>
                </div>
            <?php endif; ?>
        </section>

        <section class="dc-card dci-card dci-full">
            <div class="dc-card-head">
                <div>
                    <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_INTEGRATIONS'); ?></span>
                    <h2><?php echo Text::_('COM_DECAROCOURSES_INFO_CONNECTED_COMPONENTS'); ?></h2>
                </div>
            </div>
            <p class="dci-card-intro"><?php echo Text::_('COM_DECAROCOURSES_INFO_CONNECTED_COMPONENTS_DESC'); ?></p>

            <article class="dci-integration">
                <div class="dci-integration-main">
                    <strong>Forms by xdecaro</strong>
                    <span><?php echo Text::_('COM_DECAROCOURSES_INFO_OPTIONAL_INTEGRATION'); ?> · <code>com_decaroforms</code></span>
                </div>
                <div class="dci-integration-metrics">
                    <div>
                        <small><?php echo Text::_('COM_DECAROCOURSES_INFO_VERSION_INSTALLED'); ?></small>
                        <strong><?php echo $escape(($info['formsVersion'] ?? '') !== '' ? $info['formsVersion'] : '—'); ?></strong>
                    </div>
                    <div>
                        <small><?php echo Text::_('COM_DECAROCOURSES_INFO_FORMS_COUNT'); ?></small>
                        <strong><?php echo (int) ($info['formsCount'] ?? 0); ?></strong>
                    </div>
                </div>
                <div class="dci-integration-badges">
                    <span class="dc-badge <?php echo $formsInstalled ? 'is-success' : 'is-muted'; ?>"><?php echo Text::_($formsInstalled ? 'COM_DECAROCOURSES_INFO_INSTALLED' : 'COM_DECAROCOURSES_INFO_NOT_INSTALLED'); ?></span>
                    <span class="dc-badge is-muted"><?php echo Text::_('COM_DECAROCOURSES_INFO_OPTIONAL'); ?></span>
                </div>
            </article>
        </section>

        <section class="dc-card dci-card dci-full">
            <div class="dci-diagnostic-head">
                <div>
                    <span class="dc-eyebrow"><?php echo Text::_('COM_DECAROCOURSES_INFO_DIAGNOSTICS'); ?></span>
                    <h2><?php echo Text::_('COM_DECAROCOURSES_INFO_HEALTH'); ?></h2>
                    <p><?php echo Text::_('COM_DECAROCOURSES_INFO_DIAGNOSTICS_HELP'); ?></p>
                </div>
                <span class="dc-badge <?php echo $systemOk ? 'is-success' : 'is-danger'; ?>">
                    <?php echo Text::_($systemOk ? 'COM_DECAROCOURSES_INFO_NO_CRITICAL_ISSUES' : 'COM_DECAROCOURSES_INFO_ATTENTION_REQUIRED'); ?>
                </span>
            </div>

            <div class="dci-checks">
                <div class="dci-check <?php echo $installationConsistent ? 'is-ok' : 'is-error'; ?>"><span aria-hidden="true"><?php echo $installationConsistent ? '✓' : '!'; ?></span><?php echo Text::_('COM_DECAROCOURSES_INFO_VERSIONS_COHERENT'); ?></div>
                <div class="dci-check <?php echo $schemaAligned ? 'is-ok' : 'is-error'; ?>"><span aria-hidden="true"><?php echo $schemaAligned ? '✓' : '!'; ?></span><?php echo Text::_('COM_DECAROCOURSES_INFO_DATABASE_ALIGNED'); ?></div>
                <div class="dci-check <?php echo $tablesPresent ? 'is-ok' : 'is-error'; ?>"><span aria-hidden="true"><?php echo $tablesPresent ? '✓' : '!'; ?></span><?php echo Text::_('COM_DECAROCOURSES_INFO_TABLES_PRESENT'); ?></div>
                <div class="dci-check <?php echo $updateSiteEnabled ? 'is-ok' : 'is-warning'; ?>"><span aria-hidden="true"><?php echo $updateSiteEnabled ? '✓' : '!'; ?></span><?php echo Text::_('COM_DECAROCOURSES_INFO_UPDATE_SERVER_ACTIVE'); ?></div>
                <div class="dci-check <?php echo $environmentCompatible ? 'is-ok' : 'is-error'; ?>"><span aria-hidden="true"><?php echo $environmentCompatible ? '✓' : '!'; ?></span><?php echo Text::_('COM_DECAROCOURSES_INFO_ENVIRONMENT_COMPATIBLE'); ?></div>
                <div class="dci-check <?php echo $packageDetected ? 'is-ok' : 'is-error'; ?>"><span aria-hidden="true"><?php echo $packageDetected ? '✓' : '!'; ?></span><?php echo Text::_('COM_DECAROCOURSES_INFO_PACKAGE_DETECTED'); ?></div>
            </div>

            <details class="dci-details">
                <summary><?php echo Text::_('COM_DECAROCOURSES_INFO_DETAILS'); ?></summary>
                <dl class="dci-list">
                    <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_COMPONENT_ID'); ?></dt><dd><code>com_decarocourses</code></dd></div>
                    <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_PACKAGE_ID'); ?></dt><dd><code>pkg_decarocourses</code></dd></div>
                    <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_SCHEMA_VERSION'); ?></dt><dd><?php echo $escape($schemaVersion !== '' ? $schemaVersion : Text::_('COM_DECAROCOURSES_INFO_NOT_DETECTED')); ?></dd></div>
                    <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_DATABASE'); ?></dt><dd><?php echo $escape(trim((string) ($info['databaseType'] ?? '') . ' ' . (string) ($info['databaseVersion'] ?? '')) ?: '—'); ?></dd></div>
                    <div class="dci-row"><dt><?php echo Text::_('COM_DECAROCOURSES_INFO_UPDATE_SERVER'); ?></dt><dd><code><?php echo $escape($updateSite['location'] ?? '—'); ?></code></dd></div>
                    <div class="dci-row"><dt>Forms by xdecaro</dt><dd><?php echo $formsInstalled ? $escape((string) ($info['formsVersion'] ?? '')) : Text::_('COM_DECAROCOURSES_INFO_NOT_INSTALLED'); ?></dd></div>
                </dl>
            </details>

            <div class="dci-actions dci-diagnostic-actions">
                <button class="btn dci-btn-outline" type="button" data-dci-copy><?php echo Text::_('COM_DECAROCOURSES_INFO_COPY_DIAGNOSTICS'); ?></button>
                <button class="btn dci-btn-outline" type="button" data-dci-download><?php echo Text::_('COM_DECAROCOURSES_INFO_DOWNLOAD_DIAGNOSTICS'); ?></button>
                <a class="btn dci-btn-outline" href="https://github.com/xdecaro/courses/tree/main/releases/<?php echo rawurlencode($componentVersion); ?>" target="_blank" rel="noopener noreferrer"><?php echo Text::_('COM_DECAROCOURSES_INFO_RELEASE_GITHUB'); ?></a>
                <span class="dci-action-status" data-dci-status aria-live="polite"></span>
            </div>

            <script type="application/json" id="dci-diagnostics-data"><?php echo $diagnosticJson ?: '{}'; ?></script>
        </section>
    </div>
</div>
