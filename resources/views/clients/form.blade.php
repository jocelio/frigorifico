@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            Formulário de Cliente
                        </div>
                        <div class="col pull-right">
                            <a href="{{url('clientes')}}" class="float-right"> Listagem de Clientes </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('seccess_message'))
                        <div class="alert alert-success" role="alert">
                            {{ session('seccess_message') }}
                        </div>
                    @endif

                    @if(Request::is('*/editar'))
                            {!! Form::model($cliente, ['method'=>'PATCH', 'url'=> 'clientes/'.$cliente->id]) !!}
                    @else
                        {!! Form::open(['url' => 'clientes/insert']) !!}
                    @endif


                        {!! Form::label('nome', 'Nome') !!}
                    	{!! Form::input('text', 'nome', null, ['class' => 'form-control', 'autofocus','required', 'placeholder' => 'Nome'])  !!}
                        {!! Form::label('cpf', 'CPF') !!}
                    	{!! Form::input('text', 'cpf', null, ['class' => 'form-control', 'placeholder' => 'CPF'])  !!}
                        {!! Form::label('telefone', 'Telefone') !!}
                    	{!! Form::input('text', 'telefone', null, ['class' => 'form-control', 'required', 'placeholder' => 'Telefone'])  !!}
                        {!! Form::label('endereco', 'Endereço') !!}
                    	{!! Form::input('text', 'endereco', null, ['class' => 'form-control', 'placeholder' => 'Endereço'])  !!}

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
