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
use Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\GoogleDriveAuthProvider;
use PHPUnit\Framework\TestCase;

class GoogleDriveAuthProviderTest extends TestCase
{
    private GoogleDriveAuthProvider $provider;
    private string $credentialsPath;
    private string $clientSecretPath;

    protected function setUp(): void
    {
        // Create temporary credentials file
        $this->credentialsPath = sys_get_temp_dir() . '/test_credentials.json';
        $this->clientSecretPath = sys_get_temp_dir() . '/test_client_secret.json';

        // Create a valid credentials JSON file
        file_put_contents($this->credentialsPath, json_encode([
            'type' => 'service_account',
            'project_id' => 'test-project',
            'private_key_id' => 'key-id',
            'private_key' => '-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC7W0CpJNgCl5Nr\nqDRVm2bxF7z5JJ8xTb9T7qxsFsGPxlvHvuJkI0zrJL6YIx1xQvAXPMp4sSH7SXcp\nKJxJR8NxQRCrCzPxvFR9f3cP1z9KbdJCWIuI7W7c+r8YZLSkJIpP7W8CpJNgCl5N\nrqDRVm2bxF7z5JJ8xTb9T7qxsFsGPxlvHvuJkI0zrJL6YIx1xQvAXPMp4sSH7SXc\npKJxJR8NxQRCrCzPxvFR9f3cP1z9KbdJCWIuI7W7c+r8YZLSkJIpP7W8CpJNgCl\nAgMBAAECggEABUi7W0CpJNgCl5NrqDRVm2bxF7z5JJ8xTb9T7qxsFsGPxlvHvuJk\nI0zrJL6YIx1xQvAXPMp4sSH7SXcpKJxJR8NxQRCrCzPxvFR9f3cP1z9KbdJCWIuI\n7W7c+r8YZLSkJIpP7W8CpJNgCl5NrqDRVm2bxF7z5JJ8xTb9T7qxsFsGPxlvHvuJ\nkI0zrJL6YIx1xQvAXPMp4sSH7SXcpKJxJR8NxQRCrCzPxvFR9f3cP1z9KbdJCWIu\nI7W7c+r8YZLSkJIpP7W8CpJNgCl5NrqDRVm2bxF7z5JJ8xTb9T7qxsFsGPxlvHvu\nJkI0zrJL6YIx1xQvAXPMp4sSH7SXcpKJxJR8NxQRCrCzPxvFR9f3cP1z9KbdJCWI\nAoGBAPkCgYEA4kCgYEA4kCgYEA4kCgYEA4kCgYEA4kCgYEA4kCgYEA4kCgYEA4k\nCgYEA4kCgYEA4kCgYEA4kCgYEA4kCgYEA4kCgYEA4kCgYEA4kCgYEA4kCgYEA4k\n-----END PRIVATE KEY-----\n',
            'client_email' => 'test@test-project.iam.gserviceaccount.com',
            'client_id' => '123456789',
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
        ]));

        $configuration = new Configuration([
            'providers' => [
                'test_group' => [
                    'application_name' => 'Test Application',
                    'credentials_path' => $this->credentialsPath,
                    'client_secret_path' => $this->clientSecretPath,
                    'scopes' => ['scope1', 'scope2'],
                    'document_id' => '12345',
                    'source_resource' => 'https://docs.google.com/spreadsheets/d/12345/edit',
                    'format' => 'xlsx',
                    'temp_local_source_file' => '/tmp/test.xlsx'
                ]
            ]
        ], 'test_group');

        $this->provider = new GoogleDriveAuthProvider($configuration);
    }

    protected function tearDown(): void
    {
        // Clean up temporary files
        if (file_exists($this->credentialsPath)) {
            unlink($this->credentialsPath);
        }
        if (file_exists($this->clientSecretPath)) {
            unlink($this->clientSecretPath);
        }
    }

    public function testGetProvider(): void
    {
        $this->assertEquals('google_drive_auth', $this->provider->getProvider());
    }

    public function testCreateClient(): void
    {
        $client = $this->provider->createClient();
        $this->assertInstanceOf(\Google_Client::class, $client);
    }

    /**
     * @dataProvider documentIdProvider
     */
    public function testGetDocumentIdFromUrl(string $url, string $expectedId): void
    {
        $reflection = new \ReflectionClass($this->provider);
        $method = $reflection->getMethod('getDocumentIdFromUrl');
        $method->setAccessible(true);

        $documentId = $method->invoke($this->provider, $url);
        $this->assertEquals($expectedId, $documentId);
    }

    public static function documentIdProvider(): array
    {
        return [
            ['https://docs.google.com/spreadsheets/d/12345/edit', '12345'],
            ['https://docs.google.com/spreadsheets/d/abcdef123456/edit#gid=0', 'abcdef123456'],
            ['https://docs.google.com/document/d/test-doc-id/edit', 'test-doc-id'],
        ];
    }

    public function testGetDocumentIdFromUrlThrowsExceptionOnInvalidUrl(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Document Id not found in the url');

        $reflection = new \ReflectionClass($this->provider);
        $method = $reflection->getMethod('getDocumentIdFromUrl');
        $method->setAccessible(true);

        $method->invoke($this->provider, 'https://invalid-url.com');
    }
}
