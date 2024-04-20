<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8" />
    <title>Login | Bel Energise</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Solar Monitoring System-Monitor Solar Power Efficiently" />
    <meta property="og:description" content="Best solar monitoring system software &amp; Remote PV monitoring system app easily to track your real-time system production &amp; household energy consumption." />
    <meta property="og:url" content="https://solargenic.net/" />
    <meta property="og:site_name" content="Solar Power Monitoring System Pakistan" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:description" content="Best solar monitoring system software &amp; Remote PV monitoring system app easily to track your real-time system production &amp; household energy consumption." />
    <meta name="twitter:title" content="Solar Monitoring System-Monitor Solar Power Efficiently" />
    <meta content="Viion Technology" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png')}}">

    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap-material.min.css') }}" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
    <link href="{{ asset('assets/css/app-material.min.css')}}" rel="stylesheet" type="text/css" id="app-default-stylesheet" />

    <link href="{{ asset('assets/css/bootstrap-material-dark.min.css')}}" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" disabled />
    <link href="{{ asset('assets/css/app-material-dark.min.css')}}" rel="stylesheet" type="text/css" id="app-dark-stylesheet" disabled />

    <!-- icons -->
    <link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- custom -->
    <link href="{{ asset('assets/css/custom-stylesheet.css')}}" rel="stylesheet" type="text/css" />

</head>

<body class="auth-fluid-pages pb-0">

    <div class="page-holder d-flex align-items-center">
        <div class="">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="login-text-vt logo-vt">
                        <a href="#"><img src="{{ asset('assets/images/logo.png') }}" alt="logo"></a>
                        <div class="text-login-vt">
                            <h4>Lorem Ipsum is simply dummy text of the printing</h4>
                            <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
                        </div>
                        <ul>
                            <li><a href="#">term &amp; Condition</a></li>
                            <li><a href="#">Privacy policy</a></li>
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
                        Enter your email address and we'll send you an email with instructions to reset your password.
                    </p>
                    <form id="loginForm" class="mt-4" method="POST" action="{{ route('password.email') }}" class="needs-validation validateForm"
                              novalidate>
                        @csrf
                        <div class="form-group">
                            <label class="form-control-label">Email address</label>
                            <input type="email" placeholder="enter your email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required placeholder="Enter your email" autofocus>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="login-all-vt px-5">Send Reset Password Link </button>
                    </form>
                    <div class="text-muted-vt">
                        <p class="text-black-50">Back to <a href="{{ url('/login') }}" class="text-black ml-1"><b>Log in</b></a></p>
                    </div>
                </div>

                <!-- term & Condition  and  Privacy policy page start -->

                <!-- <div class="col-md-5 px-5 mb-md-0">
                    <h4 class="head-term-vt">term &amp; Condition</h4>
                    <div class="term-privacy-vt">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </div>
                    <div class="text-muted-vt m-0 mt-3">
                        <p class="text-black-50">Back to <a href="{{ url('/login') }}" class="text-black ml-1"><b>Log in</b></a></p>
                    </div>
                </div> -->
            </div>
        </div>
    </div>

    <!-- end auth-fluid-->

    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

</body>

</html>