<!doctype html>
<html class="h-100" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <meta name="description" content="A well made and handcrafted Bootstrap 5 template">
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/logo-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/logo-16x16.png">
    <link rel="icon" type="image/png" sizes="96x96" href="img/logo.png">
    <meta name="author" content="Yousef Elkammar">
    <meta name="HandheldFriendly" content="true">
    <title>7R Logistics</title>
    <link rel="stylesheet" href="css/theme.min.css">
    <style>
        /* Set default text color */
        * {
            color: #CE3333;
        }

        /* inter-300 - latin */
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 300;
            font-display: swap;
            src: local(''),
                url('fonts/inter-v12-latin-300.woff2') format('woff2'),
                /* Chrome 26+, Opera 23+, Firefox 39+ */
                url('fonts/inter-v12-latin-300.woff') format('woff');
            /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
        }

        /* inter-400 - latin */
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: local(''),
                url('fonts/inter-v12-latin-regular.woff2') format('woff2'),
                /* Chrome 26+, Opera 23+, Firefox 39+ */
                url('fonts/inter-v12-latin-regular.woff') format('woff');
            /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
        }

        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 500;
            font-display: swap;
            src: local(''),
                url('fonts/inter-v12-latin-500.woff2') format('woff2'),
                /* Chrome 26+, Opera 23+, Firefox 39+ */
                url('fonts/inter-v12-latin-500.woff') format('woff');
            /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
        }

        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 700;
            font-display: swap;
            src: local(''),
                url('fonts/inter-v12-latin-700.woff2') format('woff2'),
                /* Chrome 26+, Opera 23+, Firefox 39+ */
                url('fonts/inter-v12-latin-700.woff') format('woff');
            /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
        }
    </style>
    <script>
        function openContactPopup() {
            window.open('/contact', 'Contact', 'width=800,height=600');
        }
    </script>
</head>

<body class="bg-black text-white mt-0" data-bs-spy="scroll" data-bs-target="#navScroll">

    <nav id="navScroll" class="navbar navbar-dark bg-black fixed-top px-vw-5" tabindex="0">
        <div class="container">
            <a class="navbar-brand pe-md-4 fs-4 col-12 col-md-auto text-center" href="index.html">
                <img src="{{ asset('img/logo.png') }}" alt="7R Logistics" width="36" height="28">
                <span class="ms-md-1 mt-1 fw-bolder me-md-5">Logistics</span>
            </a>

            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 list-group list-group-horizontal">
                <li class="nav-item">
                    <a class="nav-link fs-5" href="/" aria-label="Homepage">
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-5" onclick="openContactPopup()" aria-label="A sample content page">
                        Contact
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-5" href="content.html" aria-label="A sample content page">
                        Pricing
                    </a>
                </li>

            </ul>
            <a href="client/login" aria-label="System" class="btn btn-outline-light">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-person" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM8 9a5 5 0 0 0-5 5v1h10v-1a5 5 0 0 0-5-5z" />
                </svg>
                <small>Login</small>
            </a>
        </div>
    </nav>

    <main>
        <div class="w-100 overflow-hidden position-relative bg-black text-white" data-aos="fade" id="home">
            <div class="position-absolute w-100 h-100 bg-black opacity-75 top-0 start-0"></div>
            <div class="container py-vh-4 position-relative mt-5 px-vw-5 text-center">
                <div class="row d-flex align-items-center justify-content-center py-vh-5">
                    <div class="col-12 col-xl-10">
                        <span class="h5 text-secondary fw">Our Mission</span>
                        <h1 class="display-huge mt-3 mb-3 lh-1"> Transportation Solutions, Built for you.</h1>
                    </div>
                    <div class="col-12 col-xl-8">
                        <p class="lead text-secondary"> To deliver tailored, reliable, and innovative transportation and
                            logistics solutions
                            that prioritize our clients' needs, ensuring timely deliveries, transparent
                            communication, and exceptional service while building trust and long-term
                            partnerships.</p>
                    </div>
                    <div class="col-12 text-center">
                        <a class="btn btn-xl btn-light" onclick="openContactPopup()">Contact us
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-arrow-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- TODO update images text -->
        <div class="w-100 position-relative bg-black text-white bg-cover d-flex align-items-center">
            <div class="container-fluid px-vw-5">
                <div class="position-absolute w-100 h-50 bg-dark bottom-0 start-0"></div>
                <div class="row d-flex align-items-center position-relative justify-content-center px-0 g-5">
                    <div class="col-12 col-lg-6">
                        <img src="img/webp/abstract18.webp" width="2280" height="1732" alt="abstract image"
                            class="img-fluid position-relative rounded-5 shadow" data-aos="fade-up">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <img src="img/webp/abstract6.webp" width="1116" height="1578" alt="abstract image"
                            class="img-fluid position-relative rounded-5 shadow" data-aos="fade-up"
                            data-aos-duration="2000">
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <img src="img/webp/abstract9.webp" width="1116" height="848" alt="abstract image"
                            class="img-fluid position-relative rounded-5 shadow" data-aos="fade-up"
                            data-aos-duration="3000">
                    </div>
                </div>
            </div>
        </div>


        <div class="bg-dark" id="about">
            <div class="container px-vw-5 py-vh-5">
                <div class="row d-flex align-items-center">
                    <div class="col-12 col-lg-7 text-lg-end" data-aos="fade-right">
                        <span class="h5 text-secondary fw-lighter">What we do</span>
                        <h2 class="display-4">At 7R’s Logistics, we specialize in
                            providing premier inland
                            transportation services that combine
                            innovation, reliability, and efficiency.</h2>
                    </div>
                    <div class="col-12 col-lg-5" data-aos="fade-left">
                        <h3 class="pt-5">Customer First</h3>
                        <p class="text-secondary"> we simplify the
                            movement of goods across regions,
                            ensuring seamless and timely
                            deliveries tailored to meet the diverse
                            needs of businesses.<br>
                            <a href="#contact" class="link-fancy link-fancy-light me-2">Tell me more</a>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z" />
                            </svg>
                        </p>
                        <h3 class="border-top border-secondary pt-5 mt-5">Exceptional Service</h3>
                        <p class="text-secondary">Our commitment to exceptional
                            service and logistics efficiency sets us
                            apart as a trusted partner for your
                            transportation needs.<br>
                            <a href="#contact" class="link-fancy link-fancy-light me-2">Tell me more</a>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                fill="currentColor" class="bi bi-arrow-right-circle" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z" />
                            </svg>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-black py-vh-3">
            <div class="container bg-black px-vw-5 py-vh-3 rounded-5 shadow">

                <div class="row gx-5">
                    <div class="col-12 col-md-6">
                        <div class="card bg-transparent mb-5" data-aos="zoom-in-up">
                            <div class="bg-dark shadow rounded-5 p-0">
                                <div class="img-container"
                                    style="position: relative; width: 100%; height: 0; padding-bottom: 56.25%;">
                                    <img src="img/webp/abstract3.webp" alt="abstract image"
                                        class="img-fluid rounded-5 no-bottom-radius" loading="lazy"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="p-5">
                                    <h2 class="fw-lighter">Tailored Logistics Services</h2>
                                    <p class="pb-4 text-secondary">Customized transportation solutions designed for
                                        your business,
                                        ensuring seamless handling of routine and urgent shipments.</p>
                                    <a href="#" class="link-fancy link-fancy-light">Know more</a>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-transparent" data-aos="zoom-in-up">
                            <div class="bg-dark shadow rounded-5 p-0">
                                <div class="img-container"
                                    style="position: relative; width: 100%; height: 0; padding-bottom: 75.95%;">
                                    <img src="img/webp/abstract2.webp" alt="abstract image"
                                        class="img-fluid rounded-5 no-bottom-radius" loading="lazy"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="p-5">
                                    <h2 class="fw-lighter"> Modern Fleet and
                                        Advanced Technology</h2>
                                    <p class="pb-4 text-secondary">Our modern fleet ensures
                                        safe, timely deliveries,
                                        supported by an ERP system
                                        for seamless shipping
                                        booking and real-time
                                        tracking, providing complete
                                        transparency.
                                    </p>
                                    <a href="#" class="link-fancy link-fancy-light">Know more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="p-5 pt-0 mt-5" data-aos="fade">
                            <span class="h5 text-secondary fw-lighter">Our Solutions</span>
                            <h2 class="display-4">We offer a wide range of solutions to meet your transportation needs.
                            </h2>
                        </div>
                        <div class="card bg-transparent mb-5 mt-5" data-aos="zoom-in-up">
                            <div class="bg-dark shadow rounded-5 p-0">
                                <div class="img-container"
                                    style="position: relative; width: 100%; height: 0; padding-bottom: 75.95%;">
                                    <img src="img/webp/abstract17.webp" alt="abstract image"
                                        class="img-fluid rounded-5 no-bottom-radius" loading="lazy"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="p-5">
                                    <h2 class="fw-lighter"> Efficient Route Optimization</h2>
                                    <p class="pb-4 text-secondary">Advanced tools for optimized routes, faster transit,
                                        cost
                                        savings, and eco-friendly logistics.</p>
                                    <a href="#" class="link-fancy link-fancy-light">Know more</a>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-transparent" data-aos="zoom-in-up">
                            <div class="bg-dark shadow rounded-5 p-0">
                                <div class="img-container"
                                    style="position: relative; width: 100%; height: 0; padding-bottom: 56.25%;">
                                    <img src="img/webp/abstract4.webp" alt="abstract image"
                                        class="img-fluid rounded-5 no-bottom-radius" loading="lazy"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="p-5">
                                    <h2 class="fw-lighter"> Skilled Workforce</h2>
                                    <p class="pb-4 text-secondary"> Experienced drivers and logistics experts ensure
                                        timely
                                        deliveries with professionalism and care.</p>
                                    <a href="#" class="link-fancy link-fancy-light">Know more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="bg-dark position-relative">
            <div class="container px-vw-5 py-vh-5">
                <div class="row d-flex align-items-center">

                    <div class="col-12 col-lg-7">
                        <!-- TODO: Add a image of our fleet -->
                        <img class="img-fluid rounded-5 mb-n5 shadow" src="img/webp/person9.webp" width="512"
                            height="512" alt="a nice person" loading="lazy" data-aos="zoom-in-right">
                        <img class="img-fluid rounded-5 ms-5 mb-n5 shadow" src="img/webp/person11.webp"
                            width="512" height="512" alt="another nice person" loading="lazy"
                            data-aos="zoom-in-up">
                    </div>
                    <div class="col-12 col-lg-5 bg-dark rounded-5 py-5" data-aos="fade">
                        <span class="h5 text-secondary fw-lighter">Our fleet</span>
                        <h2 class="display-4">
                            Our fleet is designed to meet the needs of our customers, with a focus on safety,
                            efficiency, and reliability.
                        </h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid px-vw-5 position-relative" data-aos="fade">
            <div class="position-absolute w-100 h-50 bg-black top-0 start-0"></div>
            <div class="position-relative py-vh-5 bg-cover bg-center rounded-5"
                style="background-image: url(img/webp/abstract12.webp)">
                <div class="container bg-black px-vw-5 py-vh-3 rounded-5 shadow">
                    <div class="row d-flex align-items-center">

                        <div class="col-6 d-flex align-items-center bg-dark shadow rounded-5 p-0"
                            data-aos="zoom-in-up">
                            <div class="row d-flex justify-content-center">
                                <div class="col-12">
                                    <img src="img/webp/person103.webp" width="684" height="457"
                                        alt="our CEO with some nice numbers" class="img-fluid rounded-5"
                                        loading="lazy">
                                </div>
                                <div class="col-12 col-lg-10 col-xl-8 text-center my-5">
                                    <p class="lead border-bottom pb-4 border-secondary">
                                        "In an era where logistics tech was stuck in the past, we dared to innovate. By
                                        digitizing our operations, we've revolutionized how heavy cargo moves. Every
                                        delivery is now a testament to how technology and transportation can work in
                                        perfect harmony."</p>
                                    <p class="text-secondary text-center">- Transportex Team</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-5 offset-1">
                            <span class="h5 text-secondary fw-lighter">The numbers</span>
                            <h2 class="display-huge fw-bolder" data-aos="zoom-in-left">+1460 KM</h2>
                            <p class="h4 fw-lighter text-secondary">
                                Distance covered.
                            </p>

                            <h2 class="display-huge fw-bolder border-top border-secondary pt-5 mt-5"
                                data-aos="zoom-in-left">32+
                            </h2>
                            <p class="h4 fw-lighter text-secondary">
                                Trips around the country.
                            </p>
                            <h2 class="display-huge fw-bolder border-top border-secondary pt-5 mt-5"
                                data-aos="zoom-in-left">12+</h2>
                            <p class="h4 fw-lighter text-secondary">
                                Success partners.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="bg-dark py-vh-5">
            <div class="container px-vw-5">
                <div class="row d-flex gx-5 align-items-center">
                    <div class="col-12 col-lg-6">
                        <div class="rounded-5 bg-black p-5 shadow" data-aos="zoom-in-right">

                            <p class="text-secondary lead">"Lorem ipsum dolor sit amet, consectetur tempor incididunt
                                ut labore et
                                dolore magna aliqua ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>
                            <div
                                class="d-flex justify-content-start align-items-center border-top border-secondary pt-3">
                                <img src="img/webp/person14.webp" width="96" height="96"
                                    class="rounded-circle me-3" alt="a nice person" data-aos="fade" loading="lazy">
                                <div>
                                    <span class="h6 fw-5">Jane Doemunsky</span><br>
                                    <small class="text-secondary">COO, The Boo Corp.</small>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-5 bg-black p-5 shadow mt-5" data-aos="zoom-in-right">
                            <p class="text-secondary lead">"Lorem ipsum dolor sit amet, consectetur tempor incididunt
                                ut labore et
                                dolore magna aliqua. Ut enim ad minim veniam. quis nostrud exercitation ullamco laboris
                                nisi ut aliquip
                                ex ea commodo consequat."</p>
                            <div
                                class="d-flex justify-content-start align-items-center border-top border-secondary pt-3">
                                <img src="img/webp/person13.webp" width="96" height="96"
                                    class="rounded-circle me-3" alt="a nice person" data-aos="fade" loading="lazy">
                                <div>
                                    <span class="h6 fw-5">Jane Doemunsky</span><br>
                                    <small class="text-secondary">COO, The Boo Corp.</small>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="p-5 pt-0" data-aos="fade">
                            <span class="h5 text-secondary fw-heavy">Our Partners in Excellence</span>
                            <h2 class="display-4">Shaping industries, driving innovation, and achieving greatness
                                together.</h2>
                        </div>

                        <div class="rounded-5 bg-black p-5 shadow mt-5 gradient" data-aos="zoom-in-left">
                            <p class="lead">"Consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
                                et dolore magna
                                aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                                aliquip ex ea commodo
                                consequat."</p>
                            <div class="d-flex justify-content-start align-items-center border-top pt-3">
                                <img src="img/webp/person16.webp" width="96" height="96"
                                    class="rounded-circle me-3" alt="a nice person" data-aos="fade" loading="lazy">
                                <div>
                                    <span class="h6 fw-5">Jane Doemunsky</span><br>
                                    <small>COO, The Boo Corp.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

    <footer class="bg-black border-top border-dark">
        <div class="container py-vh-4 text-secondary fw-lighter">
            <div class="row">
                <div class="col-12 col-lg-5 py-4 text-center text-lg-start">
                    <a class="navbar-brand pe-md-4 fs-4 col-12 col-md-auto text-center" href="index.html">
                        <img src="{{ asset('img/logo.png') }}" width="50" height="50"
                            alt="7R's Logistics Logo" class="img-fluid">
                        <span class="ms-md-1 mt-1 fw-bolder me-md-5">Logistics</span>
                    </a>

                </div>
                <div class="col border-end border-dark">
                    <span class="h6">Company</span>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">About us</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">Legal</a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">Career</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">Contact</a>
                        </li>
                    </ul>
                </div>
                <div class="col border-end border-dark">
                    <span class="h6">Services</span>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">Pricing</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">Products</a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">Customers</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">Portfolio</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">Success Stories</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">More</a>
                        </li>
                    </ul>
                </div>
                <div class="col">
                    <span class="h6">Support</span>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">About us</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">Legal</a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">Career</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="link-fancy link-fancy-light">Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container text-center small py-vh-2 border-top border-dark">
            &copy; {{ date('Y') }} 7R Logistics. All Rights Reserved.
            <br>
            <span class="text-muted">Crafted with dedication by 7R Logistics Tech Team</span>
        </div>
    </footer>








    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/aos.js"></script>
    <script>
        AOS.init({
            duration: 800, // values from 0 to 3000, with step 50ms
        });
    </script>
    <script>
        let scrollpos = window.scrollY;
        const header = document.querySelector(".navbar");
        const header_height = header.offsetHeight;

        const add_class_on_scroll = () => header.classList.add("scrolled", "shadow-sm");
        const remove_class_on_scroll = () => header.classList.remove("scrolled", "shadow-sm");

        window.addEventListener('scroll', function() {
            scrollpos = window.scrollY;

            if (scrollpos >= header_height) {
                add_class_on_scroll();
            } else {
                remove_class_on_scroll();
            }

            console.log(scrollpos);
        })
    </script>

</body>

</html>
