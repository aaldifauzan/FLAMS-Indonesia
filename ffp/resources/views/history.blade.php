@extends('layouts.main')

@section('container')
<div class="container mt-4">

    @if(session()->has('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form id="historyForm" action="{{ route('history') }}" method="GET">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="provinsi">Provinsi</label>
                <select class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" required>
                    <option value="" @if(old('provinsi') == '') selected @endif>-- Provinsi --</option>
                    @foreach ($provinces as $provinsi)
                        <option value="{{ $provinsi->id }}" @if(old('provinsi') == $provinsi->id || $selectedProvinsi == $provinsi->id) selected @endif>
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
                        @foreach ($provinces->find($selectedProvinsi)->regencies as $kabupaten)
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

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="start_date">Tanggal Awal:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') ?? $startDate }}" required>
            </div>
            <div class="form-group col-md-6">
                <label for="end_date">Tanggal Akhir:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') ?? $endDate }}" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-4">Submit</button>
    </form>

    @if(request()->hasAny(['provinsi', 'kabupaten', 'start_date', 'end_date']))
        @if(isset($noData) && $noData)
            <div class="alert alert-warning" role="alert">
                Data tidak tersedia untuk periode dan wilayah yang dipilih.
            </div>
        @else
            @isset($temperatureChart)
                <div class="chart-container p-6 m-20 bg-white rounded shadow">
                    {!! $temperatureChart->container() !!}
                </div>
                <script src="{{ $temperatureChart->cdn() }}"></script>
                {{ $temperatureChart->script() }}
            @endisset

            @isset($humidityChart)
                <div class="chart-container p-6 m-20 bg-white rounded shadow mt-4">
                    {!! $humidityChart->container() !!}
                </div>
                <script src="{{ $humidityChart->cdn() }}"></script>
                {{ $humidityChart->script() }}
            @endisset

            @isset($rainfallChart)
                <div class="chart-container p-6 m-20 bg-white rounded shadow mt-4">
                    {!! $rainfallChart->container() !!}
                </div>
                <script src="{{ $rainfallChart->cdn() }}"></script>
                {{ $rainfallChart->script() }}
            @endisset

            @isset($windspeedChart)
                <div class="chart-container p-6 m-20 bg-white rounded shadow mt-4">
                    {!! $windspeedChart->container() !!}
                </div>
                <script src="{{ $windspeedChart->cdn() }}"></script>
                {{ $windspeedChart->script() }}
            @endisset
        @endif
    @endif
</div>

@endsection

@section('styles')
<style>
    .chart-container {
        margin-bottom: 50px; /* Increase this value for more spacing */
    }
</style>
@endsection
