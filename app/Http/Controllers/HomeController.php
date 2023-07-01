<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    function upload_files(Request $request)
    {
        try {
            Log::alert('RequestData: ' . print_r($request->all(), true));
            if ($request->hasFile('file')) {
                $this->save_file($request->file('file'));
                return response()->json("File found", 200);
            }
            throw new \Exception('File not found');
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 200);
        }
    }

    private function save_file(\Illuminate\Http\UploadedFile $file)
    {
        $path = Storage::putFileAs('public/db/downloads',  $file, $file->getBasename());
        Log::warning('FilePath: ==> ' . url($path));
    }

    function save_file_contents_in_db($json_file_contents)
    {
        // using DB:upsert to replace row if exists [should use PK as second argument]
        return DB::table('flights')->upsert([
            ['departure' => 'Oakland', 'destination' => 'San Diego', 'price' => 99],
            ['departure' => 'Chicago', 'destination' => 'New York', 'price' => 150]
        ], ['departure', 'destination'], ['price']);
    }
}
