<?php

namespace App\Services;

class StorageService
{
    public static function put($file, $path, $filename = null)
    {
        $type = $file['type'];
        $temp = $file['tmp_name'];
        $name = $file['name'];
        $temp_count = explode('.', $name);
        $ext = '.' . $temp_count[count($temp_count) - 1];
        $newName = $filename == null ? md5(date('YmdHis')) . $ext : $filename . $ext;
        $pathFile = BASEDIR . '/storage/' . $path . '/' . $newName;
        if (!is_dir(BASEDIR . '/storage/' . $path . '/')) {
            mkdir(BASEDIR . '/storage/' . $path . '/', 777);
        }
        if (move_uploaded_file($temp, $pathFile)) {
            return $newName;
        } else {
            return false;
        }
    }
    public static function delete($file): bool
    {
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }
}
