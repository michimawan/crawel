<?php
namespace App\Lib;

use Exception;
use Config;
use Google_Client as GoogleClient;
use Google_Service_Sheets as GoogleSpreadSheets;
use Google_Service_Sheets_ValueRange as ValueRange;

class GoogleSheet
{
    public function __construct()
    {
        $this->credentials = [];
    }

    /**
     * @return GoogleClient that's been prepared
     *
     * @param GoogleClient object
     * @param path to get google credentials
     */
    public function setClient($googleClient, $path)
    {
        $this->client = $googleClient;
        $googleInfo = Config::get('google');

        $this->client->setApplicationName($googleInfo['app_name']);
        $this->client->setScopes(implode(' ', [ GoogleSpreadSheets::SPREADSHEETS ] ));
        $this->client->setAuthConfig($googleInfo['client_secret_path']);
        $this->client->setAccessType('offline');

        $credentialsPath = $this->expandHomeDir($path);
        $this->setCredentials($credentialsPath);

        return $this->client;
    }

    /*
     * this function is copied from google example
     * used to replace ~/.credentials/credentials.json
     * become /home/{user}/crawel/public/.credentials/credentials.json
     * make sure the permission is set to 777 so app can read and write freely
     */
    public function expandHomeDir($path = '')
    {
        $homeDirectory = getcwd();
        return str_replace('~', realpath($homeDirectory), $path);
    }

    private function setCredentials($credentialsPath)
    {
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
            $this->client->setAccessToken($accessToken);

            // Refresh the token if it's expired.
            if ($this->client->isAccessTokenExpired()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                file_put_contents($credentialsPath, json_encode($this->client->getAccessToken()));
            }
        } else {
            throw new Exception('credentials not exist, please provide correct credentials');
        }
    }

    /**
     * @return void
     *
     * @param GoogleClient object
     * @param array of desired data to be send to google sheet
     */
    public function sendToSpreadSheet($client, $newRow = [])
    {
        $service = new GoogleSpreadSheets($client);

        $spreadsheetId = Config::get('google.spread_sheet_id'); // '1jpq_8kSsE4b6S6hpHDIoUrTg9oTTqlO0GMjXvWqROs4';
        $range = 'A1:E1';
        $valueInputOption = 'RAW';
        $insertDataOption = 'INSERT_ROWS';

        $body = new ValueRange([
            'values' => $newRow
        ]);
        $params = array(
            'valueInputOption' => $valueInputOption
        );
        $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    }
}
