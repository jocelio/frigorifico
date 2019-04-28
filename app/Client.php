<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Mike42\Escpos\Printer;

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

    public function printTest($printer, $printerName){
        $this->printHeader($printer);

        $printer -> setTextSize(2, 2);
        $printer->text("Impressora ".$printerName." configurada e pronta para uso.");
        $printer->text("\n");

        $printer -> cut();
        $printer -> close();

    }

    public function printHeader($printer){
        $printer -> setTextSize(2, 1);
        $printer ->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Wostin das Carnes");
        $printer -> setTextSize(1, 1);
        $printer->text("\n");
        $printer->text("(85) 988387818 - 07.373.434/0001-86");
        $printer->text("\n");
        $printer->text(Carbon::now()->format('d/m/Y H:i:s'));
        $printer->text("\n");
        $printer->text("________________________________________________");
        $printer->text("\n");
    }

    public function printHistory($printer){

        $this->printHeader($printer);


        $printer ->setJustification(Printer::JUSTIFY_LEFT);
        $printer ->setColor(Printer::COLOR_2);

        $separator = " - ";

        collect($this->getAccOperations())->each(function ($operation) use ($printer, $separator) {
            $printer->text(str_pad($operation->getFormattedValue(), 7));
            $printer->text($separator);
            $printer->text(str_pad($operation->type == 0? 'COMPRA':'PAGAMENTO', 9));
            $printer->text($separator);
            $printer->text(str_pad($operation->getFormattedAcc(),7));
            $printer->text($separator);
            if($operation->acc == 0) $printer->text("FECHAMENTO");
            if($operation->acc < 0) $printer->text("S. POSITIVO");
            $printer->text("\n");
        });

        $printer->text("SALDO TOTAL: ". $this->getBalance());
        $printer->text("\n");

        $printer -> cut();
        $printer -> close();

    }

    public function printOperation($printer, $operation){

        $this->printHeader($printer);

        $printer ->setJustification(Printer::JUSTIFY_LEFT);
        $printer ->setColor(Printer::COLOR_2);

        $printer->text("CLIENTE: ". $this->nome ." - ".$this->cpf);
        $printer->text("\n");
        $printer->text("DATA DA OPERAÇÃO: ". $operation->getFormattedDate());
        $printer->text("\n");
        $printer->text("TIPO DA OPERAÇÃO: ");
        $printer->text($operation->type == 0? 'COMPRA':'PAGAMENTO');
        $printer->text("\n");
        $printer->text("VALOR: ". $operation->getFormattedValue());
        $printer->text("\n");
        $printer->text("SALDO TOTAL: ". $this->getBalance());
        $printer->text("\n");
        $printer->text("\n");
        $printer->text("*Confira se a data da operação corresponde à \ndata corrente.");
        $printer->text("\n");

        $printer -> cut();
        $printer -> close();

    }

}
