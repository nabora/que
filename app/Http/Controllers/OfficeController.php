<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Transaction;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function user()
    {
        $offices = Office::with('services')->get();
        return view('user', compact('offices'));
    }

    public function storeServices(Request $request)
{
    $request->validate([
        'office_id' => 'required|exists:offices,id', // Validate using office_id
        'services' => 'required|array',
    ]);

    // Fetch the office using office_id
    $office = Office::findOrFail($request->office_id);

    // Generate unique transaction number
    $latestTransaction = Transaction::orderBy('created_at', 'desc')->first();
    $nextNumber = $latestTransaction ? intval(substr($latestTransaction->queue_number, -3)) + 1 : 1;
    $queueNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

    // Create transaction
    Transaction::create([
        'queue_number' => $queueNumber,
        'office_id' => $office->id, // Save the office_id correctly
        'service' => json_encode($request->services),
    ]);

    return redirect()->route('user')->with('success', 'Transaction saved successfully.');
}
}
