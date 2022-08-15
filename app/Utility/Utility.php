<?php

namespace App\Utility;

use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class Utility
{
    /**
     * For instructions on how to run the full sample:
     *
     * @see https://github.com/GoogleCloudPlatform/php-docs-samples/tree/master/storage/README.md
     */

    /**
     * Generates a v4 POST Policy to be used in an HTML form and echo's form.
     *
     * @param string $bucketName The name of your Cloud Storage bucket.
     * @param string $objectName The name of your Cloud Storage object.
     */
    public static function generate_v4_post_policy(string $objectName)
    {
        return resolve(StorageClient::class)->bucket(env('GOOGLE_BUCKET_NAME'))->generateSignedPostPolicyV4(
            $objectName,
            new \DateTime('10 min'),
            [
                'conditions' => [
                    ['content-length-range', 0, 1000000]
                ],
                'fields' => [
                    'success_action_status' => '201',
                ]
            ]
        );
    }

    public static function deleteFromGoogleBucket(string $objectName)
    {
        resolve(StorageClient::class)->bucket(env('GOOGLE_BUCKET_NAME'))->object(
            $objectName
        )->delete();
        return $objectName;
    }

    public static function getGoogleBucketObjects()
    {
        $temp = array();
        foreach (resolve(StorageClient::class)->bucket(env('GOOGLE_BUCKET_NAME'))->objects() as $object) {
            array_push($temp, $object->name());
        }
        return $temp;
    }

    public static function getTronApiKeySelector()
    {
        if (!Redis::exists('selected_tron_api_key')) {
            Redis::set('selected_tron_api_key', 0);
        }

        $selector = Redis::get('selected_tron_api_key');
        if ($selector) Redis::decr('selected_tron_api_key');
        else Redis::incr('selected_tron_api_key');
        return $selector;
    }

    public static function parseObjectNameFromUrl($url)
    {
        $trimmer = "https://storage.googleapis.com/" . env('GOOGLE_BUCKET_NAME') . "/";
        return Str::after($url, $trimmer);
    }
}
