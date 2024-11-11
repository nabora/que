<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QueueDisplayController extends Controller
{
    public function index()
    {
        // Fetch data from the offices table
        $offices = DB::table('offices')->get();

        // Fetch queue data from the transactions table
        $queues = DB::table('transactions')
            ->select('queue_number', 'office_id')
            ->orderBy('created_at', 'desc') // Order by latest first
            ->get();

        // Pass the data to the view
        return view('monitor.display', [
            'offices' => $offices,
            'queues' => $queues,
        ]);
    }

    public function fetchQueues()
    {
        // Fetch data from the offices table
        $offices = DB::table('offices')->get();

        // Fetch queue data from the transactions table including status
        $queues = DB::table('transactions')
            ->select('queue_number', 'office_id', 'status', 'created_at')
            ->whereIn('status', ['called', 'pending'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Fetch only 'not present' transactions
        $notPresentTransactions = DB::table('transactions')
            ->join('offices', 'transactions.office_id', '=', 'offices.id')
            ->select('transactions.queue_number', 'offices.office_name')
            ->where('transactions.status', 'absent') // Changed from 'not_present' to 'not present'
            ->orderBy('transactions.created_at', 'asc')
            ->get();

        // Return data as JSON
        return response()->json([
            'offices' => $offices,
            'queues' => $queues,
            'notPresentTransactions' => $notPresentTransactions,
        ]);
    }

    public function dashboard()
    {
        $user = Auth::user(); // Get the authenticated user

        // Fetch the current queue number
        $currentQueue = Transaction::where('office_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->first();

        $pendingNumbers = Transaction::where('office_id', $user->id)
            ->where('status', 'pending')
            ->when($user->id == 1, function ($query) {
                $query->where('queue_number', 'like', 'SDS%');
            })
            ->orderBy('created_at', 'asc')
            ->pluck('queue_number');

        // Fetch all removed queue numbers for the current user’s office
        $removedNumbers = Transaction::where('office_id', $user->id)
            ->where('status', 'removed')
            ->orderBy('created_at', 'asc')
            ->pluck('queue_number');

        return view('dashboard', compact('user', 'currentQueue', 'pendingNumbers', 'removedNumbers'));
    }

}