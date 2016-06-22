<?php

/*
 * This file is part of the Runalyze DEM Reader.
 *
 * (c) RUNALYZE <mail@runalyze.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Runalyze\DEM\Provider;

trait GuessInvalidValuesTrait
{
    /**
     * @param array $elevationOnBoundingBox elevation data on [p0, p1, p2, p3]
     */
    protected function guessInvalidValuesOnBox(array &$elevationOnBoundingBox)
    {
        $validValues = array_filter($elevationOnBoundingBox, function ($val) {
            return false !== $val;
        });
        $numValidValues = count($validValues);

        if ($numValidValues > 0) {
            $average = array_sum($validValues) / $numValidValues;

            foreach ($elevationOnBoundingBox as $i => $value) {
                if (false === $value) {
                    $elevationOnBoundingBox[$i] = $average;
                }
            }
        }
    }
}
