<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Wash</title>
    <link rel="stylesheet" href="{{ asset('landing/assets/css/landing-page.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/main.css') }}">
</head>

<body>

    <header>
        <div class="left-navbar">
            <img class="logo" src="{{ asset('landing/assets/images/landpage/Logo.svg') }}" alt="logo">
            <ul class="nav-links">
                <li class="nav-item"><a href="#">How it works</a></li>
                <li class="nav-item"><a href="#">Pricing</a></li>
            </ul>
        </div>
        <div class="right-navbar">
            <button class="login-link"><a href="#">Login</a></button>
            <button class="get-started-link"><a href="#">Get Started</a></button>
            <button class="choose-localization"><img src="{{ asset('landing/assets/images/icons/british flag.svg') }}"
                    alt="lang"></button>
        </div>
    </header>

    <div class="mobile-menu">
        <div class="head">
            <div class="content">
                <img class="logo" src="{{ asset('landing/assets/images/landpage/Logo.svg') }}" alt="logo">
                <button class="menu-toggle-btn" id="show-menu-btn" onclick="showMenu()">
                    <img src="{{ asset('landing/assets/images/landpage/menu.svg') }}" alt="menu">
                </button>
                <button class="menu-toggle-btn" id="close-menu-btn" onclick="closeMenu()">
                    <img src="{{ asset('landing/assets/images/landpage/x.svg') }}" alt="x">
                </button>
            </div>
        </div>
        <div class="menu">
            <div class="content">
                <ul class="nav-links">
                    <li class="nav-item"><a href="#">How it works</a></li>
                    <li class="nav-item"><a href="#">Pricing</a></li>
                </ul>
                <button class="login-link"><a href="#">Login</a></button>
                <button class="get-started-link"><a href="#">Get Started</a></button>
                <button class="choose-localization"><img
                        src="{{ asset('landing/assets/images/icons/british flag.svg') }}" alt="lang"></button>
            </div>
        </div>
    </div>

    <div class="preview-slide">
        <div class="slide-images">
            <img src="{{ asset('landing/assets/images/landpage/slide-1.png') }}" alt="slide">
            <img src="{{ asset('landing/assets/images/landpage/slide-2.png') }}" alt="slide">
            <img src="{{ asset('landing/assets/images/landpage/slide-3.png') }}" alt="slide">
            <img src="{{ asset('landing/assets/images/landpage/slide-4.png') }}" alt="slide">
        </div>
        <div class="preview-welcome">
            <h1>Bringing the car wash <br>to your <span>door step</span></h1>
        </div>
    </div>

    <div class="how-it-works">
        <h3 class="landscap-title">how it <span>works</span></h3>
        <div class="tutorial">
            <div class="steps">
                <div class="step1">
                    <h4>step1</h4>
                    <p>Get Started And Create A New Account</p>
                </div>
                <div class="step2">
                    <h4>step2</h4>
                    <p>Fill Your Car Info (Type, Model, Brand)</p>
                </div>
                <div class="step3">
                    <h4>step3</h4>
                    <p>After Completing All The Data, Wait For Our Team To Start The Car Wash</p>
                </div>
                <button>Watch the video <img src="{{ asset('landing/assets/images/icons/watch-video.svg') }}"
                        alt="video-player"></button>
            </div>
            <div class="video"></div>
        </div>
    </div>

    <div class="our-services">
        <h3 class="landscap-title">our <span>services</span></h3>
        <div class="services">
            <div class="exterior-car-wash">
                <div class="washing-service">
                    <img src="{{ asset('landing/assets/images/landpage/service-img1.png') }}" alt="wash-car">
                </div>
                <div class="properties">
                    <h3>Exterior Car Wash</h3>
                    <ul>
                        <li><span>1 Wash Every 2 Days</span></li>
                        <li><span>QR Code attached to the car for recognition</span></li>
                        <li><span>Available 24/7</span></li>
                        <li><span>Highly Trained Staff</span></li>
                    </ul>
                    <button>Get Started</button>
                </div>
            </div>
            <div class="interior-car-wash">
                <div class="washing-service">
                    <img src="{{ asset('landing/assets/images/landpage/service-img2.png') }}" alt="wash-car">
                </div>
                <div class="properties">
                    <h3>Interior Car Wash</h3>
                    <h4>Coming Soon</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="frequently-questions">
        <h3 class="landscap-title">frequently asked <span>questions</span></h3>
        <div class="questions">
            <div class="items">
                <div>
                    <h3>When the car is washed ?</h3>
                    <p>1 wash every 2 days from 10:00 pm to 6:00 am</p>
                </div>
                <div>
                    <h3>What happened if I leave earlier than 6:00 am</h3>
                    <p>you will have the option to enter your leaving hour in your profile</p>
                </div>
                <div>
                    <h3>What are the car types supported ?</h3>
                    <p>Sedan, Van, Minbus, SUV</p>
                </div>
                <div>
                    <h3>How do you recognise cars with similar models ?</h3>
                    <p>QR code will be generated based on your filled data in the registration</p>
                </div>
            </div>
        </div>
    </div>

    <div class="customers-testimonial">
        <h3 class="landscap-title">what our <span>customers </span> say</h3>
        <div class="testimonials" id="testimonialsAutoRotate">
            <div class="customer">
                <div class="quote">
                    <img src="{{ asset('landing/assets/images/icons/bxs-quote-alt-left.svg') }}" alt="quote">
                </div>
                <p class="comment">
                    it’s always awesome to wash my car here, the great staff and the service. thank you for taking of my
                    car and making the service quick, efficient and affordable.
                </p>
                <div class="testimonial-information">
                    <div class="customers-information">
                        <img src="{{ asset('landing/assets/images/landpage/customer-2.png') }}" alt="customer">
                        <h4>emma doe</h4>
                    </div>
                    <div class="customers-ratting">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/light-star.svg') }}" alt="light-star">
                    </div>
                </div>
            </div>
            <div class="customer">
                <div class="quote">
                    <img src="{{ asset('landing/assets/images/icons/bxs-quote-alt-left.svg') }}" alt="quote">
                </div>
                <p class="comment">
                    it’s always awesome to wash my car here, the great staff and the service. thank you for taking of my
                    car and making the service quick, efficient and affordable.
                </p>
                <div class="testimonial-information">
                    <div class="customers-information">
                        <img src="{{ asset('landing/assets/images/landpage/customer.png') }}" alt="customer">
                        <h4>john doe</h4>
                    </div>
                    <div class="customers-ratting">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/light-star.svg') }}" alt="light-star">
                    </div>
                </div>
            </div>
            <div class="customer">
                <div class="quote">
                    <img src="{{ asset('landing/assets/images/icons/bxs-quote-alt-left.svg') }}" alt="quote">
                </div>
                <p class="comment">
                    it’s always awesome to wash my car here, the great staff and the service. thank you for taking of my
                    car and making the service quick, efficient and affordable.
                </p>
                <div class="testimonial-information">
                    <div class="customers-information">
                        <img src="{{ asset('landing/assets/images/landpage/customer-2.png') }}" alt="customer">
                        <h4>emma doe</h4>
                    </div>
                    <div class="customers-ratting">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/light-star.svg') }}" alt="light-star">
                    </div>
                </div>
            </div>
            <div class="customer">
                <div class="quote">
                    <img src="{{ asset('landing/assets/images/icons/bxs-quote-alt-left.svg') }}" alt="quote">
                </div>
                <p class="comment">
                    it’s always awesome to wash my car here, the great staff and the service. thank you for taking of my
                    car and making the service quick, efficient and affordable.
                </p>
                <div class="testimonial-information">
                    <div class="customers-information">
                        <img src="{{ asset('landing/assets/images/landpage/customer.png') }}" alt="customer">
                        <h4>john doe</h4>
                    </div>
                    <div class="customers-ratting">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/light-star.svg') }}" alt="light-star">
                    </div>
                </div>
            </div>
            <div class="customer">
                <div class="quote">
                    <img src="{{ asset('landing/assets/images/icons/bxs-quote-alt-left.svg') }}" alt="quote">
                </div>
                <p class="comment">
                    it’s always awesome to wash my car here, the great staff and the service. thank you for taking of my
                    car and making the service quick, efficient and affordable.
                </p>
                <div class="testimonial-information">
                    <div class="customers-information">
                        <img src="{{ asset('landing/assets/images/landpage/customer-2.png') }}" alt="customer">
                        <h4>emma doe</h4>
                    </div>
                    <div class="customers-ratting">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/dark-star.svg') }}" alt="dark-star">
                        <img src="{{ asset('landing/assets/images/icons/light-star.svg') }}" alt="light-star">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wash-packages">
        <h3 class="landscap-title">wash <span>packages</span></h3>
        <div class="package-types">
            <span class="active">sedan</span>
            <span>bus/minbus/suv</span>
        </div>
        <div class="sedan-plans">
            <div class="item">
                <h3 class="free-period">free wash</h3>
                <div class="specifications">
                    <h2>0 <sub>tl</sub></h2>
                    <span class="price">$0</span>
                    <p class="wash-times">1 Wash</p>
                </div>
                <button><a href="#">Get Started</a></button>
            </div>
            <div class="item">
                <h3 class="period">1 month</h3>
                <div class="specifications">
                    <h2>750 <sub>tl</sub></h2>
                    <span class="price">$53.6</span>
                    <p class="wash-times">15 Wash</p>
                </div>
                <button><a href="#">Get Started</a></button>
            </div>
            <div class="item">
                <span class="discount">Save 10%</span>
                <h3 class="period">3 months</h3>
                <div class="specifications">
                    <h2>2025 <sub>tl</sub></h2>
                    <span class="price">$144.6</span>
                    <p class="wash-times">45 Wash</p>
                </div>
                <button><a href="#">Get Started</a></button>
            </div>
            <div class="item">
                <span class="discount">Save 15%</span>
                <h3 class="period">6 months</h3>
                <div class="specifications">
                    <h2>3825 <sub>tl</sub></h2>
                    <span class="price">$273.2</span>
                    <p class="wash-times">90 Wash</p>
                </div>
                <button><a href="#">Get Started</a></button>
            </div>
            <div class="item">
                <span class="discount">Save 20%</span>
                <h3 class="period">1 year</h3>
                <div class="specifications">
                    <h2>7200<sub>tl</sub></h2>
                    <span class="price">$514.3</span>
                    <p class="wash-times">180 Wash</p>
                </div>
                <button><a href="#">Get Started</a></button>
            </div>
        </div>
    </div>

    <div class="get-started">
        <h3 class="landscap-title">get started with car wash <span>today</span></h3>
        <div class="free-wash">
            <button><a href="#">Get a free wash</a></button>
            <p>No Credit Card Required</p>
        </div>
    </div>

    <footer>
        <div>
            <div class="logo">
                <img src="{{ asset('landing/assets/images/landpage/Logo.svg') }}" alt="logo">
            </div>
            <h3 class="footer-description">TESHIL GROUP DANIŞMANLIK SAN. VE TİC. LTD. ŞTİ</h3>
        </div>

        <div class="contact">
            <div>
                <p>Azzam Al Nunu ( General Manager )</p>
                <p><img src="{{ asset('landing/assets/images/icons/phone.svg') }}" alt="phone"> +905335527211</p>
            </div>
            <div>
                <p>Saeb Al Qudwa ( Operation Manager )</p>
                <p><img src="{{ asset('landing/assets/images/icons/phone.svg') }}" alt="phone"> +905335529211</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('landing/assets/js/jquery-3.5.1.min.js') }}"></script>
    <script>
        if (window.innerWidth > 991) {
            $(document).ready(function() {
                let x = 324;

                function AutoRotate() {
                    $('#testimonialsAutoRotate').animate({
                        scrollLeft: x
                    }, 800);
                    x = x + 324;
                    let testimonials = $('#testimonialsAutoRotate');
                    let newScrollLeft = $('#testimonialsAutoRotate').scrollLeft();
                    let width = testimonials.width();
                    let scrollWidth = testimonials.get(0).scrollWidth;
                    let offset = 0;
                    if (scrollWidth - newScrollLeft - width == offset) {
                        $('#testimonialsAutoRotate').animate({
                            scrollLeft: $('#testimonialsAutoRotate').offset().left
                        }, 800);
                        x = 324;
                    }
                }
                setInterval(AutoRotate, 3000);
            });
        } else {
            $(document).ready(function() {
                let x = 150;

                function AutoRotate() {
                    $('#testimonialsAutoRotate').animate({
                        scrollLeft: x
                    }, 800);
                    x = x + 150;
                    let testimonials = $('#testimonialsAutoRotate');
                    let newScrollLeft = $('#testimonialsAutoRotate').scrollLeft();
                    let width = testimonials.width();
                    let scrollWidth = testimonials.get(0).scrollWidth;
                    let offset = 0;
                    if (scrollWidth - newScrollLeft - width == offset) {
                        $('#testimonialsAutoRotate').animate({
                            scrollLeft: $('#testimonialsAutoRotate').offset().left
                        }, 800);
                        x = 150;
                    }
                }
                setInterval(AutoRotate, 3000);
            });
        }


        function showMenu(e) {
            $('.mobile-menu .head .menu-toggle-btn#show-menu-btn').toggle();
            $('.mobile-menu .head .menu-toggle-btn#close-menu-btn').toggle();
            $('.mobile-menu .menu').toggle();
        }

        function closeMenu(e) {
            $('.mobile-menu .head .menu-toggle-btn#show-menu-btn').toggle();
            $('.mobile-menu .head .menu-toggle-btn#close-menu-btn').toggle();
            $('.mobile-menu .menu').toggle();
        }
    </script>
</body>

</html>
