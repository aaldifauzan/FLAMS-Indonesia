@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Input Data Parameter</h1>
</div>

<div class="">
    <form id="postForm" method="post" action="/dashboard/posts" class="mb-5" enctype="multipart/form-data">
        @csrf
        <div class="mb-3 row">
            <div class="">
                <label for="date" class="form-label">Date</label>
                <div class="input-group date">
                    <input type="text" class="form-control @error('date') is-invalid @enderror" id="date" name="date" required readonly placeholder="dd-mm-yyyy" value="{{ old('date') }}">
                    @error('date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <!-- Hidden input to store the date in Y-m-d format -->
                <input type="hidden" id="formatted_date" name="formatted_date">
            </div>

            <div class="form-group">
                <label for="exampleFormControlSelect1">Provinsi</label>
                <select class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" required>
                    <option value="" @if(old('provinsi') == '') selected @endif>-- Provinsi --</option>
                    @foreach ($provinces as $provinsi)
                        <option value="{{ $provinsi->id }}" @if(old('provinsi') == $provinsi->id) selected @endif>
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


            <div class="form-group">
                <label for="exampleFormControlSelect2">Kabupaten/Kota</label>
                <select class="form-control @error('kabupaten') is-invalid @enderror" id="kabupaten" name="kabupaten">
                    <option value="" selected>-- Kabupaten/Kota --</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="temperature" class="form-label">Temperature</label>
            <input type="text" class="form-control @error('temperature') is-invalid @enderror" id="temperature" name="temperature" required autofocus value="{{ old('temperature') }}">
            @error('temperature')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="rainfall" class="form-label">Rainfall</label>
            <input type="text" class="form-control @error('rainfall') is-invalid @enderror" id="rainfall" name="rainfall" required autofocus value="{{ old('rainfall') }}">
            @error('rainfall')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="humidity" class="form-label">Humidity</label>
            <input type="text" class="form-control @error('humidity') is-invalid @enderror" id="humidity" name="humidity" required autofocus value="{{ old('humidity') }}">
            @error('humidity')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="windspeed" class="form-label">Windspeed</label>
            <input type="text" class="form-control @error('windspeed') is-invalid @enderror" id="windspeed" name="windspeed" required autofocus value="{{ old('windspeed') }}">
            @error('windspeed')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Create Data</button>
    </form>
</div>

<!-- Include jQuery and Bootstrap Datepicker scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>

<style>
    /* Custom CSS to change the color of the readonly input field */
    input[readonly] {
        background-color: #ffffff !important;
        cursor: pointer;
    }
</style>

<script>
    $(document).ready(function(){
        $('#date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true
        });

        $('#postForm').on('submit', function (event) {
            const dateInput = document.getElementById('date');
            const dateValue = dateInput.value;

            if (dateValue) {
                const dateParts = dateValue.split('-');
                if (dateParts.length === 3) {
                    const formattedDate = `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
                    document.getElementById('formatted_date').value = formattedDate;
                }
            }
        });
    });
</script>
@endsection
