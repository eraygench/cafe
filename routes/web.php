<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*Route::get('login', function () {
    return Inertia::render("Auth/Login");
})->name('login');

Route::post('login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended();
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
});

Route::post('logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->middleware('auth');*/

Route::get('/', function () {
   return 'index';
});

Route::get('/menu/{organization_uuid}', function ($organization_uuid) {
    $organization = \App\Models\Organization::query()->where('active', true)->where('uuid', $organization_uuid)->with('categories.products')->firstOrFail();
    $categories = $organization
        ->categories()
        ->where('active', true)
        ->whereHas('products', function ($query) {
            $query->where('active', true);
        })
        ->get();
    $products = $organization
        ->products()
        ->where('active', true)
        ->whereNotNull('category_id')
        ->whereHas('category', function ($query) {
            $query->where('active', true);
        })
        ->get();
    $sale = request('ac') ? $organization->sales()->where('status', false)->where('access_code', request('ac'))->first() : null;
    if(request('ac') && !$sale)
        return \Illuminate\Support\Facades\Redirect::route('menu', $organization_uuid)->with(['message' => 'Order not fonud', 'icon' => 'error']);

    return Inertia::render('Menu', [
        'organization' => $organization,
        'categories' => $categories,
        'products' => $products,
        'sale' => $sale?->uuid
    ]);
})->name('menu');

Route::put('/menu/{organization_uuid}', function ($organization_uuid) {
    $organization = \App\Models\Organization::query()->where('active', true)->where('uuid', $organization_uuid)->with('categories.products')->first();
    if(!$organization)
        return \Illuminate\Support\Facades\Redirect::route('menu', $organization_uuid);

    $sale = $organization->sales()->where('status', false)->where('uuid', request('sale'))->whereNotNull('access_code')->first();
    if(!$sale)
        return \Illuminate\Support\Facades\Redirect::route('menu', $organization_uuid)->with(['message' => 'Order not found', 'icon' => 'error']);

    if(request()->has('details')) {
        collect(request()->get('details'))->each(function($detail) use ($sale) {
            $current = $sale->details()->where('product_id', $detail['product_id'])->first();
            if($current)
                $current->update(['quantity' => $current->quantity + $detail['quantity']]);
            else
                $sale->details()->updateOrCreate(['product_id' => $detail['product_id']], collect($detail)->except(['id'])->all());
        });
        $sale->update(['access_code' => null]);
        broadcast(new \App\Events\NewSale($sale->desk->id))->toOthers();
    }

    return \Illuminate\Support\Facades\Redirect::route('menu', $organization_uuid)->with(['message' => 'Successfully placed order', 'icon' => 'success']);
});

Route::middleware('auth')->prefix('admin')->group(function () {

    Route::get('/', function () {
        return Inertia::render('Admin/Home');
    })->name('home');

    Route::resource('users', \App\Http\Controllers\User::class)/*->can('isAdmin', 'App\Models\User')*/;

    Route::resource('organizations', \App\Http\Controllers\Organization::class)/*->can('isAdmin', 'App\Models\User')*/;

    Route::resource('categories', \App\Http\Controllers\Category::class)/*->can('isOrganizationUser', 'App\Models\User')*/;

    Route::resource('products', \App\Http\Controllers\Product::class)/*->can('isOrganizationUser', 'App\Models\User')*/->withTrashed();

    Route::resource('sections', \App\Http\Controllers\Section::class)/*->can('isOrganizationUser', 'App\Models\User')*/;

    Route::resource('sections.desks', \App\Http\Controllers\Desk::class)/*->can('isOrganizationUser', 'App\Models\User')*/;

    Route::get('plan', [\App\Http\Controllers\Plan::class, 'index'])->name('plan.index');
    Route::put('plan/{desk}', [\App\Http\Controllers\Plan::class, 'update'])->name('plan.update');
    Route::delete('plan/{desk}/{detail_id}', [\App\Http\Controllers\Plan::class, 'destroy'])->name('plan.delete');

    Route::get('sales', function () {
        return Inertia::render('Admin/Custom/Index', [
            'data' => \App\Models\Sale::query()
                ->where('status', true)
                ->when(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->organization_id,
                    fn ($query) => $query->where('organization_id', \Illuminate\Support\Facades\Auth::user()->organization_id),
                    fn ($query) => $query->where('user_id', \Illuminate\Support\Facades\Auth::user()->id))
                ->when(request('search'), function($query, $search) {
                    $query->where('id', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
                ->through(fn($item) => [
                    'id' => $item->id,
                    'date' => $item->created_at,
                    'desk' => $item->desk?->section?->name . ' ' . $item->desk?->name,
                    'amount' => $item->details()->get()->sum(fn ($detail) => $detail->quantity * $detail->price)
                ]),
            'filter' => request('search'),
            'header' => 'Sales',
            'columns' => [
                'id' => 'Sale Id',
                'date' => 'Date',
                'desk' => 'Desk',
                'amount' => 'Amount'
            ],
            'actions' => false,
            'routes' => [
                'search' => route('sales')
            ]
        ]);
    })->name('sales');

    Route::get('orders', function () {
        return Inertia::render('Admin/Orders');
    })->name('orders');

});
