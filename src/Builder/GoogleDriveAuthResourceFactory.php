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

use Atico\SpreadsheetTranslator\Core\Util\Strings;

class GoogleDriveAuthResourceFactory
{
    protected function getClassName($format)
    {
        $formatCamelized = Strings::camelize($format);
        $class = sprintf('\Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Builder\%s', $formatCamelized);
        return $class;
    }

    public function create(GoogleDriveAuthResource $resource)
    {
        $format = $resource->getFormat();

        $class = $this->getClassName($format);
        return new $class($resource);
    }

}