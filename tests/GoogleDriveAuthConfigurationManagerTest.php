<?php

/*
 * This file is part of the Atico/SpreadsheetTranslator package.
 *
 * (c) Samuel Vicent <samuelvicent@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Tests;

use Atico\SpreadsheetTranslator\Core\Configuration\Configuration;
use Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\GoogleDriveAuthConfigurationManager;
use PHPUnit\Framework\TestCase;

class GoogleDriveAuthConfigurationManagerTest extends TestCase
{
    private GoogleDriveAuthConfigurationManager $configurationManager;

    protected function setUp(): void
    {
        $configuration = new Configuration([
            'providers' => [
                'test_group' => [
                    'application_name' => 'Test Application',
                    'credentials_path' => '/path/to/credentials.json',
                    'client_secret_path' => '/path/to/client_secret.json',
                    'scopes' => ['scope1', 'scope2'],
                    'document_id' => '12345',
                    'source_resource' => 'https://docs.google.com/spreadsheets/d/12345/edit',
                    'format' => 'xlsx',
                    'temp_local_source_file' => '/tmp/test.xlsx'
                ]
            ]
        ], 'test_group');

        $this->configurationManager = new GoogleDriveAuthConfigurationManager($configuration);
    }

    public function testGetApplicationName(): void
    {
        $this->assertEquals('Test Application', $this->configurationManager->getApplicationName());
    }

    public function testGetCredentialsPath(): void
    {
        $this->assertEquals('/path/to/credentials.json', $this->configurationManager->getCredentialsPath());
    }

    public function testGetClientSecretPath(): void
    {
        $this->assertEquals('/path/to/client_secret.json', $this->configurationManager->getClientSecretPath());
    }

    public function testGetScopes(): void
    {
        $this->assertEquals(['scope1', 'scope2'], $this->configurationManager->getScopes());
    }

    public function testGetDocumentId(): void
    {
        $this->assertEquals('12345', $this->configurationManager->getDocumentId());
    }

    public function testGetDefaultFormat(): void
    {
        $format = $this->configurationManager->getDefaultFormat();
        $this->assertIsString($format);
    }
}
