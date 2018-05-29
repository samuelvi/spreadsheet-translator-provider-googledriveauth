<?php

/*
 * This file is part of the Atico/SpreadsheetTranslator package.
 *
 * (c) Samuel Vicent <samuelvicent@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Builder;

use Atico\SpreadsheetTranslator\Core\Resource\Resource;

class Matrix implements GoogleDriveAuthResourceInterface
{
    protected $googleDriveAuthResource;

    function __construct(GoogleDriveAuthResource $googleDriveAuthResource)
    {
        $this->googleDriveAuthResource = $googleDriveAuthResource;
    }

    public function buildResource()
    {
        return new Resource($this->googleDriveAuthResource->getValue(), $this->googleDriveAuthResource->getFormat());
    }

}