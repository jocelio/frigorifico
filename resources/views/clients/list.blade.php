@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                        Lista de Clientes
                        </div>
                        <div class="col pull-right">
                            <a href="{{url('clientes/novo')}}" class="float-right"> Novo </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('seccess_message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('seccess_message') }}
                        </div>
                    @endif

                    <table class="table datatable">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Última Compra</th>
                            <th>Saldo</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($clientes as $cliente)
                        <tr>
                            <td>{{$cliente->nome}}</td>
                            <td>{{$cliente->cpf}}</td>
                            <td>{{$cliente->telefone}}</td>
                            <td>{{$cliente->getLastPurchase()}}</td>
                            <td data-order="{{$cliente->getBalance()}}">
                                R$ <span class="{{$cliente->getBalance() >= 0? 'text-success':'text-danger'}}"> {{$cliente->getBalance()}} </span>
                            </td>
                            <td class="btn-group">
                                <a href="/clientes/{{$cliente->id}}/editar" class="btn btn-outline-info">Editar</a>
                                {!! Form::model($cliente, ['method'=>'DELETE', 'url'=> 'clientes/'.$cliente->id]) !!}
                                <button type="submit" onClick="return confirmDeletion()" href="/clientes/{{$cliente->id}}/excluir" class="btn btn-outline-danger">Excluir</button>
                                {!! Form::close() !!}
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
