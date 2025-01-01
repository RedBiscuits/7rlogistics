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
    <meta name="author" content="Holger Koenemann">
    <meta name="generator" content="Eleventy v2.0.0">
    <meta name="HandheldFriendly" content="true">
    <title>About</title>
    <link rel="stylesheet" href="css/theme.min.css">
    <script>
        function openContactPopup() {
            window.open('/contact', 'Contact', 'width=800,height=600');
        }
    </script>
    <style>
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
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#FFFFFF"
                    class="bi bi-person" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM8 9a5 5 0 0 0-5 5v1h10v-1a5 5 0 0 0-5-5z" />
                </svg>
                <small style="color: #FFFFFF;">Login</small>
            </a>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="row d-flex justify-content-center py-vh-5 pb-0">
                <div class="col-12 col-lg-10 col-xl-8">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="display-1 fw-bold">About Us
                                <span class="fs-4">Welcome to</span><br>
                                Our Story
                            </h1>
                            <p class="lead border-top pt-5 mt-5" data-aos="fade-up">
                                We are a passionate team of innovators, creators, and problem-solvers dedicated to
                                delivering exceptional
                                digital solutions. Founded in 2020, we've grown from a small startup to a dynamic
                                company serving clients
                                worldwide. Our mission is to transform ideas into powerful digital experiences that
                                drive success.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-10 col-xl-8">
                    <div class="row d-flex align-items-start" data-aos="fade-right">
                        <div class="col-12 col-lg-7">
                            <h2 class="h3 mt-5 border-top pt-5">01. Our Mission</h2>
                            <p class="text-secondary">
                                To empower businesses through innovative digital solutions that drive growth, enhance
                                efficiency, and create
                                lasting value. We believe in combining cutting-edge technology with creative thinking to
                                deliver results that
                                exceed expectations.
                            </p>
                        </div>
                        <div class="col-12 col-lg-4 offset-lg-1 bg-gray-900 p-5 mt-5">
                            <h3 class="h6">Key Point</h3>
                            <p class="text-secondary">
                                Empowering businesses through innovation and excellence in digital solutions
                            </p>
                        </div>
                    </div>

                    <div class="row d-flex align-items-start" data-aos="fade-right">
                        <div class="col-12 col-lg-7">
                            <h2 class="h3 mt-5 border-top pt-5">02. Our Values</h2>
                            <p class="text-secondary">
                                Innovation, integrity, and client success form the cornerstone of our operations. We
                                believe in transparent
                                communication, sustainable practices, and building long-term relationships with our
                                clients and partners.
                                Our commitment to excellence drives everything we do.
                            </p>
                        </div>
                        <div class="col-12 col-lg-4 offset-lg-1 bg-gray-900 p-5 mt-5">
                            <h3 class="h6">Core Values</h3>
                            <p class="text-secondary">
                                Innovation • Integrity • Excellence • Client Success • Sustainability
                            </p>
                        </div>
                    </div>

                    <div class="row d-flex align-items-start" data-aos="fade-right">
                        <div class="col-12 col-lg-7">
                            <h2 class="h3 mt-5 border-top pt-5">03. Our Expertise</h2>
                            <p class="text-secondary">
                                With expertise spanning web development, digital marketing, and software solutions, our
                                team brings diverse
                                skills and experience to every project. We stay ahead of industry trends and
                                continuously invest in our
                                capabilities to deliver cutting-edge solutions.
                            </p>
                        </div>
                        <div class="col-12 col-lg-4 offset-lg-1 bg-gray-900 p-5 mt-5">
                            <h3 class="h6">Specialties</h3>
                            <p class="text-secondary">
                                Web Development • Digital Marketing • Software Solutions • UI/UX Design • Cloud Services
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row d-flex align-items-start justify-content-center py-vh-3 text-muted" data-aos="fade">
                <div class="col-12 col-lg-10 col-xl-9">
                    <div class="p-5 small bg-gray-900">
                        <p>Join us on our journey to transform the digital landscape. Whether you're a startup looking
                            to establish
                            your presence or an enterprise seeking digital transformation, we're here to help you
                            succeed in the digital age.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <footer class="bg-black border-top border-dark" id="footer">
        <div class="container py-vh-4 text-secondary fw-lighter">
            <div class="row">
                <div class="col-12 col-lg-5 py-4 text-center text-lg-start">


                    <div class="mt-3">
                        <img src="img/webp/qr.png" alt="QR Code" />
                    </div>
                </div>
                <div class="col border-end border-dark">
                    <span class="h6">Company</span>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="/about" class="link-fancy link-fancy-light">About us</a>
                        </li>

                        <li class="nav-item">
                            <a  class="link-fancy link-fancy-light" onclick="openContactPopup()">Contact</a>
                        </li>
                    </ul>
                </div>
                <div class="col border-end border-dark">
                    <span class="h6">Services</span>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a href="/pricing" class="link-fancy link-fancy-light">Pricing</a>
                        </li>

                        <li class="nav-item">
                            <a href="/client/login" class="link-fancy link-fancy-light">Customers</a>
                        </li>
                        <li class="nav-item">
                            <a href="#numbers" class="link-fancy link-fancy-light">Portfolio</a>
                        </li>
                        <li class="nav-item">
                            <a href="#partners" class="link-fancy link-fancy-light">Success Stories</a>
                        </li>
                    </ul>
                </div>
                <div class="col">
                    <span class="h6">Support</span>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <strong> +20 110 1167 717</strong>
                        </li>
                        <li class="nav-item">
                            <strong>ops@7rlogistics.com</strong>
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
