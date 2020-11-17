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
                $surat = $this->id;
            ?>
            <div id="berkasHasil">
                <table style="width:100%">
                    <tr>
                        <th style="width:150px; text-align:center">Surat</th>
                        <th style="width:100px; text-align:center">Ayat</th> 
                        <th style="text-align:left; text-align:center">Isi</th>
                        <th style="width:150px; text-align:left; text-align:center;">Ke Ayat</th>
                    </tr>
                <?php
                    foreach($hasil as $quran){
                        echo '
                        <tr>
                            <td style="width:150px; text-align:center">'.$quran->surat.'</td>
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
                            <td style="text-align:center"><a href="'.$this->baseUrl.'hasil/ayat/'.$quran->idSurat.'_'.$quran->ayat.'"><img src="'.$this->baseUrl.'public/images/arrow.png" alt="Go To Ayat"></a></td> 
                        </tr>';   
                    }
                ?>
                </table>
                <div style="text-align:center">
                <?php
                if ($surat == 1){
                    $prev = 114;
                }else{
                    $prev = $surat-1;
                }
                if ($surat == 114){
                    $next = 1;
                }else{
                    $next = $surat+1;
                }
                echo '<br><a href="'.$this->baseUrl.'home/surat/" style="text-decoration:none; color:#fff; width: 300px; "><div style="background: #00B4CC; height: 75px; width: 300px; padding: 10px; margin: auto; line-height:75px; border-radius: 15px;">Lihat Daftar Surat</div></a>';
                echo '<br>';
                echo '<a href="'.$this->baseUrl.'hasil/surat/'.$prev.'"><img src="'.$this->baseUrl.'public/images/arrow.png" alt="Go To Surat" style="transform: rotate(180deg); margin-right:200px"></a>';
                echo '<a href="'.$this->baseUrl.'hasil/surat/'.$next.'"><img src="'.$this->baseUrl.'public/images/arrow.png" alt="Go To Surat"></a>';
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