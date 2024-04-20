<!DOCTYPE html>
<html dir="<?php echo ($front_setting->is_active_rtl) ? "rtl" : "ltr"; ?>" lang="<?php echo ($front_setting->is_active_rtl) ? "ar" : "en"; ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $page['title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="title" content="<?php echo $page['meta_title']; ?>">
    <meta name="keywords" content="<?php echo $page['meta_keyword']; ?>">
    <meta name="description" content="<?php echo $page['meta_description']; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php echo base_url($front_setting->fav_icon); ?>" type="image/x-icon">

    <link href="<?php echo $base_assets_url; ?>css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo $base_assets_url; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $base_assets_url; ?>css/owl.carousel.css" rel="stylesheet">
    <link href="<?php echo $base_assets_url; ?>css/style.css" rel="stylesheet">

    <!-- fonts -->
    <link rel="stylesheet" href="<?php echo $base_assets_url; ?>/css/fontaws/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_assets_url; ?>/css/fontaws/regular.min.css">
    <link rel="stylesheet" href="<?php echo $base_assets_url; ?>/css/fontaws/solid.min.css">
    <link rel="stylesheet" href="<?php echo $base_assets_url; ?>/css/fontaws/v4-shims.min.css">
    <link rel="stylesheet" href="<?php echo $base_assets_url; ?>/css/fontaws/fontawesome.min.css">
    <link rel="stylesheet" href="<?php echo $base_assets_url; ?>/css/fontaws/light.min.css">
    <link rel="stylesheet" href="<?php echo $base_assets_url; ?>/css/fontaws/brands.min.css">
    <link rel="stylesheet" href="<?php echo $base_assets_url; ?>/css/fontaws/duotone.min.css">
    <link rel="stylesheet" href="<?php echo $base_assets_url; ?>/css/fontaws/svg-with-js.min.css">




    <link rel="stylesheet" href="<?php echo $base_assets_url; ?>datepicker/bootstrap-datepicker3.css" />
    <script src="<?php echo $base_assets_url; ?>js/jquery.min.js"></script>
    <script type="text/javascript">
        var base_url = "<?php echo base_url() ?>";
    </script>
    <?php
    //  $this->load->view('layout/theme');

    if ($front_setting->is_active_rtl) {
    ?>
        <link href="<?php echo $base_assets_url; ?>rtl/bootstrap-rtl.min.css" rel="stylesheet">
        <link href="<?php echo $base_assets_url; ?>rtl/style-rtl.css" rel="stylesheet">
    <?php
    }
    ?>
    <?php echo $front_setting->google_analytics; ?>
</head>

<body>

    <?php if (isset($featured_image) && $featured_image != "") {
    ?>
    <?php
    }
    ?>
    <!-- <div><img src="<?php echo base_url(); ?>/uploads/slider_1.png" alt="logo"></div> -->

    <!-- <?php echo $slider; ?> -->

    <!-- Become a Certified Full Stack Web Developer (6 months) start -->
    <section class="become_vt">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <div class="head_vt">Become a Certified Full Stack Web Developer (6 months)</div>
                    <p>Take your career as a web developer to the next level with this Full-Stack Web Developer Master’s Program, where you’ll become an expert at front and back-end JavaScript technologies of the most popular MEAN (MongoDB, Express,</p>
                    <ul>
                        <li><i class="far fa-bullseye"></i> Skill Level: Intermediate</li>
                        <li><i class="far fa-user-graduate"></i> Duration: 06 Months</li>
                        <li><i class="far fa-stopwatch"></i> Next Badge: 12-Aug-2020</li>
                        <li><a href="#"><i class="far fa-money-bill-wave-alt"></i> View Fee Structure</a></li>
                    </ul>
                    <a href="#">
                        <div class="sus_btn_vt">BOOK NOW</div>
                    </a>
                    <a href="#">
                        <div class="sus_btn_vt">dOWNLOAD Course Outline</div>
                    </a>
                </div>
                <div class="col-md-5">
                    <div class="video_area_vt">
                        <!-- <img src="<?php echo base_url(); ?>/uploads/video.jpg" alt="special offer"> -->
                        <div id='media-player'>
                            <video id='media-video' controls>
                                <source src='parrots.mp4' type='video/mp4'>
                                <source src='parrots.webm' type='video/webm'>
                            </video>
                            <div id='media-controls'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- text end start -->
    <section class="text_detail_vt">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <p>Phasellus risus turpis, pretium sit amet magna non, molestie ultricies enim. Nullam pulvinar felis at metus malesuada, nec convallis lacus commodo. Duis blandit neque purus, nec auctor mi sollicitudin nec. Duis urna ipsum, tincidunt at euismod ut, placerat eget urna. Curabitur nec faucibus leo, et laoreet nibh. Pellentesque euismod quam at eros efficitur, vitae venenatis sem consectetur. Donec ut risus ultricies, dictum neque at, bibendum purus. In hac habitasse platea dictumst.</p>

                    <div id="accordion">
                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <i class="fas fa-caret-right"></i> Adipiscing tempor
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headingTwo">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <i class="fas fa-caret-right"></i> Sodales ipsum
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                <div class="card-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headingThree">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <i class="fas fa-caret-right"></i> Cras non diam
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                                <div class="card-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header" id="headingfour">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <i class="fas fa-caret-right"></i> Laoreet imperdiet nunc
                                    </button>
                                </h5>
                            </div>
                            <div id="collapsefour" class="collapse" aria-labelledby="headingfour" data-parent="#accordion">
                                <div class="card-body">
                                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="video_area_vt">
                        <img src="<?php echo base_url(); ?>/uploads/video.jpg" alt="special offer">
                    </div>
                    <p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Ut arcu libero, pulvinar non massa sed, accumsan scelerisque dui. Morbi purus mauris, vulputate quis felis nec, fermentum aliquam orci. Quisque ornare iaculis placerat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. In commodo sem arcu, sed fermentum tortor consequat vel. Phasellus lacinia quam quis leo tincidunt vehicula.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Courses start -->
    <section class="suspendisse_vt">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="head_vt">Suspendisse amet mauris</div>
                    <p>Cras gravida bibendum dolor eu varius. Morbi fermentum velit nisl, eget vehicula lorem sodales eget.<br> Donec quis volutpat orci. Sed ipsum felis, tristique id egestas et, convallis ac velit. In consequat dolor</p>
                </div>
                <div class="col-md-12 disply_vt">
                    <div class="date_sus_vt">45 <span>DAYS</span></div>
                    <div class="date_sus_vt">18 <span>HOURS</span></div>
                    <div class="date_sus_vt">25 <span>MINUTES</span></div>
                    <div class="date_sus_vt">30 <span>SECONDS</span></div>
                </div>
                <div class="col-md-12">
                    <a href="#">
                        <div class="sus_btn_vt">ACTION</div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- What Our Client Say start -->
    <section class="why_choose_vt">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="head_vt">About Course Instructor</div>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="what_area_vt">
                        <div class="what_img_vt">
                            <img src="<?php echo base_url(); ?>/uploads/avatar-1.jpg" alt="special offer">
                        </div>
                        <p>Etiam facilisis ligula nec velit posuere egestas. Nunc dictum lectus sem, vel dignissim purus luctus quis. Vestibulum et ligula suscipit, hendrerit erat a, ultricies velit. Praesent convallis in lorem nec blandit. Phasellus a porta tellus. Suspendisse sagittis metus enim. Sed molestie libero id sem pulvinar, quis euismod mauris suscipit.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Courses start -->
    <section class="popular_courses_vt">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="head_vt">Popular Courses</div>
                    <p>Cras gravida bibendum dolor eu varius. Morbi fermentum velit nisl, eget vehicula lorem sodales eget.<br> Donec quis volutpat orci. Sed ipsum felis, tristique id egestas et, convallis ac velit. In consequat dolor</p>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="popular_area_vt">
                        <div class="popular_img">
                            <a href="#">
                                <div class="cor_btn_vt">Web</div>
                            </a>
                            <div class="icon_btn_vt"><i class="far fa-heart"></i></div>
                            <img src="<?php echo base_url(); ?>/uploads/Rectangle_1.png" alt="special offer">
                        </div>
                        <h3>Web Development</h3>
                        <div class="text_area_vt">
                            <div class="img_vt"><img src="<?php echo base_url(); ?>/uploads/avatar-1.jpg"></div>
                            <div class="text_img">
                                <h5>Sara Barnett</h5>
                                <h6>Pogrammer</h6>
                            </div>
                        </div>
                        <div class="div_btn">
                            <h6>Fee: 10000 PKR</h6>
                            <a href="<?php echo site_url('online_admission') ?>">Enroll Now <i class="fa fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="popular_area_vt">
                        <div class="popular_img">
                            <a href="#">
                                <div class="cor_btn_vt">Design</div>
                            </a>
                            <div class="icon_btn_vt"><i class="far fa-heart"></i></div>
                            <img src="<?php echo base_url(); ?>/uploads/Rectangle_2.png" alt="special offer">
                        </div>
                        <h3>Web Development</h3>
                        <div class="text_area_vt">
                            <div class="img_vt"><img src="<?php echo base_url(); ?>/uploads/avatar-1.jpg"></div>
                            <div class="text_img">
                                <h5>Sara Barnett</h5>
                                <h6>Pogrammer</h6>
                            </div>
                        </div>
                        <div class="div_btn">
                            <h6>Fee: 10000 PKR</h6>
                            <a href="<?php echo site_url('online_admission') ?>">Enroll Now <i class="fa fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="popular_area_vt">
                        <div class="popular_img">
                            <a href="#">
                                <div class="cor_btn_vt">iOS</div>
                            </a>
                            <div class="icon_btn_vt"><i class="far fa-heart"></i></div>
                            <img src="<?php echo base_url(); ?>/uploads/Rectangle_3.png" alt="special offer">
                        </div>
                        <h3>Web Development</h3>
                        <div class="text_area_vt">
                            <div class="img_vt"><img src="<?php echo base_url(); ?>/uploads/avatar-1.jpg"></div>
                            <div class="text_img">
                                <h5>Sara Barnett</h5>
                                <h6>Pogrammer</h6>
                            </div>
                        </div>
                        <div class="div_btn">
                            <h6>Fee: 10000 PKR</h6>
                            <a href="<?php echo site_url('online_admission') ?>">Enroll Now <i class="fa fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <script src="<?php echo $base_assets_url; ?>js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo $base_assets_url; ?>js/jquery.waypoints.min.js"></script>
    <script type="text/javascript" src="<?php echo $base_assets_url; ?>js/jquery.counterup.min.js"></script>
    <script src="<?php echo $base_assets_url; ?>js/owl.carousel.min.js"></script>
    <script src="<?php echo $base_assets_url; ?>js/ss-lightbox.js"></script>
    <script src="<?php echo $base_assets_url; ?>js/custom.js"></script>

    <script src="<?php echo base_url(); ?>/js/fontaws/regular.js"></script>
    <script src="<?php echo base_url(); ?>/js/fontaws/fontawesome.min.js"></script>

    <script src="<?php echo $base_assets_url; ?>/js/fontaws/all.min.js"></script>
    <script src="<?php echo $base_assets_url; ?>/js/fontaws/fontawesome.min.js"></script>
    <script src="<?php echo $base_assets_url; ?>/js/fontaws/regular.min.js"></script>
    <script src="<?php echo $base_assets_url; ?>/js/fontaws/solid.min.js"></script>
    <script src="<?php echo $base_assets_url; ?>/js/fontaws/v4-shims.min.js"></script>
    <script src="<?php echo $base_assets_url; ?>/js/fontaws/light.js"></script>
    <script src="<?php echo $base_assets_url; ?>/js/fontaws/fontawesome.min.js"></script>
    <script src="<?php echo $base_assets_url; ?>/js/fontaws/brands.min.js"></script>
    <script src="<?php echo $base_assets_url; ?>/js/fontaws/duotone.js"></script>
    <script src="<?php echo $base_assets_url; ?>/js/fontaws/svg-with-js.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.js"></script>
    <script type="text/javascript" src="<?php echo $base_assets_url; ?>datepicker/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript">
        $(function() {
            jQuery('img.svg').each(function() {
                var $img = jQuery(this);
                var imgID = $img.attr('id');
                var imgClass = $img.attr('class');
                var imgURL = $img.attr('src');

                jQuery.get(imgURL, function(data) {
                    // Get the SVG tag, ignore the rest
                    var $svg = jQuery(data).find('svg');

                    // Add replaced image's ID to the new SVG
                    if (typeof imgID !== 'undefined') {
                        $svg = $svg.attr('id', imgID);
                    }
                    // Add replaced image's classes to the new SVG
                    if (typeof imgClass !== 'undefined') {
                        $svg = $svg.attr('class', imgClass + ' replaced-svg');
                    }

                    // Remove any invalid XML tags as per http://validator.w3.org
                    $svg = $svg.removeAttr('xmlns:a');

                    // Check if the viewport is set, else we gonna set it if we can.
                    if (!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
                        $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
                    }

                    // Replace image with new SVG
                    $img.replaceWith($svg);

                }, 'xml');

            });
        });
    </script>
    <script>
        $('.owl-carousel').owlCarousel({
            items: 1,
            lazyLoad: true,
            loop: true,
            nav: false,
            margin: 0
        });
        $(document).ready(function() {
            $('.customer-logos').slick({
                slidesToShow: 6,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 1000,
                arrows: false,
                dots: false,
                pauseOnHover: false,
                responsive: [{
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 4
                    }
                }, {
                    breakpoint: 520,
                    settings: {
                        slidesToShow: 3
                    }
                }]
            });
        });
    </script>
    <script>
        $(function() {

            var $container = $('#container').masonry({
                itemSelector: '.item',
                columnWidth: 200
            });

            // reveal initial images
            $container.masonryImagesReveal($('#images').find('.item'));
        });

        $.fn.masonryImagesReveal = function($items) {
            var msnry = this.data('masonry');
            var itemSelector = msnry.options.itemSelector;
            // hide by default
            $items.hide();
            // append to container
            this.append($items);
            $items.imagesLoaded().progress(function(imgLoad, image) {
                // get item
                // image is imagesLoaded class, not <img>, <img> is image.img
                var $item = $(image.img).parents(itemSelector);
                // un-hide item
                $item.show();
                // masonry does its thing
                msnry.appended($item);
            });

            return this;
        };
    </script>
</body>

</html>