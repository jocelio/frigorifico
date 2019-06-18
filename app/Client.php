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

    public static function calculateFormattedBalance($operationsList){
        return self::formatMoney(self::calculateBalance($operationsList));
    }

    public static function calculateBalance($operationsList){
        $operations = collect($operationsList);

        $sells = $operations->filter(function ($operation) {
            return $operation->type == '0';
        })->sum('value');

        $payments = $operations->filter(function ($operation) {
            return $operation->type == '1';
        })->sum('value');

        return $payments - $sells;
    }

    public function getBalance(){
        return Client::calculateBalance($this->operations);
    }

    public function getAccOperations(){
        $operations = collect($this->operations);

        $acc = $operations->reduce(function ($carry, $operation) {
            $operation['acc'] = $operation->type == 0? $operation->value + $carry : $carry - $operation->value;
            return $operation->type == 0? $operation->value + $carry : $carry - $operation->value;
        },0);

        return $operations;
    }

    public static function formatMoney($value){
        return number_format($value, 2, ',', '.');
    }

    public function getFormattedBalance(){
        return Client::formatMoney($this->getBalance());
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
        $last = $operations->filter(function ($operation) {
            return $operation->type == '1';
        })->sortBy('date')
            ->map(function ($operation) {
            return $operation->getElapsedDays();
        })->last();

        return !is_null($last)? $last: null;
    }

    public function printTest($printer, $printerName){
        $this->printHeader($printer);

        $printer -> setTextSize(2, 2);
        $printer->text("Impressora ".$printerName." configurada e pronta para uso.");
        $printer->text("\n");

        $printer -> cut();
        $printer -> close();

    }

    public static function printHeader($printer){
        $printer -> setTextSize(2, 1);
        $printer ->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Wostin da Carne");
        $printer -> setTextSize(1, 1);
        $printer->text("\n");
        $printer->text("(85) 987385952 - (85) 987621156");
        $printer->text("\n");
        $printer->text(Carbon::now()->format('d/m/Y H:i:s'));
        $printer->text("\n");
        $printer->text("________________________________________________");
        $printer->text("\n");
    }

    public function printHistory($printer, $printTotal = true){

        $this->printHeader($printer);
        $clienteFull = Client::findOrFail($this->id);

        $printer ->setJustification(Printer::JUSTIFY_LEFT);
        $printer ->setColor(Printer::COLOR_2);
        $separator = " - ";

        $printer->text("CLIENTE: ".$clienteFull->nome);
        $printer->text("\n");
        $printer->text("________________________________________________");
        $printer->text("\n");

        $printer->text("VALOR  - TIPO -    DATA    - SALDO   - INFO\n");

        collect($this->getAccOperations())->each(function ($operation) use ($printer, $separator) {
            $printer->text(str_pad($operation->getFormattedValue(), 6));
            $printer->text($separator);
            $printer->text($operation->type == 0? 'COMP':'PGTO');
            $printer->text($separator);
            $printer->text(str_pad($operation->getFormattedDate(),7));
            $printer->text($separator);
            $printer->text(str_pad($operation->getFormattedAcc(),7));
            $printer->text($separator);
            if($operation->acc == 0) $printer->text("QUIT");
            if($operation->acc < 0) $printer->text("PSTV");
            $printer->text("\n");
        });

        $printer->text("SALDO TOTAL: ". $clienteFull->getBalance());
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

    public static function printDay($printer, $operations){

        Client::printHeader($printer);

        $printer ->setJustification(Printer::JUSTIFY_LEFT);
        $printer ->setColor(Printer::COLOR_2);

        $printer ->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Relatório do dia: ". $operations[0]->getFormattedDate());
        $printer->text("\n");
        $printer->text("________________________________________________");
        $printer->text("\n");
        $printer ->setJustification(Printer::JUSTIFY_LEFT);
        $printer -> setTextSize(1, 1);

        $separator = " - ";

        $printer->text("CLIENTE         - HORA     - VALOR   - OPERAÇÃO\n");

        collect($operations)->each(function ($operation) use ($printer, $separator) {
            $printer->text(str_pad(substr($operation->client->nome, 0,14), 15));
            $printer->text($separator);
            $printer->text(str_pad($operation->getFormattedTime(),7));
            $printer->text($separator);
            $printer->text(str_pad($operation->getFormattedValue(),7));
            $printer->text($separator);
            $printer->text($operation->type == 0? 'COMP':'PGTO');
            $printer->text("\n");
        });

        $printer->text("________________________________________________");
        $printer->text("\n");
        $printer->text("SALDO TOTAL: ". Client::calculateFormattedBalance($operations));
        $printer->text("\n");

        $printer -> cut();
        $printer -> close();

    }

}
