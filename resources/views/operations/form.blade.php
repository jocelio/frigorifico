@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            Operações
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('seccess_message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('seccess_message') }}
                        </div>
                    @endif


                    {!! Form::open(['url' => 'operation/insert']) !!}
                        <div class="row">
                            <div class="col">
                                {!! Form::label('client', 'Cliente') !!}
                                {!! Form::select('client_id', $clientes, null, ['class' => 'form-control select2','autofocus']) !!}
                            </div>
                            <div class="col">
                                {!! Form::label('type', 'Operação') !!}
                                {!! Form::select('type', $types, null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                {!! Form::label('date', 'Data') !!}
                                {!! Form::input('text', 'date', null, ['class' => 'form-control datepicker'])  !!}
                            </div>
                            <div class="col">
                                {!! Form::label('value', 'Endereço') !!}
                                {!! Form::input('text', 'value', null, ['class' => 'form-control money ', 'placeholder' => 'Valor'])  !!}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                {!! Form::submit('Enviar', ['class' => 'btn btn-primary']) !!}
                            </div>
                        </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
