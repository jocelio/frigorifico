<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use Redirect;

class OperationController extends Controller
{

    protected $operationsType = ['VENDA','PAGAMENTO'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $clientes = Client::pluck('nome', 'id');
        return view('operations/form', ['clientes'=> $clientes, 'types'=> $this->operationsType]);
    }

    public function insert(Request $request){

        $cliente = new Client();

        $cliente->create($request->all());

        \Session::flash('seccess_message', 'Client cadastrado com sucesso.');
        return Redirect::to('clientes/novo');
    }

}
