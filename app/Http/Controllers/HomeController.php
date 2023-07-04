<?php

namespace App\Http\Controllers;

use App\Services\SyncOnlineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{

    public function __construct(private SyncOnlineService $service) {
    }
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
            // Log::alert('RequestData: ' . print_r($request->all(), true));
            if ($request->hasFile('file')) {
                $this->service->syncChanges($request->file('file'));
                return response()->json("File found", 200);
            }
            throw new \Exception('File not found');
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 200);
        }
    }
}
