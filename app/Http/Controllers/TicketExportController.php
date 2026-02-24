<?php

namespace App\Http\Controllers;

use App\Exports\TicketsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TicketExportController
{
    public function export(Request $request)
    {
        $eventId = $request->query('event_id');

        $fileName = 'tickets_report_'.date("d-m-Y_H-i-s");
        if ($eventId) {
            $fileName .= "_event_{$eventId}";
        }
        $fileName .= '.xlsx';

        return Excel::download(new TicketsExport($eventId), $fileName);
    }
}
