<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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

        // $fileData = Storage::disk('local')->get($this->filepath);

        $fileAddress = storage_path('app/'.$this->filepath);
        
        $file = new UploadedFile($fileAddress, 'file', test: true);

        $request = Request::create('/api/uploads/online', 'POST', cookies: [], files: ['file' => $file]);

        $response = app()->handle($request);

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
