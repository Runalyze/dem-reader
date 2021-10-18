<?php

$header = <<<EOF
This file is part of the Runalyze DEM Reader.

(c) RUNALYZE <mail@runalyze.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        'header_comment' => [
            'header' => $header
        ],
        'ordered_imports' => true,
        'php_unit_construct' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'phpdoc_separation' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__.DIRECTORY_SEPARATOR.'src')
    )
;
