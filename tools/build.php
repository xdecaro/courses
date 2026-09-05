<?php
/** Build com_decarocourses_1.0.0.zip and pkg_decarocourses_1.0.0.zip. */
$root = dirname(__DIR__);
$version = '1.0.0';
$dist = $root . '/dist';
$componentDir = $root . '/component';
$packageDir = $root . '/package';

if (!class_exists(ZipArchive::class)) {
    fwrite(STDERR, "ZipArchive non disponibile.\n");
    exit(1);
}

@mkdir($dist, 0775, true);

function zipDirectory(string $source, string $target): void {
    $zip = new ZipArchive();
    if ($zip->open($target, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException("Impossibile creare $target");
    }
    $source = realpath($source);
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file->isFile()) continue;
        $path = $file->getRealPath();
        $local = substr($path, strlen($source) + 1);
        $zip->addFile($path, str_replace(DIRECTORY_SEPARATOR, '/', $local));
    }
    $zip->close();
}

$componentZip = "$dist/com_decarocourses_$version.zip";
zipDirectory($componentDir, $componentZip);

$stage = sys_get_temp_dir() . '/pkg_decarocourses_' . bin2hex(random_bytes(4));
mkdir($stage . '/packages', 0775, true);
copy($packageDir . '/pkg_decarocourses.xml', $stage . '/pkg_decarocourses.xml');
copy($componentZip, $stage . "/packages/com_decarocourses_$version.zip");
$packageZip = "$dist/pkg_decarocourses_$version.zip";
zipDirectory($stage, $packageZip);

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($stage, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
foreach ($it as $file) { $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname()); }
rmdir($stage);

echo basename($componentZip) . "\n" . basename($packageZip) . "\n";
