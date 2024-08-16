@extends('layouts.main')

@section('container')
    <form action="{{ route('maps') }}" method="GET">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="provinsi">Provinsi</label>
                <select class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" required>
                    <option value="" @if(!$selectedProvinsi) selected @endif>-- Provinsi --</option>
                    @foreach ($provinces as $provinsi)
                        <option value="{{ $provinsi->id }}" @if($selectedProvinsi == $provinsi->id) selected @endif>
                            {{ $provinsi->name }}
                        </option>
                    @endforeach
                </select>
                @error('provinsi')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="kabupaten">Kabupaten/Kota</label>
                <select class="form-control @error('kabupaten') is-invalid @enderror" id="kabupaten" name="kabupaten">
                    <option value="" @if(!$selectedKabupaten) selected @endif>-- Kabupaten/Kota --</option>
                    @if($selectedProvinsi)
                        @foreach ($provinces->find($selectedProvinsi)->regencies ?? [] as $kabupaten)
                            <option value="{{ $kabupaten->id }}" @if($selectedKabupaten == $kabupaten->id) selected @endif>
                                {{ $kabupaten->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('kabupaten')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div id="map"></div>

    <style>
        #map { height: 500px; }
    </style>
<script>
    var map = L.map('map').setView([-1.269160, 117.825264], 5);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Get the selected province ID from the form
    var selectedProvinsi = "{{ $selectedProvinsi }}";

    // Tentukan jalur geojson berdasarkan input pengguna
    var geojsonPaths = [];

    if (selectedProvinsi) {
        // Ambil semua data kabupaten yang dimulai dengan ID provinsi yang dipilih
        for (var i = 1; i <= 80; i++) {
            // Format nomor kabupaten dengan dua digit
            var regencyId = ("00" + i).slice(-2);
            // Bentuk jalur sesuai dengan pola yang diinginkan
            geojsonPaths.push("/geojson/regencies/" + selectedProvinsi + "." + regencyId + ".geojson");
        }
    }

    // Fungsi untuk menambahkan GeoJSON ke peta
    function addGeoJsonToMap(geojson) {
        L.geoJson(geojson).addTo(map);
    }

    // Fungsi untuk mengambil data GeoJSON
    function fetchData(path) {
        fetch(path)
            .then(response => response.json())
            .then(data => {
                addGeoJsonToMap(data);
            })
            .catch(error => {
                console.error('Error fetching geojson data:', error);
            });
    }

    // Ambil semua file GeoJSON yang sesuai dengan pola yang ditentukan
    geojsonPaths.forEach(path => {
        fetchData(path);
    });
</script>





@endsection
