<?php

if (! function_exists('minutes_label')) {
    /**
     * 135 → "2 h 15 min", 40 → "40 min".
     */
    function minutes_label(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        if ($h === 0) {
            return "{$m} min";
        }

        return $m === 0 ? "{$h} h" : "{$h} h {$m} min";
    }
}
