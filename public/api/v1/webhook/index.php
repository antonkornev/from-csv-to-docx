<?php

define("DIR", dirname(__DIR__, 4));

require_once DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'WordCreator.php';
require_once DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Telegram.php';
require_once DIR . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Service.php';

try {

    $inputData = file_get_contents('php://input');
    if (empty($inputData)) {
        throw new Exception('Empty input data');
    }

    if (
        substr($inputData,0,1) !== '{'
        && substr($inputData,0,1) !== '['
    ) {
        throw new Exception('Invalid json');
    }

    $arData = json_decode($inputData, true);
    if (json_last_error() != JSON_ERROR_NONE) {
        throw new Exception('Json decode error: ' . json_last_error());
    }

    $userId = $arData['message']['from']['id'];

    $fileName = $arData['message']['document']['file_name'];
    $fileId = $arData['message']['document']['file_id'];

    $fileData = Telegram::getFileDataById($fileId);

    $formatted = Service::formatReportData($fileData);

    $file = WordCreator::create($formatted, $fileName);

    Telegram::sendMessage($userId, 'File created');
    Telegram::sendFile($userId, $file);
}
catch (Exception $e) {
    if (isset($userId)) {
        Telegram::sendMessage($userId, $e->getMessage());
    }
}