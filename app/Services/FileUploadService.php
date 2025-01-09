<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function uploadFile($file, $path)
    {
        if ($file) {
            // Generate a unique file name
            $fileName = time() . '-' . $file->getClientOriginalName();
            $destinationPath = public_path('storage/' . $path);

            // Ensure the directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Move the file to the destination directory
            $file->move($destinationPath, $fileName);

            // Return the path to be stored in the database
            return $path . $fileName;
        }

        return null; // Return null if no file is uploaded
    }
}
