<?php
$artist = empty($_GET['artist']) ? null : htmlspecialchars($_GET['artist']);
$track  = empty($_GET['track'])  ? null : htmlspecialchars($_GET['track']);
$size   = empty($_GET['size'])   ? null : strtolower($_GET['size']);

if (empty($artist)) {
    exit('Please set artist name');
}

if (empty($track)) {
    exit('Please set track name');
}

// Settings and primary classes
include('config.incl.php');
include('cache.class.php');
include('muscover.class.php');

$Api   = new MusCover();
$Cache = new Cache();

if (!$Api->CheckCoverSize($size)) {
    exit('Size is not recognized. Try: ' . implode(', ', $Api->GetCoverSizes()));
};

// Try to search data in cache
$cacheKey = md5("track|{$artist}|{$track}");
$data     = $Cache->Load($cacheKey);

if ($data) {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $data[$size]);
    exit();
}

// Search data at last.fm and store it in cache
$data = $Api->SearchByTrack($artist, $track);
$Cache->Save($cacheKey, $data);
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . $data[$size]);
