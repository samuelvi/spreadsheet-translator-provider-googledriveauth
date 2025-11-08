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

use Override;
use Atico\SpreadsheetTranslator\Core\Configuration\ProviderConfigurationInterface;
use Atico\SpreadsheetTranslator\Core\Provider\DefaultProviderManager;

class GoogleDriveAuthConfigurationManager extends DefaultProviderManager implements ProviderConfigurationInterface
{
    #[Override]
    public function getDefaultFormat()
    {
        return parent::getDefaultFormat();
        // return 'matrix';
    }

    public function getApplicationName()
    {
        return $this->getRequiredOption('application_name');
    }

    public function getCredentialsPath()
    {
        return $this->getRequiredOption('credentials_path');
    }

    public function getClientSecretPath()
    {
        return $this->getRequiredOption('client_secret_path');
    }

    public function getScopes()
    {
        return $this->getNonRequiredOption('scopes', '');
    }

    public function getDocumentId()
    {
        return $this->getRequiredOption('document_id');
    }
}