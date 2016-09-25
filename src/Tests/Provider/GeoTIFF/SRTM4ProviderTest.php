<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Tests\Provider\GeoTIFF;

use Runalyze\DEM\Interpolation\BilinearInterpolation;
use Runalyze\DEM\Provider\GeoTIFF\SRTM4Provider;

class SRTM4ProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    const PATH_TO_FILES = '/../../../../tests/testfiles';

    /**
     * @var SRTM4Provider
     */
    protected $Provider;

    public function setUp()
    {
        $this->Provider = new SRTM4Provider(__DIR__.self::PATH_TO_FILES);
        $this->Provider->setInterpolation(new BilinearInterpolation());
    }

    /**
     * @param string $filename
     */
    protected function checkFile($filename)
    {
        if (!$this->fileIsThere($filename)) {
            $this->markTestSkipped('Required testfile "'.$filename.'" is not available.');
        }
    }

    /**
     * @param  string $filename
     * @return bool
     */
    protected function fileIsThere($filename)
    {
        return file_exists(__DIR__.self::PATH_TO_FILES.'/'.$filename);
    }

    public function testThatLocationOutOfBoundsIsRecognized()
    {
        $this->assertFalse($this->Provider->hasDataFor([
            [90.0, 0.1],
        ]));
    }

    public function testThatThereIsNotDataForSomewhereOverTheRainbow()
    {
        $this->assertFalse($this->Provider->hasDataFor([[18.979, -45.703]]));
    }

    public function testInvalidLocation()
    {
        $this->assertFalse($this->Provider->getElevation(0.0, 0.0));
    }

    public function testThatAvailableFileIsRecognized()
    {
        $this->checkFile('srtm_38_03.tif');

        $this->assertTrue($this->Provider->hasDataFor([
            [49.4, 7.7],
            [49.5, 7.6],
        ]));
    }

    public function testThatFilenameFormatCanBeChanged()
    {
        $this->checkFile('srtm_38_03.tif');

        $this->Provider->setFilenameFormat('srtm-%02d-%02d.tif');
        $this->assertFalse($this->Provider->hasDataFor([[49.4, 7.7]]));

        $this->Provider->setFilenameFormat('srtm_%02d_%02d.tif');
        $this->assertTrue($this->Provider->hasDataFor([[49.4, 7.7]]));
    }

    public function testTileBoundary()
    {
        $this->checkFile('srtm_38_03.tif');

        if (
            !$this->fileIsThere('srtm_37_02.tif') &&
            !$this->fileIsThere('srtm_37_03.tif') &&
            !$this->fileIsThere('srtm_38_04.tif') &&
            !$this->fileIsThere('srtm_39_03.tif') &&
            !$this->fileIsThere('srtm_39_04.tif')
        ) {
            $this->assertTrue($this->Provider->hasDataFor([[50.00000, 5.00000]]));
            $this->assertFalse($this->Provider->hasDataFor([[50.00000, 4.99999]]));

            $this->assertTrue($this->Provider->hasDataFor([[45.00001, 9.99999]]));
            $this->assertFalse($this->Provider->hasDataFor([[45.00000, 9.99999]]));
            $this->assertFalse($this->Provider->hasDataFor([[45.00001, 10.0000]]));
        } else {
            $this->markTestSkipped('Can\'t check boundaries of tile as too many files are there.');
        }
    }

    public function testSingleElevationInGermany()
    {
        $this->checkFile('srtm_38_03.tif');

        $this->assertEquals(237, $this->Provider->getElevation(49.444722, 7.768889));
    }

    public function testThatUnknownElevationInSydneyIsGuessedBySurroundingValues()
    {
        $this->checkFile('srtm_67_19.tif');

        $this->assertEquals(4, $this->Provider->getElevation(-33.8705667, 151.1486337));
    }

    public function testMultipleElevationsInSydney()
    {
        $this->checkFile('srtm_67_19.tif');

        $this->assertEquals(
            [4, 4, 3],
            $this->Provider->getElevations(
                [-33.8706555, -33.8705667, -33.8704860],
                [151.1486918, 151.1486337, 151.1485585]
            )
        );
    }

    public function testLocationsInLondonLondon()
    {
        $this->checkFile('srtm_36_02.tif');

        $this->assertEquals(
            [20, 19, 19],
            $this->Provider->getElevations(
                [51.5073509, 51.5074509, 51.5075509],
                [-0.1277583, -0.1278583, -0.1279583]
            )
        );
    }

    public function testLocationsInWindhoek()
    {
        $this->checkFile('srtm_40_17.tif');

        $this->assertEquals(
            [1668, 1671, 1672],
            $this->Provider->getElevations(
                [-22.5700, -22.5705, -22.5710],
                [17.0836,  17.0841,  17.0846]
            )
        );
    }

    public function testNewYork()
    {
        $this->checkFile('srtm_22_04.tif');

        $this->assertEquals(
            [26, 29, 39],
            $this->Provider->getElevations(
                [40.7127,  40.7132,  40.7137],
                [-74.0059, -74.0064, -74.0069]
            )
        );
    }

    public function testNewYorkWithoutInterpolation()
    {
        $this->checkFile('srtm_22_04.tif');

        $this->Provider->removeInterpolation();

        $this->assertEquals(
            [26, 32, 32],
            $this->Provider->getElevations(
                [40.7127,  40.7132,  40.7137],
                [-74.0059, -74.0064, -74.0069]
            )
        );
    }

    /**
     * @see https://github.com/Runalyze/dem-reader/issues/1
     */
    public function testUnknownValuesSavedAs65535()
    {
        $this->checkFile('srtm_38_02.tif');

        $elevations = $this->Provider->getElevations([54.44702], [9.875502]);
        $this->assertLessThan(100, $elevations[0]);
        $this->assertGreaterThan(0, $elevations[0]);
    }
}
