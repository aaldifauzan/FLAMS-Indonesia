@extends('dashboard.layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Detail Data di {{ $province->name }} - {{ $regency->name }}</h1>
</div>

@if(session('error'))
<div class="alert alert-danger" role="alert">
    {{ session('error') }}
</div>
@endif

@if(session('success'))
<div class="alert alert-success" role="alert">
    {{ session('success') }}
</div>
@endif

<style>
    .sort-link {
        color: inherit; /* Inherits the color from parent */
        text-decoration: none; /* Removes the underline */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sort-link:hover {
        color: inherit; /* Inherits the color from parent */
        text-decoration: none; /* Removes the underline */
    }
    .sort-icon {
        margin-left: 5px;
    }
    th {
        background-color: #28a745; /* Background color hijau */
        color: white; /* Text color putih */
        text-align: center; /* Align text to the center */
        vertical-align: middle; /* Vertically align text to the middle */
    }
</style>

 

<div class="mb-3 d-flex justify-content-between align-items-center">
    <div class="btn-group" role="group">
        <form action="/dashboard/posts/create" method="GET" class="d-inline">
            <button type="submit" class="btn btn-primary me-2">Input Data Harian</button>
        </form>

        <form action="{{ route('dashboard.posts.importcsv') }}" method="GET" class="d-inline">
            <button type="submit" class="btn btn-success me-2">Import CSV</button>
        </form>
    </div>

    <div class="btn-group" role="group">
        <form action="{{ route('train') }}" method="POST" class="d-inline me-2">
            @csrf
            <input type="hidden" name="provinsi" value="{{ $province->id }}">
            <input type="hidden" name="kabupaten" value="{{ $regency->id }}">
            <button type="submit" class="btn btn-danger">Predict</button>
        </form>

        <form action="{{ route('forecast') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="selectedProvinsi" value="{{ $province->id }}">
            <input type="hidden" name="selectedKabupaten" value="{{ $regency->id }}">
            <button type="submit" class="btn btn-danger">Forecast</button>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th scope="col" class="text-center px-1" style="width: 4%;">No.</th>
                <th scope="col" class="text-center px-1" style="width: 5%; white-space: nowrap; padding-right: 10px;">
                    <a href="{{ route('dashboard.posts.show', ['province' => $province->id, 'regency' => $regency->id, 'sortOrder' => $sortOrder === 'asc' ? 'desc' : 'asc']) }}" class="sort-link">
                        Date
                        @if($sortColumn === 'date')
                            <span class="sort-icon">
                                <i class="fa fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}" style="color: white; line-height: 0.1;"></i>
                            </span>
                        @endif
                    </a>
                </th>
                <th scope="col" class="text-center px-1">Temperature</th>
                <th scope="col" class="text-center px-1">Temperature Predict</th>
                <th scope="col" class="text-center px-1">Humidity</th>
                <th scope="col" class="text-center px-1">Humidity Predict</th>
                <th scope="col" class="text-center px-1">Rainfall</th>
                <th scope="col" class="text-center px-1">Rainfall Predict</th>
                <th scope="col" class="text-center px-1">Windspeed</th>
                <th scope="col" class="text-center px-1">Windspeed Predict</th>
                <th scope="col" class="text-center px-1">FFMC</th>
                <th scope="col" class="text-center px-1">DMC</th>
                <th scope="col" class="text-center px-1">DC</th>
                <th scope="col" class="text-center px-1">ISI</th>
                <th scope="col" class="text-center px-1">BUI</th>
                <th scope="col" class="text-center px-1">FWI</th>
                <th scope="col" class="text-center px-1">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts1 as $post1)
                @php
                    $postPredict = $posts2->where('date', $post1->date)
                                          ->where('provinsi', $post1->provinsi)
                                          ->where('kabupaten', $post1->kabupaten)
                                          ->first();
                    $fwi = $posts3->where('date', $post1->date)
                                  ->where('provinsi', $post1->provinsi)
                                  ->where('kabupaten', $post1->kabupaten)
                                  ->first();
                @endphp
                <tr>
                    <td class="text-center px-1">{{ $loop->iteration }}</td>
                    <td class="text-center px-1" style="white-space: nowrap;">{{ $post1->date }}</td>
                    <td class="text-center px-1">{{ isset($post1->temperature) ? round($post1->temperature, 2) : '-' }}</td>
                    <td class="text-center px-1">{{ $postPredict ? round($postPredict->temperature_predict, 2) : '-' }}</td>
                    <td class="text-center px-1">{{ isset($post1->humidity) ? round($post1->humidity, 2) : '-' }}</td>
                    <td class="text-center px-1">{{ $postPredict ? round($postPredict->humidity_predict, 2) : '-' }}</td>
                    <td class="text-center px-1">{{ isset($post1->rainfall) ? round($post1->rainfall, 2) : '-' }}</td>
                    <td class="text-center px-1">{{ $postPredict ? round($postPredict->rainfall_predict, 2) : '-' }}</td>
                    <td class="text-center px-1">{{ isset($post1->windspeed) ? round($post1->windspeed, 2) : '-' }}</td>
                    <td class="text-center px-1">{{ $postPredict ? round($postPredict->windspeed_predict, 2) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->ffmc, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->dmc, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->dc, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->isi, 3) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->bui, 1) : '-' }}</td>
                    <td class="text-center px-1">{{ $fwi ? number_format($fwi->fwi, 5) : '-' }}</td>
                    <td class="text-center px-1">
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('dashboard.posts.edit', ['province_id' => $province->id, 'regency_id' => $regency->id, 'date' => $post1->date]) }}" class="badge bg-warning me-1">
                                <span data-feather="edit"></span>
                            </a>
                            <form action="{{ route('dashboard.posts.destroy', ['date' => $post1->date, 'provinsi' => $post1->provinsi, 'kabupaten' => $post1->kabupaten]) }}" method="POST" class="d-inline">
                                @method('delete')
                                @csrf
                                <button class="badge bg-danger border-0" onclick="return confirm('Are you sure?')">
                                    <span data-feather="x-circle"></span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $posts1->links() }}
</div>
@endsection