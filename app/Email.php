<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{

    protected $guarded = [];

    public static function storeEmailData($emailData)
    {
        $email = Email::where('message_id', $emailData['message_id'])->first();
        if (is_null($email) && $from = static::isValidEmail($emailData['From'])) {
            $clientId = static::extractClientId($emailData['Subject']);
            if (!empty($clientId)) {
                $sender = User::getSenderFromEmail($from, $emailData['From']);
                $client = Client::getClientFromId($clientId);
                $sentDate = Carbon::parse($emailData['Date']);
                $email = Email::create([
                    'message_id' => $emailData['message_id'],
                    'sender_id' => $sender->id,
                    'client_id' => $client->id,
                    'subject' => $emailData['Subject'],
                    'created_at' => $sentDate->toDateTimeString()
                ]);
            }
        }
        return $email;
    }

    private static function isValidEmail($from)
    {
        $pattern = '/[a-z0-9_\-\+]+@[a-z0-9\-]+\.([a-z]{2,3})(?:\.[a-z]{2})?/i';
        preg_match_all($pattern, $from, $matches);
        $from = $matches[0][0];
        $allowed = [env('EMAIL_DOMAIN')];
        if (filter_var($from, FILTER_VALIDATE_EMAIL)) {
            $explodedEmail = explode('@', $from);
            $domain = array_pop($explodedEmail);
            if (in_array($domain, $allowed)) {
                return $from;
            }
        }
        return false;
    }

    private static function extractClientId($subject)
    {
        preg_match_all('/(\d{4})/', $subject, $matches);
        return @$matches[0][0];
    }

    public function sender()
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }
}
