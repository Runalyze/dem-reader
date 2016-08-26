# DEM reader

[![Latest Stable Version](https://img.shields.io/packagist/v/runalyze/dem-reader.svg)](https://packagist.org/packages/runalyze/dem-reader)
[![Build Status](https://travis-ci.org/Runalyze/dem-reader.svg?branch=master)](https://travis-ci.org/Runalyze/dem-reader)
[![Code Coverage](https://scrutinizer-ci.com/g/Runalyze/dem-reader/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Runalyze/dem-reader/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Runalyze/dem-reader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Runalyze/dem-reader/?branch=master)
[![MIT License](https://img.shields.io/github/license/twbs/bootlint.svg)](https://github.com/Runalyze/dem-reader/blob/master/LICENSE)

Library to read digital elevation models, such as GeoTIFF files for SRTM.
The GeoTIFF reader itself is originally based on [Bob Osola's SRTMGeoTIFFReader](http://www.osola.org.uk/elevations/index.htm).

## Usage

```php
use Runalyze\DEM\Reader;
use Runalyze\DEM\Provider\SRTM4Provider;

$Provider = new SRTM4Provider('path/to/srtm/files');
$Reader = new Reader($Provider);
$elevations = $Reader->getElevations($latitudes, $longitudes);
```

To give you some more details: Each provider is valid for its own. You can just ignore the general reader and
use your favorite provider:

```php
use Runalyze\DEM\Provider\SRTM4Provider;

$Provider = new SRTM4Provider('path/to/srtm/files');
$elevations = $Provider->getElevations($latitudes, $longitudes);
```

But you may have more than one provider available or different paths where your dem files are located.
You can attach as many providers as you want to the reader. Each of them will be checked if it can handle
the given elevation data (in the order they were attached) and the wirst one that does will be used.

```php
use Runalyze\DEM\Reader;
use Runalyze\DEM\Provider\SRTM4Provider;

$Reader = new Reader();
$Reader->addProvider(new SRTM4Provider('path/to/europe/srtm/files'));
$Reader->addProvider(new SRTM4Provider('path/to/america/srtm/files'));

$europeElevations = $Reader->getElevations($europeLatitudes, $europeLongitudes);
$americaElevations = $Reader->getElevations($americaLatitudes, $americaLongitudes);
```

## License

Code released under [the MIT license](LICENSE).
