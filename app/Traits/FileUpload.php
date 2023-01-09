<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

trait FileUpload
{

    public static function getUniqueNineDigits()
    {
        $nineDigitRandomNumber = mt_rand(100000000, 999999999);
        return $nineDigitRandomNumber;
    }

    /**
     * To handle file Uploads
     * @param mixed $file
     *
     * @return array
     */
    public function uploadFile($file)
    {
         if ($file->isValid()){
        $image_name = $file->getClientOriginalName();
        $image_name_withoutextensions = pathinfo($image_name, PATHINFO_FILENAME);
        $name = str_replace(" ", "", $image_name_withoutextensions);
        $image_extension = $file->getClientOriginalExtension();
        $file_name_extension = $name . '_' . self::getUniqueNineDigits() . '.' . $image_extension;
        $path = $file->storeAs('public/uploads', trim($file_name_extension));
        $full_path = url('/') . '/' . $file->storeAs('storage/uploads', trim($file_name_extension));

        return $full_path;
      }
    }

     public function deleteUploadedFile($file_name)
    {
                Storage::disk('public')->delete("uploads/".$file_name);
            
    }

    public function uploadFileTocloudinary($image)
    {
             $uploadedFileUrl = cloudinary()->upload($image->getRealPath())->getSecurePath();
        return  $uploadedFileUrl;
    }

}
