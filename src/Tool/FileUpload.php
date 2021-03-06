<?php

namespace App\Tool;

use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUpload
{
    /** @var \App\Tool\EntityManagerInterfac */
    private $targetDirectory;
    /** @var SluggerInterface */
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        try {
            $manager = new ImageManager(['gd']);
            $manager
                ->make($file->getPathname())
                ->fit(300, 200)
                ->save('../public/uploads/thumbnail/'.$fileName);
            $manager
                ->make($file->getPathname())
                ->fit(500, 250)
                ->save('../public/uploads/'.$fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
