<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\IdCardTemplate;
use Illuminate\Http\Request;

class SchoolTemplateController extends Controller
{
    /**
     * Get all templates for a specific school
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function index(School $school)
    {
        $templates = IdCardTemplate::where('school_id', $school->id)
            ->where('is_active', true)
            ->select('id', 'name', 'description')
            ->get();

        return response()->json($templates);
    }
}
