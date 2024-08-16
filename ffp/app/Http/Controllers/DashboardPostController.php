<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostPredict;
use App\Models\Fwi;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Imports\ProvinceImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Pagination\LengthAwarePaginator;


class DashboardPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    $provinceId = $request->input('provinsi');
    $regencyId = $request->input('kabupaten');
    
    $postsQuery = Post::where('user_id', auth()->user()->id);

    if ($provinceId) {
        $postsQuery->where('provinsi', $provinceId);
    }

    if ($regencyId && $regencyId != "-- Kabupaten/Kota --") {
        $postsQuery->where('kabupaten', $regencyId);
    }


    $posts = $postsQuery->get();
    
    return view('dashboard.posts.index', [
        'posts' => $posts,
        'provinces' => Province::all(),
        'regencies' => Regency::all(),
        'selectedProvince' => $provinceId,
        'selectedRegency' => $regencyId,
    ]);
}



public function importCSV()
{
    $provinces = Province::all();

    // Store the previous URL in the session
    session(['previous_url' => url()->previous()]);

    return view('dashboard.posts.importcsv', compact('provinces'));
}

public function handleCSVImport(Request $request)
{
    set_time_limit(0); // Set the maximum execution time to unlimited (0)

    $validatedData = $request->validate([
        'provinsi' => 'required',
        'kabupaten' => 'required',
        'csv_file' => 'required|file|mimes:csv,txt',
    ]);

    $file = $request->file('csv_file');

    try {
        $type = 'csv';
        Excel::import(new ProvinceImport(), $file, $type);

        // Get the previous URL from the session
        $previousUrl = session('previous_url', route('dashboard.posts.index'));

        // Redirect back to the previous URL with a success message
        return redirect($previousUrl)->with('success', 'CSV file has been imported successfully.');
    } catch (\Exception $e) {
        // Log the detailed error message for debugging purposes
        Log::error('Failed to import CSV file: ' . $e->getMessage());

        // Redirect back to the importcsv page with a short error message
        return redirect()->route('dashboard.posts.importcsv')->with('error', 'Failed to import CSV file. Please check the file format and data.');
    }
}

 

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $provinces = Province::all();
    
        // Store the previous URL in the session
        session(['previous_url' => url()->previous()]);
    
        return view('dashboard.posts.create', compact('provinces'));
    }

    public function getkabupaten(request $request)
    {
        $id_provinsi = $request->id_provinsi;

        $kabupatens = Regency::where('province_id', $id_provinsi)->get();

        $option = "<option>-- Kabupaten/Kota --</option>";
        foreach($kabupatens as $kabupaten){
            $option.= "<option value='$kabupaten->id'>$kabupaten->name</option>";
        }
        echo $option;
    }



    /**
     * Store a newly created resource in storage.
     */
/**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    $validatedData = $request->validate([
        'date' => 'required|date_format:d-m-Y',
        'provinsi' => 'required',
        'kabupaten' => 'required',
        'temperature' => 'required|numeric',
        'humidity' => 'required|numeric',
        'rainfall' => 'required|numeric',
        'windspeed' => 'required|numeric',
    ]);

    $validatedData['user_id'] = auth()->user()->id;

    // Convert the date to the correct format for the database
    $validatedData['date'] = date('Y-m-d', strtotime($request->date));

    // Create a new post with the validated data
    Post::create($validatedData);

    // Get the previous URL from the session
    $previousUrl = session('previous_url', '/dashboard/posts');

    // Redirect back to the previous URL with a success message
    return redirect($previousUrl)->with('success', 'New post has been added!');
}

    

    /**
     * Display the specified resource.
     */


public function show($provinceId, $regencyId, Request $request)
{
    $province = Province::find($provinceId);
    $regency = Regency::find($regencyId);

    if (!$province || !$regency) {
        abort(404);
    }

    $sortOrder = $request->input('sortOrder', 'desc');
    $sortColumn = $request->input('sortColumn', 'date');
    $orderDirection = $sortOrder === 'asc' ? 'asc' : 'desc';

    $postsQuery1 = Post::where('provinsi', $provinceId)->where('kabupaten', $regencyId)->orderBy($sortColumn, $orderDirection);
    $postsQuery2 = PostPredict::where('provinsi', $provinceId)->where('kabupaten', $regencyId)->orderBy($sortColumn, $orderDirection);
    $postsQuery3 = Fwi::where('provinsi', $provinceId)->where('kabupaten', $regencyId)->orderBy($sortColumn, $orderDirection);

    $posts1 = $postsQuery1->get();
    $posts2 = $postsQuery2->get();
    $posts3 = $postsQuery3->get();

    if ($posts1->isEmpty() && $posts2->isEmpty()) {
        return redirect()->route('dashboard.posts.index')->with('error', 'No data found for the specified province and regency.');
    }

    if ($posts1->isEmpty()) {
        $posts1 = $posts2;
    }

    $combinedPosts = $posts1->merge($posts2)->unique('date')->sortBy('date', SORT_REGULAR, $orderDirection === 'desc');

    $perPage = 50;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $currentItems = $combinedPosts->slice(($currentPage - 1) * $perPage, $perPage)->all();
    $paginatedCombinedPosts = new LengthAwarePaginator($currentItems, $combinedPosts->count(), $perPage);
    $paginatedCombinedPosts->setPath(route('dashboard.posts.show', ['province' => $provinceId, 'regency' => $regencyId, 'sortOrder' => $sortOrder, 'sortColumn' => $sortColumn]));

    return view('dashboard.posts.show', [
        'province' => $province,
        'regency' => $regency,
        'posts1' => $paginatedCombinedPosts,
        'posts2' => $posts2,
        'posts3' => $posts3,
        'sortOrder' => $sortOrder,
        'sortColumn' => $sortColumn,
    ]);
}







     
     
    
    




    public function train(Request $request)
    {
        $provinceId = $request->input('provinsi');
        $regencyId = $request->input('kabupaten');
    
        Log::info('Training with Province ID: ' . $provinceId . ' and Regency ID: ' . $regencyId);
    
        // Define the endpoint for the API request
        $endpoint = 'https://forestfirepredictionidn.cloud/predict/train';
    
        try {
            // Make the API request to Flask with increased timeout
            $response = Http::timeout(120)->post($endpoint, [
                'selectedProvinsi' => $provinceId,
                'selectedKabupaten' => $regencyId,
            ]);
    
            // Check if the response is successful
            if ($response->successful()) {
                $predictionData = $response->json();
    
                // Handle the prediction data as needed
                foreach ($predictionData as $date => $data) {
                    // Process the data, for example, store it in the database or display it in the view
                }
    
                return redirect()->back()->with('success', 'Prediction completed successfully.');
            } else {
                // Log the error message from the response
                $errorMessage = $response->json()['error'] ?? 'Unknown error';
                \Log::error('Prediction failed: ' . $errorMessage);
                return redirect()->back()->with('error', 'Prediction failed: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            // Log the exception message
            \Log::error('Prediction failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Prediction failed: ' . $e->getMessage());
        }
    }


public function forecast(Request $request)
{
    $provinceId = $request->input('selectedProvinsi');
    $regencyId = $request->input('selectedKabupaten');

    \Log::info('Received inputs for forecast - Province ID: ' . $provinceId . ', Regency ID: ' . $regencyId);

    $endpoint = 'https://forestfirepredictionidn.cloud/forecast';

    try {
        $response = Http::timeout(120)->get($endpoint, [
            'selectedProvinsi' => $provinceId,
            'selectedKabupaten' => $regencyId,
        ]);

        \Log::info('Sending request to API endpoint with data - Province ID: ' . $provinceId . ', Regency ID: ' . $regencyId);

        if ($response->successful()) {
            $predictionData = $response->json();
            return redirect()->back()->with('success', 'Forecast completed successfully.');
        } else {
            $errorMessage = $response->json()['error'] ?? 'Unknown error';
            $errorDetails = $response->json()['details'] ?? 'No additional details';
            \Log::error('Forecast failed: ' . $errorMessage . '. Details: ' . $errorDetails);
            return redirect()->back()->with('error', 'Forecast failed: ' . $errorMessage . '. Details: ' . $errorDetails);
        }
    } catch (\Exception $e) {
        \Log::error('Forecast failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Forecast failed: ' . $e->getMessage());
    }
}






    
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($provinceId, $regencyId, $date)
    {
        // Retrieve the province and regency based on IDs
        $province = Province::find($provinceId);
        $regency = Regency::find($regencyId);
        
        // Check if both province and regency exist
        if (!$province || !$regency) {
            abort(404); // Handle the case when either the province or regency is not found
        }
        
        // Fetch the post based on date, provinceId, and regencyId
        $post = Post::where('date', $date)
                    ->where('provinsi', $provinceId)
                    ->where('kabupaten', $regencyId)
                    ->first();
        
        // Check if the post exists
        if (!$post) {
            // Redirect back to the specific URL with an error message
            return redirect()->back()
                             ->with('error', 'No data found in posts for the specified date, province, and regency.');
        }
        
        // Fetch all provinces and regencies
        $provinces = Province::all();
        $regencies = Regency::all();
        
        return view('dashboard.posts.edit', [
            'province' => $province,
            'regency' => $regency,
            'post' => $post,
            'provinces' => $provinces,
            'regencies' => $regencies,
        ]);
    }
    
    
    
    
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $rules = [
            'date' => 'required|date_format:Y-m-d',
            'provinsi' => 'required',
            'kabupaten' => 'required',
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'rainfall' => 'required|numeric',
            'windspeed' => 'required|numeric',
        ];
    
        $validatedData = $request->validate($rules);
        $validatedData['user_id'] = auth()->user()->id;
    
        // Convert the date to the correct format for the database
        $validatedData['date'] = date('Y-m-d', strtotime($request->date));
    
        // Update the post based on the model
        $post->update($validatedData);
    
        return redirect()->route('dashboard.posts.show', ['province' => $post->provinsi, 'regency' => $post->kabupaten])
            ->with('success', 'Post has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($date, $provinsi, $kabupaten)
    {
        // Hapus entri terkait di PostPredict
        $relatedPostPredicts = PostPredict::where('date', $date)
                                          ->where('provinsi', $provinsi)
                                          ->where('kabupaten', $kabupaten)
                                          ->get();
    
        foreach ($relatedPostPredicts as $postPredict) {
            $postPredict->delete();
        }
    
        // Hapus entri terkait di Fwi
        $relatedFwis = Fwi::where('date', $date)
                          ->where('provinsi', $provinsi)
                          ->where('kabupaten', $kabupaten)
                          ->get();
    
        foreach ($relatedFwis as $fwi) {
            $fwi->delete();
        }
    
        // Hapus entri terkait di Post
        $relatedPosts = Post::where('date', $date)
                            ->where('provinsi', $provinsi)
                            ->where('kabupaten', $kabupaten)
                            ->get();
    
        foreach ($relatedPosts as $post) {
            $post->delete();
        }
    
        return redirect()->back()->with('success', 'Post and related data have been deleted!');
    }
    


    public function showRegenciesByProvince($provinceId)
    {
        $province = Province::find($provinceId);
    
        if (!$province) {
            abort(404); // Handle the case when the province is not found
        }
    
        $regencies = Regency::where('province_id', $provinceId)->get();
    
        return view('dashboard.posts.index_regency', [
            'province' => $province,
            'regencies' => $regencies,
        ]);
    }
}


