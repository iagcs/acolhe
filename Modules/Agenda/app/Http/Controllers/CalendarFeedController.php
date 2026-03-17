<?php

namespace Modules\Agenda\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Modules\Agenda\Actions\GenerateCalendarFeedAction;
use Modules\Agenda\Exceptions\CalendarTokenNotFoundException;
use Modules\Auth\Models\User;

class CalendarFeedController extends Controller
{
    /**
     * Serve an iCalendar feed for the user identified by the provided calendar token.
     *
     * @param string $token The calendar token used to locate the user.
     * @param GenerateCalendarFeedAction $action Generates the iCal content for the located user.
     * @throws CalendarTokenNotFoundException If no user is found for the given token.
     * @return Response The HTTP response containing the ICS content and headers (Content-Type and Content-Disposition).
     */
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
