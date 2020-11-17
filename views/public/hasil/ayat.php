<?php
    $hasil =  $viewModel;
?>
<div id="content">
    <div id="hasil">
        <div class="logoHasil">
            <a href="<?php echo $this->baseUrl ?>"><img src="<?php echo $this->baseUrl ?>public/images/quran.png" alt="CariAyat" height="70" width="220"></a>
        </div>
        <div class="searchHasil">
            <input type="text" id="query" class="searchTerm" placeholder="Ketik Ayat..." value="">
            <button id="cari" onclick="location.href='<?php echo $this->baseUrl ?>hasil/index/1_' + document.getElementById('query').value;" class="searchButton">
                Cari
            </button>
        </div>
        
        <div id="containerHasil">
            <?php
                $scope = explode("_", $this->id);
                $surat = strtolower($scope[0]);
                $ayat = strtolower($scope[1]);
            ?>
            <div id="berkasHasil">
                <table style="width:100%">
                    <tr>
                        <th style="width:150px; text-align:center">Surat</th>
                        <th style="width:100px; text-align:center">Ayat</th> 
                        <th style="text-align:left; text-align:center">Isi</th>
                    </tr>
                <?php
                        $quran = $hasil[0];
                        echo '
                        <tr>
                            <td style="width:150px; text-align:center"><a href="'.$this->baseUrl.'hasil/surat/'.$surat.'" style="text-decoration:none; color:#00B4CC; ">'.$quran->surat.'</a></td>
                            <td style="width:100px; text-align:center">Ayat '.$quran->ayat.'</td> 
                            <td>
                                <div id="nontab"><table style="width:100%; padding:10px;">
                                    <tr><td style="text-align:right; font-size:34px">';
                                        echo "<span style='float:right'>".$quran->arab.'</span>';
                                    echo '</td></tr>
                                    <tr><td style="text-align:left; padding:10px">';
                                        echo "<span style='float:right'>".$quran->isi.'</span>';
                                echo '</td></tr>
                                </table></div>
                            </td>
                        </tr>';
                ?>
                </table>
                <br>
                <?php
                    $words = explode(" ", $quran->isi);
                    $query = explode(" ", strtolower($quran->isi));
                    $fonetis = [];
                    for ($i = 0; $i < count($query); $i++){
                        $queryNormal = $this->normalisasi($query[$i]);
                        $fonetis[] = $this->doSoundex($queryNormal);
                    }
                ?>
                    
                Kata yang terkandung pada ayat ini (<?php echo count($words) ?> kata):<br>
                <table style="width:100%">
                    <tr>
                        <th style="width:150px; text-align:center">Kata Ke-</th> 
                        <th style="width:150px; text-align:left; text-align:center">Kode Fonetis</th>
                        <th style="text-align:left; text-align:center">Isi</th>
                        <th style="width:150px; text-align:left; text-align:center;">Cari Kata</th>
                    </tr>
                    
                <?php
                    for ($iterator = 0; $iterator < count($words); $iterator++){
                        $word = $words[$iterator];
                        $kode = $fonetis[$iterator];
                        echo '
                        <tr>
                            <td style="text-align:center">'.($iterator+1).'</td> 
                            <td style="text-align:center">'.$kode.'</td>
                            <td style="text-align:center">'.$word.'</td>
                            <td style="text-align:center"><a href="'.$this->baseUrl.'hasil/index/1_'.$word.'"><img src="'.$this->baseUrl.'public/images/arrow.png" alt="Go To Ayat"></a></td> 
                        </tr>';
                    }
                ?>
                </table>
                
                
                <div style="text-align:center">
                    <br>
                    <?php
                    echo '<a href="'.$this->baseUrl.'hasil/surat/'.$surat.'" style="text-decoration:none; color:#fff; width: 300px; "><div style="background: #00B4CC; height: 75px; width: 300px; padding: 10px; margin: auto; line-height:75px; border-radius: 15px;">Lihat QS. '.$quran->surat.'</div></a>';
                    echo '<br>';
                    $nextAyat = $this->getNextAyat($surat,$ayat)[0];
                    $prevAyat = $this->getPrevAyat($surat,$ayat)[0];
                    echo'
                    <table style="width:500px; margin:auto; background: #f2f2f2">
                        <tr>
                            <td style="background: #f2f2f2"><a href="'.$this->baseUrl.'hasil/ayat/'.$prevAyat->idSurat.'_'.$prevAyat->ayat.'"><img src="'.$this->baseUrl.'public/images/arrow.png" alt="Go To Ayat" style="transform: rotate(180deg); "></a></td>
                            <td style="background: #f2f2f2">Ganti Ayat</td>
                            <td style="background: #f2f2f2"><a href="'.$this->baseUrl.'hasil/ayat/'.$nextAyat->idSurat.'_'.$nextAyat->ayat.'"><img src="'.$this->baseUrl.'public/images/arrow.png" alt="Go To Ayat"></a></td>
                        </tr>
                    </table>
                    ';
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.getElementById('query').onkeypress=function(e){
        if(e.keyCode==13){
            document.getElementById('cari').click();
        }
    }
</script>