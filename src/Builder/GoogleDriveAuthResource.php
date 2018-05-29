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

class GoogleDriveAuthResource extends Resource
{
    protected $destinationFolder;

    public function __construct($value, $format, $destinationFolder)
    {
        parent::__construct($value, $format);
        $this->destinationFolder = $destinationFolder;
    }

    public function getDestinationFolder()
    {
        return $this->destinationFolder;
    }

    public function setDestinationFolder($destinationFolder)
    {
        $this->destinationFolder = $destinationFolder;
    }
}