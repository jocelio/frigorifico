@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                        Lista de Pendências
                        </div>
                        <div class="col pull-right">
                            <a href="{{url('clientes/')}}" class="float-right"> Lista De Clientes </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('seccess_message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('seccess_message') }}
                        </div>
                    @endif

                    <table class="table datatable table-striped">
                        <thead>
                        <tr>
                            <th>Histórico</th>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Última Compra</th>
                            <th>Saldo</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($clientsAlert as $cliente)
                            <tr>
                                <td><a href="/operation/{{$cliente->id}}/historico" class="btn btn-outline-secondary">Ver Histórico</a></td>
                                <td>{{$cliente->nome}}</td>
                                <td>{{$cliente->cpf}}</td>
                                <td>{{$cliente->telefone}}</td>
                                <td>{{$cliente->getLastPurchase()}}</td>
                                <td data-order="{{$cliente->getBalance()}}">
                                <span class="{{$cliente->getBalance() >= 0? 'text-success':'text-danger'}}">
                                    <strong class="font-weight-bold">R$ </strong>
                                    {{$cliente->getFormattedBalance()}}
                                </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
