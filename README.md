# DEM reader

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

## License

Code released under [the MIT license](LICENSE).