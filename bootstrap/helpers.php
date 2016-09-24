<?php

use Illuminate\Support\Debug\Dumper;

if (! function_exists('d')) {
  function d() {
    array_map(function ($x) {
      (new Dumper)->dump($x);
    }, func_get_args());
  }
}

if (! function_exists('resource_path')) {
  function resource_path($path = '') {
    return base_path('resources' . DIRECTORY_SEPARATOR . $path);
  }
}
