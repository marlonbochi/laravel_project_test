<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
	{
		$search = $request->input('search');
		
		return Inertia::render('Products/Index', [
			'filters' => $request->only('search'),
			'products' => fn() => Product::query()  // ← Closure lazy!
				->when($search, fn($q, $s) => 
					$q->where('name', 'like', "%{$s}%")
					->orWhere('description', 'like', "%{$s}%")
					->orWhere('category', 'like', "%{$s}%")
					->orWhere('sku', 'like', "%{$s}%")
				)
				->orderBy('name')
				->paginate(15)
				->withQueryString()
		]);
	}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
