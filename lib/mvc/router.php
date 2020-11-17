<?php

namespace lib\mvc;

class router {
    private $controller;
    private $action;
    private $id;
    private $urlParams;

    private $Controller_Namespace = "\\controllers\\";
    private $Base_Controller_Name = "lib\\mvc\\controller\\basecontroller";

    public function __construct($urlParams) {
        $this->urlParams = $urlParams;
        $i = 0;
        if (empty($this->urlParams["controller"])) {
            $this->controller = $this->Controller_Namespace . "home";
        } else {
            $this->controller = $this->Controller_Namespace . $this->urlParams["controller"];
        }

        if (empty($this->urlParams["action"])) {
            $this->action = "index";
        } else {
            $this->action = $this->urlParams["action"];
        }

        if (empty($this->urlParams["id"])) {
            $this->id = "";
        } else {
            $this->id = $this->urlParams["id"];
        }
    }

    public function getController() {
        $home = "http://localhost/ta_ulfa/";
        if (class_exists($this->controller)) {
            $parent = class_parents($this->controller);
            if (in_array($this->Base_Controller_Name, $parent)) {
                if (method_exists($this->controller, $this->action)) {
                    return new $this->controller($this->action, $this->urlParams, $this->id);
                } else {
                    header("Location: ".$home);
                    die();
                }
            } else {
                throw new \Exception("Wrong class for controller. Not derived from BaseController.");
            }
        } else {
            header("Location: ".$home);
            die();
        }
    }
} 
