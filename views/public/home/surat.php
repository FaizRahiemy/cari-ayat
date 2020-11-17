<?php
    $hasil = $viewModel;
?>
<div id="hasil">
    <div class="logoHasil">
        <a href="<?php echo $this->baseUrl ?>"><img src="<?php echo $this->baseUrl ?>public/images/quran.png" alt="CariAyat" height="70" width="220"></a>
    </div>
    <div class="searchHasil">
        <input type="text" id="query" class="searchTerm" placeholder="Ketik Kata Kunci..." value="">
        <button id="cari" onclick="location.href='<?php echo $this->baseUrl ?>hasil/index/1_' + document.getElementById('query').value;" class="searchButton">
            Cari
        </button>
    </div>

    <div id="containerHasil">
        Jumlah Surat (Ditemukan <?php echo count($hasil); ?> surat):
        <div id="berkasHasil">
            <table style="width:100%">
                <tr>
                    <th style="width:150px; text-align:center">Surat ke-</th>
                    <th style="text-align:center">Nama Surat</th> 
                    <th style="width:150px; text-align:center">Jumlah Ayat</th>
                    <th style="width:150px; text-align:left; text-align:center;">Ke Surat</th>
                </tr>
            <?php
                $page = $this->id;
                if ($this->id == ""){
                    $page = 1;
                }
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
                        <td style="text-align:center">'.$quran->id.'</td>
                        <td style="text-align:center">'.$quran->surat.'</td> 
                        <td style="text-align:center">'.$quran->jumlah.' ayat</td>
                        <td style="text-align:center"><a href="'.$this->baseUrl.'hasil/surat/'.$quran->id.'"><img src="'.$this->baseUrl.'public/images/arrow.png" alt="Go To Surat"></a></td> 
                    </tr>';
                }
            ?>
            </table>
            <?php
            $jumData = count($hasil);
            $jumPage = ceil($jumData/10);
            $noPage = $this->id;
            if ($this->id == ""){
                $noPage = 1;
            }
            ?>
            <div id="paging">
                <?php 
                echo "<b>Halaman </b>".$noPage."/".$jumPage."<br><br>";
                if ($noPage > 1) echo  "<a href='".$this->baseUrl."home/surat/".($noPage-1)."'><</a>";
                $showPage=0;
                for($page = 1; $page <= $jumPage; $page++){
                    if ((($page >= $noPage - 3) && ($page <= $noPage + 3)) || ($page == 1) || ($page == $jumPage)){   
                        if (($showPage == 1) && ($page != 2))  echo "..."; 
                        if (($showPage != ($jumPage - 1)) && ($page == $jumPage))  echo "...";
                        if ($page == $noPage) echo "<b>".$page."</b>";
                    else echo "<a href='".$this->baseUrl."home/surat/".$page."'>".$page."</a>";
                    $showPage = $page;
                    }
                }	
                if ($noPage < $jumPage) echo "<a href='".$this->baseUrl."home/surat/".($noPage+1)."'>></a>";
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