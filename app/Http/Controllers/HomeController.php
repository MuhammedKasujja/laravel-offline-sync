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

    private function save_file_contents_in_db($json_file_contents)
    {
        // using DB:upsert to replace row if exists [should use PK as second argument]
        return DB::table('flights')->upsert([
            ['departure' => 'Oakland', 'destination' => 'San Diego', 'price' => 99],
            ['departure' => 'Chicago', 'destination' => 'New York', 'price' => 150]
        ], ['departure', 'destination'], ['price']);
    }

    private function get_file_content(\Illuminate\Http\UploadedFile $file)
    {
        // $content = file_get_contents($file);
        // $json = json_decode($content, true);
        $file->open('r');
        $contents = $file->fread($file->getSize());
        return json_decode($contents, true);
    }

    private function save_to_db(string $tableName, array $data): bool
    {
        $pk = $data['id'];
        $values = array_diff($data, ['id']);

        return  DB::table($tableName)->updateOrInsert(
                $values,
                ['id' => $pk]
            );
    }
}
