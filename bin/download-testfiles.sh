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

for file in n40_w075 n49_e007 n51_w001 s23_e017 s34_e151
do
    if [ ! -f tests/testfiles/${file}_1arc_v3.tif ]
    then
        curl https://cdn.runalyze.com/static/srtm1/${file}_1arc_v3.tif > tests/testfiles/${file}_1arc_v3.tif
    fi
done