@extends('layouts.layout')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default">
					<div class="panel-heading">Login</div>
					<div class="panel-body">
						<form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
							{{ csrf_field() }}

							<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
								<label for="email" class="col-md-4 control-label">E-Mail Address</label>

								<div class="col-md-6">
									<input id="email" type="text" class="form-control" name="email"
									       value="{{ old('email') }}" autofocus>

									@if ($errors->has('email'))
										<span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
								<label for="password" class="col-md-4 control-label">Password</label>

								<div class="col-md-6">
									<input id="password" type="password" class="form-control" name="password" value="">

									@if ($errors->has('password'))
										<span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-6 col-md-offset-4">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="remember"> Remember Me
										</label>
									</div>
								</div>
							</div>

							@if (Auth::showCaptcha())
								<div class="form-group {{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
									<div class="col-md-6 col-sm-offset-4">
										{!! View::make('recaptcha::display') !!}
									</div>
									@if ($errors->has('g-recaptcha-response'))
										<span class="help-block col-md-6 col-sm-offset-4"><strong>{{ $errors->first('g-recaptcha-response') }}</strong></span>
									@endif
								</div>
							@endif

							<div class="form-group">
								<div class="col-md-8 col-md-offset-4">
									<button type="submit" class="btn btn-primary">
										Login
									</button>

									<a class="btn btn-link" href="{{ url('/register') }}">
										Registration
									</a>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
