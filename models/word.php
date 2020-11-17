<?php

namespace models;

use lib\mvc\model\basemodel;
use models\quran;

class word extends basemodel {
    
    public $ayat;
    public $idSurat;
    public $surat;
    public $urutan;
    public $isi;
    public $fonetis;
    public $similarity;

    public function __construct($ayat, $idSurat, $surat, $urutan, $isi, $similarity) {
        $this->ayat = $ayat;
        $this->idSurat = $idSurat;
        $this->surat = $surat;
        $this->urutan = $urutan;
        $this->isi = $isi;
        $this->similarity = $similarity;
    }
    
    public static function sortBig($a, $b){ 
        if ($a->similarity == $b->similarity) {
            if ($a->idSurat == $b->idSurat){
                if ($a->ayat == $b->ayat){
                    if ($a->urutan == $b->urutan){
                        return 0;
                    }
                    return ($a->urutan < $b->urutan) ? -1 : 1;
                }
                return ($a->ayat < $b->ayat) ? -1 : 1;
            }
            return ($a->idSurat < $b->idSurat) ? -1 : 1;
        }
        return ($a->similarity > $b->similarity) ? -1 : 1;
    } 

    public static function getWords($scope) {
        $quran = quran::getQuran();
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
                
//                if ($ayat->id == 61){

                    $key = 0;
                    $similarity = 0;
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
                    $ayatKeyCampur = [];
                    $ubah = 0;
                    while ($key < count($similarAyatKey)){
                        $ayatKey = $similarAyatKey[$key];
                        $ayatPecah = $ayatLatin[$ayatKey];
                        $ayatPecah = str_split($ayatPecah);
                        $ayatCampur = array_merge($ayatCampur, $ayatPecah);
                        
                        if (count($ayatKeyCampur) > 0){
                            if ($ayatKey-1 != $ayatKeyCampur[count($ayatKeyCampur)-1]){
                                $ayatCampur = $ayatPecah;
                                $ayatKeyCampur = [];
                                $ubah = 0;
                            }
                        }
                        
//                        echo "-------------<br>-".$ayatKey."-<br>";
//                        echo $ayatLatin[$ayatKey].'<br>';
//                        echo "ubah : ".$ubah."<br>";
//                        echo "(".($ubah) ."<". count($query) .")<br>";
                        
                        if ($ubah < count($query)){
//                            echo "(".($key+1) ."<". count($query) .")<br>";
                            if ($key+1 < count($query)){
                                if ($key+1 < count($similarAyatKey)){
                                    if ($ayatKey+1 != $similarAyatKey[$key+1]){
//                                        echo ($ayatKey+1)."-".$similarAyatKey[$key+1].'<br>';
//                                        echo "masuk<br>";
                                        $ayatCampur = $ayatPecah;
                                        $ayatKeyCampur = [];
                                        $ubah = 0;
                                    }else{
                                        if (count($ayatKeyCampur) == 0){
//                                            echo "masuk2<br>";
                                            $ayatCampur = $ayatPecah;
                                            $ayatKeyCampur = [];
                                            $ayatKeyCampur[] = $ayatKey;
                                            $ubah++;
                                        }else{
//                                            echo "masuk3<br>";
                                            $ubah++;
                                        }
                                    }
                                }else{
//                                    echo "masuk6<br>";
                                }
                            }else{
                                if (count($ayatKeyCampur) == 0){
//                                    echo "masuk4<br>";
                                    $ayatCampur = $ayatPecah;
                                    $ayatKeyCampur = [];
                                    $ayatKeyCampur[] = $ayatKey;
                                    $ubah++;
                                }else{
//                                    echo "masuk5<br>";
                                    $ubah++;
                                }
                            }
                        }else{
                            $ayatCampur = $ayatPecah;
                            $ayatKeyCampur = [];
                            $ubah = 0;
//                            echo "masuk5<br>";
                        }
                        
                        $kataLimit = $kataPecahCampur;
                        
//                        foreach ($ayatKeyCampur as $akc){
//                            echo $akc."<br>";
//                        }

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
                        if ($similarityKata >= 45){
                            $result[] = new word($ayat->ayat, $ayat->idSurat, $ayat->surat, $ayatKey+1, $ayatLatin[$ayatKey], round($similarityKata, 2));
                        }

                        if (($ayatCampur != $ayatPecah) && ($ubah > 1)){
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
                            
//                            print_r($kataLimit);
//                            echo '<br>';
//                            print_r($ayatCampur);
                            
                            foreach($kataLimit as $kataHuruf){
                                $ayatSimilar[$keyAyat] = 0;
                                foreach($ayatCampur as $ayatHuruf){
//                                    echo  $kataHuruf." == ".$ayatHuruf."<br>";
                                    if ($kataHuruf == $ayatHuruf){
                                        $ayatSimilar[$keyAyat] += 1;
                                    }
                                }
//                            echo $ayatSimilar[$keyAyat];
                                $barisBawahKanan += pow($ayatSimilar[$keyAyat], 2);
                                $keyAyat++;
                            }

//                            echo $barisBawahKanan;
                            $barisBawahKanan = sqrt($barisBawahKanan);
                            $barisBawah = $barisBawahKiri * $barisBawahKanan;
                            $keySama = 0;
                            $barisAtas = (float) 0.0;
                            foreach($kataLimit as $kataHuruf){
                                $barisAtas += ($querySimilar[$keySama] * $ayatSimilar[$keySama]);
                                $keySama++;
                            }
                            $similarityKata = ($barisAtas/$barisBawah)*100;
                            if ($similarityKata >= 45){
                                if ($ubah > 0){
                                    $ayatKeyCampur[] = $similarAyatKey[$key];
                                }
                                $urutanke = ($ayatKeyCampur[0]+1) . "-" . ($ayatKeyCampur[count($ayatKeyCampur)-1]+1);
                                $isikata = "";
                                foreach($ayatKeyCampur as $akc){
//                                    echo $ayat->idSurat.'|'.$ayat->ayat."|".$akc."<br>";
                                    $isikata .= " " . $ayatLatin[$akc];
                                }
                                $result[] = new word($ayat->ayat, $ayat->idSurat, $ayat->surat, $urutanke, $isikata, round($similarityKata, 2));
                            }
                        }

                        $key++;
                    }
                    
                }
                
//            }
            usort($result, array('models\word','sortBig'));
        }else{
//            foreach($quran as $ayat){
//                $ayat->similarity = 0;
//                $result[] = $ayat;
//            }
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
