<!DOCTYPE html>

<html lang="{{ config('app.locale') }}">



<head>
    <meta charset="utf-8" />

    <title>Login | Bel Energise</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Bel Energise" />
    <meta property="og:description" content="Best Bel Energise" />
    <meta property="og:url" content="https://bel.net/" />
    <meta property="og:site_name" content="Solar Power Monitoring System Pakistan" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:description" content="Best Bel Energise" />
    <meta name="twitter:title" content="Bel Energise" />
    <meta content="Viion Technology" name="author" />

    <!-- App favicon -->

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png')}}">



    <!-- App css -->

    <link href="{{ asset('assets/css/bootstrap-material.min.css') }}" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />

    <link href="{{ asset('assets/css/app-material.min.css')}}" rel="stylesheet" type="text/css" id="app-default-stylesheet" />



    <link href="{{ asset('assets/css/bootstrap-material-dark.min.css')}}" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" disabled />

    <link href="{{ asset('assets/css/app-material-dark.min.css')}}" rel="stylesheet" type="text/css" id="app-dark-stylesheet" disabled />



    <!-- icons -->
    <link rel="stylesheet" media="screen" href="{{ asset('assets/demo/css/style.css')}}">
    <link href="{{ asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/animate.css')}}" rel="stylesheet" type="text/css" />



    <!-- custom -->

    <link href="{{ asset('assets/css/custom-stylesheet.css')}}" rel="stylesheet" type="text/css" />



</head>
<style>

    /* ---- particles.js container ---- */

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

    .js-count-particles{
        font-size: 1.1em;
    }

    #stats,
    .count-particles{
        -webkit-user-select: none;
    }

    #stats{
        display: none;
    }

    .count-particles{
        visibility: hidden !important;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .qr_cod{
        width: 100%;
        display: flex;
        justify-content: space-evenly;
        align-items: center;
        margin-top: 15px;
    }
    .input-group-text {
        background-color: #f9f9f9 !important;
        border: 1px solid #f9f9f9 !important;
    }

    @keyframes fadeInDown {
        from {
            opacity:0;
            -webkit-transform: translatey(-70px);
            -moz-transform: translatey(-70px);
            -o-transform: translatey(-70px);
            transform: translatey(-70px);
        }
        to {
            opacity:1;
            -webkit-transform: translatey(0);
            -moz-transform: translatey(0);
            -o-transform: translatey(0);
            transform: translatey(0);
        }
    }
    .in-down {
        -webkit-animation-name: fadeInDown;
        -moz-animation-name: fadeInDown;
        -o-animation-name: fadeInDown;
        animation-name: fadeInDown;
        -webkit-animation-fill-mode: both;
        -moz-animation-fill-mode: both;
        -o-animation-fill-mode: both;
        animation-fill-mode: both;
        -webkit-animation-duration: 1s;
        -moz-animation-duration: 1s;
        -o-animation-duration: 1s;
        animation-duration: 1s;
        float: left;
        width: 100%;
    }
    /** fadeInLeft **/

    @-webkit-keyframes fadeInLeft {
        from {
            opacity:0;
            -webkit-transform: translatex(-10px);
            -moz-transform: translatex(-10px);
            -o-transform: translatex(-10px);
            transform: translatex(-10px);
        }
        to {
            opacity:1;
            -webkit-transform: translatex(0);
            -moz-transform: translatex(0);
            -o-transform: translatex(0);
            transform: translatex(0);
        }
    }
    @-moz-keyframes fadeInLeft {
        from {
            opacity:0;
            -webkit-transform: translatex(-40px);
            -moz-transform: translatex(-40px);
            -o-transform: translatex(-40px);
            transform: translatex(-40px);
        }
        to {
            opacity:1;
            -webkit-transform: translatex(0);
            -moz-transform: translatex(0);
            -o-transform: translatex(0);
            transform: translatex(0);
        }
    }
    @keyframes fadeInLeft {
        from {
            opacity:0;
            -webkit-transform: translatex(-140px);
            -moz-transform: translatex(-140px);
            -o-transform: translatex(-140px);
            transform: translatex(-140px);
        }
        to {
            opacity:1;
            -webkit-transform: translatex(0);
            -moz-transform: translatex(0);
            -o-transform: translatex(0);
            transform: translatex(0);
        }
    }
    .in-left {
        -webkit-animation-name: fadeInLeft;
        -moz-animation-name: fadeInLeft;
        -o-animation-name: fadeInLeft;
        animation-name: fadeInLeft;
        -webkit-animation-fill-mode: both;
        -moz-animation-fill-mode: both;
        -o-animation-fill-mode: both;
        animation-fill-mode: both;
        -webkit-animation-duration: 1s;
        -moz-animation-duration: 1s;
        -o-animation-duration: 1s;
        animation-duration: 1s;
        -webkit-animation-delay: 1s;
        -moz-animation-delay: 1s;
        -o-animation-duration:1s;
        animation-delay: 1s;
        float: left;
        width: 100%;
    }

    .modal-backdrop {
        display: none;
    }
    .modal {
        background: #0009;
    }
    .modal-header .close {
        color: #fff;
        opacity: 9;
    }
    .modal-dialog.modal-dialog-centered {
        width: 480px ;
    }
    .modal-body {
        padding: 4rem 0 !important;
        text-align: center;
        font-size: 1.5rem;
        color: #f00;
    }
    
</style>


<body>
<div class="auth-fluid-pages">
    <div class="page-holder d-flex align-items-center">
        <div class="login_area_vt">
            <div class="logo_vt in-down"><img src="{{ asset('assets/images/logoin_logo.png')}}" alt=""></div>
            <div class="text-login-vt">
                <h4 class="h4_vt">BEL CONNECT</h4>
            </div>
            <div class="log_in_screen_vt">
                <p class="vt_p">
                    A cloud-based data intelligence platform designed by BEL to drive up the performance of Solar PV plants.
                </p>
            </div>
            <div class="text-login-vt">
                <h3>Sign-In</h3>
            </div>
            <div class="log_in_screen_vt">
                <p class="vt_p">
                    Access the BEL dashboard using your email and password
                </p>
            </div>
            @include('alert')
            <form id="loginForm" method="POST" action="{{ route('login') }}">

                @csrf

                <div class="form-group in-down">

                    <label for="emailaddress">Email</label>

                    <input type="email" placeholder="Enter your email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required placeholder="Enter your email" autofocus>
                    <input type="hidden" placeholder="Enter your password" class="form-control" name="is_active" id="is_active" value="Y" autocomplete="email" required>

                    @error('email')

                    <span class="invalid-feedback" role="alert">

                            <strong>{{ $message }}</strong>

                        </span>

                    @enderror

                </div>

                <div class="form-group in-down">
                    <label for="emailaddress">Password</label>
                    <div class="input-group">
                        <input type="password" placeholder="Enter your password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" autocomplete="email" required>
                        <div class="input-group-append" data-password="false">
                            <div class="input-group-text showPassDiv" onclick="showPass()">
                                <span class="password-eye"></span>
                            </div>
                        </div>
                    </div>

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group" id="boxq" style="float: left;width: auto;">
                    <input type="checkbox" tabindex="3" class="" name="remember_me" id="remember">
                    <label for="remember" style="color: #626262 !important;">{{ __('Remember Me') }}</label>
                </div>
                <div class="text_muted_vt">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-muted ml-1">
                            {{ __('Forgot Password?') }}
                        </a>
                    @endif
                </div>

                <div class="form-group mb-0 text-center in-down">

                    <button class="login-all-vt px-5" id="loginSubmitBtn" type="submit">LogIn </button>

                </div>

            </form>
            <div class="copyright_vt">
                <p><a href="#">Privacy Policy</a> <a> - <a href="#">Term & Condition</a> </p>
                <p>© Beacon Energy Limited - 2021. All rights reserved</p>
            </div>
        </div>
        <div class="modal fade bs-example-modal-center" id="loginModel" tabindex="-1" role="dialog" aria-labelledby="myCenterModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myCenterModalLabel">Bel error message</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" id="bel-error-data">
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
</div>


<!-- end auth-fluid-->
<div id="particles-js"></div>
<script src="{{ asset('assets/demo/js/particles.min.js') }}"></script>
<script src="{{ asset('assets/demo/js/app.js') }}"></script>
<script src="{{ asset('assets/demo/js/lib/stats.js') }}"></script>
<!-- Vendor js -->
<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('assets/js/wow.min.js') }}"></script>
<script>
    new WOW().init();
</script>
<!-- App js -->

<script src="{{ asset('assets/js/app.min.js') }}"></script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

<script type="text/javascript">
    $(document).ready(function() {

        $('#loginSubmitBtn').on('click', function (e) {
            e.preventDefault();

            $('#email').css('border-bottom', '');
            $('.emailStrong').html('');
            $('#password').css('border-bottom', '');
            $('.passwordStrong').html('');

            if($('#email').val().length == 0) {

                $('.emailStrong').html('Email is required');
                $('#email').css('border-bottom', '1px solid #f1556c');
            }

            else {

                $('.emailStrong').html('');
                $('#email').css('border-bottom', '');

                if($('#password').val().length == 0) {

                    $('.passwordStrong').html('Password is required');
                    $('#password').css('border-bottom', '1px solid #f1556c');
                }

                else {

                    $('.passwordStrong').html('');
                    $('#password').css('border-bottom', '');

                    $.ajax({
                        url: '{{route('login')}}',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            email: $('#email').val(),
                            password: $('#password').val(),
                            remember_me: $('#remember').is(":checked") ? 1 : ''
                        },
                        type: 'post',
                        dataType: 'json',
                        success: function (data) {

                            if (data.status == false) {
                                // alert(data.message)

                                document.getElementById('bel-error-data').innerHTML = data.message;
                                event.preventDefault();
                                jQuery.noConflict();
                                $('#loginModel').modal('show')
                                $('#email').css('border-bottom', '1px solid #f1556c');
                                $('#password').css('border-bottom', '1px solid #f1556c');
                            }
                            else if (typeof data.redirect !== 'undefined' && data.redirect) {

                                window.location.href = data.redirect
                            }
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            }

        });

    });

    function showPass() {

        // console.log('adsdas');

        var pass = document.getElementById("password");

        if (pass.type === "password") {

            pass.type = "text";

        } else {

            pass.type = "password";

        }

    }


</script>
<script>
    var count_particles, stats, update;
    stats = new Stats;
    stats.setMode(0);
    stats.domElement.style.position = 'absolute';
    stats.domElement.style.left = '0px';
    stats.domElement.style.top = '0px';
    document.body.appendChild(stats.domElement);
    count_particles = document.querySelector('.js-count-particles');
    update = function() {
        stats.begin();
        stats.end();
        if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) {
            count_particles.innerText = window.pJSDom[0].pJS.particles.array.length;
        }
        requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
</script>
<script>
    /* ---- particles.js config ---- */

    particlesJS("particles-js", {
        "particles": {
            "number": {
                "value": 380,
                "density": {
                    "enable": true,
                    "value_area": 800
                }
            },
            "color": {
                "value": "#ffffff"
            },
            "shape": {
                "type": "circle",
                "stroke": {
                    "width": 0,
                    "color": "#000000"
                },
                "polygon": {
                    "nb_sides": 5
                },
                "image": {
                    "src": "img/github.svg",
                    "width": 100,
                    "height": 100
                }
            },
            "opacity": {
                "value": 0.5,
                "random": false,
                "anim": {
                    "enable": false,
                    "speed": 1,
                    "opacity_min": 0.1,
                    "sync": false
                }
            },
            "size": {
                "value": 3,
                "random": true,
                "anim": {
                    "enable": false,
                    "speed": 40,
                    "size_min": 0.1,
                    "sync": false
                }
            },
            "line_linked": {
                "enable": true,
                "distance": 150,
                "color": "#d5d5d5",
                "opacity": 0.4,
                "width": 1
            },
            "move": {
                "enable": true,
                "speed": 6,
                "direction": "none",
                "random": false,
                "straight": false,
                "out_mode": "out",
                "bounce": false,
                "attract": {
                    "enable": false,
                    "rotateX": 600,
                    "rotateY": 1200
                }
            }
        },
        "interactivity": {
            "detect_on": "canvas",
            "events": {
                "onhover": {
                    "enable": true,
                    "mode": "grab"
                },
                "onclick": {
                    "enable": false,
                    "mode": "push"
                },
                "resize": true
            },
            "modes": {
                "grab": {
                    "distance": 140,
                    "line_linked": {
                        "opacity": 1
                    }
                },
                "bubble": {
                    "distance": 400,
                    "size": 40,
                    "duration": 2,
                    "opacity": 8,
                    "speed": 3
                },
                "repulse": {
                    "distance": 200,
                    "duration": 0.4
                },
                "push": {
                    "particles_nb": 4
                },
                "remove": {
                    "particles_nb": 2
                }
            }
        },
        "retina_detect": true
    });


    /* ---- stats.js config ---- */

    var count_particles, stats, update;
    stats = new Stats;
    stats.setMode(0);
    stats.domElement.style.position = 'absolute';
    stats.domElement.style.left = '0px';
    stats.domElement.style.top = '0px';
    document.body.appendChild(stats.domElement);
    count_particles = document.querySelector('.js-count-particles');
    update = function() {
        stats.begin();
        stats.end();
        if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) {
            count_particles.innerText = window.pJSDom[0].pJS.particles.array.length;
        }
        requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
</script>
</body>



</html>
