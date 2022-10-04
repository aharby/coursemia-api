<?php

return [

    /**
     * Local Paths.
     *
     * These are paths on your local instance where the s3-migrate
     * package should look for files to migrate
     */
    'local_paths' => [
        storage_path("app/public"),
    ],

    /**
     * Extensions.
     *
     * Define the file type extensions that should be migrated
     * Any extension not in this list will be ignored
     */
    'extensions' => [
//        'jpg',
//        'jpeg',
//        'png',
//        'mp3',
//        'mp4',
//        'pdf',
//        'GIF',
//        'MP3',
//        'MP4',
//        'PDF',
        'avi',
        'csv',
        'doc',
//        'docx',
        'flv',
        'gif',
        'mov',
//        'mpeg',
        'mpg',
        'svg',
        'txt',
//        'webm',
//        'webp',
        'xls',
//        'xlsx',
        'zip',
    ],

    /**
     * AWS Bucket Name.
     *
     * This is the AWS S3 bucket where the local files will be saved to
     */
    'aws_bucket' => env('AWS_BUCKET'),

    /**
     * AWS S3 Bucket Path.
     *
     * This is the path in the S3 bucket where the assets
     * will be transferred to.
     */
    'aws_bucket_path' => '/',
];
