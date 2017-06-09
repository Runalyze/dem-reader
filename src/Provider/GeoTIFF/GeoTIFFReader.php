<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Provider\GeoTIFF;

use Runalyze\DEM\Exception\RuntimeException;
use Runalyze\DEM\Provider\AbstractResourceReader;

/**
 * @see http://www.awaresystems.be/imaging/tiff/tifftags.html
 */
class GeoTIFFReader extends AbstractResourceReader
{
    /**
     * The number of bytes required to hold a TIFF offset address.
     * @var int
     */
    const LEN_OFFSET = 4;

    /**
     * Magic number located at bytes 2-3 which identifies a TIFF file.
     * @var int
     */
    const MAGIC_TIFF_ID = 42;

    /** @var int */
    const TIFF_CONST_STRIPOFSETS = 273;

    /** @var int */
    const TIFF_CONST_IMAGE_WIDTH = 256;

    /** @var int */
    const TIFF_CONST_IMAGE_LENGTH = 257;

    /** @var int */
    const TIFF_CONST_BITS_PER_SAMPLE = 258;

    /** @var int */
    const TIFF_CONST_SAMPLES_PER_PIXEL = 277;

    /** @var int */
    const TIFF_CONST_ROWS_PER_STRIP = 278;

    /** @var int */
    const TIFF_CONST_STRIPBYTECOUNTS = 279;

    /** @var int */
    const TIFF_CONST_IFD_ENTRY_BYTES = 12;

    /** @var string */
    const BIG_ENDIAN = 'MM';

    /** @var string */
    const LITTLE_ENDIAN = 'II';

    /** @var int */
    const UNKNOWN = -32768;

    /** @var int The number of bytes containing each item of elevation data */
    protected $BytesPerSample = 2;

    /** @var int 	The number of components per pixel */
    protected $SamplesPerPixel = 1;

    /** @var int The number of rows per strip */
    protected $RowsPerStrip = 1;

    /** @var int */
    protected $NumDataRows;

    /** @var int */
    protected $NumDataCols;

    /** @var int */
    protected $StripOffsets;

    /** @var string */
    protected $ByteOrder;

    public function readHeader()
    {
        $this->checkByteOrderAndTiffIdentifier();
        $this->goToIFDEntries();
        $this->readIFDEntries();
    }

    /**
     * Go to the file header and work out the byte order (bytes 0-1) and TIFF identifier (bytes 2-3).
     * @throws RuntimeException
     */
    protected function checkByteOrderAndTiffIdentifier()
    {
        fseek($this->FileResource, 0);
        $data = unpack('c2chars/vTIFF_ID', fread($this->FileResource, 4));

        if (static::MAGIC_TIFF_ID !== $data['TIFF_ID']) {
            throw new RuntimeException('Provided GeoTIFF file is not a valid tiff file.');
        }

        $this->ByteOrder = sprintf('%c%c', $data['chars1'], $data['chars2']);

        if ($this->ByteOrder !== static::LITTLE_ENDIAN && $this->ByteOrder !== static::BIG_ENDIAN) {
            throw new RuntimeException('Provided GeoTIFF file has an unknown byte order.');
        }
    }

    /**
     * The remaining 4 bytes in the header are the offset to the IFD.
     */
    protected function goToIFDEntries()
    {
        fseek($this->FileResource, 4);

        $ifdOffsetFormat = $this->isLittleEndian() ? 'VIFDoffset' : 'NIFDoffset';
        $data = unpack($ifdOffsetFormat, fread($this->FileResource, 4));

        fseek($this->FileResource, $data['IFDoffset']);
    }

    /**
     * Read IFD entries which (the number of entries in each is in the first two bytes).
     */
    protected function readIFDEntries()
    {
        $countFormat = $this->isLittleEndian() ? 'vcount' : 'ncount';
        $countData = unpack($countFormat, fread($this->FileResource, 2));
        $ifdFormat = $this->isLittleEndian() ? 'vtag/vtype/Vcount/Voffset' : 'ntag/ntype/Ncount/Noffset';

        for ($i = 0; $i < $countData['count']; ++$i) {
            $constData = unpack($ifdFormat, fread($this->FileResource, static::TIFF_CONST_IFD_ENTRY_BYTES));

            switch ($constData['tag']) {
                case static::TIFF_CONST_IMAGE_WIDTH:
                    $this->NumDataCols = $constData['offset'];
                    break;
                case static::TIFF_CONST_IMAGE_LENGTH:
                    $this->NumDataRows = $constData['offset'];
                    break;
                case static::TIFF_CONST_BITS_PER_SAMPLE:
                    $this->BytesPerSample = (int) ($constData['offset'] / 8);
                    break;
                case static::TIFF_CONST_SAMPLES_PER_PIXEL:
                    $this->SamplesPerPixel = $constData['offset'];
                    break;
                case static::TIFF_CONST_ROWS_PER_STRIP:
                    $this->RowsPerStrip = $constData['offset'];
                    break;
                case static::TIFF_CONST_STRIPOFSETS:
                    $this->StripOffsets = $constData['offset'];
                    break;
            }
        }
    }

    /**
     * @return bool
     */
    protected function isLittleEndian()
    {
        return static::LITTLE_ENDIAN === $this->ByteOrder;
    }

    /**
     * @param  float   $relativeLatitude
     * @param  float   $relativeLongitude
     * @return float[] array(row, col)
     */
    public function getExactRowAndColFor($relativeLatitude, $relativeLongitude)
    {
        return [
            $relativeLatitude * ($this->NumDataRows - 1),
            $relativeLongitude * ($this->NumDataCols - 1),
        ];
    }

    /**
     * @param  int      $row
     * @param  int      $col
     * @return int|bool
     */
    public function getElevationFor($row, $col)
    {
        fseek($this->FileResource, $this->StripOffsets + ceil($row / $this->RowsPerStrip) * static::LEN_OFFSET);

        $firstColumnData = unpack('Voffset', fread($this->FileResource, static::LEN_OFFSET));

        fseek($this->FileResource, $firstColumnData['offset'] + ($this->NumDataCols * ($row % $this->RowsPerStrip) + $col) * $this->BytesPerSample);

        $elevation = unpack('velevation', fread($this->FileResource, $this->BytesPerSample))['elevation'];

        return ($elevation <= self::UNKNOWN || $elevation >= -self::UNKNOWN) ? false : $elevation;
    }
}
