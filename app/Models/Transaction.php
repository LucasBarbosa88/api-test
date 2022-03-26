<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'type',
        'value',
        'account_id'
    ];

    public static function createTransaction($transaction){
        $account = Account::find($transaction['account_id']);
        
        if($transaction['value'] < 0) throw new Exception("0", 400);
        
        if($transaction['type'] == 'withdraw'){
            if(!$account) throw new Exception("0", 404);
            if($account->balance < $transaction['value']) throw new Exception("0", 400);

            $transaction['value'] *= -1;
        } else if (!$account){
            $account = Account::createAccount($transaction['account_id']);
        }

        return Transaction::create($transaction);
    }

    public function account(){
        return $this->belongsTo(Account::class);
    }
}
