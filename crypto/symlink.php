<?php

if (!$_REQUEST['from'] || !$_REQUEST['to']) exit('ERR');

$mapFile = './storage/symlinks.map';
$map = file_exists($mapFile) ? unserialize(file_get_contents($mapFile)) : [];
$map[$_REQUEST['to']] = $_REQUEST['from'];
file_put_contents($mapFile, serialize($map));
print 'OK';
