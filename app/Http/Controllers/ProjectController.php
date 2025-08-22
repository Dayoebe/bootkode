<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show($slug)
    {
        $project = Project::where('slug', $slug)->with(['course', 'submissions.user'])->firstOrFail();
        return view('projects.show', compact('project'));
    }
}