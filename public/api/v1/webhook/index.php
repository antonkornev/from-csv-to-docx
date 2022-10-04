<?php

require_once dirname(__DIR__, 4) . '/vendor/autoload.php';
require_once dirname(__DIR__, 4) . '/inc/Telegram.php';

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

try {

    $inputData = file_get_contents('php://input');
    if (empty($inputData)) {
        exit();
    }

    if (
        substr($inputData,0,1) !== '{'
        && substr($inputData,0,1) !== '['
    ) {
        exit();
    }

    $arData = json_decode($inputData, true);
    if (json_last_error() != JSON_ERROR_NONE) {
        exit();
    }

    file_put_contents('log.txt', json_encode($arData), FILE_APPEND);

    $userId = $arData['message']['from']['id'];

    $fileName = $arData['message']['document']['file_name'];
    $fileId = $arData['message']['document']['file_id'];

    $data = file_get_contents("https://api.telegram.org/bot".TOKEN."/getFile?file_id=" . $fileId);
    $data = json_decode($data, true);

    $filePath = $data['result']['file_path'];

    $data = file_get_contents("https://api.telegram.org/file/bot".TOKEN."/" . $filePath);

    file_put_contents(dirname(__DIR__, 4) . '/files/' . $fileName, $data);

    $data = file_get_contents(dirname(__DIR__, 4) . '/files/' . $fileName);

    $exploded = explode("\n", $data);

    unset($exploded[0]);
    unset($exploded[count($exploded)]);

    $formatted = [];
    foreach ($exploded as $item) {

        $split = explode(',', $item);

        if (!strpos($item, '"') !== false) {
            $formatted[$split[0]][] = [
                'task' => $split[2],
                'time' => $split[3],
            ];
        }
        else {
            $firstMarkPoint = strpos($item, '"');
            $lastMarkPoint = strpos($item, '"', $firstMarkPoint + 1);

            $task = substr($item, $firstMarkPoint + 1, $lastMarkPoint - $firstMarkPoint - 1);
            $time = substr($item, $lastMarkPoint + 2);

            $formatted[$split[0]][] = [
                'task' => $task,
                'time' => $time,
            ];
        }

        $firstMarkPoint = strpos($item, ',');
        $project = substr($item, 0, $firstMarkPoint);
    }

    $phpWord = new PhpWord();

    $section = $phpWord->addSection();

    $tableStyle = array(
        'borderColor' => '006699',
        'borderSize'  => 6,
        'cellMargin'  => 50
    );

    $phpWord->addTableStyle('myTable', $tableStyle);
    $table = $section->addTable('myTable');

    $tableWidth1 = 3000;
    $tableWidth2 = 6000;
    $tableWidth3 = 1000;

    $table->addRow();
    $cell1 = $table->addCell($tableWidth1);
    $cell1->addText('Проект (модуль)');
    $cell2 = $table->addCell($tableWidth2);
    $cell2->addText('Выполненные работы по Разработке ОИС');
    $cell3 = $table->addCell($tableWidth3);
    $cell3->addText('Время');

    foreach ($formatted as $taskName => $item) {
        $table->addRow();
        $cell1 = $table->addCell($tableWidth1);
        $cell1->addText($taskName);
        $cell2 = $table->addCell($tableWidth2);
        $cell3 = $table->addCell($tableWidth3);

        foreach ($item as $task) {
            $cell2->addListItem($task['task']);
            $cell3->addText($task['time'] . '<w:br/>');
        }
    }

    $table->addRow();
    $cell1 = $table->addCell($tableWidth1);
    $cell2 = $table->addCell($tableWidth2);
    $cell2->addText('Итого');
    $cell3 = $table->addCell($tableWidth3);
    $cell3->addText('Время');

    $objWriter = IOFactory::createWriter($phpWord);
    $file = dirname(__DIR__, 4) . '/reports/' . "$fileName.docx";

    $objWriter->save($file);

    Telegram::sendMessage($userId, 'File created');
    Telegram::sendFile($userId, $file);
}
catch (Exception $e) {
    if (isset($userId)) {
        Telegram::sendMessage($userId, $e->getMessage());
    }
}

