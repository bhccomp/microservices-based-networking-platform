<?php

namespace App\Service;

use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProfileImageUploadService
{
    private $s3;
    private $bucket;

    public function __construct(S3Client $s3)
    {
        $this->s3 = $s3;
        $this->bucket = $_SERVER['MINIO_BUCKET'];
    }

    public function upload(UploadedFile $file): string
    {
        $filename = uniqid() . '.' . $file->guessExtension();

        $this->s3->putObject([
            'Bucket' => $this->bucket,
            'Key'    => $filename,
            'Body'   => fopen($file->getPathname(), 'rb'),
            'ACL'    => 'public-read'
        ]);

        return $filename;
    }
}
