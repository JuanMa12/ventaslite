<?php

namespace App\Helpers;


class GlobalApp
{
    public static function saveFile($file, $folder_name)
    {
        $customFileName = uniqid() . '_.' . $file->extension();
        $file->storeAs('public/' . $folder_name, $customFileName);

        return $customFileName;
    }

    public static function viewImage($image, $folder)
    {
        $path_url = 'storage/' . $folder . '/';
        if (file_exists($path_url . $image) && !is_null($image))
            return $path_url . $image;
        else
            return '/assets/img/200x200.jpg';
    }
}
