<?php

namespace App\Jobs;

use App\Email;
use App\GMailAPI;
use Google_Service_Gmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FetchUserThread implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var
     */
    private $userId;
    /**
     * @var
     */
    private $threadId;
    /**
     * @var
     */
    private $accessToken;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $threadId, $accessToken)
    {
        //
        $this->userId = $userId;
        $this->threadId = $threadId;
        $this->accessToken = $accessToken;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = GMailAPI::getClient($this->accessToken);
        $service = new Google_Service_Gmail($client);
        $user = 'me';
        $thread = $service->users_threads->get($user, $this->threadId);
        $messages = $thread->getMessages();
        $emailData = [];
        foreach ($messages as $message) {
            $emailData['message_id'] = $message->getId();
            $payload = $message->getPayload();
            $headers = $payload->getHeaders();
            foreach ($headers as $header) {
                if (in_array($header['name'], ['Subject', 'From', 'Date'])) {
                    $emailData[$header['name']] = $header['value'];
                }
            }
            break;
        }
        if (!empty($emailData)) {
            Email::storeEmailData($emailData);
        }
    }
}
