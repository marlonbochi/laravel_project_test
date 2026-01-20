<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = Purchase::with(['user', 'products'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Purchases/Index', [
            'purchases' => $purchases,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Purchases/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,completed,cancelled,refunded',
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer,cash',
            'transaction_id' => 'required|unique:purchases,transaction_id',
            'notes' => 'nullable|string',
        ]);

        $purchase = Purchase::create($validated);

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase, Request $request)
    {
        $purchase->load(['user', 'products']);
        
        // Get all purchases to determine position
        $allPurchases = Purchase::orderBy('id')->pluck('id')->toArray();
        $currentIndex = array_search($purchase->id, $allPurchases) + 1;
        $total = count($allPurchases);
        
        // Get previous and next purchases
        $previousId = $currentIndex > 1 ? $allPurchases[$currentIndex - 2] : null;
        $nextId = $currentIndex < $total ? $allPurchases[$currentIndex] : null;

        return Inertia::render('Purchases/Show', [
            'purchase' => $purchase,
            'pagination' => [
                'current' => $currentIndex,
                'total' => $total,
                'previous_id' => $previousId,
                'next_id' => $nextId,
                'page' => $request->get('page', 1)
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        return Inertia::render('Purchases/Edit', [
            'purchase' => $purchase,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,completed,cancelled,refunded',
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer,cash',
            'transaction_id' => 'required|unique:purchases,transaction_id,' . $purchase->id,
            'notes' => 'nullable|string',
        ]);

        $purchase->update($validated);

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase deleted successfully.');
    }
}
