<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

$data = new \TaskForce\Spl\CsvImporterSpl(__DIR__ . '/data/cities.csv');
$data->import();
$inserter = new \TaskForce\Spl\SqlExporterSpl(
    __DIR__ . '/db/cities.sql',
    $data->getData()
);
$inserter->export();