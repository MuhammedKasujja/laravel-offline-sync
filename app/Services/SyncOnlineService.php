<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;


class SyncOnlineService
{
    public function syncChanges(\Illuminate\Http\UploadedFile $file)
    {
        $contents =  $this->get_file_content($file);

        $this->save_file_contents_in_db_v2($contents);

        $this->save_file($file);
    }
    private function save_file(\Illuminate\Http\UploadedFile $file): bool|string
    {
        $path = Storage::putFileAs('public/db/downloads',  $file, $file->getBasename());
        // Log::warning('FilePath: ==> ' . url($path));
        return $path;
    }

    private function save_file_contents_in_db_v2(array $json_file_contents)
    {
        Schema::disableForeignKeyConstraints();
        // Log::emergency(print_r($json_file_contents, true));
        foreach ($json_file_contents as $key => $value) {
            $this->save_to_db(tableName: $key, updatedRows: $value);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function get_file_content(\Illuminate\Http\UploadedFile $file)
    {
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        // remove array index from the generated data
        return array_merge(...$data);
    }

    private function save_to_db(string $tableName, array $updatedRows): bool
    {
        foreach ($updatedRows as  $row) {
            $this->save_to_table($tableName, $row);
        }
        return true;
    }

    function save_to_table(string $tableName, array $row): bool
    {
        $pk = $row['id'];

        unset($row['id']);

        // Update table if row with [id] =>$pk exists, if not, insert new data
        return  DB::table($tableName)->updateOrInsert(
            ['id' => $pk],
            $row,
        );
    }
}
