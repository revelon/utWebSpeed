<?php

// Shared secret
define('STORAGE_SECRET', 'Krakonos666');

/**
 * Returns array with query params
 *
 * @param $path Path part of the url starting with / (e.g. /view/1234.mp4)
 * @param $expires Timestamp when the link expires (default: time() + 3600)
 */
function getParams($path, $expires = NULL) {
  if ($expires == NULL) $expires = time() + 3600;

  $hash = $expires . $path . STORAGE_SECRET;
  $hash = md5($hash, TRUE);
  $hash = base64_encode($hash);
  $hash = str_replace([ '+', '/', '=' ], [ '-', '_', '' ], $hash);

  return [
    'expires' => $expires,
    'hash' => $hash,
  ];
}

$url = 'storage1.ulozto.srw.cz/view/2/n05U7mF9msY0H3jhPGUbRhEmFK8Tv2FVaT6QSPvcqKC9OIvYSz2hVAi3R.mp4';
$url = 'storage1.ulozto.srw.cz/view/13/xnNSvOiA1uGmH2JteRH37UBGiq6uPZSjBiP6uyVSOGToPI6ETwxc9JhDx.mp4';
$url = 'storage1.ulozto.srw.cz/view/13/e6oSGZijyTwvu6oSzPHOvsEki2zuW0SrqiKDuMAfn3cZgF6WToPIarszK.vtt';

list($server, $path) = explode('/', $url, 2);
var_dump('http://' . $url . '?' . http_build_query(getParams('/' . $path)));
