<?php

use MaiVu\Php\Registry;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$request = Registry::request();

echo '<pre>' . print_r($request->get->toArray(), true) . '</pre>';
echo '<pre>' . print_r($request->post->toArray(), true) . '</pre>';
echo '<pre>' . print_r($request->server->toArray(), true) . '</pre>';
echo '<pre>' . print_r($request->files->toArray(), true) . '</pre>';