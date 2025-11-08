<?php

/*
 * This file is part of the Atico/SpreadsheetTranslator package.
 *
 * (c) Samuel Vicent <samuelvicent@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Tests\Builder;

use Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Builder\GoogleDriveAuthResource;
use Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Builder\GoogleDriveAuthResourceFactory;
use PHPUnit\Framework\TestCase;

class GoogleDriveAuthResourceFactoryTest extends TestCase
{
    private GoogleDriveAuthResourceFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new GoogleDriveAuthResourceFactory();
    }

    public function testGetClassName(): void
    {
        $reflection = new \ReflectionClass($this->factory);
        $method = $reflection->getMethod('getClassName');
        $method->setAccessible(true);

        $className = $method->invoke($this->factory, 'xlsx');
        $this->assertEquals('\Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Builder\Xlsx', $className);

        $className = $method->invoke($this->factory, 'matrix');
        $this->assertEquals('\Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Builder\Matrix', $className);
    }

    public function testCreateWithXlsxFormat(): void
    {
        $resource = $this->createMock(GoogleDriveAuthResource::class);
        $resource->method('getFormat')->willReturn('xlsx');

        $builder = $this->factory->create($resource);
        $this->assertInstanceOf(\Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Builder\Xlsx::class, $builder);
    }

    public function testCreateWithMatrixFormat(): void
    {
        $resource = $this->createMock(GoogleDriveAuthResource::class);
        $resource->method('getFormat')->willReturn('matrix');

        $builder = $this->factory->create($resource);
        $this->assertInstanceOf(\Atico\SpreadsheetTranslator\Provider\GoogleDriveAuth\Builder\Matrix::class, $builder);
    }
}
