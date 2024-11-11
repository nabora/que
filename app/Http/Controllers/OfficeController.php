<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection; // Import the Collection class

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

            $today = now()->toDateString();
            $latestTransaction = Transaction::where('office_id', $office->id)
                ->whereDate('created_at', $today)
                ->orderBy('queue_number', 'desc')
                ->first();

            if ($latestTransaction) {
                $lastNumber = intval(substr($latestTransaction->queue_number, -3));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1; // Start from 1 if it's the first transaction of the day
            }

            $queueNumber = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'queue_number' => $queueNumber,
                'office_id' => $office->id,
                'service' => json_encode($request->services),
                'session_id' => $sessionId,
            ]);

            // Only store the current transaction in the session
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

    public function dashboard(Request $request)
    {
        // Get the selected month and year from the request, defaulting to the current month and year if not selected
        $selectedMonth = $request->input('month', now()->month);
        $selectedYear = $request->input('year', now()->year);

        // Get the offices and services
        $offices = Office::with('services')->get(); // Fetch offices with their services
        $services = []; // Initialize an array to hold service transaction counts
        $transactions = collect(); // Initialize a collection for transactions
        $totalTransactionsPerOffice = []; // Initialize an array for total transactions per office

        foreach ($offices as $office) {
            $totalTransactions = 0; // Initialize total transactions for the office

            foreach ($office->services as $service) {
                // Count transactions for the selected month and year
                $service->transactions_count = DB::table('transactions')
                    ->where('office_id', $office->id)
                    ->whereRaw('JSON_CONTAINS(service, \'["' . $service->service . '"]\')')
                    ->whereMonth('created_at', $selectedMonth)
                    ->whereYear('created_at', $selectedYear)
                    ->count();

                // Add to total transactions for the office
                $totalTransactions += $service->transactions_count;

                // Store service data for later use
                $services[] = $service;

                // Store transaction data in the collection
                $transactions->push([
                    'service' => $service->service,
                    'count' => $service->transactions_count,
                ]);
            }

            // Store total transactions for the office
            $totalTransactionsPerOffice[$office->office_name] = $totalTransactions;
        }

        // Pass the selected month and year to the view, along with offices, services, transactions, and total transactions data
        return view('Monitor.superDashboard', compact('offices', 'services', 'transactions', 'totalTransactionsPerOffice', 'selectedMonth', 'selectedYear'));
    }

    public function officeDetails($id, Request $request)
    {
        $office = Office::with('services')->findOrFail($id);
        $selectedDate = $request->input('date', now()->format('Y-m')); // Default to current month

        // Fetch transactions for the selected month, grouped by service
        $transactions = DB::table('transactions')
            ->select('service', DB::raw('count(*) as count'))
            ->where('office_id', $id)
            ->whereMonth('created_at', date('m', strtotime($selectedDate))) // Filter by month
            ->whereYear('created_at', date('Y', strtotime($selectedDate))) // Filter by year
            ->groupBy('service')
            ->get()
            ->map(function ($transaction) {
                // Decode the service if it's a JSON string
                $servicesArray = json_decode($transaction->service, true); // Decode as associative array
                // Convert the array to a string
                $transaction->service = is_array($servicesArray) ? implode(', ', $servicesArray) : $transaction->service; // Join array into a string
                return $transaction;
            });

        return view('Monitor.officeDetails', compact('office', 'transactions', 'selectedDate'));
    }

}