<?php

namespace Modules\Agenda\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Modules\Agenda\Actions\GenerateCalendarFeedAction;
use Modules\Agenda\Exceptions\CalendarTokenNotFoundException;
use Modules\Auth\Models\User;

class CalendarFeedController extends Controller
{
    public function __invoke(string $token, GenerateCalendarFeedAction $action): Response
    {
        $user = User::where('calendar_token', $token)->first();

        if (! $user) {
            throw new CalendarTokenNotFoundException;
        }

        $ical = $action->execute($user);

        return response($ical, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'inline; filename="psiagenda.ics"',
        ]);
    }
}
