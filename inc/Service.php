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

        if ($minutes) {
            $minutes = round($minutes / 60, 2);
            $minutes = str_replace('0.', '', $minutes);
        }

        return $hours . ($minutes > 0 ? ',' . $minutes : '') . 'ч';
    }
}

