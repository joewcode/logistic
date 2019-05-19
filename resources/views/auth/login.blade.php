@extends('layouts.guest')

@section('content')
<div class="middle-box text-center loginscreen animated fadeInDown">
<div>
	<p>Пожалуйста, авторизируйтесь.</p>
	<form class="m-t" role="form" method="POST" action="{{ url('/login') }}">
		{{ csrf_field() }}
		<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
			<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
			@if ($errors->has('email'))
				<span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
			@endif
		</div>	
		<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
			<input id="password" type="password" class="form-control" name="password" required>
			@if ($errors->has('password'))
				<span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
			@endif
		</div>
		<button type="submit" class="btn btn-primary block full-width m-b">Вход</button>
		<div class="form-group">
			<div class="checkbox"><label><input type="checkbox" name="remember"> Запомнить меня</label></div>
		</div>
		<a class="btn btn-link" href="{{ url('/password/reset') }}">Забыли пароль?</a>
	</form>
</div>
</div>
@endsection