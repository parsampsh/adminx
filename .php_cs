<?php

$header = <<<EOF
This file is part of Adminx.
  Copyright 2020 parsa shahmaleki <parsampsh@gmail.com>
Licensed Under GPL-v3
For more information, please view the LICENSE file
EOF;

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->name('*.php');

return PhpCsFixer\Config::create()
    ->setUsingCache(true)
    ->setRiskyAllowed(true)
    ->setRules(array(
        'header_comment' => array('header' => $header),
    ))
    ->setFinder($finder);

