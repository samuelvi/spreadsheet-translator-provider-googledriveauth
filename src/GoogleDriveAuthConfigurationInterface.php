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

interface GoogleDriveAuthConfigurationInterface
{
    public function getApplicationName();
    public function getCredentialsPath();
    public function getClientSecretPath();
    public function getScopes();
}