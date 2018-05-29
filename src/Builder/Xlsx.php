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
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;

class Xlsx implements GoogleDriveAuthResourceInterface
{
    protected $googleDriveAuthResource;

    function __construct(GoogleDriveAuthResource $googleDriveAuthResource)
    {
        $this->googleDriveAuthResource = $googleDriveAuthResource;
    }

    public function buildResource()
    {
        $this->writeFileFromContentsArray($this->googleDriveAuthResource->getValue(), $this->googleDriveAuthResource->getDestinationFolder());
        return new Resource($this->googleDriveAuthResource->getDestinationFolder(), $this->googleDriveAuthResource->getFormat());
    }

    private function writeFileFromContentsArray($contents, $tempLocalResource)
    {
        if (count($contents) == 0) {
            throw new \Exception('No data found');
        } else {
            $doc = new PHPExcel();
            $doc->removeSheetByIndex(0);

            foreach ($contents as $range => $content) {
                $workSheet = new PHPExcel_Worksheet();
                $workSheet->setTitle($range);
                $workSheet->fromArray($content, null, 'A1');
                $doc->addSheet($workSheet);
            }

            $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel2007');
            $writer->save($tempLocalResource);
        }
    }
}