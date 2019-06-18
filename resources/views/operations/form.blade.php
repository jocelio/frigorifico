@extends('layouts.app')

@section('content')
<div class="container" style="zoom: 180%">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            Operações
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('seccess_message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('seccess_message') }}
                        </div>
                    @endif

                    {!! Form::open(['url' => 'operation/insert', 'class'=>'form-prevent-multiple-submit' ]) !!}
                        <div class="row">
                            <div class="col">
                                {!! Form::label('client', 'Cliente') !!}
                                {!! Form::select('client_id', $clientes, null, ['class' => 'form-control select2','autofocus', 'required'=>'true', 'placeholder' => 'Selecione o Cliente']) !!}
                            </div>
                            <div class="col">
                                {!! Form::label('type', 'Operação') !!}
                                {!! Form::select('type', $types, null, ['class' => 'form-control', 'required'=>'true']) !!}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col">
                                {!! Form::label('date', 'Data') !!}
                                {!! Form::input('text', 'date', \Carbon\Carbon::now()->format('d/m/Y'), ['class' => 'form-control datepicker', 'required'=>'true'])  !!}
                            </div>
                            <div class="col">
                                {!! Form::label('value', 'Valor') !!}
                                {!! Form::input('text', 'value', null, ['class' => 'form-control money', 'required'=>'true', 'placeholder' => 'Valor'])  !!}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col mt-4">
                                {!! Form::label('print', 'Imprimir') !!}
                                {!! Form::checkbox('print', true, true) !!}
                            </div>
                            <div class="col-md-6">
                                {!! Form::label('comment', 'Comentários') !!}
                                {!! Form::textarea('comment', null, ['class' => 'form-control', 'placeholder' => 'Comentários', 'rows' => 3, 'cols' => 54, ])  !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mt-4">
                            <button type="submit" class="btn btn-primary button-prevent-multiple-submit">
                                <i class="spinner fa fa-spinner fa-spin btn text-white"></i>
                                Enviar
                            </button>
                            </div>
                        </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            Alertas
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        <a href="operation/pendencias" class="alert-link">
                            Você tem {!! $clientsAlert !!}
                            @if ($clientsAlert > 1)
                              pendências.
                            @else
                              pendência.
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
