<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login | Bel Energise</title>
    <meta name="description" content="">
    <meta content="Bel Energise" name="description"/>
    <meta content="Viion Technology" name="author"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <link rel="stylesheet" href='{{ asset("css/bootstrap.min.css")}}'>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <!-- Google fonts - Roboto -->
    <link rel="stylesheet" href="{{ asset('css/fonts-roboto.css')}}">
    <link rel="stylesheet" href="{{ asset('css/orionicons.css')}}">
    <link rel="stylesheet" href="{{ asset('css/style.default.css')}}" id="theme-stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css')}}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png')}}">
</head>

<body>
    <div class="page-holder d-flex align-items-center">
        <div class="">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="login-text-vt logo-vt">
                        <a href="{{ url('/login') }}"><img src="{{ asset('img/bel_logo.png')}}" alt="logo"></a>
                        <div class="text-login-vt">
                            <h4>Lorem Ipsum is simply dummy text of the printing</h4>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
                        </div>
                        <ul>
                            <li><a href="javascript:void(0);">term & Condition</a></li>
                            <li><a href="javascript:void(0);">Privacy policy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-5 px-5 mb-md-5">
                    @if(Session::has('message'))
                        <div class="alert {{ Session::get('alert-class', 'alert-info') }}" role="alert">
                            <i class="mdi mdi-alert-circle-outline mr-2"></i>{{ Session::get('message') }}
                            <script>
                                setTimeout(function () {
                                    $('div.alert').toggle(1000);
                                }, 3500);
                            </script>
                        </div>
                    @endif
                    <p class="text-muted-vt">
                        Enter your email address and password to access admin panel
                    </p>
                    <form id="loginForm" class="mt-4" method="POST" action="{{ route('login') }}" class="needs-validation validateForm"
                              novalidate>
                        @csrf
                        <div class="form-group">
                            <label class="form-control-label">Email address</label>
                            <input type="email" placeholder="Enter your email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required placeholder="Enter your email" autofocus>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-control-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Enter your password" required autocomplete="current-password">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group" id="boxq">
                            <input type="checkbox" tabindex="3" class="" name="remember"
                                           id="remember" value="{{ old('remember') ? 'checked' : '' }}">
                            <label for="remember">{{ __('Remember Me') }}</label>
                        </div>
                        <button type="submit" class="login-all-vt px-5">Login</button>
                    </form>
                    <div class="text-muted-vt">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                        <!-- <h5><a href="reset-password.html">reset-password</a></h5> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript files-->
    <script src="{{ asset('jquery/jquery.min.js')}}"></script>
    <script src="{{ asset('popper.js/umd/popper.min.js')}}">
    </script>
    <script src="{{ asset('js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('jquery.cookie/jquery.cookie.js')}}">
    </script>
    <script src="{{ asset('chart.js/Chart.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
    <script src="{{ asset('js/front.js')}}"></script>
</body>

</html>