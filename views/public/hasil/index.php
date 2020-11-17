<?php
    $hasil = $viewModel;
?>
<div id="hasil">
    <div class="logoHasil">
        <a href="<?php echo $this->baseUrl ?>"><img src="<?php echo $this->baseUrl ?>public/images/quran.png" alt="CariAyat" height="70" width="220"></a>
    </div>
    <div class="searchHasil">
        <input type="text" id="query" class="searchTerm" placeholder="Ketik Kata Kunci..." value="<?php echo explode("_", $this->id)[1]; ?>">
        <button id="cari" onclick="location.href='<?php echo $this->baseUrl ?>hasil/index/1_' + document.getElementById('query').value;" class="searchButton">
            Cari
        </button>
    </div>

    <div id="containerHasil">
        <?php
            $scope = explode("_", $this->id);
            $scope = strtolower($scope[1]);
            if ($scope != ""){
                echo "Query: ".$scope."<br>";
                $query = explode(" ", $scope);
                $fonetisQuery = [];
                for ($i = 0; $i < count($query); $i++){
                    $queryNormal = $this->normalisasi($query[$i]);
                    $fonetisQuery[] = $this->doSoundex($queryNormal);
                }
                echo "Kode Fonetis Query: ";
                foreach($fonetisQuery as $fonQue){
                    echo $fonQue . " ";
                }
                echo "<br><br>";
            }
        ?>
        Hasil Pencarian (Ditemukan <?php echo count($hasil); ?> kata):
        <div id="berkasHasil">
            <table style="width:100%">
                <tr>
                    <th style="width:250px; text-align:center">Surat</th>
                    <th style="width:150px; text-align:center">Ayat</th> 
                    <th style="width:150px; text-align:center">Kata Ke-</th> 
                    <th style="text-align:left; text-align:center">Isi</th>
                    <th style="width:100px; text-align:left; text-align:center;">Similarity</th>
                    <th style="width:150px; text-align:left; text-align:center;">Ke Ayat</th>
                </tr>
            <?php
                $page = explode("_", $this->id)[0];
                $offset = ($page - 1) * 10;
                $iterator = 0;
                if ($offset + 10 > count($hasil)){
                    $batas = count($hasil);
                }else{
                    $batas = $offset + 10;
                }
                for ($iterator = $offset; $iterator < $batas; $iterator++){
                    $quran = $hasil[$iterator];
//                    foreach ($hasil as $quran){
                    echo '
                    <tr>
                        <td style="text-align:center">QS:'.$quran->idSurat.' - '.$quran->surat.'</td>
                        <td style="text-align:center">Ayat '.$quran->ayat.'</td> 
                        <td style="text-align:center">'.$quran->urutan.'</td> 
                        <td style="text-align:center">'.$quran->isi.'</td> 
                        <td style="text-align:right; padding:10px">';
                        if ($scope != ""){
                            echo $quran->similarity.'%';
                        }else{
                            echo "-";
                        }
                        echo '</td>
                        <td style="text-align:center"><a href="'.$this->baseUrl.'hasil/ayat/'.$quran->idSurat.'_'.$quran->ayat.'"><img src="'.$this->baseUrl.'public/images/arrow.png" alt="Go To Ayat"></a></td> 
                    </tr>';
                }
            ?>
            </table>
            <?php
            $jumData = count($hasil);
            $jumPage = ceil($jumData/10);
            $noPage = explode("_", $this->id)[0];
            $scope = urlencode($scope);
            ?>
            <div id="paging">
                <?php 
                echo "<b>Halaman </b>".$noPage."/".$jumPage."<br><br>";
                if ($noPage > 1) echo  "<a href='".$this->baseUrl."hasil/index/".($noPage-1)."_".$scope."'><</a>";
                $showPage=0;
                for($page = 1; $page <= $jumPage; $page++){
                    if ((($page >= $noPage - 3) && ($page <= $noPage + 3)) || ($page == 1) || ($page == $jumPage)){   
                        if (($showPage == 1) && ($page != 2))  echo "..."; 
                        if (($showPage != ($jumPage - 1)) && ($page == $jumPage))  echo "...";
                        if ($page == $noPage) echo "<b>".$page."</b>";
                    else echo "<a href='".$this->baseUrl."hasil/index/".$page."_".$scope."'>".$page."</a>";
                    $showPage = $page;
                    }
                }	
                if ($noPage < $jumPage) echo "<a href='".$this->baseUrl."hasil/index/".($noPage+1)."_".$scope."'>></a>";
                echo "";
                ?>
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