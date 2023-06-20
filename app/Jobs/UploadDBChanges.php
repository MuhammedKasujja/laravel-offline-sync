<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

// for internal testing
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadDBChanges implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 
     * Create a new job instance.
     */
    public function __construct(private string $filepath)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = 'http://127.0.0.1:8000/api/uploads/online';

        // $file = Storage::disk('local')->get($this->filepath);

        // $storagePath  = Storage::disk('local')->getAdapter()->getPathPrefix();

        // $fileAddress = storage_path().'/file.jpg';
        // $file = new UploadedFile($fileAddress, 'file');

        $tmpFile = new File($this->filepath);

        $file = new UploadedFile(
            $tmpFile->getPathname(),
            $tmpFile->getFilename(),
            $tmpFile->getMimeType(),
            0,
            true // Mark it as test, since the file isn't from real HTTP POST.
        );

        // $file = new UploadedFile($this->filepath, 'file.json', 'application/json', null, true);

        // Log::alert(print_r(base64_encode($file), true));

        $request = Request::create('/api/uploads/online', 'POST', cookies: [], files: ['file' => $file]);

        $response = app()->handle($request);

        // Log::alert(print_r($response, true));

        $responseBody = json_decode($response->getContent(), true);

        Log::alert(print_r('responseBody: ' . $responseBody, true));
        // $response = Http::attach(
        //     'upload',
        //     $file,
        //     // 'file.json'
        // )->post($url);

        // $response->onError(function ($error) {
        //     Log::alert(print_r('Connection error', true));
        //     Log::alert(print_r($error, true));
        // });

        // if ($response->ok()) {
        //     Log::alert('Connection successfull');
        // } else {
        //     Log::alert(print_r('Connection failed: ' . $response->reason(), true));
        // }
    }
}
