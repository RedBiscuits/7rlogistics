<!doctype html>
<html class="h-100" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <meta name="description" content="A well made and handcrafted Bootstrap 5 template">
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <title>7R Logistics</title>

    <link rel="icon" type="image/png" sizes="32x32" href="img/logo-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/logo-16x16.png">
    <link rel="icon" type="image/png" sizes="96x96" href="img/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">


    <meta name="author" content="Yousef Elkammar">
    <meta name="HandheldFriendly" content="true">
    <link rel="stylesheet" href="css/theme.min.css">
    <link rel="stylesheet" href="css/home.css">
    <script rel="script" src="js/home.js"></script>
   <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
                <div class="row d-flex align-items-center position-relative justify-content-center px-0 g-5"
                    style="min-height: 600px;">
                    <div class="col-12 col-lg-6" style="height: 500px;">
                        <div class="img-container" style="position: relative; width: 100%; height: 100%;">
                            <img src="img/webp/aaa.avif" alt="abstract image"
                                class="img-fluid position-relative rounded-5 shadow" data-aos="fade-up"
                                data-aos-duration="2000"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        </div>

                    </div>
                    <div class="col-12 col-md-6 col-lg-3" style="height: 450px;">
                        <div class="img-container" style="position: relative; width: 100%; height: 100%;">
                            <img src="img/webp/abstract6.jpg" alt="abstract image"
                                class="img-fluid position-relative rounded-5 shadow" data-aos="fade-up"
                                data-aos-duration="2000"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3" style="height: 250px;">
                        <div class="img-container" style="position: relative; width: 100%; height: 100%;">

                            <img src="img/webp/abstract9.jpg" alt="abstract image"
                                class="img-fluid position-relative rounded-5 shadow" data-aos="fade-up"
                                data-aos-duration="2000"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        </div>

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
                            <a href="/about" class="link-fancy link-fancy-light me-2">Tell me more</a>
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
                            <a href="/about" class="link-fancy link-fancy-light me-2">Tell me more</a>
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
                    <div class="p-5 pt-0 mt-5" data-aos="fade">
                        <h2 class="display-4">Wide range of solutions to meet your transportation needs.</h2>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card bg-transparent mb-5" data-aos="zoom-in-up">
                            <div class="bg-dark shadow rounded-5 p-0">

                                <div class="p-5">
                                    <h2 class="fw-lighter"><strong>Tailored Logistics Services</strong></h2>
                                    <p class="pb-4 text-secondary">Customized transportation solutions designed for
                                        your business,
                                        ensuring seamless handling of routine and urgent shipments.</p>
                                    <a href="/about" class="link-fancy link-fancy-light">Know more</a>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-transparent" data-aos="zoom-in-up">
                            <div class="bg-dark shadow rounded-5 p-0">
                                <div class="p-5">
                                    <h2 class="fw-lighter"><strong> Modern Fleet and
                                            Advanced Technology</strong></h2>
                                    <p class="pb-4 text-secondary">Our modern fleet ensures
                                        safe, timely deliveries,
                                        supported by an ERP system
                                        for seamless shipping
                                        booking and real-time
                                        tracking, providing complete
                                        transparency.
                                    </p>
                                    <a href="/about" class="link-fancy link-fancy-light">Know more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">

                        <div class="card bg-transparent mb-5 mt-5" data-aos="zoom-in-up">
                            <div class="bg-dark shadow rounded-5 p-0">
                                <div class="p-5">
                                    <h2 class="fw-lighter"><strong> Efficient Route Optimization</strong></h2>
                                    <p class="pb-4 text-secondary">Advanced tools for optimized routes, faster transit,
                                        cost
                                        savings, and eco-friendly logistics.</p>
                                    <a href="/about" class="link-fancy link-fancy-light">Know more</a>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-transparent" data-aos="zoom-in-up">
                            <div class="bg-dark shadow rounded-5 p-0">
                                <div class="p-5">
                                    <h2 class="fw-lighter"><strong> Skilled Workforce</strong></h2>
                                    <p class="pb-4 text-secondary"> Experienced drivers and logistics experts ensure
                                        timely
                                        deliveries with professionalism and care.</p>
                                    <a href="/about" class="link-fancy link-fancy-light">Know more</a>
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
                        <!-- Image and Description Slider -->
                        <div id="imageSlider" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <!-- First Item (already updated) -->
                                <div class="carousel-item active">
                                    <div class="carousel-card-wrapper">
                                        <img class="carousel-card-image" src="img/webp/dababah.png"
                                            alt="Dababah truck" loading="lazy">
                                        <div class="carousel-card-content">
                                            <h3 class="truck-name">Dababah</h3>
                                            <div class="specs-grid">
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Length</div>
                                                        <div class="spec-value">230 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Width</div>
                                                        <div class="spec-value">170 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Height</div>
                                                        <div class="spec-value">138 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Payload</div>
                                                        <div class="spec-value">1.7 Tons</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="truck-description">A medium-sized flatbed truck for versatile
                                                cargo transport</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Second Item -->
                                <div class="carousel-item">
                                    <div class="carousel-card-wrapper">
                                        <img class="carousel-card-image" src="img/webp/dababah_box.png"
                                            alt="Dababah Box truck" loading="lazy">
                                        <div class="carousel-card-content">
                                            <h3 class="truck-name">Dababah Box</h3>
                                            <div class="specs-grid">
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Length</div>
                                                        <div class="spec-value">215 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Width</div>
                                                        <div class="spec-value">150 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Height</div>
                                                        <div class="spec-value">140 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Payload</div>
                                                        <div class="spec-value">1.5 Tons</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="truck-description">A covered truck designed for secure and
                                                protected cargo</p>
                                            <span class="reefer-badge">Available as Reefer</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Third Item -->
                                <div class="carousel-item">
                                    <div class="carousel-card-wrapper">
                                        <img class="carousel-card-image" src="img/webp/jumbo.png" alt="Jumbo truck"
                                            loading="lazy">
                                        <div class="carousel-card-content">
                                            <h3 class="truck-name">Jumbo 7000</h3>
                                            <div class="specs-grid">
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Length</div>
                                                        <div class="spec-value">437 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Width</div>
                                                        <div class="spec-value">210 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Height</div>
                                                        <div class="spec-value">150 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Payload</div>
                                                        <div class="spec-value">3.4 Tons</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="truck-description">A large open flatbed truck designed for
                                                heavy-duty transport</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fourth Item -->
                                <div class="carousel-item">
                                    <div class="carousel-card-wrapper">
                                        <img class="carousel-card-image" src="img/webp/jumbo_box.png"
                                            alt="Jumbo Box truck" loading="lazy">
                                        <div class="carousel-card-content">
                                            <h3 class="truck-name">Jumbo 7000 Box</h3>
                                            <div class="specs-grid">
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Length</div>
                                                        <div class="spec-value">545 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Width</div>
                                                        <div class="spec-value">195 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" />
                                                        <path d="M7 12h2v5H7zm4-3h2v8h-2zm4-3h2v11h-2z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Height</div>
                                                        <div class="spec-value">190 cm</div>
                                                    </div>
                                                </div>
                                                <div class="spec-item">
                                                    <svg class="spec-icon" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" />
                                                    </svg>
                                                    <div>
                                                        <div class="spec-label">Payload</div>
                                                        <div class="spec-value">3.8 Tons</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="truck-description">A large covered truck, perfect for
                                                transporting large cargo</p>
                                            <span class="reefer-badge">Available as Reefer</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Carousel Controls -->
                            <button class="carousel-control-prev" type="button" data-bs-target="#imageSlider"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#imageSlider"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
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





        <div class="container-fluid px-vw-5 position-relative" data-aos="fade" id="numbers">
            <div class="position-absolute w-100 h-50 bg-black top-0 start-0"></div>
            <div class="position-relative py-vh-5 bg-cover bg-center rounded-5"
                style="background-image: url(img/webp/abstract12.webp)">
                <div class="container bg-black px-vw-5 py-vh-3 rounded-5 shadow">
                    <div class="row d-flex align-items-center">

                        <div class="col-6 d-flex align-items-center bg-dark shadow rounded-5 p-0"
                            data-aos="zoom-in-up">
                            <div class="row d-flex justify-content-center">
                                <div class="col-12" style="border-radius: 2rem;">
                                    <dotlottie-player direction="1"
                                        id="animation_team-work_e79e0354-1162-11ee-94de-af5f3bbf4233" speed="1"
                                        mode="normal" autoplay
                                        loop
                                        src="https://assets-v2.lottiefiles.com/a/165556e0-1170-11ee-ba69-cb0e26064da5/puJi8zQL3x.lottie"></dotlottie-player>
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
        <div class="bg-dark py-vh-5" id="partners">
            <div class="container px-vw-5">
                <div class="row d-flex gx-5 align-items-center">
                    <div class="col-12 col-lg-6">
                        <div class="rounded-5 bg-black p-5 shadow" data-aos="zoom-in-right">
                            <img src="img/webp/elite.png" class="img-fluid w-100 mb-3 rounded-4" alt="Elite Logistics"
                                style="border-radius: 2rem !important;" />

                            <div
                                class="d-flex justify-content-start align-items-center border-top border-secondary pt-3">
                                <div>
                                    <span class="h6 fw-5">Elite Logistics</span><br>
                                    <small class="text-secondary">Trusted logistics partner with a focus on
                                        efficiency and reliability.</small>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-5 bg-black p-5 shadow mt-5" data-aos="zoom-in-left">
                            <img src="img/webp/HAT.svg" class="img-fluid w-100 mb-3 rounded-4" alt="Hassan Allam Trading & Engineering"
                            style="border-radius: 2rem !important;" />
                            <div class="d-flex justify-content-start align-items-center border-top pt-3">
                                <div>
                                    <span class="h6 fw-5">Hassan Allam Trading & Engineering</span><br>
                                    <small>Core supplier of heavy machinery and equipment.</small>
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
                        <div class="rounded-5 bg-black p-5 shadow mt-5" data-aos="zoom-in-right">
                            <img src="img/webp/hilti.svg" class="img-fluid w-100 mb-3 rounded-4" alt="Hilti"
                                style="border-radius: 2rem !important;" />
                            <div
                                class="d-flex justify-content-start align-items-center border-top border-secondary pt-3">
                                <div>
                                    <span class="h6 fw-5">Hilti</span><br>
                                    <small class="text-secondary">Leading supplier of construction tools and equipment.</small>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-5 bg-black p-5 shadow mt-5" data-aos="zoom-in-right">
                            <img src="img/webp/TS.png" class="img-fluid w-100 mb-3 rounded-4" alt="T.S Logistics"
                                style="border-radius: 2rem !important;" />
                            <div
                                class="d-flex justify-content-start align-items-center border-top border-secondary pt-3">
                                <div>
                                    <span class="h6 fw-5">T.S Logistics</span><br>
                                    <small class="text-secondary">Leading logistics partner specializing in supply
                                        chain management and timely deliveries..</small>
                                </div>
                            </div>
                        </div>
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
                        <img src="img/webp/qr.png" alt="QR Code"/>
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
                            <strong>+20 110 1167 717</strong>
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

    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
</body>

</html>
