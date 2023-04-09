<?php
require 'vendor/autoload.php';     
use Aws\S3\S3Client;

try {

// Instantiate the S3 client with your AWS credentials
$s3Client = S3Client::factory(array(
    'credentials' => array(
        'key'    => 'YOUR_AWS_ACCESS_KEY_ID',
        'secret' => 'YOUR_AWS_SECRET_ACCESS_KEY',
    )
));

$buckets = $s3Client->listBuckets();


}
catch(Exception $e) {

   exit($e->getMessage());
} 
