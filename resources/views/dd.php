<?php
require 'vendor/autoload.php';     
use Aws\S3\S3Client;

try {

// Instantiate the S3 client with your AWS credentials
$s3Client = S3Client::factory(array(
    'credentials' => array(
        'key'    => 'AKIAQDZODZKLQG73J5ZJ',
        'secret' => 'JvqlR9IY/BL+BHRGldYg7llEhc0ZbLh0Dv2GBnVT',
    )
));

$buckets = $s3Client->listBuckets();


}
catch(Exception $e) {

   exit($e->getMessage());
} 
