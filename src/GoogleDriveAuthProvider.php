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
        $spreadsheetId = $this->configuration->getDocumentId();

        /** @var Google_Client $client */
        $client = $this->getClient();
        $contents = $this->getContentsArrayFromRemoteExcelFile($client, $spreadsheetId);

        $googleDriveAuthResource = new GoogleDriveAuthResource($contents, $this->configuration->getFormat(), $tempLocalResource);

        /** @var GoogleDriveAuthResourceInterface $googleDriveAuthResourceBuilder */
        $googleDriveAuthResourceBuilder = $this->googleDriveAuthResourceFactory->create($googleDriveAuthResource);
        return $googleDriveAuthResourceBuilder->buildResource();
    }

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
    private function getClient()
    {
        $credentialsPath = $this->configuration->getCredentialsPath();

        /** @var Google_Client $client */
        $client = $this->createClient();

        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            $accessToken = $this->obtainAccessTokenInformationByUserInteraction($credentialsPath, $client);
        }
        $client->setAccessToken($accessToken);
        $this->refreshAccessTokenIfRequired($credentialsPath, $client);
        return $client;
    }

    public function createClient()
    {
        $client = new Google_Client();

        $applicationName = $this->configuration->getApplicationName();
        $clientSecretPath = $this->configuration->getClientSecretPath();

        if (!is_array($scopesArray = $this->configuration->getScopes())) {
            $scopesArray = explode(',', $this->configuration->getScopes());
        }

        $client->setAccessType('offline');
        $client->setApplicationName($applicationName);
        $client->setAuthConfig($clientSecretPath);
        $client->setScopes($scopesArray);

        return $client;
    }

    private function obtainAccessTokenInformationByUserInteraction($credentialsPath, Google_Client $client)
    {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        $authCode = trim(fgets(STDIN));

        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        // Store the credentials to disk.
        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, json_encode($accessToken));
        printf("Credentials saved to %s\n", $credentialsPath);
        return $accessToken;
    }

    /**
     * @param $credentialsPath
     * @param $client
     */
    private function refreshAccessTokenIfRequired($credentialsPath, Google_Client $client)
    {
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
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

}