<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Charts\WeatherChart;
use GuzzleHttp\Client;
use App\Models\PostPredict;

use App\Models\Province;
use App\Models\Regency;

use Carbon\Carbon;

class PostController extends Controller
{
    public function index()
    {
        $title = '';
        if(request('category')){
            $category = Category::firstWhere('slug', request('category'));
            $title = ' in ' . $category->name;
        }

        if(request('author')){
            $author = User::firstWhere('username', request('author'));
            $title =  ' by ' . $author->name;
        }
    }

    public function home()
    {
        return view('home', [
            "title" => "Home",
            "active" => 'home',
        ]);
    }

    public function maps(Request $request)
    {
        $provinces = Province::all();
        $selectedProvinsi = $request->query('provinsi');
        $selectedKabupaten = $request->query('kabupaten');
        
        $postsQuery = Post::query();
        if ($selectedProvinsi) {
            $postsQuery->where('provinsi', $selectedProvinsi);
        }
        $posts = $postsQuery->get();
    
        $title = 'Maps';
        $active = 'maps';
    
        $startDate = $request->query('start_date', now()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->addDays(7)->format('Y-m-d'));        
    
        $geojsonData = $this->loadGeojsonData($selectedProvinsi, $selectedKabupaten);
        $colorMapping = [];
    
        // Fetch initial FWI data for the current date
        $currentFWIResponse = Http::post('https://forestfirepredictionidn.cloud/predict/api/fwi-data-map', [
            'date' => now()->format('Y-m-d'),
        ]);
    
        if ($currentFWIResponse->successful()) {
            $fwiData = $currentFWIResponse->json();
            foreach ($fwiData as $entry) {
                $alt_name = $entry['name'];
                $fwi = $entry['FWI'];
    
                if ($fwi < 1) {
                    $colorMapping[$alt_name] = '#0E7AD1';
                } elseif ($fwi < 6) {
                    $colorMapping[$alt_name] = '#00FF00';
                } elseif ($fwi < 13) {
                    $colorMapping[$alt_name] = '#FFFF00';
                } else {
                    $colorMapping[$alt_name] = '#FF0000';
                }
            }
        }
    
        return view('maps', compact('provinces', 'title', 'active', 'selectedProvinsi', 'selectedKabupaten', 'startDate', 'endDate', 'geojsonData', 'colorMapping'));
    }
    

    private function loadGeojsonData($selectedProvinsi, $selectedKabupaten)
    {
        $filePath = public_path('geojson/Full/alldata.geojson');

        if ($selectedProvinsi) {
            $filePath = public_path("geojson/provinces/{$selectedProvinsi}.geojson");
        }
        if ($selectedKabupaten) {
            $regencyId = substr($selectedKabupaten, 2);
            $filePath = public_path("geojson/regencies/{$selectedProvinsi}.{$regencyId}.geojson");
        }

        if (File::exists($filePath)) {
            $contents = File::get($filePath);
            return json_decode($contents, true);
        }

        return [];
    }

    public function getFWIDataRange(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedProvinsi = $request->input('provinsi');
        $selectedKabupaten = $request->input('kabupaten');

        $response = Http::post('https://forestfirepredictionidn.cloud/predict/api/fwi-data-all', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'provinsi' => $selectedProvinsi,
            'kabupaten' => $selectedKabupaten,
        ]);

        if ($response->successful()) {
            $fwiData = $response->json();

            return response()->json([
                'status' => 'success',
                'data' => $fwiData
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No data found or an error occurred.'
            ]);
        }
    }

    public function getFWIDataCurrent(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));  // Default ke hari ini jika tanggal tidak disediakan
    
        $response = Http::post('https://forestfirepredictionidn.cloud/predict/api/fwi-data-current', [
            'date' => $date,
        ]);
    
        if ($response->successful()) {
            $fwiData = $response->json();
            $colorMapping = [];
            $colorid = [];  // Tambahkan inisialisasi untuk colorid
    
            foreach ($fwiData as $entry) {
                $alt_name = $entry['name'];
                $id = $entry['kabupaten_id'];
                $fwi = $entry['FWI'];
    
                if ($fwi < 1) {
                    $colorMapping[$alt_name] = '#0E7AD1';
                } elseif ($fwi < 6) {
                    $colorMapping[$alt_name] = '#00FF00';
                } elseif ($fwi < 13) {
                    $colorMapping[$alt_name] = '#FFFF00';
                } else {
                    $colorMapping[$alt_name] = '#FF0000';
                }
    
                if ($fwi < 1) {
                    $colorid[$id] = '#ADD8E6';
                } elseif ($fwi < 6) {
                    $colorid[$id] = '#00FF00';
                } elseif ($fwi < 13) {
                    $colorid[$id] = '#FFFF00';
                } else {
                    $colorid[$id] = '#FF0000';
                }
            }
    
            return response()->json([
                'status' => 'success',
                'colorMapping' => $colorMapping,
                'colorKabupaten' => $colorid,
                'data' => $fwiData
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No data found or an error occurred.'
            ]);
        }
    }

    

    


public function history(Request $request, WeatherChart $weatherChart)
{
    $provinces = Province::all();
    $selectedProvinsi = $request->query('provinsi');
    $selectedKabupaten = $request->query('kabupaten');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Fetch data from Flask backend
    $response = Http::get('https://forestfirepredictionidn.cloud/predict/api/history', [
        'provinsi' => $selectedProvinsi,
        'kabupaten' => $selectedKabupaten,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);

    $data = $response->json();

    // Extract data for charts
    $chartLabels = array_column($data, 'date');
    $temperatureData = array_column($data, 'temperature');
    $temperaturePredictData = array_column($data, 'temperature_predict');
    $humidityData = array_column($data, 'humidity');
    $humidityPredictData = array_column($data, 'humidity_predict');
    $rainfallData = array_column($data, 'rainfall');
    $rainfallPredictData = array_column($data, 'rainfall_predict');
    $windspeedData = array_column($data, 'windspeed');
    $windspeedPredictData = array_column($data, 'windspeed_predict');

    // Build weather charts
    $temperatureChart = !empty($temperatureData) ? $weatherChart->buildWeatherChart($temperatureData, $temperaturePredictData, $chartLabels, 'Temperature') : null;
    $humidityChart = !empty($humidityData) ? $weatherChart->buildWeatherChart($humidityData, $humidityPredictData, $chartLabels, 'Humidity') : null;
    $rainfallChart = !empty($rainfallData) ? $weatherChart->buildWeatherChart($rainfallData, $rainfallPredictData, $chartLabels, 'Rainfall') : null;
    $windspeedChart = !empty($windspeedData) ? $weatherChart->buildWeatherChart($windspeedData, $windspeedPredictData, $chartLabels, 'Windspeed') : null;
    
    $title = 'History';
    $active = 'history';

    return view('history', compact('provinces', 'temperatureChart', 'humidityChart', 'rainfallChart', 'windspeedChart', 'title', 'active', 'selectedProvinsi', 'selectedKabupaten', 'startDate', 'endDate'));
}




public function getkota(Request $request)
{
    $id_provinsi = $request->id_provinsi;

    // Make a POST request to the API
    $response = Http::post('https://forestfirepredictionidn.cloud/predict/api/getkota', [
        'id_provinsi' => $id_provinsi,
    ]);

    // Decode the JSON response into an associative array
    $kabupatens = $response->json();

    // Ensure that kabupatens is an array and not null
    if (is_array($kabupatens)) {
        return response()->json($kabupatens);
    } else {
        return response()->json([], 200);
    }
}



    public function getkota2(request $request)
    {

        $id_provinsi = $request->id_provinsi;

        $response = Http::post('https://forestfirepredictionidn.cloud/predict/api/getkota', [
            'id_provinsi' => $id_provinsi,
        ]);

        $kabupatens = Regency::where('province_id', $id_provinsi)->get();

        $option = "<option>-- Kabupaten/Kota --</option>";
        foreach($kabupatens as $kabupaten){
            $option.= "<option value='$kabupaten->id'>$kabupaten->name</option>";
        }
        echo $option;
    }
}
