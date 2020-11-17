<?php

namespace controllers;

use lib\mvc\controller\basecontroller;
use models\surat;

class home extends BaseController {
    protected function index() {
        $this->titlePage = "Home";
        $this->alias = "";
        $this->controllerName = "Home";
        $viewModel = "";
        $this->RenderView($viewModel,'public');
    }
    
    protected function surat() {
        $this->titlePage = "Daftar Surat";
        $this->alias = "";
        $this->controllerName = "Home";
        $viewModel = surat::getSurat();;
        $this->RenderView($viewModel,'public');
    }
}
