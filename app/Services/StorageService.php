<?php

namespace App\Services;

class StorageService
{
    public static function put($file, $path, $filename = null)
    {
        $type = $file['type'];
        $temp = $file['tmp_name'];
        $ext = '.' . pathinfo($temp)['extension'];
        $newName = $filename == null ? md5(date('YmdHis')) . $ext : $filename . $ext;
        $pathFile = BASEDIR . '/storage/' . $path . '/' . $filename;
        if (!is_dir(BASEDIR . '/storage/' . $path . '/')) {
            mkdir(BASEDIR . '/storage/' . $path . '/', 777);
        }
        return move_uploaded_file($temp, $pathFile);
    }
    public static function delete()
    {
    }
}
