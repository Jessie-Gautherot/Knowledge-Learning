<?php

use Doctum\Doctum;
use Symfony\Component\Finder\Finder;

$finder = Finder::create()
    ->files()
    ->in(__DIR__ . '/src');

return new Doctum($finder, [
    'title' => 'Knowledge Learning',
    'build_dir' => __DIR__ . '/docs/api',
    'cache_dir' => __DIR__ . '/docs/cache',
    'default_opened_level' => 2,
]);