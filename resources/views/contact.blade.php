<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Us | Dumaguete EZE Car Rental</title>
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
                url("{{ asset('storage/cars/bgcontact.png') }}") center/cover no-repeat;
        }

        .contact-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            padding: 36px 24px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .contact-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.10);
            border-color: #fed7aa;
        }

        .contact-card-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 18px;
            border-radius: 9999px;
            background: #fff7ed;
            color: #ff5a1f;
            border: 1px solid #fed7aa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .contact-card-title {
            color: #0f172a;
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .contact-card-text {
            color: #64748b;
            font-size: 15px;
            line-height: 1.8;
        }

        .contact-card-text a:hover {
            color: #ff5a1f;
        }

        .map-wrap {
            overflow: hidden;
            border-radius: 28px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            background: #fff;
        }

        .info-panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
            height: 100%;
        }

        .mini-contact-card {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 16px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .mini-contact-card:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .mini-contact-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: #fff7ed;
            color: #ff5a1f;
            border: 1px solid #fed7aa;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .quick-channel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
            transition: all 0.25s ease;
            height: 100%;
        }

        .quick-channel:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.10);
            border-color: #fed7aa;
        }

        .quick-channel-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: #fff7ed;
            color: #ff5a1f;
            border: 1px solid #fed7aa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
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
                <p class="section-subtitle text-orange-400 mb-4">Contact Details</p>
                <h1 class="text-4xl md:text-6xl font-extrabold">Contact Us</h1>
                <p class="text-white/80 text-base md:text-lg mt-4 max-w-3xl mx-auto leading-relaxed">
                    Reach out to Dumaguete EZE Car Rental for bookings, inquiries, directions, and fast assistance.
                    We are here to help make your rental experience smooth and convenient.
                </p>
            </div>
        </div>
    </section>

    <!-- CONTACT INFO -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <p class="section-subtitle mb-3">Contact Details</p>
                <h2 class="section-title">Contact Information</h2>
                <p class="text-slate-500 max-w-2xl mx-auto mt-3">
                    Choose the most convenient way to reach us for bookings, questions, and rental assistance.
                </p>
            </div>

            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-8">
                <div class="contact-card">
                    <div class="contact-card-icon">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <h4 class="contact-card-title">Our Location</h4>
                    <div class="contact-card-text">
                        <p class="font-medium text-slate-700">Noreco Rd, Mangnao, Dumaguete City, Philippines</p>
                        <p class="mt-2 text-sm text-slate-500">Visit us for booking inquiries, rental coordination, and exact pickup guidance.</p>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-card-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4 class="contact-card-title">Email Address</h4>
                    <div class="contact-card-text">
                        <a href="mailto:EZECARRENTAL@gmail.com" class="block font-medium text-slate-700 transition">EZECARRENTAL@gmail.com</a>
                        <a href="mailto:support@ezecarrental.com" class="block font-medium text-slate-700 transition">support@ezecarrental.com</a>
                        <p class="mt-2 text-sm text-slate-500">Send us your booking questions, requests, and support concerns anytime.</p>
                    </div>
                </div>

                <div class="contact-card">
                    <div class="contact-card-icon">
                        <i class="fas fa-phone-volume"></i>
                    </div>
                    <h4 class="contact-card-title">Phone Number</h4>
                    <div class="contact-card-text">
                        <a href="tel:09812255442" class="block font-medium text-slate-700 transition hover:text-orange-500">0981-225-5442</a>
                        <p class="mt-2 text-sm text-slate-500">Available for quick booking assistance, directions, and customer support.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAP + CONTACT PANEL -->
    <section class="pb-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <p class="section-subtitle mb-3">Find Us</p>
                <h2 class="section-title">Visit Our Location</h2>
                <p class="text-slate-500 max-w-2xl mx-auto mt-3">
                    Use the map below to locate Dumaguete EZE Car Rental and open the exact location in Google Maps.
                </p>
            </div>

            <div class="grid lg:grid-cols-[1.4fr_0.8fr] gap-8 items-stretch">
                <div class="map-wrap">
                    <iframe
                        src="https://www.google.com/maps?q=9.2885478,123.3022624&z=18&output=embed"
                        width="100%"
                        height="100%"
                        class="min-h-[420px]"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy">
                    </iframe>
                </div>

                <div class="info-panel">
                    <h3 class="text-2xl font-extrabold text-slate-900 mb-2">Need Quick Assistance?</h3>
                    <p class="text-slate-500 leading-relaxed mb-5">
                        Contact us directly for the fastest response regarding vehicle availability, rates, and booking support.
                    </p>

                    <div class="mini-contact-card">
                        <div class="mini-contact-icon">
                            <i class="fab fa-square-facebook"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Facebook Page</h4>
                            <p class="text-slate-500 text-sm mt-1">Dumaguete Eze Car Rental</p>
                        </div>
                    </div>

                    <div class="mini-contact-card">
                        <div class="mini-contact-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">WhatsApp / Viber</h4>
                            <p class="text-slate-500 text-sm mt-1">09812255442</p>
                        </div>
                    </div>

                    <div class="mini-contact-card">
                        <div class="mini-contact-icon">
                            <i class="fas fa-map-location-dot"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Open Exact Location</h4>
                            <a href="https://www.google.com/maps/@9.2885478,123.3022624,3a,75y,45.75h,50.95t/data=!3m7!1e1!3m5!1spSgGGyDcnBCxk8CIZ_bpig!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D39.048022740347264%26panoid%3DpSgGGyDcnBCxk8CIZ_bpig%26yaw%3D45.749946175791706!7i16384!8i8192?entry=ttu&g_ep=EgoyMDI2MDQxOS4wIKXMDSoASAFQAw%3D%3D"
                               target="_blank"
                               class="inline-flex items-center gap-2 mt-2 text-sm font-semibold text-orange-500 hover:text-orange-600 transition">
                                View in Google Maps
                                <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                            </a>
                        </div>
                    </div>

                    <a href="https://www.google.com/maps/@9.2885478,123.3022624,3a,75y,45.75h,50.95t/data=!3m7!1e1!3m5!1spSgGGyDcnBCxk8CIZ_bpig!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D39.048022740347264%26panoid%3DpSgGGyDcnBCxk8CIZ_bpig%26yaw%3D45.749946175791706!7i16384!8i8192?entry=ttu&g_ep=EgoyMDI2MDQxOS4wIKXMDSoASAFQAw%3D%3D"
                       target="_blank"
                       class="inline-flex items-center justify-center gap-2 mt-6 rounded-full bg-brand-orange px-6 py-3 text-white font-semibold hover:opacity-90 transition">
                        <i class="fas fa-map-location-dot"></i>
                        Open Exact Location
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- QUICK CONTACT CHANNELS -->
    <section class="pb-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <p class="section-subtitle mb-3">Reach Us Faster</p>
                <h2 class="section-title">Quick Contact Channels</h2>
                <p class="text-slate-500 max-w-2xl mx-auto mt-3">
                    Connect with us through your preferred platform for easier communication and faster updates.
                </p>
            </div>

            <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-6">
                <div class="quick-channel">
                    <div class="quick-channel-icon">
                        <i class="fab fa-square-facebook"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Facebook Page</h4>
                    <p class="text-slate-500 text-sm leading-7">Dumaguete Eze Car Rental</p>
                </div>

                <div class="quick-channel">
                    <div class="quick-channel-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Facebook Account</h4>
                    <p class="text-slate-500 text-sm leading-7">LM Amare</p>
                </div>

                <div class="quick-channel">
                    <div class="quick-channel-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">WhatsApp / Viber</h4>
                    <p class="text-slate-500 text-sm leading-7">09812255442</p>
                    <p class="text-slate-400 text-xs mt-2">Dumaguete Eze Car Rental</p>
                </div>

                <div class="quick-channel">
                    <div class="quick-channel-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Instagram</h4>
                    <p class="text-slate-500 text-sm leading-7">Millenjay Litong</p>
                    <p class="text-slate-500 text-sm leading-7">zeyn_capitan</p>
                </div>
            </div>
        </div>
    </section>

    <x-footer />

  

</body>

</html>