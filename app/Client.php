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

    public function getAccOperations(){
        $operations = collect($this->operations);

        $acc = $operations->reduce(function ($carry, $operation) {
            $operation['acc'] = $operation->type == 0? $operation->value + $carry : $carry - $operation->value;
            return $operation->type == 0? $operation->value + $carry : $carry - $operation->value;
        },0);

        return $operations;
    }

    public function getFormattedBalance(){
        return number_format($this->getBalance(), 2, ',', '.');
    }

    public function getLastPurchase(){
        $operations = collect($this->operations);
        return $operations->filter(function ($operation) {
            return $operation->type == '0';
        })->sortBy('date')->map(function ($operation) {
            return $operation->getFormattedDate();
        })->first();
    }

    public function getLastPurchaseInDays(){
        $operations = collect($this->operations);
        return $operations->filter(function ($operation) {
            return $operation->type == '0';
        })->sortBy('date')->map(function ($operation) {
            return $operation->getElapsedDays();
        })->first();
    }
}
