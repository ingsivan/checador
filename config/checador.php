<?php

return [
    // Minutes allowed for lunch. The timer turns amber near the limit and red past it.
    'lunch_minutes' => (int) env('CHECADOR_LUNCH_MINUTES', 60),

    // Minutes of grace after the scheduled start before an arrival counts as late.
    'late_tolerance_minutes' => (int) env('CHECADOR_LATE_TOLERANCE', 10),
];
