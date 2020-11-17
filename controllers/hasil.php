<?php

namespace controllers;

use lib\mvc\controller\basecontroller;
use models\quran;
use models\word;

class hasil extends BaseController {
    protected function index() {
        $this->titlePage = "Hasil";
        $this->alias = "hasil";
        $this->controllerName = "hasil";
        if (empty($this->id) || $this->id == "1-"){
            $scope = '';
            $page = 1;
        }else{
            $page = explode("_", $this->id)[0];
            $scope = strtolower(explode("_", $this->id)[1]);
        }
        $viewModel = word::getWords($scope);
        $this->RenderView($viewModel,'public');
    }
    
    protected function ayat() {
        $this->titlePage = "Ayat";
        $this->alias = "ayat";
        $this->controllerName = "hasil";
        $scope = explode("_", $this->id);
        $surat = strtolower($scope[0]);
        $ayat = strtolower($scope[1]);
        $viewModel = quran::getQuranAyat($surat, $ayat);
        $this->RenderView($viewModel,'public');
    }
    
    protected function surat() {
        $this->titlePage = "Surat";
        $this->alias = "surat";
        $this->controllerName = "hasil";
        $surat = $this->id;
        $viewModel = quran::getQuranSurat($surat);
        $this->RenderView($viewModel,'public');
    }
    
    public function getQuran(){
        return quran::getQuran();
    }
    
    public function normalisasi($query){
        return quran::normalisasi($query);
    }
    
    public function normalisasiArab($query, $batas){
        return quran::normalisasiArab($query, $batas);
    }
    
    public function doSoundex($query){
        return quran::doSoundex($query);
    }
    
    public function getNextAyat($surat, $ayat){
        return quran::getNextAyat($surat, $ayat);
    }
    
    public function getPrevAyat($surat, $ayat){
        return quran::getPrevAyat($surat, $ayat);
    }
}
