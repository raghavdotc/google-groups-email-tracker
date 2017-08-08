<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function findByEmail($email)
    {
        $user = static::byEmail($email)->first();

        return $user;
    }

    public static function getSenderFromEmail($from, $fromString)
    {
        $sender = static::where('email', $from)->first();
        if (is_null($sender)) {
            $emailParts = explode('<', $fromString);
            if (count($emailParts) > 1) {
                $name = trim($emailParts[0]);
            } else {
                $name = $from;
            }
            $sender = static::create([
                'name' => $name,
                'email' => $from,
                'password' => 'pass#' . rand(1000, 9999)
            ]);
        }
        return $sender;
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', '=', $email);
    }

    public function setToken($accessToken, $expiresIn)
    {
        if (!isset($this->id)) {
            return false;
        }
        SocialAccessToken::where('user_id', $this->id)->delete();
        return SocialAccessToken::create([
            'access_token' => $accessToken,
            'expires_at' => Carbon::now()->addMinutes($expiresIn),
            'user_id' => $this->id,
        ]);
    }


}
