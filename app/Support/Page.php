<?php

namespace App\Support;

use Exception;

class Page
{
    const TITLE = "Sistema";
    const TEMPLATE = "default";

    public function url(array $data): string
    {
        $page = $data['page'] ?? null;
        $action = $data['go'] ?? 'index';
        return BASEURL . ($page != null ? "?page=$page&go=$action" : "?go=$action");
    }
    /**
     * @param pathfile = "folder.nameFile"
     */
    public function view(string $pathfile, array $props = [])
    {
        $pathfile = BASEDIR . '/app/Views/Pages/' . str_replace('.', '/', $pathfile) . '.phtml';
        if (!file_exists($pathfile)) {
            throw new Exception("Error, view not find {$pathfile}", 1);
        }
        //mount view
        self::header();
        require_once $pathfile;
        self::footer();
    }

    public static function component(string $file, array $props = [])
    {
        $pathFile = BASEDIR . '/app/Views/Components/' . str_replace('.', '/', $file) . '.phtml';
        if (!file_exists($pathFile)) {
            throw new Exception("Error, file of component not find {$pathFile}", 1);
        }
        require_once $pathFile;
    }

    public static function header()
    {
        $template = static::TEMPLATE ?? 'default';
        $pathFile = BASEDIR . '/app/Views/Templates/' . $template . '/head.phtml';
        if (!file_exists($pathFile)) {
            throw new Exception("Error, template head component not find {$pathFile}", 1);
        }
        require_once $pathFile;
    }
    public static function footer()
    {
        $template = static::TEMPLATE ?? 'default';
        $pathFile = BASEDIR . '/app/Views/Templates/' . $template . '/footer.phtml';
        if (!file_exists($pathFile)) {
            throw new Exception("Error, template footer component not find {$pathFile}", 1);
        }
        require_once $pathFile;
    }
}
