<?php


namespace App;

use Google_Client;
use Google_Service_Gmail;

class GMailAPI
{
    public static function getClient($accessToken = null)
    {
        $client = new Google_Client();
        $client->setApplicationName('Gmail API PHP Quickstart');
        $scopes = implode(' ', [Google_Service_Gmail::GMAIL_READONLY]);
        $client->setScopes($scopes);
        if (is_null($accessToken)) {
            $client->setAuthConfig(storage_path(env('GOOGLE_API_CLIENT_SECRET_PATH')));
            $client->setAccessType('offline');
            $path = storage_path();
            $credentialsPath = static::expandHomeDirectory($path . env('GOOGLE_API_CREDENTIALS_PATH'));
            if (file_exists($credentialsPath)) {
                $accessToken = json_decode(file_get_contents($credentialsPath), true);
            } else {
                $authUrl = $client->createAuthUrl();
//            printf("Open the following link in your browser:\n%s\n", $authUrl);
//            print 'Enter verification code: ';
                $authCode = '4/QXnrEYO5qs63qHvYzq-icabPlz2Y_I9UdhchOoEggZQ';

                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

                if (!file_exists(dirname($credentialsPath))) {
                    mkdir(dirname($credentialsPath), 0700, true);
                }
                file_put_contents($credentialsPath, json_encode($accessToken));
                printf("Credentials saved to %s\n", $credentialsPath);
            }
        }
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            return false;
        }
        return $client;
    }

    public static function expandHomeDirectory($path)
    {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

}
