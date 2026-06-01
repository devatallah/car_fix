<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Script;
use App\Models\SolutionTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class SolutionTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $scripts = Script::get();

        return view('portals.admin.solution_templates.index', compact('scripts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $scripts = Script::get();

        return view('portals.admin.solution_templates.create', compact('scripts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'script_uuid' => 'required|exists:scripts,uuid',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'patterns' => 'nullable|json',
            'template_file' => 'nullable|string',
        ]);

        SolutionTemplate::create($request->all());

        Session::flash('success', 'Solution Template created successfully.');

        return redirect()->route('admin.solution-templates.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SolutionTemplate  $solutionTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(SolutionTemplate $solutionTemplate)
    {
        return view('portals.admin.solution_templates.show', compact('solutionTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SolutionTemplate  $solutionTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(SolutionTemplate $solutionTemplate)
    {
        $scripts = Script::get();

        return view('portals.admin.solution_templates.edit', compact('solutionTemplate', 'scripts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SolutionTemplate  $solutionTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SolutionTemplate $solutionTemplate)
    {
        $request->validate([
            'script_uuid' => 'required|exists:scripts,uuid',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'patterns' => 'nullable|json',
            'template_file' => 'nullable|string',
        ]);

        $solutionTemplate->update($request->all());

        Session::flash('success', 'Solution Template updated successfully.');

        return redirect()->route('admin.solution-templates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SolutionTemplate  $solutionTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(SolutionTemplate $solutionTemplate)
    {
        $solutionTemplate->delete();

        Session::flash('success', 'Solution Template deleted successfully.');

        return redirect()->route('admin.solution-templates.index');
    }

    public function getData(Request $request)
    {
        $query = SolutionTemplate::with('script');

        return DataTables::of($query)
            ->addColumn('actions', function ($template) {
                return view('portals.admin.solution_templates.partials.actions', compact('template'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}