<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Curl\Curl;

class Card extends Model
{
    
    public static function getCards()
    {
        $curl = new Curl();
        $curl->get('https://cards.davidneal.io/api/cards');

        if ($curl->error) {
            throw new Exception($curl->errorMessage);
            return;
        }
        
        dd($curl->response);
    }
}
