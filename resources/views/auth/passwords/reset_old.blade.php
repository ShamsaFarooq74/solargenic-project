<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8" />
    <title>Set Your new Password | Bel Energise</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Bel Energise" name="description"/>
    <meta content="Viion Technology" name="author"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.png') }}">

    <!-- App css -->
    <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/css/app.min.css') }}" rel="stylesheet" type="text/css" />

</head>

<body class="authentication-bg authentication-bg-pattern">

<div class="account-pages mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-pattern">
                    @if(Session::has('message'))
                        <div class="alert {{ Session::get('alert-class', 'alert-info') }}" role="alert">
                            <i class="mdi mdi-alert-circle-outline mr-2"></i>{{ Session::get('message') }}
                            <script>
                                // setTimeout(function(){
                                $('div.alert').toggle(1000);
                                // },3500);
                            </script>
                        </div>
                    @endif
                    <div class="card-body p-4">

                        <div class="text-center w-75 m-auto">
                            <span><img src="{{ asset('assets/admin/images/logo.png') }}" alt="" height="90"></span>
                            <p class="text-muted mb-4 mt-3">Please set your new password</p>
                        </div>

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Reset Password') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div> <!-- end card-body -->
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

<!-- Plugin js-->
<script src="{{ asset('assets/admin/libs/parsleyjs/parsley.min.js') }}"></script>

<!-- Validation init js-->
<script src="{{ asset('assets/admin/js/pages/form-validation.init.js') }}"></script>

</body>

</html>
