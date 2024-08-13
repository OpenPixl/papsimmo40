<?php

namespace App\Service;

class DirectoryService
{
    public function delRepertory($path)
    {
        $handle = opendir($path);
        while($elem = readdir($handle))
        {
            if(is_dir($path.'/'.$elem) && substr($elem, -2, 2) !== '..' && substr(
                    $elem, -1, 1) !== '.') //si c'est un repertoire
            {
                $this->delRepertory($path.'/'.$elem);
            }
            else
            {
                if(substr($elem, -2, 2) !== '..' && substr($elem, -1, 1) !== '.')
                {
                    unlink($path.'/'.$elem);
                }
            }

        }

        $handle = opendir($path);
        while($elem = readdir($handle)) //ce while efface tous les dossiers
        {
            if(is_dir($path.'/'.$elem) && substr($elem, -2, 2) !== '..' && substr(
                    $elem, -1, 1) !== '.') //si c'est un repertoire
            {
                $this->delRepertory($path.'/'.$elem);
                rmdir($path.'/'.$elem);
            }

        }
        rmdir($path); // ce rmdir efface le repertoire principale

        if(is_dir($path)){
            $result = true;
        }else{
            $result = false;
        }
        return $result;

    }
}