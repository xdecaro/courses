<?php
/** Publish built ZIPs into the repository update channel. */
$root = dirname(__DIR__);
$packageManifest = $root . '/package/pkg_decarocourses.xml';
$xml = simplexml_load_file($packageManifest);

if ($xml === false) {
    fwrite(STDERR, "Impossibile leggere il manifest del pacchetto.\n");
    exit(1);
}

$version = trim((string) $xml->version);

if (!preg_match('/^\d+\.\d+\.\d+(?:[-+][A-Za-z0-9.-]+)?$/', $version)) {
    fwrite(STDERR, "Versione non valida: {$version}\n");
    exit(1);
}

$componentSource = $root . "/dist/com_decarocourses_{$version}.zip";
$packageSource = $root . "/dist/pkg_decarocourses_{$version}.zip";

foreach ([$componentSource, $packageSource] as $file) {
    if (!is_file($file)) {
        fwrite(STDERR, "File di build mancante: {$file}\n");
        exit(1);
    }
}

$releaseDir = $root . "/releases/{$version}";
@mkdir($releaseDir, 0775, true);

$targets = [
    $componentSource => $releaseDir . "/com_decarocourses_{$version}.zip",
    $packageSource => $releaseDir . "/pkg_decarocourses_{$version}.zip",
];

foreach ($targets as $source => $target) {
    if (is_file($target)) {
        $oldHash = hash_file('sha256', $target);
        $newHash = hash_file('sha256', $source);

        if (!hash_equals($oldHash, $newHash)) {
            fwrite(STDERR, "La versione {$version} esiste gia con contenuto diverso. Incrementa la versione prima di pubblicare.\n");
            exit(1);
        }

        continue;
    }

    if (!copy($source, $target)) {
        fwrite(STDERR, "Impossibile pubblicare {$target}.\n");
        exit(1);
    }
}

$packageHash = hash_file('sha256', $packageSource);
$componentHash = hash_file('sha256', $componentSource);

file_put_contents(
    $releaseDir . '/SHA256SUMS.txt',
    "{$componentHash}  com_decarocourses_{$version}.zip\n{$packageHash}  pkg_decarocourses_{$version}.zip\n"
);

$updateXml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<updates>
  <update>
    <name>Courses</name>
    <description>School and course management for Joomla by xdecaro.</description>
    <element>pkg_decarocourses</element>
    <type>package</type>
    <client>site</client>
    <version>{$version}</version>
    <downloads>
      <downloadurl type="full" format="zip">https://raw.githubusercontent.com/xdecaro/courses/main/releases/{$version}/pkg_decarocourses_{$version}.zip</downloadurl>
    </downloads>
    <tags><tag>stable</tag></tags>
    <maintainer>Luca De Caro</maintainer>
    <maintainerurl>https://github.com/xdecaro/courses</maintainerurl>
    <targetplatform name="joomla" version="6\.[0-9]+" />
    <php_minimum>8.3.0</php_minimum>
    <sha256>{$packageHash}</sha256>
  </update>
</updates>
XML;

@mkdir($root . '/updates', 0775, true);
file_put_contents($root . '/updates/pkg_decarocourses.xml', $updateXml . "\n");

echo "Published Courses {$version}\n";
