<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
                $this->upload_file($request->file('file'));
                return response()->json("File found", 200);
            }
            throw new \Exception('File not found');
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 200);
        }
    }

    private function upload_file(\Illuminate\Http\UploadedFile $file)
    {
       $path = Storage::putFileAs('public/db/downloads',  $file, $file->getBasename());
       Log::warning('FilePath: ==> '.url($path));
    }
}
