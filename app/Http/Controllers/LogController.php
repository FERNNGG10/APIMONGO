<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(){
        $logs = Log::all();

        $data = $logs->map(function ($log) {
            $user = $log->Log['user_id'] !== null 
                ? User::where('id', $log->Log['user_id'])->first() 
                : (object) ['name' => 'No usuario'];
    
            return (object) [
                'log' => $log,
                'user' => $user
            ];
        });
    
        return response()->json($data);
    }
}
