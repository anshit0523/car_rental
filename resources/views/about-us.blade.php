<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>About Us | Dumaguete EZE Car Rental</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #f8fafc;
        }

        .brand-orange {
            color: #ff5a1f;
        }

        .bg-brand-orange {
            background-color: #ff5a1f;
        }

        .border-brand-orange {
            border-color: #ff5a1f;
        }

        .section-subtitle {
            color: #ff5a1f;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .section-title {
            color: #0f172a;
            font-size: 2rem;
            line-height: 1.2;
            font-weight: 800;
        }

        .breadcrumb-hero {
            background:
                linear-gradient(to right, rgba(2, 6, 23, 0.84), rgba(2, 6, 23, 0.58)),
                url("{{ asset('storage/cars/toyota-bg.png') }}") center/cover no-repeat;
        }

        .info-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            padding: 28px 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            transition: all 0.3s ease;
            height: 100%;
        }

        .info-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.10);
            border-color: #fed7aa;
        }

        .info-card-icon {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: #fff7ed;
            color: #ff5a1f;
            border: 1px solid #fed7aa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
        }

        .feature-list li {
            position: relative;
            padding-left: 28px;
            margin-bottom: 12px;
            color: #475569;
            line-height: 1.8;
        }

        .feature-list li::before {
            content: "\f058";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            left: 0;
            top: 2px;
            color: #ff5a1f;
        }

        .stats-box {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        }

        .cta-box {
            position: relative;
            overflow: hidden;
            border-radius: 32px;
            background:
                linear-gradient(to right, rgba(2, 6, 23, 0.92), rgba(15, 23, 42, 0.84)),
                url("{{ asset('storage/cars/dgte.jpg') }}") center/cover no-repeat;
            padding: 56px 28px;
            box-shadow: 0 22px 50px rgba(15, 23, 42, 0.20);
        }

        .cta-box::before {
            content: "";
            position: absolute;
            top: -60px;
            right: -60px;
            width: 180px;
            height: 180px;
            border-radius: 9999px;
            background: rgba(255, 90, 31, 0.18);
            filter: blur(16px);
        }

        #navbarContainer {
            transition: all 0.3s ease;
        }

        #navbarShell {
            transition: all 0.3s ease;
        }

        #siteHeader.scrolled #navbarContainer {
            max-width: 100%;
            width: 100%;
            padding-left: 0;
            padding-right: 0;
        }

        #siteHeader.scrolled #navbarShell {
            margin-top: 0;
            border-radius: 0;
            background: rgba(15, 23, 42, 0.92);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.28);
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -7px;
            width: 0;
            height: 2px;
            background: #ff5a1f;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        .nav-link.active {
            color: #ffffff;
        }
    </style>
</head>

<body class="text-slate-800">

        <!-- Header -->
<x-header />

    <!-- BREADCRUMB -->
    <section class="breadcrumb-hero min-h-[46vh] flex items-center pt-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="py-20 text-center text-white">
                <p class="section-subtitle text-orange-400 mb-4">About Our Business</p>
                <h1 class="text-4xl md:text-6xl font-extrabold">About Us</h1>
                <p class="text-white/80 text-base md:text-lg mt-4 max-w-3xl mx-auto leading-relaxed">
                    Learn more about Dumaguete EZE Car Rental, our commitment to reliable service, and why customers
                    trust us for smooth and convenient travel.
                </p>
            </div>
        </div>
    </section>

    <!-- ABOUT INTRO -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-14 items-center">
                <div>
                    <p class="section-subtitle mb-3">Who We Are</p>
                    <h2 class="section-title mb-5">Trusted Local Car Rental Service in Dumaguete</h2>

                    <p class="text-slate-600 leading-8 mb-5">
                        Dumaguete EZE Car Rental is dedicated to providing clean, reliable, and affordable vehicles
                        for customers who need convenient transportation around Dumaguete City and nearby areas.
                    </p>

                    <p class="text-slate-600 leading-8 mb-5">
                        We focus on making the rental experience simple and dependable by offering responsive
                        communication, well-maintained vehicles, and professional customer assistance from inquiry
                        to return.
                    </p>

                    <p class="text-slate-600 leading-8">
                        Whether you need a car for personal travel, family trips, airport pickup, business use,
                        or out-of-town rides, our goal is to help you travel with comfort and confidence.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="info-card">
                        <div class="info-card-icon">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Local Service</h3>
                        <p class="text-slate-600 text-sm leading-7">
                            Based in Dumaguete City and ready to assist local customers and travelers.
                        </p>
                    </div>

                    <div class="info-card">
                        <div class="info-card-icon">
                            <i class="fas fa-car-side"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Reliable Vehicles</h3>
                        <p class="text-slate-600 text-sm leading-7">
                            Vehicles prepared for daily transport, family rides, and flexible travel needs.
                        </p>
                    </div>

                    <div class="info-card">
                        <div class="info-card-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Easy Booking</h3>
                        <p class="text-slate-600 text-sm leading-7">
                            Convenient booking process with faster coordination and customer support.
                        </p>
                    </div>

                    <div class="info-card">
                        <div class="info-card-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Responsive Support</h3>
                        <p class="text-slate-600 text-sm leading-7">
                            Assistance for booking concerns, trip coordination, and rental inquiries.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MISSION / VISION -->
    <section class="pb-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-8">
                <div class="stats-box">
                    <p class="section-subtitle mb-3">Our Mission</p>
                    <h2 class="section-title mb-4">What We Aim to Provide</h2>
                    <p class="text-slate-600 leading-8">
                        Our mission is to provide customers with a convenient, trustworthy, and affordable car rental
                        service that makes travel easier, safer, and more comfortable in Dumaguete and nearby areas.
                    </p>
                </div>

                <div class="stats-box">
                    <p class="section-subtitle mb-3">Our Vision</p>
                    <h2 class="section-title mb-4">What We Want to Become</h2>
                    <p class="text-slate-600 leading-8">
                        Our vision is to become one of the most dependable local car rental providers in Dumaguete by
                        consistently offering quality service, reliable vehicles, and customer-focused support.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE US -->
    <section class="pb-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <p class="section-subtitle mb-3">Why Choose Us</p>
                <h2 class="section-title">A Better Rental Experience</h2>
                <p class="text-slate-500 max-w-2xl mx-auto mt-3">
                    We focus on service quality, convenience, and dependable support to help customers travel with ease.
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-14 items-start">
                <div>
                    <ul class="feature-list">
                        <li>Clean and reliable vehicles suitable for different travel needs.</li>
                        <li>Convenient local rental service based in Dumaguete City.</li>
                        <li>Responsive support for questions, bookings, and rental coordination.</li>
                        <li>Affordable and practical options for personal, family, and business use.</li>
                        <li>Smooth booking process designed for convenience and customer confidence.</li>
                    </ul>
                </div>

                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="info-card">
                        <div class="info-card-icon">
                            <i class="fas fa-shield-heart"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Dependable Service</h3>
                        <p class="text-slate-600 text-sm leading-7">
                            We value customer trust by maintaining professional communication and consistent support.
                        </p>
                    </div>

                    <div class="info-card">
                        <div class="info-card-icon">
                            <i class="fas fa-route"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">Travel Convenience</h3>
                        <p class="text-slate-600 text-sm leading-7">
                            Better mobility for city rides, local errands, and out-of-town trips.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

   

    <x-footer />


</body>

</html>