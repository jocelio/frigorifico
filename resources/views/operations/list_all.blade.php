@extends('layouts.app')

@section('content')
<div class="container">

    Selecione uma data para imprimir o relatório diário.

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            Histórico Geral
                        </div>
                        <div>
                            {!! Form::open(['url' => 'operation/all', 'method'=>'get', 'autocomplete'=> 'off']) !!}
                            <div class="row">
                                <div class="col">
                                    {!! Form::input('text', 'date', null, ['class' => 'form-control datepicker','placeholder'=> 'Data'])  !!}
                                </div>
                                <div class="col-4">
                                    {!! Form::submit('Ver', ['class' => 'btn btn-primary']) !!}
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <table class="tablee w-100">
                        <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Hora</th>
                            <th>Valor</th>
                            <th>Operação</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($groupedOperations as $operationDay => $operations)
                            <tr>
                                <td class="text-center border-bottom border-top bg-light" colspan="6">
                                    <h5 class="pt-2"> {{\Carbon\Carbon::parse($operationDay)->format('d/m/Y')}}</h5>
                                </td>
                            </tr>
                            @foreach($operations as $operation)
                                <tr class="{{$operation->type == 1? 'table-success': ''}}">
                                    <td>{{$operation->client->nome}}</td>
                                    <td>{{$operation->getFormattedTime()}}</td>
                                    <td>R$ {{$operation->getFormattedValue()}}</td>
                                    <td>{{$operation->type == 0? 'VENDA':'PAGAMENTO'}}</td>
                                </tr>
                            @endforeach
                        @endforeach

                        @if ($dayBalance != null)
                        <tr class="{{$dayBalance > 0? 'table-success': 'table-danger'}} border">
                            <th>Total</th>
                            <th></th>
                            <th>R$ {{$dayBalance}}</th>
                            <th></th>
                        </tr>
                        @endif

                        </tbody>
                    </table>

                    @if ($date != null)
                        <a href="/operation/{{$date}}/print-date" class="float-right btn btn-primary mt-3"> Imprimir </a>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
