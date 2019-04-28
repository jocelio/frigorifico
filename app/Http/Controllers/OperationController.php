<?php

namespace App\Http\Controllers;

use App\Client;
use App\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Redirect;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
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
        $connector = new CupsPrintConnector($this->printerName);
        $this->printer = new Printer($connector);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $clientesAlerts = $clients = Client::with('operations')->get();

        //calculate last payment as filter
        $alerts = collect($clientesAlerts)->filter(function ($client) {
            return $client->getBalance() < 0 && $client->getLastPurchaseInDays() > $this->daysForAlert;
        })->count();

        $clientes = Client::pluck('nome', 'id');
        return view('operations/form', ['clientes'=> $clientes, 'types'=> $this->operationsType, 'clientsAlert' => $alerts]);
    }

    public function pendencias()
    {

        $clientesAlerts = $clients = Client::with('operations')->get();

        //calculate last payment as filter
        $alerts = collect($clientesAlerts)->filter(function ($client) {
            return $client->getBalance() < 0 && $client->getLastPurchaseInDays() > $this->daysForAlert;
        });

        $clientes = Client::pluck('nome', 'id');
        return view('clients/pendencias', ['clientes'=> $clientes, 'types'=> $this->operationsType, 'clientsAlert' => $alerts]);
    }

    public function printHistory($id){

        $cliente = Client::findOrFail($id);

        $cliente->printHistory($this->printer);

        return Redirect::to('/operation/'.$id.'/historico');
    }

    public function printTest(){

        $cliente = new Client();

        $cliente->printTest($this->printer, $this->printerName);

        return Redirect::back();
    }

    public function insert(Request $request){

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

}
