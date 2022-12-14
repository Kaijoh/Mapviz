<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    public function addReport(Request $request)
    {
        $report = new Report;
        $report->user_id = $request->user_id;
        $report->name = $request->name;
        $report->location = $request->location;
        $report->latitude = $request->latitude;
        $report->longitude = $request->longitude;
        $report->date = $request->date;
        $report->save();

        return back()->with('success','Report added successfully!');
    }
}
