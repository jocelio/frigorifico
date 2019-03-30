<?php

namespace App\Http\Controllers;

use App\Client;
use App\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Redirect;

class OperationController extends Controller
{

    protected $operationsType = [0=>'VENDA',1=>'PAGAMENTO'];

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

        $operation = new Operation();

        $fields = $request->all();
        $fields['user_id'] = Auth::user()->id;
        $operation->create($fields);

        \Session::flash('seccess_message', 'Operação inserida com sucesso.');
        return Redirect::to('/home');
    }

}
