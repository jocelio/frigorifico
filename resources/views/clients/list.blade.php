@extends('layouts.app')

@section('content')
<div class="container">

    <script>
        function confirmDeletion() {
            let x = confirm("Are you sure you want to delete?");
            if (x)
                return true;
            else
                return false
        }
    </script>
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

                    <table class="table">
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Endereço</th>
                            <th>Saldo</th>
                            <th>Ações</th>
                        </tr>
                        @foreach($clientes as $cliente)
                        <tr>
                                <td>{{$cliente->nome}}</td>
                                <td>{{$cliente->cpf}}</td>
                                <td>{{$cliente->telefone}}</td>
                                <td>{{$cliente->endereco}}</td>
                                <td style="width: 200px;">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend">R$</span>
                                        </div>
                                        <input type="text" value="{{mt_rand(50, 1000)}},00" class="form-control" disabled>
                                    </div>
                                </td>
                                <td class="btn-group">
                                    <a href="/clientes/{{$cliente->id}}/editar" class="btn btn-outline-info">Editar</a>
                                    {!! Form::model($cliente, ['method'=>'DELETE', 'url'=> 'clientes/'.$cliente->id]) !!}
                                    <button type="submit" onClick="return confirmDeletion()" href="/clientes/{{$cliente->id}}/excluir" class="btn btn-outline-danger">Excluir</button>
                                    {!! Form::close() !!}
                                </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
