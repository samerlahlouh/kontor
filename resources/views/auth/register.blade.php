@extends('layouts.app')

@section('content')
<!--__________________________________Login Style__________________________________-->
<link href="{{ asset('css/login-'.app()->getLocale().'.css') }}" rel="stylesheet">

<div class="container">
	<div class="d-flex justify-content-center h-100">
		<div class="card">
			<div class="card-header">
				<h3>{{ __('register_lng.add_user') }}</h3>
				<div class="d-flex justify-content-end social_icon">
					<span><i class="fa fa-id-card"></i></span>
				</div>
			</div>
			<div class="card-body">
				<form  method="POST" action="{{ route('register') }}">
                @csrf
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-user"></i></span>
						</div>
						<input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus placeholder="{{ __('register_lng.name') }}">
                        
                        @if ($errors->has('name'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
					</div>
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fa fa-envelope"></i></span>
						</div>
						<input  id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required placeholder="{{ __('register_lng.email') }}">

                        @if ($errors->has('email'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
					</div>
                    <div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-mobile"></i></span>
						</div>
						<input id="mobile" type="number" class="form-control{{ $errors->has('mobile') ? ' is-invalid' : '' }}" name="mobile" value="{{ old('mobile') }}" required autofocus placeholder="{{ __('register_lng.mobile') }}">
                        
                        @if ($errors->has('mobile'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('mobile') }}</strong>
                            </span>
                        @endif
					</div>
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-user"></i></span>
						</div>
						<input id="user_name" type="text" class="form-control{{ $errors->has('user_name') ? ' is-invalid' : '' }}" name="user_name" value="{{ old('user_name') }}" required autofocus placeholder="{{ __('register_lng.user_name') }}">
                        
                        @if ($errors->has('user_name'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('user_name') }}</strong>
                            </span>
                        @endif
					</div>
                    @if(Auth::user()->type == 'admin')
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-sitemap"></i></span>
						</div>
						<select id="type" type="text" class="form-control{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" value="{{ old('type') }}" required autofocus>
							<option hidden disabled selected>{{ __('register_lng.type') }}</option>
							@foreach ($types as $type)
								<option value='{{$type}}'>{{ __('main_lng.'.$type) }}</option>
							@endforeach
						</select>
                        
                        @if ($errors->has('type'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                        @endif
					</div>
					@endIf
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-key"></i></span>
						</div>
						<input  id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="{{ __('register_lng.password') }}">

                        @if ($errors->has('password'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
					</div>
					<div class="input-group form-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-key"></i></span>
						</div>
						<input  id="password-confirm" type="password" class="form-control" name="password_confirmation" required placeholder="{{ __('register_lng.confirm_pass') }}">
					</div>
					<div class="form-group">
						<button type="submit" class="btn float-right login_btn">
                            {{ __('register_lng.add') }}
                            <i class='fa fa-plus-circle' aria-hidden='true'></i>
                        </button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
