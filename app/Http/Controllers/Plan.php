<?php

namespace App\Http\Controllers;

use App\Events\NewOrder;
use App\Events\NewSale;
use App\Jobs\OrderJob;
use App\Models\Category;
use App\Models\Desk;
use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Nette\Utils\Random;

class Plan extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render("Admin/Plan", [
            'sections' => Section::query()
                ->where('organization_id', Auth::user()->organization_id)
                ->where('active', true)
                ->chunkMap(fn($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'desk_count' => $item->desks()
                        ->where('active', true)
                        ->count()
                ])
                ->filter(fn($item) => $item["desk_count"] > 0),
            '_desks' => Desk::query()
                ->where('active', true)
                ->whereNotNull('section_id')
                ->whereHas('section', function ($query) {
                    $query
                        ->where('active', true)
                        ->where('organization_id', Auth::user()->organization_id);
                })
                ->get()
                ->map(fn(Desk $desk) => [
                    'id' => $desk->id,
                    'name' => $desk->name,
                    'sale' => $desk->sale() ?
                        collect($desk->sale())->put('total', $desk->sale()?->details()->get()->sum(fn ($detail) => $detail->quantity * $detail->price))->all()
                        : null,
                    'section' => $desk->section()->first(),
                    'openedBy' => null
                ]),
            'categories' => Category::query()
                ->where('organization_id', Auth::user()->organization_id)
                ->where('active', true)
                ->whereHas('products', function ($query) {
                    $query
                        ->where('active', true)
                        ->where('organization_id', Auth::user()->organization_id);
                })
                ->get(),
            'products' => Product::query()
                ->where('organization_id', Auth::user()->organization_id)
                ->where('active', true)
                ->whereNotNull('category_id')
                ->whereHas('category', function ($query) {
                    $query
                        ->where('active', true)
                        ->where('organization_id', Auth::user()->organization_id);
                })
                ->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Desk $record
     * @return RedirectResponse
     */
    public function update(Request $request, Desk $record)
    {
        if($request->has('details') || ($request->has('access_code') && in_array($request->get('access_code'), [true, null]))) {
            if($request->get('access_code') === true)
                $request->merge(['access_code' => Random::generate(6, '0-9A-Z')]);

            $sale = $record->sales()->updateOrCreate(['id' => $request->get('id')], $request->except(['details']));

            collect($request->get('details'))->except(['id'])->each(function($detail) use ($sale) {
                $current = $sale->details()->where('product_id', $detail['product_id'])->first();
                if($current)
                    $current->update(['quantity' => $current->quantity + $detail['quantity']]);
                else
                    $sale->details()->updateOrCreate(['product_id' => $detail['product_id']], collect($detail)->except(['id'])->all());
            });

            if(!$sale->access_code && $record->sale() && $record->sale()->details()->count() == 0)
                $record->sale()->delete();

            NewSale::broadcast($record->id)->toOthers();

            if($request->has('details'))
                OrderJob::dispatch($record->load('section'), $request->get('details'));
        }
        return Redirect::route('plan.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Desk $record
     * @param int $detail_id
     * @return RedirectResponse
     */
    public function destroy(Request $request, Desk $record, int $detail_id)
    {
        $record->sale()->details()->firstWhere('id', $detail_id)->delete() && $record->sale()->details()->count() == 0 && $record->sale()->delete();
        NewSale::broadcast($record->id);
        return Redirect::route('plan.index');
    }
}
