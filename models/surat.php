<?php

namespace models;

use lib\mvc\model\basemodel;

class surat extends basemodel {
    
    public $id;
    public $surat;
    public $jumlah;

    public function __construct($id, $surat, $jumlah) {
        $this->id = $id;
        $this->surat = $surat;
        $this->jumlah = $jumlah;
    }

    public static function getSurat() {
        $query = self::getDB()->prepare("SELECT idSurat, surat, count(id) FROM 'dataset' GROUP BY idSurat ORDER BY idSurat;");
        $query->execute();
        
        $result = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = new surat($row["idSurat"], $row["surat"], $row["count(id)"]);
        }

        return $result;
    }
}
