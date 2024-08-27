<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Fetch queue data from the transactions table
        $queues = DB::table('transactions')
            ->select('queue_number', 'office_id')
            ->orderBy('created_at', 'asc') // Order by oldest first
            ->get();
            

        // Return data as JSON
        return response()->json([
            'offices' => $offices,
            'queues' => $queues,
        ]);
    }
}