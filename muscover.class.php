<?php

class MusCover {
    private $ApiKey             = LASTFM_API_KEY;
    private $AllowedCoverSizes  = array(
        'small'      => true,
        'medium'     => true,
        'large'      => true,
        'extralarge' => true
    );

    function __contruct() {
        return true;
    }

    /**
     * Make request to last.fm api
     * @param string $method Method name
     * @param array $params Associative array of parameters for selected method
     *
     * @return mixed Server answer or NULL in case of connection error
     */
    private function LoadFromApi($method, $params) {
        $url = "http://ws.audioscrobbler.com/2.0/?method={$method}&api_key={$this->ApiKey}&format=json";
        foreach($params as $k => $v) {
            $url .= '&' . $k . '='. rawurlencode($v);
        }

        // Request to last.fm api
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 3);
        curl_setopt($ch, CURLOPT_REFERER, 'http://muscover.veikus.com/');
        $data = curl_exec($ch);
        curl_close($ch);

        if (empty($data)) {
            return false;
        }

        return json_decode($data, 1);
    }

    /**
     * Check is selected cover size is correct
     * @param string $size Cover size
     *
     * @return bool Is cover size correct
     */
    public function CheckCoverSize($size) {
        return array_key_exists($size, $this->AllowedCoverSizes);
    }

    /**
     * Get allowed cover sizes
     *
     * @return array List of allowed sizes
     */
    public function GetCoverSizes() {
        return array_keys($this->AllowedCoverSizes);
    }

    /**
     * Search with artist and track name
     * @param string $artist Artist name
     * @param string $track Track name
     *
     * @return array Covers in all sizes
     */
    public function SearchByTrack($artist, $track) {
        $data = $this->LoadFromApi('track.getInfo', array('artist' => $artist, 'track' => $track, 'autocorrect' => 1));

        // Default dummy images
        $images = array(
            'small'         => 'http://muscover.veikus.com/dummy/no_album_small.jpg',
            'medium'        => 'http://muscover.veikus.com/dummy/no_album_medium.jpg',
            'large'         => 'http://muscover.veikus.com/dummy/no_album_large.jpg',
            'extralarge'    => 'http://muscover.veikus.com/dummy/no_album_extralarge.jpg',
        );

        if (empty($data['track']['album']['image'])) {
            return $images;
        }

        // Fill array with real images
        foreach($data['track']['album']['image'] as $v) {
            $size = $v['size'];
            $url  = $v['#text'];

            if (!empty($url) && array_key_exists($size, $images)) {
               $images[$size] = $v['#text'];
            }
        }

        return $images;
    }

    /**
     * Search album cover with artist and album name
     * @param string $artist Artist name
     * @param string $album Album name
     *
     * @return array Covers in all sizes
     */
    public function SearchByAlbum($artist, $album) {
        $data = $this->LoadFromApi('album.getInfo', array('artist' => $artist, 'album' => $album, 'autocorrect' => 1));

        // Default dummy images
        $images = array(
            'small'         => 'http://muscover.veikus.com/dummy/no_album_small.jpg',
            'medium'        => 'http://muscover.veikus.com/dummy/no_album_medium.jpg',
            'large'         => 'http://muscover.veikus.com/dummy/no_album_large.jpg',
            'extralarge'    => 'http://muscover.veikus.com/dummy/no_album_extralarge.jpg',
        );

        if (empty($data['album']['image'])) {
            return $images;
        }

        // Fill array with real images
        foreach($data['album']['image'] as $v) {
            $size = $v['size'];
            $url  = $v['#text'];

            if (!empty($url) && array_key_exists($size, $images)) {
                $images[$size] = $v['#text'];
            }
        }

        return $images;
    }


    /**
     * Search artist photo with artist name
     * @param string $artist Artist name
     *
     * @return array Photos in all sizes
     */
    public function SearchByArtist($artist) {
        $data = $this->LoadFromApi('artist.getInfo', array('artist' => $artist, 'autocorrect' => 1));

        // Default dummy images
        $images = array(
            'small'         => 'http://muscover.veikus.com/dummy/no_artist_small.jpg',
            'medium'        => 'http://muscover.veikus.com/dummy/no_artist_medium.jpg',
            'large'         => 'http://muscover.veikus.com/dummy/no_artist_large.jpg',
            'extralarge'    => 'http://muscover.veikus.com/dummy/no_artist_extralarge.jpg',
        );

        if (empty($data['artist']['image'])) {
            return $images;
        }

        // Fill array with real images
        foreach($data['artist']['image'] as $v) {
            $size = $v['size'];
            $url  = $v['#text'];

            if (!empty($url) && array_key_exists($size, $images)) {
                $images[$size] = $v['#text'];
            }
        }

        return $images;
    }
}