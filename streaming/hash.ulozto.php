<?php

// Shared secret
define('STORAGE_SECRET', 'Krakonos666');

/**
 * Returns array with query params
 *
 * @param $path Path part of the url starting with / (e.g. /view/1234.mp4)
 * @param $expires Timestamp when the link expires (default: time() + 3600)
 */
function getParams($path, $expires = NULL, $ip = NULL) {
  if ($expires == NULL) $expires = time() + 3600;

  $hash = $expires . $path . STORAGE_SECRET . ($ip !== NULL ? $ip : '');
  $hash = md5($hash, TRUE);
  $hash = base64_encode($hash);
  $hash = str_replace([ '+', '/', '=' ], [ '-', '_', '' ], $hash);

  $result = [
    'expires' => $expires,
    'hash' => $hash,
  ];
  if ($ip) $result['ip'] = $ip;
  return $result;
}

$url = '...demo-url...';

list($server, $path) = explode('/', $url, 2);
var_dump($url . '?' . http_build_query(getParams('/' . $path, NULL, '84.42.180.3/25')));
