<?php

/*
 * This file is part of the Atico/SpreadsheetTranslator package.
 *
 * (c) Samuel Vicent <samuelvicent@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth;

use Atico\SpreadsheetTranslator\Core\Configuration\Configuration;
use Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Builder\GoogleDriveAuthResource;
use Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Builder\GoogleDriveAuthResourceFactory;
use Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Builder\GoogleDriveAuthResourceInterface;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_Sheet;

use Atico\SpreadsheetTranslator\Core\Provider\ProviderInterface;

class GoogleDriveAuthProvider implements ProviderInterface
{
    /** @var GoogleDriveAuthConfigurationManager $configuration */
    protected $configuration;

    /** @var  GoogleDriveAuthResourceFactory $googleDriveAuthResourceFactory */
    protected $googleDriveAuthResourceFactory;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = new GoogleDriveAuthConfigurationManager($configuration);
        $this->googleDriveAuthResourceFactory = new GoogleDriveAuthResourceFactory();
    }

    public function getProvider()
    {
        return 'google_drive_auth';
    }

    public function handleSourceResource()
    {
        $tempLocalResource = $this->configuration->getTempLocalSourceFile();
        $spreadsheetId = $this->getDocumentIdFromUrl($this->configuration->getSourceResource());

        /** @var Google_Client $client */
        $client = $this->getClient();
        $contents = $this->getContentsArrayFromRemoteExcelFile($client, $spreadsheetId);

        $googleDriveAuthResource = new GoogleDriveAuthResource($contents, $this->configuration->getFormat(), $tempLocalResource);

        /** @var GoogleDriveAuthResourceInterface $googleDriveAuthResourceBuilder */
        $googleDriveAuthResourceBuilder = $this->googleDriveAuthResourceFactory->create($googleDriveAuthResource);
        return $googleDriveAuthResourceBuilder->buildResource();
    }

    /**
     * @throws \Exception
     */
    protected function getDocumentIdFromUrl($url)
    {
        $portions = explode('/', $url);
        foreach ($portions as $index => $portion) {
            if ($portion == 'd') return $portions[$index + 1];
        }
        throw new \Exception(sprintf('Document Id not found in the url: "$url"', $url));
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    public function getClient()
    {
        $credentialsPath = $this->configuration->getCredentialsPath();
        $accessTokenPath = $this->configuration->getClientSecretPath();

        /** @var Google_Client $client */
        $client = $this->createClient();

        if (file_exists($accessTokenPath)) {
            $accessToken = json_decode(file_get_contents($accessTokenPath), true);
        } else {
            $accessToken = $this->obtainAccessTokenInformationByUserInteraction($credentialsPath, $accessTokenPath, $client);
        }

        $client->setAccessToken($accessToken);
        $this->refreshAccessTokenIfRequired($accessTokenPath, $client);
        return $client;
    }

    public function createClient()
    {
        $client = new Google_Client();

        $applicationName = $this->configuration->getApplicationName();
        $clientSecretPath = $this->configuration->getCredentialsPath();
        $scopesArray = self::getScopes();

        $client->setAccessType('offline');
        $client->setApplicationName($applicationName);
        $client->setAuthConfig($clientSecretPath);
        $client->setScopes($scopesArray);

        return $client;
    }

    private function obtainAccessTokenInformationByUserInteraction($credentialsPath, $accessTokenPath, Google_Client $client)
    {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        $authCode = trim(fgets(STDIN));

        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        // Store the credentials to disk.
        if (!file_exists(dirname($accessTokenPath))) {
            mkdir(dirname($accessTokenPath), 0700, true);
        }
        file_put_contents($accessTokenPath, json_encode($accessToken));
        printf("Credentials saved to %s\n", $accessTokenPath);
        return $accessToken;
    }

    /**
     * @param $credentialsPath
     * @param $client
     */
    private function refreshAccessTokenIfRequired($accessTokenPath, Google_Client $client)
    {
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($accessTokenPath, json_encode($client->getAccessToken()));
        }
    }

    /**
     * @return array
     */
    private function getContentsArrayFromRemoteExcelFile($client, $spreadsheetId)
    {
        /** @var Google_Service_Sheets $service */
        $service = new Google_Service_Sheets($client);
        $sheets = $service->spreadsheets->get($spreadsheetId)->getSheets();
        $contents = [];

        /** @var Google_Service_Sheets_Sheet $sheet */
        foreach ($sheets as $sheet) {
            $range = $sheet->getProperties()->getTitle();
            $contents[$range] = $service->spreadsheets_values->get($spreadsheetId, $range)->getValues();
        }
        return $contents;
    }

    private static function getScopes()
    {
        return [
            'https://docs.google.com/feeds/',
            Google_Service_Sheets::SPREADSHEETS_READONLY,
            Google_Service_Sheets::SPREADSHEETS,
            Google_Service_Sheets::DRIVE,
            Google_Service_Sheets::DRIVE_READONLY,
            \Google_Service_Drive::DRIVE_METADATA_READONLY
        ];
    }
}