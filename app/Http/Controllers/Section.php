<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class Section extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Admin/Custom/Index', [
            'data' => \App\Models\Section::query()
                ->where('organization_id', '=', Auth::user()->organization_id)
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
                        'activate' => route('sections.update', [$item->id]),
                        'show' => route('sections.desks.index', [$item->id]),
                        'edit' => route('sections.edit', [$item->id]),
                        'delete' => route('sections.destroy', [$item->id]),
                    ]
                ]),
            'filter' => request('search'),
            'header' => 'Sections',
            'route' => 'sections',
            'columns' => [
                'name' => 'Name'
            ],
            'routes' => [
                'create' => route('sections.create'),
                'search' => route('sections.index')
            ],
            'actions' => [
                'show' => true
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Admin/Custom/Create', [
            'routes' => [
                'create' => route('sections.store'),
                'cancel' => route('sections.index')
            ],
            'header' => 'New Section',
            'fields' => [
                [
                    'name' => 'name',
                    'label' => 'Name'
                ],
                [
                    'name' => 'active',
                    'label' => 'Active',
                    'type' => 'checkbox',
                    'value' => true
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
    public function store(Request $request)
    {
        $input = $request->validate([
            'name' => ['required', 'max:100'],
            'active' => ['boolean', 'required']
        ]);
        \App\Models\Section::create($input);

        return Redirect::route('sections.index')->with(['message' => 'New section successfully created', 'icon' => 'success']);
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
    public function edit($id)
    {
        $record = \App\Models\Section::find($id);
        if(!$record)
            return Redirect::route('sections.index')->with(['message' => 'No records found', 'icon' => 'error']);

        return Inertia::render('Admin/Custom/Create', [
            'id' => $record->id,
            'routes' => [
                'update' => route('sections.update', [$record->id]),
                'cancel' => route('sections.index')
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
    public function update(Request $request, $id)
    {
        $record = \App\Models\Section::find($id);
        if(!$record)
            return Redirect::route('sections.index')->with(['message' => 'No records found', 'icon' => 'error']);

        $record->update($request->all());
        return Redirect::route('sections.index')->with(['message' => 'Section successfully updated', 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $record = \App\Models\Section::find($id);
        if(!$record)
            return Redirect::route('sections.index')->with(['message' => 'No records found', 'icon' => 'error']);

        $record->delete();
        return Redirect::route('sections.index')->with(['message' => 'Section successfully deleted', 'icon' => 'success']);
    }
}
