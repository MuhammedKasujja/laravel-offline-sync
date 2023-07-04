<?php

namespace App\Services;

use App\Jobs\UploadDBChanges;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SyncOfflineService
{

    public function checkDBChanges()
    {

        $latest_updates = $this->get_latest_db_updates();

        Log::info(print_r($latest_updates, true));

        $file_path =  $this->save_db_updates_to_file($latest_updates);

        Log::info('ConnectionStatus: ' . $this->check_internet_connection());

        dispatch(new UploadDBChanges($file_path));
    }
    private function check_internet_connection(): string
    {
        $host_name = 'www.google.com';
        $port_no = '80';

        $st = (bool)@fsockopen($host_name, $port_no, $err_no, $err_str, 10);
        if ($st) {
            return 'You are connected to internet.';
        } else {
            return 'Sorry! You are offline.';
        }
    }

    private function save_db_updates_to_file(array $latest_db_changes)
    {
        $data = json_encode($latest_db_changes);
        $filename = '' . time() . '.json';
        $file_path = 'db/backup/' . $filename;
        Storage::disk('local')->put($file_path, $data);

        $size = Storage::size($file_path) / 1024;

        DB::table('sync_db_changes')->insert([
            'file_name' => $filename,
            'file_path' => $file_path,
            'file_size' => $size
        ]);
        return $file_path;
    }

    private function get_db_tables(): array
    {
        $tables_in_db = DB::select("SHOW TABLES");
        $db = "Tables_in_" . env('DB_DATABASE');
        $tables = [];
        foreach ($tables_in_db as $table) {
            $tables[] = $table->{$db};
        }
        return $tables;
    }

    private function get_latest_db_updates(): array
    {
        $last_update_time = "2023-06-04 16:43:11";
        $tables = $this->get_db_tables();
        $latest_updates = [];
        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'updated_at')) {
                $latest_data = DB::table($table)->select()
                    ->where($table . '.updated_at',  '>', '' . $last_update_time)->get();;
                array_push($latest_updates, [$table => $latest_data]);
            }
        }
        return $latest_updates;
    }

}
