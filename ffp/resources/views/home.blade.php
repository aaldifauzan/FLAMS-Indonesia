@extends('layouts.main')

@section('container')
    <div style="background-color: #121212; color: #ffffff;">
        <div class="home-image-container">
            <div class="home-dark-overlay"></div>
            <img src="{{ asset('img/home1.png') }}" alt="Kebakaran Hutan" class="home-image">
            <div class="home-overlay">
                <h1 class="home-title">Selamat Datang di FLAMS<br><strong>(Fire Land Alert and Monitoring System)</strong></h1>
                <p class="home-text">Bergabunglah dalam melindungi hutan kita dengan  prediksi kebakaran.</p>            
            </div>
        </div>

        <div class="website-discussion" style="margin: 40px 20px 110px 20px; background-color: #121212; color: #ffffff;">
            <h2>Tentang Situs Ini</h2>
            <p>Situs ini didedikasikan untuk memprediksi kebakaran hutan menggunakan metode teknologi canggih dan analisis data. Tujuan kami adalah membantu komunitas dan lembaga lingkungan untuk secara proaktif mengelola dan mengurangi risiko terkait kebakaran hutan.</p>
            <p>Komponen yang digunakan dalam model prediksi kami meliputi:</p>
            <div class="components-container">
                <div class="component">
                    <div>
                        <h3>FFMC</h3>
                        <p>Salah satu dari tiga komponen kode kelembaban bahan bakar dari sistem indeks cuaca kebakaran hutan Kanada (FWI)...</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-index91f7.html?page=Fine+fuel+moisture+code" class="btn btn-primary" target="_blank">Baca Lebih Lanjut</a>
                </div>
                <div class="component">
                    <div>
                        <h3>DMC</h3>
                        <p>Salah satu dari tiga komponen kode kelembaban bahan bakar dari sistem indeks cuaca kebakaran hutan Kanada (FWI)...</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-index9436.html?page=Duff+moisture+code" class="btn btn-primary" target="_blank">Baca Lebih Lanjut</a>
                </div>
                <div class="component">
                    <div>
                        <h3>DC</h3>
                        <p>Salah satu dari tiga komponen kode kelembaban bahan bakar dari sistem indeks cuaca kebakaran hutan Kanada (FWI)...</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-indexd5c6.html?page=Drought+code" class="btn btn-primary" target="_blank">Baca Lebih Lanjut</a>
                </div>
                <div class="component">
                    <div>
                        <h3>ISI</h3>
                        <p>Salah satu dari dua indeks menengah yang diperlukan untuk menghitung indeks cuaca kebakaran hutan Kanada (FWI)...</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-index4de6.html?page=Initial+spread+index" class="btn btn-primary" target="_blank">Baca Lebih Lanjut</a>
                </div>
                <div class="component">
                    <div>
                        <h3>BUI</h3>
                        <p>Salah satu dari dua indeks menengah yang diperlukan untuk menghitung indeks cuaca kebakaran hutan Kanada (FWI)...</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-index8720.html?page=Buildup+index" class="btn btn-primary" target="_blank">Baca Lebih Lanjut</a>
                </div>
                <div class="component">
                    <div>
                        <h3>FWI</h3>
                        <p>Indeks akhir dari sistem FWI...</p>
                    </div>
                    <a href="https://wikifire.wsl.ch/tiki-index259b.html?page=Fire+weather+index" class="btn btn-primary" target="_blank">Baca Lebih Lanjut</a>
                </div>
            </div>

            <div class="components-container" style="margin-top: 20px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div class="component">
                    <h2>Video Demo</h2>
                    <div style="position: relative; padding-bottom: 58%; height: 0; overflow: hidden; max-width: 100%; background: #000;">
                        <iframe src="https://www.youtube.com/embed/ESBJ-a6Ko-E" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
                    </div>
                    <div>
                        <p>Video ini memberikan gambaran umum tentang sistem prediksi kebakaran hutan kami, termasuk teknologi yang digunakan, sumber data, dan dampaknya pada komunitas dan lingkungan. Dengan memahami cara kerja sistem kami, Anda dapat melihat langkah-langkah yang kami ambil untuk memprediksi dan mengelola kebakaran hutan secara efektif.</p>
                        <p>Situs web ini berisi beberapa halaman: Home, History, Maps, Login, Dashboard, dll.</p>
                    </div>
                </div>
                <div class="component">
                    <h2>Flowchart FWI</h2>
                    <img src="{{ asset('img/Flowchart FWI-1.png') }}" alt="Flowchart FWI" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>

        <footer class="home-footer">
            <div class="footer-left">
                <div class="logo-container">
                    <img src="{{ asset('img/TelU.png') }}" alt="Logo" class="footer-logo">
                </div>
                <div class="logo-container">
                    <img src="{{ asset('img/BMKG.png') }}" alt="Logo Kedua" class="footer-logo">
                </div>
            </div>
            <div class="footer-right">
                <p><strong>Hubungi Kami:</strong> <br> Prediksi Kebakaran Hutan <br> Telp: 082289597498 <br> Email: forestfirepredictionelm@gmail.com</p>
            </div>
        </footer>
    </div>
@endsection
