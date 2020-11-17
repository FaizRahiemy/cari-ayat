<?php

namespace lib\mvc\model;

abstract class basemodel {
    public static function getDB() {
        return new \PDO("sqlite:dataset.db");
    }
}