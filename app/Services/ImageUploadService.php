<?php

namespace App\Services;

use Intervention\Image\Facades\Image;

class ImageUploadService
{
    public function uploadPhoto($file, $path, $height = 300)
    {
        if ($file) {
            $imageName = time() . '-' . $file->getClientOriginalName();
            $destinationPath = public_path('storage/' . $path);

            // Ensure the directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Resize and save the image
            $image = Image::make($file);
            $image->resize(null, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->save($destinationPath . $imageName);

            // Return the path to be stored in the database
            return $path . $imageName;
        }

        return null; // Return null if no file is uploaded
    }
}
