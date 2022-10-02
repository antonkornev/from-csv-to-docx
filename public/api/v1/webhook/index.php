<?php

die('yes');

require_once dirname(__DIR__, 4) . '/vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

$data = file_get_contents(dirname(__DIR__, 4) . '/files/Toggl_Track_summary_report_2022-09-01_2022-09-30.csv');

$exploded = explode("\n", $data);

unset($exploded[0]);
unset($exploded[count($exploded)]);

$formatted = [];
foreach ($exploded as $item) {

    $split = explode(',', $item);

    if (!str_contains($item, '"')) {
        $formatted[] = [
            'project' => $split[0],
            'task' => $split[2],
            'time' => $split[3],
        ];
    }
    else {
        $firstMarkPoint = strpos($item, '"');
        $lastMarkPoint = strpos($item, '"', $firstMarkPoint + 1);

        $task = substr($item, $firstMarkPoint + 1, $lastMarkPoint - $firstMarkPoint - 1);
        $time = substr($item, $lastMarkPoint + 2);

        $formatted[] = [
            'project' => $split[0],
            'task' => $task,
            'time' => $time,
        ];
    }

    $firstMarkPoint = strpos($item, ',');
    $project = substr($item, 0, $firstMarkPoint);
}

$price = array_column($formatted, 'project');

array_multisort($price, SORT_ASC, $formatted);

$phpWord = new PhpWord();

$section = $phpWord->addSection();

$tableStyle = array(
    'borderColor' => '006699',
    'borderSize'  => 6,
    'cellMargin'  => 50
);

foreach ($formatted as $item) {

    $phpWord->addTableStyle('myTable', $tableStyle);
    $table = $section->addTable('myTable');

    $table->addRow();

    $cell1 = $table->addCell(3000);
    $cell1->addText($item['project']);

    $cell2 = $table->addCell(5000);
    $cell2->addText($item['task']);

    $cell3 = $table->addCell(2000);
    $cell3->addText($item['time']);
}

$objWriter = IOFactory::createWriter($phpWord);
$objWriter->save('reports/report.docx');