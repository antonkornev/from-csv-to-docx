<?php

$data = file_get_contents('C:\Users\m5f10\Desktop\tilda-report\files\Toggl_Track_summary_report_2022-09-01_2022-09-30.csv');

$exploded = explode("\n", $data);

unset($exploded[0]);
unset($exploded[count($exploded)]);

$formatted = [];
foreach ($exploded as $item) {

    if (strpos($item, '"') === false) {
        $split = explode(',', $item);

        $formatted[] = [
            'project' => $split[0],
            'task' => $split[2],
            'time' => $split[3],
        ];

    }

    $firstMarkPoint = strpos($item, ',');

    $project = substr($item, 0, $firstMarkPoint);
}

print_r($formatted);