@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Data</h1>
</div>

@if(session()->has('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="col-lg-8">
    <form method="post" action="{{ route('posts.update', ['post' => $post->id]) }}" class="mb-5">
        @method('put')
        @csrf
        <div class="mb-3 row">
            <div class="col-md-6">
                <label for="date" class="form-label">Date</label>
                <div class="input-group date" id="datepicker">
                    <input type="text" class="form-control @error('date') is-invalid @enderror" placeholder="dd-mm-yyyy" id="date" name='date' readonly required autofocus value="{{ old('date', $post->date) }}">
                    @error('date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <label for="province" class="form-label">Province</label>
                <input type="text" class="form-control" readonly value="{{ $province->name }}">
                <input type="hidden" name="provinsi" value="{{ $province->id }}">
            </div>
            <div class="col-md-6">
                <label for="regency" class="form-label">Kabupaten/Kota</label>
                <input type="text" class="form-control" readonly value="{{ $regency->name }}">
                <input type="hidden" name="kabupaten" value="{{ $regency->id }}">
            </div>
        </div>
        <div class="mb-3">
            <label for="temperature" class="form-label">Temperature</label>
            <input type="text" class="form-control @error('temperature') is-invalid @enderror" id="temperature" name='temperature' required value="{{ old('temperature', $post->temperature) }}">
            @error('temperature')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="rainfall" class="form-label">Rainfall</label>
            <input type="text" class="form-control @error('rainfall') is-invalid @enderror" id="rainfall" name='rainfall' required value="{{ old('rainfall', $post->rainfall) }}">
            @error('rainfall')
            <div class="invalid-feedback">
                {{ $message }}
            @enderror
        </div>
        <div class="mb-3">
            <label for="humidity" class="form-label">Humidity</label>
            <input type="text" class="form-control @error('humidity') is-invalid @enderror" id="humidity" name='humidity' required value="{{ old('humidity', $post->humidity) }}">
            @error('humidity')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="windspeed" class="form-label">Windspeed</label>
            <input type="text" class="form-control @error('windspeed') is-invalid @enderror" id="windspeed" name='windspeed' required value="{{ old('windspeed', $post->windspeed) }}">
            @error('windspeed')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Update Data</button>
    </form>
</div>

@endsection
