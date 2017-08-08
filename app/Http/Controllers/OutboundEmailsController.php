<?php
/**
 * Created by PhpStorm.
 * User: raghavendra
 * Date: 6/8/17
 * Time: 10:19 AM
 */

namespace App\Http\Controllers;


use App\Client;
use App\Email;
use App\GMailAPI;
use App\User;
use Carbon\Carbon;
use Google_Service_Gmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutboundEmailsController extends Controller
{

    public function index(Request $request)
    {
        $input = $request->all();
        $senders = User::all();
        $clients = Client::all();
        $query = Email::query();
        foreach ($input as $key => $value) {
            if (!empty($value)) {
                if ($key == 'client_id') {
                    $query->where('client_id', $value);
                }
                if ($key == 'sender_id') {
                    $query->where('sender_id', $value);
                }
                if ($key == 'from') {
                    $query->where('created_at', '>=', $value . " 00:00:00");
                }
                if ($key == 'to') {
                    $query->where('created_at', '<=', $value . " 00:00:00");
                }
            }
        }
        $emails = $query->paginate(15);
        return view('tracker.filters', compact('emails', 'senders', 'clients'));
    }

    public function dashboard(Request $request)
    {
        $input = $request->all();
        $query = DB::table('emails');
        foreach ($input as $key => $value) {
            if (!empty($value)) {
                if ($key == 'from') {
                    $query->where('created_at', '>=', $value . " 00:00:00");
                }
                if ($key == 'to') {
                    $query->where('created_at', '<=', $value . " 00:00:00");
                }
            }
        }
        $query->select('client_id', DB::raw('count(*) as count'));
        $query->groupBy('client_id');
        $counts = $query->paginate(15);
        return view('tracker.dashboard', compact('counts'));
    }

}