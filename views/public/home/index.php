<div id="main">
    <img src="<?php echo $this->baseUrl ?>public/images/quran.png" alt="CariAyat" height="130" width="450">
    <div class="search">
        <input type="text" id="query" class="searchTerm" placeholder="Ketik Kata Kunci...">
        <button id="cari" onclick="location.href='<?php echo $this->baseUrl ?>hasil/index/1_' + encodeURI(document.getElementById('query').value);" class="searchButton">
            Cari
        </button>
    </div>
    <div id="fitur" style="margin-top: 50px;">
        <b>Fitur Lainnya</b><br>
        <a href="<?php echo $this->baseUrl ?>home/surat">Lihat daftar surat</a>
    </div>
</div>
<script>
    document.getElementById('query').onkeypress=function(e){
        if(e.keyCode==13){
            document.getElementById('cari').click();
        }
    }
</script>