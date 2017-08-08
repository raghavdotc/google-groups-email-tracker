<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    protected $guarded = [];

    public static function getClientFromId($clientId)
    {
        $client = static::where('id', $clientId)->first();
        if (is_null($client)) {
            $client = static::create([
                'id' => $clientId,
                'name' => $clientId
            ]);
        }
        return $client;
    }
}
