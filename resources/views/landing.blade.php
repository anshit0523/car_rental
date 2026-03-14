<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dumaguete EZE Car Rental</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #efefef;
            color: #ffffff;
        }

        .navbar .container {
            min-height: 60px;
            padding-top: 2px;
            padding-bottom: 8px;
        }

        .navbar {
            background: transparent !important;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand img {
            height: 85px;
            width: auto;
            object-fit: contain;
        }

        .navbar .btn {
            padding: .45rem .9rem;
            font-size: 1rem;
            line-height: 1.2;
        }

        .btn-orange {
            background-color: #ff4d00;
            color: white;
            border: none;
        }

        .btn-orange:hover {
            background-color: #e04300;
        }

        .hero {
            padding: 190px 0 135px;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url("{{ asset('storage/cars/bgcar.png') }}");
            background-size: cover;
            background-position: center;

        }

        .search-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px;
        }

      
        .card-custom {
            background-color: #ffffff;
            color: #000000;
            border: none;
            border-radius: 12px;
            transition: 0.3s;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            /* box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4); */
        }

        .card-custom .text-muted {
            color: #bbbbbb !important;
        }

        .price {
            color: #ff4d00;
            font-weight: 600;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 40px;
        }

        .feature-icon {
            font-size: 40px;
            color: #ff4d00;
            margin-bottom: 15px;
        }
        .brand-container {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background-color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 20px;
        }
        .brand-logo {
            width: 100%;
            height: 100px;
            object-fit: contain;
        }

        .cta {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            text-align: center;
        }

        footer {
            background: #000;
            padding: 20px 0;
            text-align: center;
        }
    </style>
</head>

<body>
    
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <img src="{{ asset('storage/cars/ezelogo.png') }}" alt="Logo">
            </a>
            <div class="d-flex">
                <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-orange">Register</a>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">DUMAGUETE EZE CAR RENTAL</h1>
            <p class="lead mb-4">Drive with Comfort & Confidence</p>

            
        </div>
    </section>

    <!-- AVAILABLE CARS -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center section-title" style="color: #000000;">Available Cars</h2>
            <div class="row">

                @forelse ($cars as $car)
                    <div class="col-md-4 mb-4">
                        <div class="card card-custom h-100 ">
                            @php
                                $images = is_string($car->images) ? json_decode($car->images, true) : $car->images;
                            @endphp

                            @if($images && count($images) > 0)
                                <img src="{{ asset('storage/' . $images[0]) }}" class="card-img-top"
                                    style="height:220px; object-fit:cover;">
                            @endif

                            <div class="card-body">
                                <h5 class="card-title">
                                    {{ $car->brand->name ?? '' }} {{ $car->model }}
                                </h5>

                                <p class="text-muted small">
                                    <i class="fas fa-user"></i> {{ $car->seats }} seats •
                                    <i class="fas fa-cogs"></i> {{ $car->transmission->type ?? '' }} •
                                    <i class="fas fa-gas-pump"></i> {{ $car->fuelType->type ?? '' }}
                                </p>

                                <p class="price">
                                    ₱{{ number_format($car->price_per_day, 2) }} / day
                                </p>

                                <a href="{{ route('login') }}" class="btn btn-orange w-100">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">No cars available.</p>
                @endforelse

            </div>
        </div>
    </section>

    <!-- ✅ WHY CHOOSE US -->
    <section class="py-5 text-center">
        <div class="container mb-5">
            <h2 class="section-title" style="color: #000000;">Why Choose Us?</h2>
            <div class="row">

                <div class="col-md-3">
                    <div class="feature-icon"><i class="fas fa-car"></i></div>
                    <h5 style="color: #000000;">Wide Selection</h5>
                    <p class="text-muted" style="color: #000000;">Luxury & Economy Cars</p>
                </div>

                <div class="col-md-3">
                    <div class="feature-icon"><i class="fas fa-tags"></i></div>
                    <h5 style="color: #000000;">Affordable Rates</h5>
                    <p class="text-muted" style="color: #000000;">Best Price Guarantee</p>
                </div>

                <div class="col-md-3">
                    <div class="feature-icon"><i class="fas fa-headset"></i></div>
                    <h5 style="color: #000000;">24/7 Support</h5>
                    <p class="text-muted" style="color: #000000;">Always Here for You</p>
                </div>

                <div class="col-md-3">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h5 style="color: #000000;">Trusted Local Rental</h5>
                    <p class="text-muted" style="color: #000000;">Based in Dumaguete</p>
                </div>

            </div>
        </div>
    </section>
    <!-- EXPLORE PREMIUME CAR BRANDS -->
    <section class="py-5 text-center">
        <div class="container">
            <h2 class="section-title" style="color: #000000;">Drive with Premium Car Brands</h2>
            <div class="row g-4">

                <div class="col-md-3">
                    <div class="brand-container"><img class="brand-logo" src="{{ asset('storage/cars/Toyota.jpg') }}" alt="Toyota">
                    </div>
                    <h5 style="color: #000000;">Toyota</h5>
                </div>

                <div class="col-md-3">
                    <div class="brand-container"><img class="brand-logo" src="{{ asset('storage/cars/Honda.jpg') }}" alt="Honda">
                    </div>
                    <h5 style="color: #000000;">Honda</h5>
                </div>

                <div class="col-md-3">
                    <div class="brand-container"><img class="brand-logo" src="{{ asset('storage/cars/Mazda.png') }}" alt="Mazda">
                    </div>
                    <h5 style="color: #000000;">Mazda</h5>
                </div>

                <div class="col-md-3">
                    <div class="brand-container"><img class="brand-logo" src="{{ asset('storage/cars/Nissan.png') }}" alt="Nissan">
                    </div>
                    <h5 style="color: #000000;">Nissan</h5>
                </div>

            </div>
        </div>
    </section>

    <!-- ✅ CTA SECTION -->
    <section class="cta">
        <div class="container">
            <h2 class="fw-bold mb-3" style="color: #ffffff;">Ready for Your Next Adventure?</h2>
            <p class="mb-4" style="color: #ffffff;">Book Your Ride Today!</p>
            <a href="{{ route('register') }}" class="btn btn-orange btn-lg px-5">
                Get Started
            </a>
        </div>
    </section>

    <footer>
        <div class="container">
            <p class="mb-0">
                © {{ date('Y') }} Dumaguete EZE Car Rental. All Rights Reserved.
            </p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>