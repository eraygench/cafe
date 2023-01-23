<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class User extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Admin/Custom/Index', [
            'data' => \App\Models\User::query()
                ->when(request('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->where('id', '!=', Auth::user()->id)
                ->paginate(10)
                ->withQueryString()
                ->through(fn($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'email' => $item->email,
                    'organization' => $item->organization?->name,
                    'active' => $item->active,
                    'routes' => [
                        'activate' => route('users.update', [$item->id]),
                        'edit' => route('users.edit', [$item->id]),
                        'delete' => route('users.destroy', [$item->id]),
                    ]
                    /*'can' => [
                        'edit' => Auth::user()->can('edit', $user),
                        'delete' => Auth::user()->can('delete', $user)
                    ]*/
                ]),
            'filter' => request('search'),
            'header' => 'Users',
            'columns' => [
                'name' => 'Name'
            ],
            'routes' => [
                'create' => route('users.create'),
                'search' => route('users.index')
            ],
            /*'can' => [
                'createUser' => Auth::user()->can('create', User::class)
            ]*/
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
                'create' => route('users.store'),
                'cancel' => route('users.index')
            ],
            'header' => 'New User',
            'fields' => [
                [
                    'name' => 'organization_id',
                    'label' => 'Organization',
                    'type' => 'select',
                    'optional' => true,
                    'items' => \App\Models\Organization::query()
                        ->where('active', true)
                        ->get()
                        ->pluck('name', 'id')
                ],
                [
                    'name' => 'name',
                    'label' => 'Name'
                ],
                [
                    'name' => 'email',
                    'label' => 'Email',
                    'type' => 'email'
                ],
                [
                    'name' => 'password',
                    'label' => 'Password',
                    'type' => 'password'
                ],
                [
                    'name' => 'is_admin',
                    'label' => 'Admin',
                    'type' => 'checkbox',
                    'value' => false
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
            'email' => ['required', 'email', 'max:50', 'unique:users'],
            'password' => ['required', 'min:4', 'max:32'],
            'active' => ['required', 'boolean'],
            'is_admin' => ['required', 'boolean'],
            'organization_id' => ['nullable', 'exists:organizations,id']
        ]);
        $input["password"] = Hash::make($input["password"]);
        \App\Models\User::create($input);

        return Redirect::route('users.index')->with(['message' => 'New user successfully created', 'icon' => 'success']);
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
        $record = \App\Models\User::find($id);
        if(!$record)
            return Redirect::route('users.index')->with(['message' => 'No records found', 'icon' => 'error']);

        return Inertia::render('Admin/Custom/Create', [
            'id' => $record->id,
            'routes' => [
                'update' => route('users.update', [$record->id]),
                'cancel' => route('users.index')
            ],
            'fields' => [
                [
                    'name' => 'organization_id',
                    'label' => 'Organization',
                    'type' => 'select',
                    'optional' => true,
                    'items' => \App\Models\Organization::query()
                        ->where('active', true)
                        ->get()
                        ->pluck('name', 'id'),
                    'value' => $record->organization_id
                ],
                [
                    'name' => 'name',
                    'label' => 'Name',
                    'value' => $record->name
                ],
                [
                    'name' => 'email',
                    'label' => 'Email',
                    'type' => 'email',
                    'value' => $record->email
                ],
                [
                    'name' => 'password',
                    'label' => 'Password',
                    'type' => 'password'
                ],
                [
                    'name' => 'is_admin',
                    'label' => 'Admin',
                    'type' => 'checkbox',
                    'value' => $record->is_admin
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
        $record = \App\Models\User::find($id);
        if(!$record)
            return Redirect::route('users.index')->with(['message' => 'No records found', 'icon' => 'error']);

        $record->update($request->isNotFilled('password') ? $request->except('password') : collect($request->all())->put('password', Hash::make($request->get('password')))->all());
        return Redirect::route('users.index')->with(['message' => 'User successfully updated', 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $record = \App\Models\User::find($id);
        if(!$record)
            return Redirect::route('users.index')->with(['message' => 'No records found', 'icon' => 'error']);

        $record->delete();
        return Redirect::route('users.index')->with(['message' => 'User successfully deleted', 'icon' => 'success']);
    }
}
