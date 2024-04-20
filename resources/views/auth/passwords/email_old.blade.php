<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8" />
    <title>Reset Password | Bel Energise</title>
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
                @if(Session::has('message'))
                    <div class="alert {{ Session::get('alert-class', 'alert-info') }}" role="alert">
                        <i class="mdi mdi-alert-circle-outline mr-2"></i>{{ Session::get('message') }}
                        <script>
                            setTimeout(function(){
                                $('div.alert').toggle(1000);
                            },3500);
                        </script>
                    </div>
                @endif
                <div class="card bg-pattern">

                    <div class="card-body p-4">

                        <div class="text-center w-75 m-auto">
                            <a href="{{ url('/login') }}">
                                <span><img src="{{ asset('assets/admin/images/logo.png') }}" alt="" height="90"></span>
                            </a>
                            <p class="text-muted mb-4 mt-3">Enter your email address and we'll send you an email with instructions to reset your password.</p>
                        </div>

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="email">Email Address</label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                            </div>

                            <div class="form-group mb-0 text-center">
                                <button class="btn btn-primary btn-block" type="submit"> Send Reset Password Link </button>
                            </div>
                        </form>

                    </div> <!-- end card-body -->
                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-black-50">Back to <a href="{{ url('/login') }}" class="text-black ml-1"><b>Log in</b></a></p>
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
