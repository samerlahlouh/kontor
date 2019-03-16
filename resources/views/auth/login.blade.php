@extends('layouts.app')

@section('content')
<!--__________________________________Login Style__________________________________-->
<link href="{{ asset('css/login-'.app()->getLocale().'.css') }}" rel="stylesheet">

<div class="container">
	<div class="d-flex justify-content-center h-100">
		<div class="card">
			<div class="card-header">
				<h3>{{ __('login_lng.login') }}</h3>
				<div class="d-flex justify-content-end social_icon">
					<span><i class="fa fa-id-card"></i></span>
				</div>
			</div>
			<div class="card-body">
				<form method="POST" action="{{ route('login') }}">
                @csrf
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-user"></i></span>
						</div>
						<input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ __('login_lng.email') }}">
                        
                        @if ($errors->has('email'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
					</div>
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-key"></i></span>
						</div>
						<input  id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="{{ __('login_lng.password') }}">

                        @if ($errors->has('password'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
					</div>
					<div class="row align-items-center remember">
						<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>{{ __('login_lng.remember_me') }}
					</div>
					<div class="form-group">
						<button id="btn_submit" type="submit" class="btn submit_btn">
                            {{ __('login_lng.login') }}
                            <i class='fa fa-sign-in' aria-hidden='true'></i>
                        </button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
