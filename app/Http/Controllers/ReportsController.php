<?php

namespace App\Http\Controllers;

use App\Models\School;

class ReportsController extends Controller
{
    /**
     * Display the schools report.
     */
    public function schools()
    {
        // Get all schools with counts for students and staff
        // Assuming relationships 'students' and 'staff' exist on School model
        // If not, we might need to check School model.
        // Based on previous conversations/code, Student belongsTo School, so School hasMany Student.

        $schools = School::withCount(['students', 'staff'])->get();

        return view('reports.schools', compact('schools'));
    }
}
