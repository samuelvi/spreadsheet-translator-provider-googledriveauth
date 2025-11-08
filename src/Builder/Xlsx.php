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

use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Atico\SpreadsheetTranslator\Core\Resource\Resource;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;

class Xlsx implements GoogleDriveAuthResourceInterface
{
    function __construct(protected GoogleDriveAuthResource $googleDriveAuthResource)
    {
    }

    /**
     * @throws Exception
     */
    public function buildResource(): Resource
    {
        $this->writeFileFromContentsArray($this->googleDriveAuthResource->getValue(), $this->googleDriveAuthResource->getDestinationFolder());
        return new Resource($this->googleDriveAuthResource->getDestinationFolder(), $this->googleDriveAuthResource->getFormat());
    }

    /**
     * @throws Exception
     */
    private function writeFileFromContentsArray($contents, $tempLocalResource): void
    {
        if (count($contents) === 0) {
            throw new Exception('No data found');
        }
        $doc = new Spreadsheet();
        $doc->removeSheetByIndex(0);
        foreach ($contents as $range => $content) {
            $workSheet = new Worksheet();
            $workSheet->setTitle($range);
            $workSheet->fromArray($content, null, 'A1');
            $doc->addSheet($workSheet);
        }
        /** @var IWriter $writer */
        $writer = IOFactory::createWriter($doc, "Xlsx");
        // new PHPExcel_Writer_Excel2007($objPHPExcel);
        $writer->save($tempLocalResource);
    }
}