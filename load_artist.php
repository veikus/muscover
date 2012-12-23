<?php
$artist = empty($_GET['artist']) ? null : htmlspecialchars($_GET['artist']);
$size   = empty($_GET['size'])   ? null : strtolower($_GET['size']);

if (empty($artist)) {
    exit('Please set artist name');
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
$cacheKey = md5("artist|{$artist}");
$data     = $Cache->Load($cacheKey);

if ($data) {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $data[$size]);
    exit();
}

// Search data at last.fm and store it in cache
$data = $Api->SearchByArtist($artist, $track);
$Cache->Save($cacheKey, $data);
header('HTTP/1.1 301 Moved Permanently');
header('Location: ' . $data[$size]);
