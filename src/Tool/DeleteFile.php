<?php

namespace App\Tool;

class DeleteFile
{
    public function delete($name): void
    {
        $filename = '../public/uploads/';
        $filename2 = '../public/uploads/thumbnail/';
        if (file_exists($filename.$name) && file_exists($filename2.$name)) {
            unlink($filename.$name);
            unlink($filename2.$name);
        }
    }
}
