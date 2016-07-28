@extends('default')


@section('content')
    <h1>Login no Sistema</h1>
    <form action="{!! route('sistema.store') !!}" method="post" class="form form-horizontal">
        {!! csrf_field() !!}
        <div class="form-group">
            <label class="col-md-2 control-label">Email: </label>
            <div class="col-md-10">
                <input type="email" name="email" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-2 control-label">Senha: </label>
            <div class="col-md-10">
                <input type="password" name="password" class="form-control"
            </div>
        </div>

        <div class="form-group">
            <button class="btn btn-primary">Login</button>
        </div>

    </form>
@endsection