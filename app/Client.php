<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'nome', 'endereco', 'cpf', 'telefone'
    ];

    public function operations()
    {
        return $this->hasMany('App\Operation');
    }


    public function getBalance(){

        $operations = collect($this->operations);

        $sells = $operations->filter(function ($operation) {
            return $operation->type == '0';
        })->sum('value');

        $payments = $operations->filter(function ($operation) {
            return $operation->type == '1';
        })->sum('value');

        return $payments - $sells;

    }

    public function getLastPurchase(){
        $operations = collect($this->operations);
        return $operations->filter(function ($operation) {
            return $operation->type == '0';
        })->sortBy('date')->map(function ($operation) {
            return $operation->getFormattedDate();
        })->first();
    }
}
