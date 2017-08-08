<?php

namespace App\Jobs;

use App\GMailAPI;
use Carbon\Carbon;
use Google_Service_Gmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FetchUserThreads implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $userId;

    protected $accessToken;

    /**
     * Create a new job instance.
     *
     * @param $userId
     * @param $accessToken
     */
    public function __construct($userId, $accessToken)
    {
        $this->userId = $userId;
        $this->accessToken = $accessToken;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->accessToken['expires_in'] = Carbon::now()->diffInMinutes(Carbon::parse($this->accessToken['expires_at']));
        $this->accessToken['created'] = Carbon::parse($this->accessToken['created_at'])->getTimestamp();
        $client = GMailAPI::getClient($this->accessToken);

        $service = new Google_Service_Gmail($client);

        $user = 'me';

        $results = $service->users_threads->listUsersThreads($user, [
            'q' => 'in:inbox newer_than:2d'
        ]);

        foreach ($results->getThreads() as $result) {
            dispatch(new FetchUserThread($this->userId, $result->id, $this->accessToken));
        }
    }
}
