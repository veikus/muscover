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
        $url = "http://ws.audioscrobbler.com/2.0/?method={$method}&api_key={$this->ApiKey}&format=json&autocorrect=1";
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
        $data = $this->LoadFromApi('track.getInfo', array('artist' => $artist, 'track' => $track));

        // Default dumb images
        $images = array(
            'small'         => 'http://placehold.it/64x64',
            'medium'        => 'http://placehold.it/126x126',
            'large'         => 'http://placehold.it/174x174',
            'extralarge'    => 'http://placehold.it/300x300',
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
        $data = $this->LoadFromApi('album.getInfo', array('artist' => $artist, 'album' => $album));

        // Default dumb images
        $images = array(
            'small'         => 'http://placehold.it/64x64',
            'medium'        => 'http://placehold.it/126x126',
            'large'         => 'http://placehold.it/174x174',
            'extralarge'    => 'http://placehold.it/300x300',
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
        $data = $this->LoadFromApi('artist.getInfo', array('artist' => $artist));

        // Default dumb images
        $images = array(
            'small'         => 'http://placehold.it/64x64',
            'medium'        => 'http://placehold.it/126x126',
            'large'         => 'http://placehold.it/174x174',
            'extralarge'    => 'http://placehold.it/300x300',
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