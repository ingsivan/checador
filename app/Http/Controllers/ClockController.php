<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClockController extends Controller
{
    /**
     * Register the next mark of the day (arrival, lunch out, lunch back or leave).
     * Steps must happen in order (lunch is optional), so the request names the step it
     * wants and a stale page (double tap, two tabs) can't skip or repeat one.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'step' => ['required', Rule::in(array_keys(Attendance::STEPS))],
        ]);

        $attendance = $request->user()->todayAttendance()->first()
            ?? new Attendance(['user_id' => $request->user()->id, 'date' => today()]);

        if (! in_array($data['step'], $attendance->allowedSteps(), true)) {
            return back()->with('error', 'Ese registro ya no está disponible. Revisa tu estado de hoy.');
        }

        $attendance->{$data['step']} = now();
        $attendance->save();

        $message = match ($data['step']) {
            'check_in_at' => '¡Buen día! Llegada registrada a las '.now()->format('H:i').'.',
            'lunch_out_at' => '¡Buen provecho! Tu timer de comida ya está corriendo.',
            'lunch_in_at' => 'Bienvenido de vuelta. Comiste '.$attendance->lunchMinutes().' min.',
            'check_out_at' => 'Salida registrada. ¡Hasta mañana!',
        };

        return redirect()->route('dashboard')->with('status', $message);
    }
}
