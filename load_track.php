<?php
$allowedSizes   = array('small', 'medium', 'large', 'extralarge');

$artist         = empty($_GET['artist'])    ? null : htmlspecialchars($_GET['artist']);
$track          = empty($_GET['track'])     ? null : htmlspecialchars($_GET['track']);
$size           = empty($_GET['size'])      ? null : strtolower($_GET['size']);

if (!in_array($size, $allowedSizes)) {
    exit('Size is not recognized. Try: ' . implode(', ', $allowedSizes));
}

if (empty($artist)) {
    exit('Please set artist name');
}

if (empty($track)) {
    exit('Please set track name');
}

// Try to search data in cache
include('config.incl.php');
include('cache.class.php');
$cacheKey = md5("track|{$artist}|{$track}");
$Cache    = new Cache();
$data     = $Cache->Load($cacheKey);

if ($data) {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $data[$size]);
    exit();
}

// Search data at last.fm and store it in cache
include('muscover.class.php');
$Api = new MusCover();

$data = $Api->SearchByTrack($artist, $track);
$Cache->Save($cacheKey, $data);
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . $data[$size]);