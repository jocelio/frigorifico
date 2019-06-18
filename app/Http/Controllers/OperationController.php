<?php

namespace App\Http\Controllers;

use App\Client;
use App\Operation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Redirect;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

class OperationController extends Controller
{

    protected $operationsType = [0=>'VENDA',1=>'PAGAMENTO'];
    protected $daysForAlert = 30;
    protected $printerName = 'EPSON_TM_T20';
    protected $printer;



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
          $connector = new WindowsPrintConnector($this->printerName);
        } else {
          $connector = new CupsPrintConnector($this->printerName);
        }

        $this->printer = new Printer($connector);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $clientesAlerts = Client::with('operations')->get();

        //calculate last payment as filter
        $alerts = collect($clientesAlerts)->filter(function ($client) {
            return $client->getBalance() < 0
                && (is_null($client->getLastPurchaseInDays()) || $client->getLastPurchaseInDays() > $this->daysForAlert);
        })->count();

        $clientes = collect($clientesAlerts)->sortBy('nome')->mapWithKeys(function ($item) {
            return [$item['id'] => strtoupper($item['nome'])];
        });


        return view('operations/form', ['clientes'=> $clientes, 'types'=> $this->operationsType, 'clientsAlert' => $alerts]);
    }

    public function pendencias()
    {

        $clientesAlerts = $clients = Client::with('operations')->get();

        //calculate last payment as filter
        $alerts = collect($clientesAlerts)->filter(function ($client) {
            return $client->getBalance() < 0
                && (is_null($client->getLastPurchaseInDays()) || $client->getLastPurchaseInDays() > $this->daysForAlert);
        });

        $clientes = Client::pluck('nome', 'id');
        return view('clients/pendencias', ['clientes'=> $clientes, 'types'=> $this->operationsType, 'clientsAlert' => $alerts]);
    }

    public function printHistory($clientId, $operationId = null){

        if($operationId){
            $cliente = Client::with(['operations' => function($query) use ($operationId) {
            $query->where('id', '>=', $operationId);
        }])->find($clientId);
        }else{
            $cliente = Client::findOrFail($clientId);
        }


        $cliente->printHistory($this->printer, $operationId == null);

        return Redirect::to('/operation/'.$clientId.'/historico');
    }

    public function printDate($date){

        $operations = Operation::where('date', $date)->with('client')->get();

        Client::printDay($this->printer, $operations);

        return Redirect::back();
    }

    public function printTest(){

        $cliente = new Client();

        $cliente->printTest($this->printer, $this->printerName);

        return Redirect::back();
    }

    public function insert(Request $request){

        $request->validate([
            'value' => 'required',
            'date' => 'required',
            'type' => 'required',
            'client_id' => 'required',
        ]);

        $operation = new Operation();

        $fields = $request->all();
        $fields['user_id'] = Auth::user()->id;
        $newOperation = $operation->create($fields);

        if($request->has('print')){
            $cliente = Client::findOrFail($fields['client_id']);
            $cliente->printOperation($this->printer, $newOperation);
        }

        \Session::flash('seccess_message', 'Operação inserida com sucesso.');
        return Redirect::to('/home');
    }

    public function history($id){
        $cliente = Client::findOrFail($id);

        return view('operations/list', ['cliente' => $cliente]);
    }

    public function all(Request $request)
    {
        $day = $request->input('date');

        if ($day){
            $date = Carbon::createFromFormat('d/m/Y', $request->input('date'))->toDateString();
            $operations = Operation::where('date', $date)->with('client')->get();
            $dayBalance = Client::calculateFormattedBalance($operations);

        }else{
            $operations = Operation::with('client')->get();
            $dayBalance = null;
            $date = null;
        }

        $groupedOperations = collect($operations)->sortByDesc('date')->groupBy('date');

        return view('operations/list_all', ['groupedOperations' => $groupedOperations, 'dayBalance' => $dayBalance, 'date' => $date]);
    }

}
