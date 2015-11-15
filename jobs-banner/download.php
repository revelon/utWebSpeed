<?php
$zip = new ZipArchive();
$ret = $zip->open('./banners.zip', ZipArchive::OVERWRITE);
if ($ret !== TRUE) {
    printf('Failed with code %d', $ret);
} else {
    $directory = realpath('.');
    $options = array('add_path' => ' ', 'remove_path' => $directory);
    $zip->addPattern('/\.html$/', $directory, $options);
    $zip->close();
}

header('Content-Type: application/zip');
header('Content-disposition: attachment; filename=banners.zip');
header('Content-Length: ' . filesize('./banners.zip'));
readfile('./banners.zip');
