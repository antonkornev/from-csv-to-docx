<?php

class Service
{
    public static function formatTime($time): string
    {
        $time = substr($time, 0, strripos($time, ':'));

        $exploded = explode(':', $time);

        $hours = (int)$exploded[0];
        $minutes = (int)$exploded[1];
        $minutes = round($minutes/15) * 15;

        if ($minutes == 60) {
            $hours++;
            $minutes = 0;
        }
        else {
            $minutes = round($minutes / 60, 2);
            $minutes = str_replace('0.', '', $minutes);
        }

        if ($hours == 0 && $minutes == 0) {
            $minutes = 25;
        }

        return (double)$hours . ($minutes > 0 ? '.' . $minutes : '');
    }

    public static function formatReportData($data): array
    {
        $exploded = explode("\n", $data);

        unset($exploded[0]);
        unset($exploded[count($exploded)]);

        $formatted = [];
        foreach ($exploded as $item) {

            $split = explode(',', $item);

            $project = $split[0];

            if (!strpos($item, '"') !== false) {
                $time = self::formatTime($split[3]);
                $formatted[$project][] = [
                    'task' => $split[2],
                    'time' => $time,
                ];
            }
            else {
                $firstMarkPoint = strpos($item, '"');
                $lastMarkPoint = strpos($item, '"', $firstMarkPoint + 1);

                $task = substr($item, $firstMarkPoint + 1, $lastMarkPoint - $firstMarkPoint - 1);
                $time = substr($item, $lastMarkPoint + 2);

                $time = self::formatTime($time);

                $formatted[$project][] = [
                    'task' => $task,
                    'time' => $time,
                ];
            }
        }

        return $formatted;
    }
}

