<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;

class Desk extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index($id)
    {
        $record = \App\Models\Section::find($id);
        if(!$record)
            return Redirect::route('sections.index')->with(['message' => 'No records found', 'icon' => 'error']);

        return Inertia::render('Admin/Custom/Index', [
            'data' => \App\Models\Desk::query()
                ->where('organization_id', '=', Auth::user()->organization_id)
                ->where('section_id', '=', $record->id)
                ->when(request('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
                ->through(fn($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'active' => $item->active,
                    'routes' => [
                        'activate' => route('sections.desks.update', [$record->id,$item->id]),
                        'edit' => route('sections.desks.edit', [$record->id,$item->id]),
                        'delete' => route('sections.desks.destroy', [$record->id,$item->id]),
                    ]
                ]),
            'filter' => request('search'),
            'header' => 'Desks of the '. $record->name,
            'columns' => [
                'name' => 'Name'
            ],
            'routes' => [
                'create' => route('sections.desks.create', [$record->id]),
                'search' => route('sections.desks.index', [$record->id])
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create($id)
    {
        $record = \App\Models\Section::find($id);
        if(!$record)
            return Redirect::route('sections.index')->with(['message' => 'No records found', 'icon' => 'error']);

        return Inertia::render('Admin/Custom/Create', [
            'routes' => [
                'create' => route('sections.desks.store', [$record->id]),
                'cancel' => route('sections.desks.index', [$record->id])
            ],
            'route' => 'sections/'.$record->id.'/desks',
            'header' => 'Generate Desk',
            'fields' => [
                [
                    'name' => 'count',
                    'label' => 'Number of Desks',
                    'type' => 'number',
                    'value' => 1
                ]
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $id)
    {
        $section = \App\Models\Section::find($id);
        if(!$section)
            return Redirect::route('sections.index')->with(['message' => 'No records found', 'icon' => 'error']);

        $input = $request->validate([
            'count' => ['required', 'numeric', 'min:1', 'max:100']
        ]);

        $lastName = intval(\App\Models\Desk::query()
            ->where('organization_id', '=', Auth::user()->organization_id)
            ->where('section_id', '=', $section->id)
            ->orderByDesc('id')
            ->first()?->name ?? 0);

        /*if($lastName)
            $lastName = intval(!is_numeric(substr($lastName, 0, 1)) ? substr($lastName, 2) : $lastName);
        else
            $lastName = 0;*/

        \App\Models\Desk::factory(intval($input['count']), [
            'section_id' => $section->id
        ])->make()->each(function ($desk) use (&$lastName) {
            $desk->name = ++$lastName; //strtoupper($input['desk_name'] ? (substr($section->name, 0, 1) . '-') : '') . str_pad(++$lastName, 3, '0', STR_PAD_LEFT);
            $desk->save();
        });

        return Redirect::route('sections.desks.index', [$section->id])->with(['message' => 'New desks successfully created', 'icon' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit($section_id, $id)
    {
        $section = \App\Models\Section::find($section_id);
        if(!$section)
            return Redirect::route('sections.index')->with(['message' => 'No records found', 'icon' => 'error']);

        $record = \App\Models\Desk::find($id);
        if(!$record)
            return Redirect::route('sections.desks.index', [$section->id])->with(['message' => 'No records found', 'icon' => 'error']);

        return Inertia::render('Admin/Custom/Create', [
            'id' => $record->id,
            'route' => 'sections/'.$section->id.'/desks',
            'routes' => [
                'update' => route('sections.desks.update', [$section->id,$record->id]),
                'cancel' => route('sections.desks.index', [$section->id])
            ],
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'Name',
                    'value' => $record->name
                ],
                [
                    'name' => 'active',
                    'label' => 'Active',
                    'type' => 'checkbox',
                    'value' => $record->active
                ]
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $section_id, $id)
    {
        $section = \App\Models\Section::find($section_id);
        if(!$section)
            return Redirect::route('sections.index')->with(['message' => 'No records found', 'icon' => 'error']);

        $record = \App\Models\Desk::find($id);
        if(!$record)
            return Redirect::route('sections.desks.index', [$record->section->id])->with(['message' => 'No records found', 'icon' => 'error']);

        $record->update($request->all());
        return Redirect::route('sections.desks.index', [$record->section->id])->with(['message' => 'Desk successfully updated', 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($section_id, $id)
    {
        $section = \App\Models\Section::find($section_id);
        if(!$section)
            return Redirect::route('sections.index')->with(['message' => 'No records found', 'icon' => 'error']);

        $record = \App\Models\Desk::find($id);
        if(!$record)
            return Redirect::route('sections.desks.index', [$section->id])->with(['message' => 'No records found', 'icon' => 'error']);

        $record->delete();
        return Redirect::route('sections.desks.index', [$section->id])->with(['message' => 'Desk successfully deleted', 'icon' => 'success']);
    }
}
