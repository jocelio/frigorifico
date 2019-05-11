@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                        Histórico do Cliente: {{$cliente->nome}} {{$cliente->cpf}}
                        </div>
                        <div class="col pull-right">
                            <a href="/operation/{{$cliente->id}}/print" class="float-right"> Imprimir </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('seccess_message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('seccess_message') }}
                        </div>
                    @endif

                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Data</th>
                            <th>Valor</th>
                            <th>Operação</th>
                            <th>Resto</th>
                            <th>Impressão</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cliente->getAccOperations() as $operation)
                        <tr class="{{$operation->type == 1? 'table-success': ''}}">
                            <td>{{$operation->id}}</td>
                            <td>{{$operation->getFormattedDate()}}</td>
                            <td>R$ {{$operation->getFormattedValue()}}</td>
                            <td>{{$operation->type == 0? 'VENDA':'PAGAMENTO'}}</td>
                            <td>R$ {{$operation->getFormattedAcc()}}</td>
                            <td> <a href="/operation/{{$cliente->id}}/print/{{$operation->id}}"> Imprimir
                                    <img src="{{ asset('/images/level-down.svg') }}" style="width: 10px">
                                </a> </td>
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
