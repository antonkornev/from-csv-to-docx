<?php

define("DIR", dirname(__DIR__));

require_once DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'WordCreator.php';
require_once DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Telegram.php';
require_once DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Service.php';

$filename = '2023_02';

// edit this for another person
$surname = 'kornev';
$resultFilename = "report_{$filename}_$surname";

$fileData = file_get_contents(DIR . "/files/$filename.csv");
$formatted = Service::formatReportData($fileData);
WordCreator::create($formatted, $resultFilename);