<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8"/>
    <title>Login | Bel Energise</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Bel Energise" name="description"/>
    <meta content="Viion Technology" name="author"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.png') }}">

    <!-- App css -->
    <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/css/icons.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/admin/css/app.min.css') }}" rel="stylesheet" type="text/css"/>

</head>

<body class="authentication-bg">

<div class="account-pages mt-5 mb-5">
    <div class="container">
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
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-pattern">

                    <div class="card-body p-4">

                        <div class="text-center w-75 m-auto">
                            <a href="{{ url('/login') }}">
                                <span><img src="{{ asset('assets/admin/images/logo.png') }}" alt="" height="90"></span>
                            </a>
                            <p class="text-muted mb-4 mt-3">Enter your email address and password to access admin
                                panel.</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="needs-validation validateForm"
                              novalidate>
                            @csrf

                            <div class="form-group mb-3">
                                <label for="emailaddress">Email address</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email"
                                       name="email" id="email" value="{{ old('email') }}" autocomplete="email" required
                                       placeholder="Enter your email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input id="password" type="password"
                                       class="form-control @error('password') is-invalid @enderror" name="password"
                                       placeholder="Enter your password"
                                       required autocomplete="current-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" id="checkbox-signin" type="checkbox" name="remember"
                                           id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="custom-control-label" for="checkbox-signin">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-0 text-center">
                                <button class="btn btn-primary btn-block" type="submit"> LOGIN </button>
                            </div>
                        </form>


                    </div> <!-- end card-body -->
                    <div class="row mt-3">
                        <div class="col-12 text-center mb-4">
                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end card -->

            </div> <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</div>
<!-- end page -->


<footer class="footer footer-alt">
    &copy; {{ date('Y') }} Bel Energise. All rights reserved.
</footer>

<!-- Vendor js -->
<script src="{{ asset('assets/admin/js/vendor.min.js') }}"></script>

<!-- App js -->
<script src="{{ asset('assets/admin/js/app.min.js') }}"></script>

<!-- Validation init js-->
<script src="{{ asset('assets/admin/js/pages/form-validation.init.js') }}"></script>

</body>

</html>
