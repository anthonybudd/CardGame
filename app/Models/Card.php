<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Curl\Curl;
use Exception;

class Card extends Model
{
    protected $fillable = [
        'value', 'suit',
    ];

    public function isSuit($suit)
    {
        return $suit === $this->suit;
    }

    public function getSymbol()
    {
        switch($this->suit){
            case 'spades':
                return '♠';
            case 'diamonds':
                return '♦';
            case 'hearts':
                return '♥';
            case 'clubs':
                return '♣';
        }
    }

    public static function getCards()
    {
        $curl = new Curl();
        $curl->get('https://cards.davidneal.io/api/cards');

        if ($curl->error) {
            throw new Exception($curl->errorMessage);
            return;
        }
        
        $response = $curl->response;
        if (!is_array($response) || count($response) !== 52) {
            throw new Exception('Bad API response');
        }

        $collection = collect();
        foreach ($response as $key => $card) {
            $collection->push(new Card([
                'value' => $card->value,
                'suit' => $card->suit,
            ]));
        }
        $collection->shuffle();
        return $collection;
    }


    public function isHigherThan(Card $otherCard)
    {
        $table = ['A', '2', '3', '4', '5', '6', '7', '8', '9', 'J', 'Q', 'K'];
        $cardValue = array_search($this->value, $table);
        $otherCardValue = array_search($otherCard->value, $table);
        return $cardValue > $otherCardValue;
    }

    public function isLowerThan(Card $otherCard)
    {
        $table = ['A', '2', '3', '4', '5', '6', '7', '8', '9', 'J', 'Q', 'K'];
        $cardValue = array_search($this->value, $table);
        $otherCardValue = array_search($otherCard->value, $table);
        return $cardValue < $otherCardValue;
    }   
}
