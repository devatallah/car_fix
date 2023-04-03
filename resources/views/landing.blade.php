<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" type="image/x-icon" href="http://magicsol.net/landing/assets/images/landpage/svg/logo1.png">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magic Solution</title>
    <link rel="stylesheet" href="{{ asset('landing/assets/css/landing-page.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/main.css') }}">
</head>

<body>

    <header>
        <div class="left-navbar">
            <img class="logo" src="{{ asset('landing/assets/images/landpage/svg/logo1.svg') }}" alt="logo">
            <ul class="nav-links">
                <li class="nav-item"><a href="#howItWork"></a></li>
                <li class="nav-item"><a href="#pricing">Pricing</a></li>
            </ul>
        </div>
       
    </header>

    <div class="mobile-menu">
        <div class="head">
            <div class="content">
                <img class="logo" src="{{ asset('landing/assets/images/landpage/svg/logo1.png') }}" alt="logo">
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
                    <li class="nav-item"><a href="#howItWork">How it work</a></li>
                    <li class="nav-item"><a href="#pricing">Pricing</a></li>
                </ul>
                <button class="login-link"><a href="#">Login</a></button>
                <button class="get-started-link"><a href="#">Get Started</a></button>
            </div>
        </div>
    </div>

    <div class="preview-slide">
        <div class="slide-images">
            <img src="{{ asset('landing/assets/images/landpage/slide-1.jpg') }}" alt="slide">
            <img src="{{ asset('landing/assets/images/landpage/slide-2.jpg') }}" alt="slide">
            <img src="{{ asset('landing/assets/images/landpage/slide-3.jpg') }}" alt="slide">
            <img src="{{ asset('landing/assets/images/landpage/slide-4.jpg') }}" alt="slide">
        </div>
        <div class="preview-welcome">
            <h1>MAGIC SOLUTION SECURE <br><span>FAST AND EASY</span></h1>
        </div>
    </div>

    <div class="how-it-works" id="howItWork">
        <h3 class="landscap-title">how it <span>works</span></h3>
        <div class="tutorial">
            <p>
                MagicSolution is a software that specializes in solving environmental systems such as (dpf , egr ,
                adblue
                off ) and many other solutions. The program works according to guaranteed easy and fast software
                solutions.
                There is full support around the clock
                The user will get a free update over the course of the subscription.
            </p>
        </div>
    </div>

    <div class="our-services">
        <h3 class="landscap-title">our <span>services</span></h3>
        <div class="services">
            <div class="exterior-car-wash">
                <div class="washing-service">
                    <img src="{{ asset('landing/assets/images/landpage/file-manager.jpg') }}" alt="wash-car">
                </div>
                <div class="properties">
                    <h3>You will not need someone to work your files for you after this program</h3>
                </div>
            </div>
            <div class="interior-car-wash">
                <div class="washing-service">
                    <img src="{{ asset('landing/assets/images/landpage/payment.jpg') }}" alt="wash-car">
                </div>
                <div class="properties">
                    <h3>Legal license</h3>
                    <h4>Payment via PayPal or Western Union</h4>
                </div>
            </div>
        </div>
    </div>
    <div >
        <div class="landscap-title" >
            <h1>We have the best, fastest, and most reliable system in the world.</h1>
        </div>
        <img src="landing/assets/images/landpage/app.png" alt="Snow" style="width:100%;">
    </div>
    <br>
    <div class="frequently-questions">
        <h3 class="landscap-title">frequently asked <span>questions</span></h3>
        <div class="questions">
            <div class="items">
                <div>
                    <h3>MagicSolution working 7/24 totally online</h3>
                    <p>
                        DPF, EGR,Lambda, Adblue, NOX, DTC, CAT, HotStart, Flap, O2, Speedlimit, Torqmonitoring,
                        Start-Stop and much more
                    </p>
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
                    Would highly recommend this auto shop! He was very professional, very helpful & had great prices.
                    Will definitely come back
                </p>
            </div>
            <div class="customer">
                <div class="quote">
                    <img src="{{ asset('landing/assets/images/icons/bxs-quote-alt-left.svg') }}" alt="quote">
                </div>
                <p class="comment">
                    Very polite, excellent service; I’m very satisfied on the work done in my car, I highly recommend to
                    anybody. Thank you.
                </p>
            </div>
            <div class="customer">
                <div class="quote">
                    <img src="{{ asset('landing/assets/images/icons/bxs-quote-alt-left.svg') }}" alt="quote">
                </div>
                <p class="comment">
                I recommend buying it, it is easy and has many solutions. There is fast support
                </p>
            </div>
            <div class="customer">
                <div class="quote">
                    <img src="{{ asset('landing/assets/images/icons/bxs-quote-alt-left.svg') }}" alt="quote">
                </div>
                <p class="comment">
                It's great, very fast, different from other programs
                </p>
            </div>
            <div class="customer">
                <div class="quote">
                    <img src="{{ asset('landing/assets/images/icons/bxs-quote-alt-left.svg') }}" alt="quote">
                </div>
                <p class="comment">
                There are many solutions and immediate support                </p>
            </div>
            <div class="customer">
                <div class="quote">
                    <img src="{{ asset('landing/assets/images/icons/bxs-quote-alt-left.svg') }}" alt="quote">
                </div>
                <p class="comment">
                    Very professional , quick, and took care of my car needs.
                </p>
            </div>
        </div>
    </div>

    <div class="wash-packages" id="pricing">
        <h3 class="landscap-title">wash <span>packages</span></h3>
        <div class="sedan-plans">
            <div class="item">
                <h3 class="period">One month</h3>
                <div class="specifications">
                    <p>$95</p>
                </div>
                <button><a href="#">Get Started</a></button>
            </div>
            <div class="item">
                <h3 class="period">6 months</h3>
                <div class="specifications">
                    <p>$400</p>
                    <br>
                    <p>Renewal $100</p>
                </div>
                <button><a href="#">Get Started</a></button>
            </div>
            <div class="item">
                <h3 class="period">One-year</h3>
                <div class="specifications">
                    <p>$600</p>
                    <br>
                    <p>Renewal $150</p>
                </div>
                <button><a href="#">Get Started</a></button>
            </div>
            <div class="item">
                <h3 class="period">Pops and Bangs</h3>
                <div class="specifications">
                    <p>1 CREDIT</p>
                    <br>
                    <p>30$ 1 CREDIT</p>
                </div>
                <button><a href="#">Get Started</a></button>
            </div>
            <div class="item">
                <h3 class="period">STAGE</h3>
                <div class="specifications">
                    <p>1.5 CREDIT</p>
                    <br>
                    <p>30$ 1 CREDIT</p>
                </div>
                <button><a href="#">Get Started</a></button>
            </div>
        </div>
    </div>

    <div class="get-started">
        <h3 class="landscap-title">Unlimited  <span>Usage</span></h3>
        <div class="free-wash">
            <button><a href="https://mycarfix1.s3.eu-west-1.amazonaws.com/ecus/file/magicsolution.zip">Download App </a></button>
            <p>The Program Only Works On 1 PC</p>
        </div>
    </div>

    <footer>
        <div>
            <div class="logo">
                <img src="{{ asset('landing/assets/images/landpage/svg/logo1.png') }}" alt="logo">
            </div>
            <h3 class="footer-description">Magic Solution </h3>
        </div>

        <div class="contact">
            <div>
                <p><img src="https://img.icons8.com/external-justicon-flat-justicon/128/external-email-notifications-justicon-flat-justicon.png 2x, https://img.icons8.com/external-justicon-flat-justicon/64/external-email-notifications-justicon-flat-justicon.png" alt="phone"> Email || info@magicsol.net</p>
                <p><img src="{{ asset('landing/assets/images/icons/phone.svg') }}" alt="phone"> +972567777212</p>
                <p>MagicSolution Support</p>

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