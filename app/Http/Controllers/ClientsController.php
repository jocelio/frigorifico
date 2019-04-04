<?php

namespace App\Http\Controllers;

use App\Client;
use function foo\func;
use Illuminate\Http\Request;
use Redirect;

class ClientsController extends Controller
{
    public function index(){
      $clients = Client::with('operations')->get();
      return view('clients/list', ['clientes' => $clients]);
    }

    public function form(){
      return view('clients/form');
    }

    public function insert(Request $request){

        $cliente = new Client();

        $cliente->create($request->all());

        \Session::flash('seccess_message', 'Client cadastrado com sucesso.');
        return Redirect::to('clientes/novo');
    }

    public function edit($id){

        $cliente = Client::findOrFail($id);

        return view('clients/form', ['cliente'=> $cliente]);
    }

    public function update($id, Request $request){

        $cliente = Client::findOrFail($id);

        $cliente->update($request->all());

        \Session::flash('seccess_message', 'Client atualizado com sucesso.');
        return Redirect::to('clientes');
    }

    public function delete($id){

        $cliente = Client::findOrFail($id);

        $cliente->delete();

        \Session::flash('seccess_message', 'Client removido com sucesso.');
        return Redirect::to('clientes');
    }

}
