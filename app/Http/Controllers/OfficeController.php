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

    public function store(Request $request)
{
    try {
        $request->validate([
            'office_id' => 'required|exists:offices,id',
            'services' => 'required|array',
        ]);

        $office = Office::findOrFail($request->office_id);
        $officeName = $office->office_name;
        $prefix = $this->getOfficePrefix($officeName);

        $sessionId = session()->getId(); 

        $latestTransaction = Transaction::where('office_id', $office->id)->orderBy('created_at', 'desc')->first();
        $nextNumber = $latestTransaction ? intval(substr($latestTransaction->queue_number, strlen($prefix))) + 1 : 1;
        $queueNumber = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        Transaction::create([
            'queue_number' => $queueNumber,
            'office_id' => $office->id,
            'service' => \json_encode($request->services),
            'session_id' => $sessionId,
        ]);

        session()->push('transactions', [
            'queue_number' => $queueNumber,
            'session_id' => $sessionId,
            'office_name' => $officeName,
            'services' => $request->services,
        ]);

        return redirect()->route('user')->with('success', "Transaction Saved Successfully! Select Another Transaction");
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
}


private function getOfficePrefix($officeName)
{
    $prefixes = [
        'Office of the Schools Division Superintendent' => 'SDS',
        'Office of the Assistant Schools Division Superintendent' => 'ASDS',
        'Administration Section' => 'AS',
        'Curriculum Implementation Division' => 'CID',
        'Accounting and Budget Section' => 'ABS',
        'Information and Communication Technology' => 'ICT',
        'Legal Section' => 'LS',
        'School Governance and Operations Division' => 'SGOD',
        'Schools' => 'SCH',
    ];

    return $prefixes[$officeName] ?? 'UNKNOWN';
}

public function print(Request $request)
    {
        try {
            $request->validate([
                'office_id' => 'required|exists:offices,id',
                'services' => 'required|array',
            ]);
            
            $this->store($request);

            // Fetch the stored transactions associated with this session
            $transactions = Transaction::where('session_id', session()->getId())->get();

            // Clear the session after fetching transactions
            session()->forget('transactions');

             // Invalidate the session completely
            session()->invalidate();

            // Redirect to the printTransactions view with the stored transactions
            return view('printTransactions', compact('transactions'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}