<!DOCTYPE html>

<html lang="{{ config('app.locale') }}">



<head>

    <meta charset="utf-8" />

    <title>Forget Password | Bel Energise</title>

    <meta content="Bel Energise" name="description" />

    <meta content="Viion Technology" name="author" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->

    <link rel="shortcut icon" href="{{ asset('assets/images/fav_icon.png')}}">



    <!-- App css -->

    <link href="{{ asset('assets/css/bootstrap-material.min.css') }}" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />

    <link href="{{ asset('assets/css/app-material.min.css')}}" rel="stylesheet" type="text/css" id="app-default-stylesheet" />



    <link href="{{ asset('assets/css/bootstrap-material-dark.min.css')}}" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" disabled />

    <link href="{{ asset('assets/css/app-material-dark.min.css')}}" rel="stylesheet" type="text/css" id="app-dark-stylesheet" disabled />


    <link href="{{ asset('assets/css/animate.css')}}" rel="stylesheet" type="text/css" />
    <!-- icons -->

    <link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />



    <!-- custom -->

    <link href="{{ asset('assets/css/custom-stylesheet.css')}}" rel="stylesheet" type="text/css" />



</head>
<style>
    body{
        margin: 0;
        padding: 0;
    }
    #particles-js {
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: #fff;
        background-image: url("");
        background-repeat: no-repeat;
        background-size: cover;
        background-position: 50% 50%;
    }
    .invalid-feedback {
        display:block !important;
    }
    .form-control.is-invalid, .was-validated .form-control:invalid {
        border-color: #f1556c !important;
    }
    .login_area_vt p {
        color: #828383 !important;
        font-size: 13px;
        margin:10px  0;
        text-align: left !important;
        font-family: 'Poppins', sans-serif;
    }
    p.text-black-50.wow.slideInUp {
        text-align: center !important;
        font-size: 13px;
    }
    p.text-black-50.wow.slideInUp a{
        color:#000;
    }
</style>

@php
    //dd(session()->all());
@endphp
<body class="auth-fluid-pages pb-0">
<div class="page-holder d-flex align-items-center">
    <div class="row align-items-center">
        <div class="col-md-12">
            <div class="login_area_vt">

                @if(Session::get('alert-class') == 'alert-success')
                    <div class="alert alert-success wow slideInUp">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {{ Session::get('message') }}
                    </div>
                @endif

                <a href="#" class="logo_vt"><img src="{{ asset('assets/images/bel_logo.png')}}" alt="logo"></a>

                <div class="text-login-vt mb-2 wow slideInUp">
                    <h3>Reset Password</h3>
                    <p class="vt_p">Enter your email address and we'll send you an email with instructions to reset your password.</p>
                </div>


                <form id="loginForm" class="mt-4" method="POST" action="{{ url('generateForgotPassCode') }}" class="needs-validation validateForm" novalidate>

                    @csrf

                    <div class="form-group wow slideInUp">

                        <label class="form-control-label">Email address<span class="text-danger">*</span>&nbsp;&nbsp;<span class="error_email text-danger"></span></label>

                        <input type="email" class="form-control @if(Session::get('alert-class') == 'alert-danger') is-invalid @endif" name="email" id="email" required="">

                        @if(Session::get('alert-class') == 'alert-danger')
                            <span class="invalid-feedback" role="alert">

                                <strong>{{ Session::get('message') }}</strong>

                            </span>

                        @endif

                    </div>

                    <input type="hidden" name="web" value="web">

                    <button type="submit" class="login-all-vt px-5 wow slideInUp" onclick="check_validation();">Send Reset Password Link </button>

                </form>

                <p class="text-black-50 wow slideInUp">Back to <a href="{{ url('/login') }}" class="text-black ml-1"><b>Log in</b></a></p>

            </div>
        </div>
    </div>
</div>

<!-- end auth-fluid-->







<!-- Vendor js -->


<div id="particles-js"></div>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script src="{{ asset('assets/js/stats.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
<script>
    particlesJS("particles-js", { "particles": { "number": { "value": 80, "density": { "enable": true, "value_area": 800 } }, "color": { "value": "#D2D2D2" }, "shape": { "type": "circle", "stroke": { "width": 0, "color": "#D2D2D2" }, "polygon": { "nb_sides": 5 }, "image": { "src": "img/github.svg", "width": 100, "height": 100 } }, "opacity": { "value": 0.5, "random": false, "anim": { "enable": false, "speed": 1, "opacity_min": 0.1, "sync": false } }, "size": { "value": 3, "random": true, "anim": { "enable": false, "speed": 40, "size_min": 0.5, "sync": false } }, "line_linked": { "enable": true, "distance": 150, "color": "#D2D2D2", "opacity": 0.5, "width": 1 }, "move": { "enable": true, "speed": 8, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false, "attract": { "enable": false, "rotateX": 600, "rotateY": 1200 } } }, "interactivity": { "detect_on": "canvas", "events": { "onhover": { "enable": true, "mode": "repulse" }, "onclick": { "enable": true, "mode": "push" }, "resize": true }, "modes": { "grab": { "distance": 400, "line_linked": { "opacity": 1 } }, "bubble": { "distance": 400, "size": 40, "duration": 2, "opacity": 8, "speed": 3 }, "repulse": { "distance": 200, "duration": 0.4 }, "push": { "particles_nb": 4 }, "remove": { "particles_nb": 2 } } }, "retina_detect": true }); var count_particles, stats, update; stats = new Stats; stats.setMode(0); stats.domElement.style.position = 'absolute'; stats.domElement.style.left = '0px'; stats.domElement.style.top = '0px'; document.body.appendChild(stats.domElement); count_particles = document.querySelector('.js-count-particles'); update = function () { stats.begin(); stats.end(); if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) { count_particles.innerText = window.pJSDom[0].pJS.particles.array.length; } requestAnimationFrame(update); }; requestAnimationFrame(update);;
</script>

<!-- App js -->

<script src="{{ asset('assets/js/app.min.js') }}"></script>

<script src="{{ asset('assets/js/wow.min.js') }}"></script>
<script>
    new WOW().init();
</script>

<script type="text/javascript">
    function check_validation() {

        var email = $('#email').val();

        if (email == '') {

            $('.error_email').html('This field is required');

            event.preventDefault();

            return false;

        } else {

            $('.error_email').html('');

            return true;

        }

    }
</script>



</body>



</html>
