#!/bin/sh
set -ex

mkdir -p tests/testfiles

for file in srtm_38_03 srtm_38_02 srtm_67_19 srtm_36_02 srtm_40_17 srtm_22_04
do
    if [ ! -f tests/testfiles/${file}.tif ]
    then
        curl http://srtm.csi.cgiar.org/SRT-ZIP/SRTM_V41/SRTM_Data_GeoTiff/${file}.zip > tests/testfiles/${file}.zip
        unzip -o tests/testfiles/${file}.zip ${file}.tif -d tests/testfiles
        rm -rf tests/testfiles/${file}.zip
    fi
done