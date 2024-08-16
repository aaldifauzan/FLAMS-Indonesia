<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="generator" content="Hugo 0.84.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Forest Fire Prediction | Dashboard</title>

    <!-- Bootstrap core CSS (v5.0.2) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Bootstrap datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- Bootstrap and Bootstrap datepicker JavaScript (v5.0.2) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <!-- Trix Editor -->
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <!-- Custom styles for this template -->
    <link href="/css/dashboard.css" rel="stylesheet">

    <link rel="icon" href="{{ asset('img\favicon.ico') }}">



    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <style>
        trix-toolbar [data-trix-button-group="file-tools"] {
            display: none;
        }
    </style>
</head>
<body>

@include('dashboard.layouts.header')

<div class="container-fluid">
    <div class="row">
        @include('dashboard.layouts.sidebar')

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            @yield('container')
        </main>
    </div>
</div>

<!-- Feather Icons -->
<script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script>

<!-- Dashboard JavaScript -->
<script src="/js/dashboard.js"></script>

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
                    url: "https://forestfirepredictionidn.cloud/predict/api/getkota",
                    contentType: "application/json",
                    data: JSON.stringify({ id_provinsi: id_provinsi }),
                    cache: false,
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
