<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>
    

    <!-- Bootstrap datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <!-- Bootstrap and Bootstrap datepicker JavaScript (v5.0.2) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    {{-- Maps --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
    

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    {{-- My Style --}}
    <link rel="stylesheet" href="/css/style.css">

    <link rel="icon" href="{{ asset('img\favicon.ico') }}">



    <title>Forest Fire Prediction | {{ $title }}</title>
  </head>
  <body>
    
    @include('partials.navbar')

        @yield('container')


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

<script>
    $(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $('#provinsi').on('change', function() {
            let id_provinsi = $('#provinsi').val();
            let kabupatenDropdown = $('#kabupaten');
            
            // Reset the kabupaten dropdown to default
            kabupatenDropdown.empty();
            kabupatenDropdown.append($('<option></option>').attr('value', '').text('-- Kabupaten/Kota --'));

            // If a valid provinsi is selected, make an AJAX request
            if (id_provinsi) {
                $.ajax({
                    type: 'POST',
                    url: '/getkota',  // Sesuaikan dengan route API Anda
                    data: { id_provinsi: id_provinsi },
                    success: function(response) {
                        console.log(response);  // Tambahkan log untuk melihat respons dari server
                        if (Array.isArray(response)) {
                            // Populate the dropdown with response data
                            $.each(response, function(key, value) {
                                kabupatenDropdown.append($('<option></option>').attr('value', value.id).text(value.name));
                            });
                        } else {
                            console.error('Invalid response format:', response);
                        }
                    },
                    error: function(data) {
                        console.log('error:', data);
                    },
                });
            }
        });
    });
</script>


  </body>
</html>