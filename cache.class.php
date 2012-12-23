<?php
class Cache {
    private $CacheTable  = MYSQL_TABLE;
    private $DbConnected = false;
    private $Db;

    /**
     * Конструктор, выполняет подключение к БД
     */
    function __construct() {
        if (MYSQL_HOST == '') {
            return false;
        }

        $this->Db = new Mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB);

        if ($this->Db->connect_error) {
            return false;
        }

        $this->DbConnected = true;
        $this->Db->set_charset('UTF8');
        return true;
    }

    /**
     * Выполняем поиск по кешу в БД
     * @param string $key Ключ по которому будет выполнен поиск
     *
     * @return mixed Массив с набором картинок или false если ничего не найдено
     */
    public function Load($key) {
        if (!$this->DbConnected) {
            return false;
        }

        $query = $this->Db->query("SELECT value FROM {$this->CacheTable} WHERE id = '{$key}' AND expire > NOW();");
        return $query->num_rows ? json_decode(end($query->fetch_row()), 1) : false;
    }

    /**
     * Сохраняем знаечение в кеш
     * @param string $key Ключ
     * @param mixed $value Значение
     *
     * @return boolean Сохранено ли значение
     */
    public function Save($key, $value) {
        if (!$this->DbConnected) {
            return false;
        }

        $value = $this->Db->real_escape_string(json_encode($value));

        $this->Db->query("INSERT INTO {$this->CacheTable} SET id = '{$key}', value = '{$value}', expire = NOW() + INTERVAL 1 WEEK;");
        if ($this->Db->affected_rows) {
            return true;
        } else {
            $this->Db->query("UPDATE {$this->CacheTable} SET value = '{$value}', expire = NOW() + INTERVAL 1 WEEK WHERE id = '{$key}';");
            return !!$this->Db->error;
        }
    }
}