<?php

namespace models;

use lib\mvc\model\basemodel;

class quran extends basemodel {
    
    public $id;
    public $idSurat;
    public $ayat;
    public $surat;
    public $isi;
    public $arab;
    public $fonetis;
    public $similarity;
    public $similar;

    public function __construct($id, $idSurat, $ayat, $surat, $isi, $arab, $fonetis) {
        $this->id = $id;
        $this->idSurat = $idSurat;
        $this->ayat = $ayat;
        $this->surat = $surat;
        $this->isi = $isi;
        $this->arab = $arab;
        $this->fonetis = $fonetis;
    }
        
    public static function sortBig($a, $b) { 
        if($a->similarity == $b->similarity) {
            return 0;
        } 
        return ($a->similarity > $b->similarity) ? -1 : 1;
    } 

    public static function getQuranQuery($scope) {
        $quran = self::getQuran();
        $result = [];
        if ($scope != ""){
            $scope = str_replace("’", "'", $scope);//penghilangan petik
            $query = explode(" ", strtolower($scope));
            $fonetisQuery = [];
            for ($i = 0; $i < count($query); $i++){
                $queryNormal = self::normalisasi($query[$i]);
                $fonetisQuery[] = self::doSoundex($queryNormal);
            }
            
            $queryUnique = array_unique($query);
            
            $fonetisQueryUnique = array_unique($fonetisQuery);

            foreach($quran as $ayat){
                
                $ayat->similar = "";
                $ayatFonetis = $ayat->fonetis;
                $fonetisAyat = explode(" ", $ayatFonetis);
                $ayatLatin = explode(" ", strtolower($ayat->isi));

                $key = 0;
                $similarAyatKey = [];
                foreach($fonetisAyat as $kataAyat){
                    foreach($fonetisQueryUnique as $kataUnique){
                        if ($kataAyat == $kataUnique){
                            $similarAyatKey[] = $key;
                        }
                    }
                    $key++;
                }
                
//                if ($ayat->id == 381){

                    $key = 0;
                    $similarity = 0;
                    $similarityKey = -1;
                    $level = 0;
                    $similarityLevel = -1;
                    $kataPecahCampur = [];
                                
                    $scopeNoSpace = str_replace(' ', '', $scope);
                    $queryPecah = str_split($scopeNoSpace);

                    foreach($similarAyatKey as $ayatKey){
                        $ayatPecah = $ayatLatin[$ayatKey];
                        $ayatPecah = str_split($ayatPecah);

                        $kataPecah = array_merge($queryPecah, $ayatPecah);
                        $kataPecah = array_unique($kataPecah);
                        $kataPecahCampur = array_merge($kataPecahCampur,$kataPecah);
                        $kataPecahCampur = array_unique($kataPecahCampur);
                    }

                    $ayatCampur = [];
                    $ayatKeyCampur = "";
                    while ($key < count($similarAyatKey)){
                        $ayatKey = $similarAyatKey[$key];
                        $ayatPecah = $ayatLatin[$ayatKey];
                        $ayatPecah = str_split($ayatPecah);
                        $ayatCampur = array_merge($ayatCampur, $ayatPecah);

                        if ($key+1 < count($similarAyatKey)){
                            if ($ayatKey+1 != $similarAyatKey[$key+1]){
                                $ayatCampur = [];
                                $ayatKeyCampur = "";
                            }else{
                                if ($ayatKeyCampur == ""){
                                    $ayatKeyCampur = $ayatKey . "," . $similarAyatKey[$key+1];
                                }else{
                                    $ayatKeyCampur .= ',' . $similarAyatKey[$key+1];
                                }
                            }
                        }

                        $kataLimit = $kataPecahCampur;

                        $keyQuery = 0;
                        $querySimilar = [];
                        $barisBawahKiri = 0;
                        foreach ($kataLimit as $kataHuruf){
                            $querySimilar[$keyQuery] = 0;
                            foreach($queryPecah as $queryHuruf){
                                if ($queryHuruf == $kataHuruf){
                                    $querySimilar[$keyQuery] += 1;
                                }
                            }
                            $barisBawahKiri += pow($querySimilar[$keyQuery], 2);
                            $keyQuery++;
                        }
                        $barisBawahKiri = sqrt($barisBawahKiri);
                        $keyAyat = 0;
                        $ayatSimilar = [];
                        $barisBawahKanan = 0.0;
                        foreach($kataLimit as $kataHuruf){
                            $ayatSimilar[$keyAyat] = 0;
                            foreach($ayatPecah as $ayatHuruf){
                                if ($kataHuruf == $ayatHuruf){
                                    $ayatSimilar[$keyAyat] += 1;
                                }
                            }
                            $barisBawahKanan += pow($ayatSimilar[$keyAyat], 2);
                            $keyAyat++;
                        }

                        $barisBawahKanan = sqrt($barisBawahKanan);
                        $barisBawah = $barisBawahKiri * $barisBawahKanan;
                        $keySama = 0;
                        $barisAtas = (float) 0.0;
                        foreach($kataLimit as $kataHuruf){
                            $barisAtas += ($querySimilar[$keySama] * $ayatSimilar[$keySama]);
                            $keySama++;
                        }

                        $similarityKata = ($barisAtas/$barisBawah)*100;
                        if ($similarityKata > $similarity){
                            if ($level == 0){
                                $similarityLevel = $level;
                                $similarity = $similarityKata;
                                $ayat->similar = $ayatKey;

                                if (count($queryUnique) >= ($level+1)){
                                    $level++;
                                }
                            }
                        }

                        if (($ayatCampur != $ayatPecah) && (count($ayatCampur) > 0)){
                            $keyQuery = 0;
                            $querySimilar = [];
                            $barisBawahKiri = 0;
                            foreach ($kataLimit as $kataHuruf){
                                $querySimilar[$keyQuery] = 0;
                                foreach($queryPecah as $queryHuruf){
                                    if ($queryHuruf == $kataHuruf){
                                        $querySimilar[$keyQuery] += 1;
                                    }
                                }
                                $barisBawahKiri += pow($querySimilar[$keyQuery], 2);
                                $keyQuery++;
                            }
                            $barisBawahKiri = sqrt($barisBawahKiri);
                            $keyAyat = 0;
                            $ayatSimilar = [];
                            $barisBawahKanan = 0.0;
                            foreach($kataLimit as $kataHuruf){
                                $ayatSimilar[$keyAyat] = 0;
                                foreach($ayatCampur as $ayatHuruf){
                                    if ($kataHuruf == $ayatHuruf){
                                        $ayatSimilar[$keyAyat] += 1;
                                    }
                                }
                                $barisBawahKanan += pow($ayatSimilar[$keyAyat], 2);
                                $keyAyat++;
                            }

                            $barisBawahKanan = sqrt($barisBawahKanan);
                            $barisBawah = $barisBawahKiri * $barisBawahKanan;
                            $keySama = 0;
                            $barisAtas = (float) 0.0;
                            foreach($kataLimit as $kataHuruf){
                                $barisAtas += ($querySimilar[$keySama] * $ayatSimilar[$keySama]);
                                $keySama++;
                            }

                            $similarityKata = ($barisAtas/$barisBawah)*100;
                            if ($similarityKata > $similarity){
                                if ($level > $similarityLevel){
                                    $similarityLevel = $level;
                                    $similarity = $similarityKata;
                                    $ayat->similar = $ayatKeyCampur;
                                    if (count($queryUnique) >= ($level+1)){
                                        $level++;
                                    }
                                }
                            }
                        }

                        $key++;
                    }

                    if ($similarity >= 70){
                        $ayat->similarity = round($similarity, 2);
                        $result[] = $ayat;
                    }
                    
//                }
                
            }
            usort($result, array('models\quran','sortBig'));
        }else{
            foreach($quran as $ayat){
                $ayat->similarity = 0;
                $result[] = $ayat;
            }
        }

        return $result;
    }

    public static function getQuran() {
        $query = self::getDB()->prepare("SELECT * FROM `dataset`");
        $query->execute();
        
        $result = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $ayats = explode(" ", strtolower($row["isiAyat"]));
            $fonetis = [];
            for ($i = 0; $i < count($ayats); $i++){
                $normal = self::normalisasi($ayats[$i]);
//                echo $row['id'] . "<br> ";
                $fonetis[] = self::doSoundex($normal);
            }
            
            $kodeFonetis = "";
            for ($i = 0; $i < count($fonetis); $i++){
                $kodeFonetis .= $fonetis[$i];
                if ($i != count($fonetis)-1){
                    $kodeFonetis .= " ";
                }
            }
            
            $result[] = new quran($row["id"], $row["idSurat"], $row["ayat"], $row["surat"], $row["isiAyat"], $row["arabAyat"], $kodeFonetis);
        }

        return $result;
    }

    public static function getQuranAyat($surat, $ayat) {
        $query = self::getDB()->prepare("SELECT * FROM `dataset` WHERE idSurat='".$surat."' AND ayat='".$ayat."'");
        $query->execute();
        
        $result = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = new quran($row["id"], $row["idSurat"], $row["ayat"], $row["surat"], $row["isiAyat"], $row["arabAyat"], "");
        }

        return $result;
    }

    public static function getNextAyat($surat, $ayat) {
        $query = self::getDB()->prepare("SELECT * FROM `dataset` WHERE idSurat='".$surat."' AND ayat='".$ayat."'");
        $query->execute();
        
        $result = [];
        $id = "";
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            if ($row["id"] < 1204){
                $id = $row["id"]+1;
            }else{
                $id = 0;
            }
        }
        
        $query = self::getDB()->prepare("SELECT * FROM `dataset` WHERE id='".$id."'");
        $query->execute();
        
        $result = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = new quran($row["id"], $row["idSurat"], $row["ayat"], $row["surat"], $row["isiAyat"], $row["arabAyat"], "");
        }

        return $result;
    }

    public static function getPrevAyat($surat, $ayat) {
        $query = self::getDB()->prepare("SELECT * FROM `dataset` WHERE idSurat='".$surat."' AND ayat='".$ayat."'");
        $query->execute();
        
        $result = [];
        $id = "";
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            if ($row["id"] > 1){
                $id = $row["id"]-1;
            }else{
                $id = 1204;
            }
        }
        
        $query = self::getDB()->prepare("SELECT * FROM `dataset` WHERE id='".$id."'");
        $query->execute();
        
        $result = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = new quran($row["id"], $row["idSurat"], $row["ayat"], $row["surat"], $row["isiAyat"], $row["arabAyat"], "");
        }

        return $result;
    }

    public static function getQuranSurat($surat) {
        $query = self::getDB()->prepare("SELECT * FROM `dataset` WHERE idSurat='".$surat."'");
        $query->execute();
        
        $result = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = new quran($row["id"], $row["idSurat"], $row["ayat"], $row["surat"], $row["isiAyat"], $row["arabAyat"], "");
        }

        return $result;
    }
    
    public function normalisasi($ayats){
        $ayats = str_replace("\\b'([^aiu])", "k$1", $ayats);//normalisasi huruf ain mati '[^aiu] menjadi K
        $ayats = str_replace("\\b`([^aiu])", "k$1", $ayats);//normalisasi huruf hamzah mati '[^aiu] menjadi K
        
        $ayats = str_replace("\\bal`", "", $ayats);//penghilangan al` hamzah hidup
        $ayats = str_replace("'", "", $ayats);//penghilangan petik
        $ayats = str_replace("`", "", $ayats);//penghilangan petik
                
        $ayats = str_replace("\\b(a)l([t,s,d,z,r,d,l,n])", "$1$2",  $ayats);//alif lam syamsiah
        $ayats = str_replace("\\b([^aiu][aiu])l([t,s,d,z,r,d,l,n])", "$1$2",  $ayats);
                //.replaceAll("\\b(a)[^aiu]", "$1",  $ayats);//penghilangan al tasdid,doubel alrr,alshsh
                
        $ayats = str_replace("\\biyy", "i",  $ayats);
        $ayats = str_replace("kh","h",  $ayats);
        $ayats = str_replace("sh", "s",  $ayats); //referensi Tabel 3 paper IPB dan 
        $ayats = str_replace("ts", "s",  $ayats);//pemadanan aksara latin arab-indo kemenag
        $ayats = str_replace("sy", "s",  $ayats);
        $ayats = str_replace("dz", "z",  $ayats);
        $ayats = str_replace("zh", "z",  $ayats);              
        $ayats = str_replace("dh", "d",  $ayats);
        $ayats = str_replace("th", "t",  $ayats);
        $ayats = str_replace("q", "k",  $ayats);
        $ayats = str_replace("aw", "au",  $ayats);
        $ayats = str_replace("ay", "ai",  $ayats);
                
        $ayats = str_replace("v","f",  $ayats);
        $ayats = str_replace("p","f",  $ayats);
        $ayats = str_replace("j","z",  $ayats);
                
        $ayats = str_replace("ng", "n",  $ayats);//ikhfa
        $ayats = str_replace("nb", "mb",  $ayats);//iqlab
        $ayats = str_replace("ny", "y",  $ayats);//idgham
        $ayats = str_replace("nw", "w",  $ayats);//idgham
        $ayats = str_replace("nm", "m",  $ayats);//idgham
        $ayats = str_replace("nn", "n",  $ayats);//idgham
        $ayats = str_replace("nl", "l",  $ayats);//idgham
        $ayats = str_replace("nr", "r",  $ayats);//idgham
        
        return $ayats;
    }
    
    public function doSoundex($normal){
        $encode = self::encode($normal);
        $noRepeat = self::deleteRepeats($encode);
        $fonetis = $normal[0] . substr($noRepeat, 1);
        $fonetis = str_replace("/", "", $fonetis) . "***";
        $fonetis = substr($fonetis, 0, 4);
            
        return $fonetis;
    }
    
    public function encode($normal){
        $encode = "";
        for ($i = 0; $i < strlen($normal); $i++){
            //referensi TA Sukma Rahadian string matching soundex dan metaphone 113030012 (2007)
            if($normal[$i] == 'a'|| $normal[$i] == 'e'|| $normal[$i] == 'i'|| $normal[$i] == 'o'|| $normal[$i] == 'u'|| $normal[$i] == 'h'|| $normal[$i] == 'y')
            	$encode .= "/";
            if($normal[$i] == 'b'|| $normal[$i] == 'p')
            	$encode .= "0";
            if($normal[$i] == 'c'|| $normal[$i] == 'j'|| $normal[$i] == 's'|| $normal[$i] == 'x'|| $normal[$i] == 'z')
            	$encode .= "1";
            if($normal[$i] == 'd')
            	$encode .= "2";
            if($normal[$i] == 'f' || $normal[$i]=='v')
            	$encode .= "3";
            if($normal[$i] == 'g'|| $normal[$i] == 'k' || $normal[$i] =='q')
            	$encode .= "4";
            if($normal[$i] == 'l')
            	$encode .= "5";   
            if($normal[$i] == 'm')
            	$encode .= "6";
            if($normal[$i] == 'n')
            	$encode .= "7";
            if($normal[$i] == 'r')
            	$encode .= "8";
            if($normal[$i] == 't')
            	$encode .= "9";
            if($normal[$i] == 'w')
            	$encode .= "w";
        }
        return $encode;
    }
    
    public function deleteRepeats($encode){
        $temp = $encode[0] . "";
        
    	if(strlen($encode) != 1){
	    	for($i = 0; $i < strlen($encode)-1; $i++){
	    		if($encode[$i] != $encode[$i+1]){	// return when initial letter is not repeating anymore
	    			$temp .= $encode[$i+1] . "";
                }
	    	}
	    	return $temp;
    	}else{
	    	return $encode; // default; return input
        }
    }
    
    public function normalisasiArab($query, $batas){
        $ayatArab = explode(" ", $query);
        $ayatBatas = "";
        for ($i = 0; $i < $batas; $i++){
            $ayatBatas .= $ayatArab[$i] . " ";
        }
        $ayatBatasPecah = explode(" ", $ayatBatas);
        $ayatNormalisasi = str_replace(" ۚ ", " ", $ayatBatas);
        $ayatNormalisasi = str_replace(" ۗ ", " ", $ayatNormalisasi);
        $ayatNormalisasi = str_replace(" ۖ ", " ", $ayatNormalisasi);
        $ayatNormalisasi = str_replace(" ۛ ", " ", $ayatNormalisasi);
        $ayatNormalisasiPecah = explode(" ", $ayatNormalisasi);
//        foreach ($ayatNormalisasiPecah as $ay){
//            echo $ay . "<br>";
//        }
//        echo "<br>".$ayatNormalisasi . "<br>-<br>" . $query. "<br>-";
        $selisih = abs(count($ayatBatasPecah) - count($ayatNormalisasiPecah));
        
        return $selisih;
    }
}
