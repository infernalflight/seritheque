<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger) {
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, string $originalName, string $dir): string {
        $name = $this->slugger->slug($originalName.'-'.uniqid() . '.'.$file->guessExtension());
        $file->move($dir, $name);

        return $name;
    }

}