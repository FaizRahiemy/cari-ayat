<?php

namespace lib\mvc\controller;

use models\coretanmodel;
use models\kreasimodel;
use models\bisnismodel;
use models\komentar;
use models\pengguna;
use models\information;

abstract class basecontroller {
    protected $urlParams;
    protected $action;
    protected $id;
    public $titlePage;
    public $alias;
    public $metakey = "quran";
    public $metadesc = "Pencarian Ayat Alquran";
    public $controllerName;
    public $baseUrl = "http://localhost/ta_ulfa/";
//    public $baseUrl = "http://192.168.100.2/ta_ulfa/";

    public function __construct($action, $urlParams, $id) {
        $this->action = $action;
        $this->urlParams = $urlParams;
        $this->id = $id;
        @ob_start();
        if(session_status()!=PHP_SESSION_ACTIVE) session_start();
        //DATE
        date_default_timezone_set("Asia/Jakarta");
    }

    public function ExecuteAction() {
        return $this->{$this->action}();
    }

    protected function RenderView($viewModel, $type, $fullView = true) {
        $classData = explode("\\", get_class($this));
        $className = end($classData);
        $content = __DIR__ . "/../../../views/public/" . $className . "/" . $this->action . ".php";
        if ($fullView) {
            require __DIR__ . "/../../../views/public/layout/layout.php";
        } else {
            require $content;
        }
    }
} 