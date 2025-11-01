<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     */
    public function index(Request $request)
    {
        $query = Transaction::with('user', 'order');

        // Search by reference or user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('paystack_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(20);

        // Calculate statistics
        $totalAmount = Transaction::successful()->sum('amount');
        $pendingAmount = Transaction::pending()->sum('amount');
        $failedCount = Transaction::where('status', 'failed')->count();

        return view('admin.transactions.index', compact('transactions', 'totalAmount', 'pendingAmount', 'failedCount'));
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load('user', 'order.items');
        return view('admin.transactions.show', compact('transaction'));
    }
}