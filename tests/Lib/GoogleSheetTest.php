<?php

use App\Lib\GoogleSheet;
use Google_Client as GoogleClient;
use Google_Service_Sheets as GoogleSpreadSheets;
use Illuminate\Routing\Redirector;

class GoogleSheetTest extends BaseLibTest
{
    public function test_setClient_should_initialize_google_client()
    {
        $googleInfo = Config::get('google');

        $mockGClient = $this->createMock(GoogleClient::class);
        $mockGClient->expects($this->once())
            ->method('setApplicationName')
            ->with($googleInfo['app_name']);
        $mockGClient->expects($this->once())
            ->method('setScopes')
            ->with(implode(' ', [GoogleSpreadSheets::SPREADSHEETS]));
        $mockGClient->expects($this->once())
            ->method('setAuthConfig')
            ->with($googleInfo['client_secret_path']);
        $mockGClient->expects($this->once())
            ->method('setAccesstype')
            ->with('offline');
        $mockGClient->expects($this->once())
            ->method('setAccessToken');
        $mockGClient->expects($this->once())
            ->method('isAccesstokenExpired')
            ->will($this->returnValue(true));
        $mockGClient->expects($this->once())
            ->method('fetchAccessTokenwithRefreshToken');

        $path = '~/tests/_data/bar.json';
        $gSheet = new GoogleSheet();
        $gSheet->setClient($mockGClient, $path);
    }

    /**
     * @expectedException Exception
     */
    public function test_setClient_should_throw_exception_when_credential_file_not_exist()
    {
        $googleInfo = Config::get('google');

        $mockGClient = $this->createMock(GoogleClient::class);
        $mockGClient->expects($this->once())
            ->method('setApplicationName')
            ->with($googleInfo['app_name']);
        $mockGClient->expects($this->once())
            ->method('setScopes')
            ->with(implode(' ', [GoogleSpreadSheets::SPREADSHEETS]));
        $mockGClient->expects($this->once())
            ->method('setAuthConfig')
            ->with($googleInfo['client_secret_path']);
        $mockGClient->expects($this->once())
            ->method('setAccesstype')
            ->with('offline');
        $mockGClient->expects($this->never())
            ->method('setAccessToken');
        $mockGClient->expects($this->never())
            ->method('isAccesstokenExpired');
        $mockGClient->expects($this->never())
            ->method('fetchAccessTokenwithRefreshToken');

        $path = '~/tests/_data/foo.json';
        $gSheet = new GoogleSheet();
        $gSheet->setClient($mockGClient, $path);
    }

    public function test_expandHomeDir()
    {
        $path = '~/foo/bar.json';
        $expected = getcwd() . '/foo/bar.json';
        $this->assertEquals($expected, (new GoogleSheet())->expandHomeDir($path));
    }
}
