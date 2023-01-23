<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class Organization extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Admin/Custom/Index', [
            'data' => \App\Models\Organization::query()
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
                        'activate' => route('organizations.update', [$item->id]),
                        'edit' => route('organizations.edit', [$item->id]),
                        'delete' => route('organizations.destroy', [$item->id]),
                    ]
                ]),
            'filter' => request('search'),
            'header' => 'Organizations',
            'columns' => [
                'name' => 'Name'
            ],
            'routes' => [
                'create' => route('organizations.create'),
                'search' => route('organizations.index')
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return Inertia::render('Admin/Custom/Create', [
            'routes' => [
                'create' => route('organizations.store'),
                'cancel' => route('organizations.index')
            ],
            'header' => 'New Organization',
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
            'name' => ['required', 'max:50'],
            'active' => ['boolean', 'required']
        ]);
        \App\Models\Organization::create($input);

        return Redirect::route('organizations.index')->with(['message' => 'New organization successfully created', 'icon' => 'success']);
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
     * @return Response
     */
    public function edit($id)
    {
        $record = \App\Models\Organization::find($id);
        if(!$record)
            return Redirect::route('organizations.index')->with(['message' => 'No records found', 'icon' => 'error']);

        return Inertia::render('Admin/Custom/Create', [
            'id' => $record->id,
            'routes' => [
                'update' => route('organizations.update', [$record->id]),
                'cancel' => route('organizations.index')
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
        $record = \App\Models\Organization::find($id);
        if(!$record)
            return Redirect::route('organizations.index')->with(['message' => 'No records found', 'icon' => 'error']);

        $record->update($request->all());
        return Redirect::route('organizations.index')->with(['message' => 'Organization successfully updated', 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $record = \App\Models\Organization::find($id);
        if(!$record)
            return Redirect::route('organizations.index')->with(['message' => 'No records found', 'icon' => 'error']);

        $record->delete();
        return Redirect::route('organizations.index')->with(['message' => 'Organization successfully deleted', 'icon' => 'success']);
    }
}
