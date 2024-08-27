<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class DataCleanupController extends Controller
{
    public function resetExpiredData()
    {
        Artisan::call('data:reset-expired');
        return redirect()->back()->with('status', 'Expired data has been deleted.');
    }
}
