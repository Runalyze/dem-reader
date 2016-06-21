<?php

$header = <<<EOF
This file is part of the Runalyze DEM Reader.

(c) RUNALYZE <mail@runalyze.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config\Config::create()
    ->fixers(array(
        'header_comment',
        'ordered_use',
        'php_unit_construct',
        'strict',
        'strict_param',
        '-phpdoc_separation',
    ))
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in(__DIR__.DIRECTORY_SEPARATOR.'src')
    )
;
