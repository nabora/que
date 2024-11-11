<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{


    public function showDashboard()
    {
        $user = Auth::user(); // Get the authenticated user
        $officePrefix = $user->office_prefix; // Get office prefix from the user model

        // Fetch the current active transaction
        $currentTransaction = Transaction::where('status', 'active')->first();
        
        // Determine the office prefix based on the user's office ID
        $officePrefix = '';

        switch ($user->id) {
            case 2:
                $officePrefix = 'SDS';
                break;
            case 3:
                $officePrefix = 'ASDS';
                break;
            case 4:
                $officePrefix = 'AS';
                break;
            case 5:
                $officePrefix = 'CID';
                break;
            case 6:
                $officePrefix = 'ABS';
                break;
            case 7:
                $officePrefix = 'ICT';
                break;
            case 8:
                $officePrefix = 'LS';
                break;
            case 9:
                $officePrefix = 'SGOD';
                break;
            case 10:
                $officePrefix = 'SCH';
                break;
        }

        // Fetch all pending transactions by office prefix
        $pendingTransactions = Transaction::where('status', 'pending')
            ->where('queue_number', 'like', "$officePrefix%")
            ->orderBy('created_at', 'asc')
            ->pluck('queue_number');

        // Fetch all removed transactions by office prefix
        $removedTransactions = Transaction::where('status', 'removed')
            ->where('queue_number', 'like', "$officePrefix%")
            ->orderBy('created_at', 'asc')
            ->pluck('queue_number');

        // Fetch all absent transactions by office prefix
        $absentTransactions = Transaction::where('status', 'absent')
            ->where('queue_number', 'like', "$officePrefix%")
            ->orderBy('created_at', 'asc')
            ->pluck('queue_number');

        // Pass data to the view
        return view('dashboard', [
            'user' => $user,
            'currentQueue' => $currentTransaction ? $currentTransaction->queue_number : 'None',
            'pendingNumbers' => $pendingTransactions,
            'removedNumbers' => $removedTransactions,
            'absentNumbers' => $absentTransactions
        ]);
    }


    public function nextQueue()
    {
        $user = auth()->user();
        $officePrefix = $this->getOfficePrefix($user->id);

        $nextQueue = Transaction::where('status', 'pending')
            ->where('queue_number', 'like', "$officePrefix%")
            ->orderBy('created_at', 'asc')
            ->first();

        if ($nextQueue) {
            // Change status to 'active'
            $nextQueue->status = 'active';
            $nextQueue->save();

            return response()->json([
                'success' => true,
                'queueNumber' => $nextQueue->queue_number,
                'office' => $this->getOfficeFromQueueNumber($nextQueue->queue_number)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No pending queues available.'
        ]);
    }

    protected function getOfficePrefix($officeId)
    {
        // Define the office prefixes based on office IDs
        $officePrefixes = [
            2 => 'SDS',
            3 => 'ASDS',
            4 => 'AS',
            5 => 'CID',
            6 => 'ABS',
            7 => 'ICT',
            8 => 'LS',
            9 => 'SGOD',
            10 => 'SCH',
            // Add more mappings as needed
        ];

        // Return the prefix for the given office ID
        return $officePrefixes[$officeId] ?? '';
    }

    protected function getOfficeFromQueueNumber($queueNumber)
    {
        $prefix = substr($queueNumber, 0, strpos($queueNumber, '-') ?: 3);
        $officeMap = [
            'SDS' => 'SDS Office',
            'ASDS' => 'ASDS Office',
            'AS' => 'AS Office',
            'CID' => 'CID Office',
            'ABS' => 'ABS Office',
            'ICT' => 'ICT Office',
            'LS' => 'LS Office',
            'SGOD' => 'SGOD Office',
            'SCH' => 'School Office',
        ];
        return $officeMap[$prefix] ?? 'Unknown Office';
    }

    public function callQueue(Request $request)
    {
        $queueNumber = $request->input('queueNumber');
        $transaction = Transaction::where('queue_number', $queueNumber)->first();

        if ($transaction && $transaction->status === 'active') {
            $transaction->status = 'called';
            $transaction->save();

            return response()->json([
                'success' => true,
                'message' => 'Queue called successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No active queue to call.'
        ]);
    }

    public function removeQueue(Request $request)
    {
        $queueNumber = $request->input('queueNumber');
        $transaction = Transaction::where('queue_number', $queueNumber)->first();

        if ($transaction) {
            $transaction->status = 'removed';
            $transaction->save();

            return response()->json([
                'success' => true,
                'message' => 'Queue removed successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Queue not found.'
        ]);
    }

    public function repeatQueue(Request $request)
    {
        // Fetch the current active transaction
        $currentTransaction = Transaction::where('status', 'active')->first();

        // Return the current queue number for repetition
        return response()->json(['queueNumber' => $currentTransaction ? $currentTransaction->queue_number : 'None']);
    }

    public function markAbsent(Request $request)
    {
        $queueNumber = $request->input('queueNumber');
        $transaction = Transaction::where('queue_number', $queueNumber)->first();

        if ($transaction) {
            $transaction->status = 'absent';
            $transaction->save();

            return response()->json([
                'success' => true,
                'message' => 'Queue marked as absent successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Queue not found.'
        ]);
    }

    public function absentQueue(Request $request)
    {
        $queueNumber = $request->input('queueNumber');
        $transaction = Transaction::where('queue_number', $queueNumber)->first();

        if ($transaction) {
            $transaction->status = 'absent';
            $transaction->save();

            return response()->json([
                'success' => true,
                'message' => 'Queue marked as absent successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Queue not found.'
        ]);
    }

    public function removeAbsentQueue(Request $request)
    {
        $queueNumber = $request->input('queueNumber');
        
        $transaction = Transaction::where('queue_number', $queueNumber)
                              ->where('status', 'absent')
                              ->first();

        if ($transaction) {
            $transaction->status = 'removed';
            $transaction->save();
            return response()->json(['success' => true, 'message' => 'Queue removed successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to remove queue.']);
    }
  
}