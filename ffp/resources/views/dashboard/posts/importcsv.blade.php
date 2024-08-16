@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Import CSV</h1>
</div>

@if(session()->has('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session()->has('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

<!-- CSV Import Form -->
<form method="post" action="{{ route('dashboard.posts.handleCSVImport') }}" class="mb-5" enctype="multipart/form-data">
    @csrf
    <div class="mb-3 row">
        <div class="form-group col-md-6">
            <label for="provinsi">Provinsi</label>
            <select class="form-control" id="provinsi" name="provinsi">
                <option value="">-- Provinsi --</option>
                @foreach ($provinces as $provinsi)
                    <option value="{{ $provinsi->id }}">{{ $provinsi->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-6">
            <label for="kabupaten">Kabupaten/Kota</label>
            <select class="form-control" id="kabupaten" name="kabupaten">
                <option value="">-- Kabupaten/Kota --</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label for="csv_file" class="form-label">CSV File</label>
        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
    </div>

    <button type="submit" class="btn btn-primary">Import CSV</button>
</form>

<script>
    document.getElementById('provinsi').addEventListener('change', function () {
        const provinsiId = this.value;
        const kabupatenSelect = document.getElementById('kabupaten');

        // Clear existing options
        kabupatenSelect.innerHTML = '<option value="">-- Kabupaten/Kota --</option>';

        if (provinsiId) {
            // Fetch and add kabupaten options based on the selected province using AJAX or another method
            // You might need to implement this logic based on your application's structure
            // For demonstration purposes, I'm leaving it as is.
        }
    });
</script>

@endsection
