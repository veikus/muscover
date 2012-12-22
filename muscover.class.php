<?php

class MusCover {
    private $ApiKey = LASTFM_API_KEY;

    function __contruct() {
        return true;
    }

    /**
     * Выполняем запрос к api last.fm
     * @param string $method Название метода
     * @param array $params Набор параметров для выбранного метода
     *
     * @return mixed Ответ сервера или null в случае ошибки
     */
    private function LoadFromApi($method, $params) {
        $url = "http://ws.audioscrobbler.com/2.0/?method={$method}&api_key={$this->ApiKey}&format=json&autocorrect=1";
        foreach($params as $k => $v) {
            $url .= '&' . $k . '='. rawurlencode($v);
        }

        $data = file_get_contents($url); // TODO (Артем): Переделать на curl
        if (!$data) {
            return false;
        }

        return json_decode($data, 1);
    }

    /**
     * Выполняем поиск по треку
     * @param string $artist Имя исполнителя
     * @param string $track Название трека
     *
     * @return string Ссылка на картинку альбома (или заглушку)
     */
    public function SearchByTrack($artist, $track) {
        $data = $this->LoadFromApi('track.getInfo', array('artist' => $artist, 'track' => $track));

        // Набор стандартных заглушек
        $images = array(
            'small'         => 'http://placehold.it/64x64',
            'medium'        => 'http://placehold.it/126x126',
            'large'         => 'http://placehold.it/174x174',
            'extralarge'    => 'http://placehold.it/300x300',
        );

        if (empty($data['track']['album']['image'])) {
            return $images;
        }

        // Заполняем список картинок
        foreach($data['track']['album']['image'] as $v) {
            $size = $v['size'];

            if (array_key_exists($size, $images)) {
               $images[$size] = $v['#text'];
            }
        }

        return $images;
    }
}