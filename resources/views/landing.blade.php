<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dumaguete EZE Car Rental</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
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

        .hero-overlay {
            background:
                linear-gradient(to right, rgba(0,0,0,.72), rgba(0,0,0,.45)),
                url("{{ asset('storage/bg/toyota bg.png') }}") center/cover no-repeat;
        }

        .glass {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,0.15);
        }

        .card-hover {
            transition: all .3s ease;
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.12);
        }

        .brand-box {
            transition: all .3s ease;
        }

        .brand-box:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.10);
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

   .popular-showcase-card {
    position: relative;
    height: 100%;
}

.popular-showcase-image-wrap {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}

.popular-showcase-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    display: block;
}

.popular-showcase-price {
    position: absolute;
    top: 16px;
    right: 16px;
    background: #f8f8f8;
    color: #475569;
    border-radius: 8px;
    padding: 10px 18px;
    font-weight: 500;
    font-size: 14px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
}

.popular-showcase-price span {
    color: #ff5a1f;
    font-size: 15px;
    line-height: 1;
    font-weight: 700;
    margin-right: 4px;
}

.popular-showcase-content {
    position: relative;
    width: calc(100% - 30px);
    margin: -42px auto 0;
    background: #ffffff;
    border-radius: 10px;
    padding: 22px 22px 24px;
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.07);
    z-index: 2;
    display: flex;
    flex-direction: column;
    min-height: 250px;
}

.popular-showcase-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 18px;
}

.popular-showcase-title {
    font-size: 19px;
    line-height: 1.25;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    flex: 1;
}

.popular-showcase-rating {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid #e5e7eb;
    border-radius: 9999px;
    padding: 6px 12px;
    font-size: 14px;
    color: #64748b;
    background: #fff;
    white-space: nowrap;
}

.popular-showcase-rating i {
    color: #f4b000;
    font-size: 15px;
}

.popular-showcase-info {
    list-style: none;
    padding: 0;
    margin: 0 0 22px 0;
}

.popular-showcase-info li {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    color: #1e293b;
    margin-bottom: 12px;
}

.popular-showcase-info li i {
    color: #ff5a1f;
    width: 18px;
    text-align: center;
    font-size: 17px;
}

.popular-showcase-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 48px;
    border: 1.5px solid #1f2937;
    border-radius: 10px;
    background: #fff;
    color: #0f172a;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-top: -10px;
}

.popular-showcase-btn:hover {
    background: #ff5a1f;
    border-color: #ff5a1f;
    color: #fff;
}

@media (max-width: 768px) {
    .popular-showcase-image {
        height: 220px;
    }

    .popular-showcase-content {
        width: calc(100% - 20px);
        margin-top: -34px;
        padding: 18px 18px 20px;
        min-height: auto;
    }

    .popular-showcase-top {
        flex-direction: row;
        align-items: center;
    }

    .popular-showcase-title {
        font-size: 18px;
    }

    .popular-showcase-price {
        padding: 8px 14px;
        font-size: 13px;
    }

    .popular-showcase-price span {
        font-size: 20px;
    }
}

    /* How it works cards */
.work-step-card-light {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    padding: 22px 18px;
    text-align: center;
    transition: all 0.3s ease;
    height: 100%;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
}

.work-step-card-light:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 36px rgba(15, 23, 42, 0.10);
    border-color: #fed7aa;
}

.work-step-icon-light {
    width: 56px;
    height: 56px;
    margin: 0 auto 14px;
    border-radius: 9999px;
    background: #fff7ed;
    color: #ff5a1f;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    border: 1px solid #fed7aa;
}

.work-step-title-light {
    color: #0f172a;
    font-size: 17px;
    font-weight: 700;
    margin-bottom: 8px;
}

.work-step-text-light {
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
}

}

    </style>
</head>
<body class="text-slate-800">

    <!-- NAVBAR -->
    <header class="absolute top-0 left-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <a href="#" class="flex items-center">
                    <img src="{{ asset('storage/logo/header pic.png') }}" alt="Logo" class="h-16 md:h-20 w-auto object-contain">
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}"
                       class="rounded-full border border-white/40 px-5 py-2 text-sm md:text-base text-white hover:bg-white hover:text-slate-900 transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="rounded-full bg-brand-orange px-5 py-2 text-sm md:text-base text-white hover:opacity-90 transition">
                        Register
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero-overlay min-h-[92vh] flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid lg:grid-cols-2 gap-10 items-center pt-28 pb-16">
                <div class="text-white">
                    <p class="section-subtitle mb-4 text-orange-400"> Eze Car Rental</p>
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-5">
                        Drive Dumaguete with
                        <span class="text-orange-400">Comfort & Confidence</span>
                    </h1>
                    <p class="text-white/85 text-lg md:text-xl max-w-xl mb-8">
                        Premium and affordable car rentals for your next city ride, out-of-town trip, or island adventure.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('register') }}"
                           class="rounded-full bg-brand-orange px-7 py-3 text-white font-semibold hover:opacity-90 transition">
                            Book Now
                        </a>
                        <a href="#available-cars"
                           class="rounded-full border border-white/40 px-7 py-3 text-white font-semibold hover:bg-white hover:text-slate-900 transition">
                            Explore Cars
                        </a>
                    </div>
                </div>

                <!-- Search / Booking Style Box -->
                <div class="glass rounded-3xl p-6 md:p-8 shadow-2xl">
                    <h3 class="text-white text-2xl font-bold mb-6">Quick Booking Preview</h3>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-white/80 text-sm mb-2">Pickup Location</label>
                            <input type="text" placeholder="Dumaguete City"
                                   class="w-full rounded-xl bg-white px-4 py-3 text-slate-800 outline-none">
                        </div>

                        <div>
                            <label class="block text-white/80 text-sm mb-2">Car Type</label>
                            <select class="w-full rounded-xl bg-white px-4 py-3 text-slate-800 outline-none">
                                <option>Choose Type</option>
                                <option>Sedan</option>
                                <option>SUV</option>
                                <option>Van</option>
                                <option>Pickup</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-white/80 text-sm mb-2">Start Date</label>
                            <input type="date" class="w-full rounded-xl bg-white px-4 py-3 text-slate-800 outline-none">
                        </div>

                        <div>
                            <label class="block text-white/80 text-sm mb-2">End Date</label>
                            <input type="date" class="w-full rounded-xl bg-white px-4 py-3 text-slate-800 outline-none">
                        </div>
                    </div>

                    <a href="{{ route('login') }}"
                       class="mt-6 block w-full rounded-xl bg-brand-orange px-5 py-3 text-center text-white font-semibold hover:opacity-90 transition">
                        Search Available Cars
                    </a>
                </div>
            </div>
        </div>
    </section>


 <section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <p class="section-subtitle mb-3">How It Works</p>
            <h2 class="section-title">How it works</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
            <div class="work-step-card-light">
                <div class="work-step-icon-light">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="work-step-title-light">Sign up Account</h3>
                <p class="work-step-text-light">Create your account to start booking quickly and easily.</p>
            </div>

            <div class="work-step-card-light">
                <div class="work-step-icon-light">
                    <i class="fas fa-magnifying-glass"></i>
                </div>
                <h3 class="work-step-title-light">Search your Vehicle</h3>
                <p class="work-step-text-light">Browse the available cars and choose the best one for your trip.</p>
            </div>

            <div class="work-step-card-light">
                <div class="work-step-icon-light">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3 class="work-step-title-light">Pay the Car Rent</h3>
                <p class="work-step-text-light">Confirm your reservation and complete the payment securely.</p>
            </div>

            <div class="work-step-card-light">
                <div class="work-step-icon-light">
                    <i class="fas fa-car-side"></i>
                </div>
                <h3 class="work-step-title-light">Take Car to Road</h3>
                <p class="work-step-text-light">Pick up the car and enjoy a smooth ride on your journey.</p>
            </div>
        </div>
    </div>
</section>

<!-- AVAILABLE CARS -->
<section class="pt-20 pb-16 bg-slate-50" id="available-cars">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <p class="section-subtitle mb-3">Popular Cars</p>
            <h2 class="section-title">Most Popular Cars</h2>
        </div>

        <div class="grid sm:grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            @forelse ($cars as $car)
                @php
                    $images = is_string($car->images) ? json_decode($car->images, true) : $car->images;
                @endphp

                <div class="popular-showcase-card">
                    <div class="popular-showcase-image-wrap">
                        @if($images && count($images) > 0)
                            <img src="{{ asset('storage/' . $images[0]) }}"
                                 alt="{{ $car->model }}"
                                 class="popular-showcase-image">
                        @else
                            <div class="popular-showcase-image flex items-center justify-center bg-slate-200 text-slate-500">
                                No Image
                            </div>
                        @endif

                        <div class="popular-showcase-price">
                            <span>₱{{ number_format($car->price_per_day, 0) }}</span> / Day
                        </div>
                    </div>

                    <div class="popular-showcase-content">
                        <div class="popular-showcase-top">
                            <h3 class="popular-showcase-title">
                                {{ $car->brand->name ?? '' }} {{ $car->model }}
                            </h3>

                            
                        </div>

                        <ul class="popular-showcase-info">
                            <li>
                                <i class="fas fa-car-side"></i>
                                <span>Doors: {{ $car->doors ?? '2' }}</span>
                            </li>
                            <li>
                                <i class="fas fa-cogs"></i>
                                <span>Transmission: {{ $car->transmission->type ?? 'Automatic' }}</span>
                            </li>
                            <li>
                                <i class="fas fa-user"></i>
                                <span>Passengers: {{ $car->seats ?? '0' }}</span>
                            </li>
                        </ul>

                        <a href="{{ route('login') }}" class="popular-showcase-btn">
                            Rent Now
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-slate-500">
                    No cars available.
                </div>
            @endforelse
        </div>
    </div>
</section>

    <!-- WHY CHOOSE US -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="section-subtitle mb-3">Why Choose Us</p>
                <h2 class="section-title">A Better Car Rental Experience</h2>
            </div>

            <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-8">
                <div class="rounded-3xl border border-slate-200 p-8 text-center bg-slate-50">
                    <div class="text-4xl brand-orange mb-4"><i class="fas fa-car"></i></div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Wide Selection</h4>
                    <p class="text-slate-600">Luxury, family, and economy vehicles for every travel style.</p>
                </div>

                <div class="rounded-3xl border border-slate-200 p-8 text-center bg-slate-50">
                    <div class="text-4xl brand-orange mb-4"><i class="fas fa-tags"></i></div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Affordable Rates</h4>
                    <p class="text-slate-600">Competitive daily pricing with value you can trust.</p>
                </div>

                <div class="rounded-3xl border border-slate-200 p-8 text-center bg-slate-50">
                    <div class="text-4xl brand-orange mb-4"><i class="fas fa-headset"></i></div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">24/7 Support</h4>
                    <p class="text-slate-600">We’re here whenever you need help before or during your trip.</p>
                </div>

                <div class="rounded-3xl border border-slate-200 p-8 text-center bg-slate-50">
                    <div class="text-4xl brand-orange mb-4"><i class="fas fa-shield-alt"></i></div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Trusted Local Rental</h4>
                    <p class="text-slate-600">A dependable Dumaguete-based rental service you can rely on.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- BRANDS -->
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <p class="section-subtitle mb-3">Brands</p>
                <h2 class="section-title">Drive with Premium Car Brands</h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="brand-box bg-white rounded-3xl p-6 text-center shadow-sm">
                    <div class="h-24 flex items-center justify-center mb-4">
                        <img class="max-h-20 object-contain" src="{{ asset('storage/logo/toyota1.png') }}" alt="Toyota">
                    </div>
                    <h5 class="font-semibold text-slate-900">Toyota</h5>
                </div>

                <div class="brand-box bg-white rounded-3xl p-6 text-center shadow-sm">
                    <div class="h-24 flex items-center justify-center mb-4">
                        <img class="max-h-20 object-contain" src="{{ asset('storage/logo/fordlogo.png') }}" alt="Ford">
                    </div>
                    <h5 class="font-semibold text-slate-900">Ford</h5>
                </div>

                <div class="brand-box bg-white rounded-3xl p-6 text-center shadow-sm">
                    <div class="h-24 flex items-center justify-center mb-4">
                        <img class="max-h-20 object-contain" src="{{ asset('storage/logo/mishubishi.png') }}" alt="Mitsubishi">
                    </div>
                    <h5 class="font-semibold text-slate-900">Mitsubishi</h5>
                </div>

                <div class="brand-box bg-white rounded-3xl p-6 text-center shadow-sm">
                    <div class="h-24 flex items-center justify-center mb-4">
                        <img class="max-h-20 object-contain" src="{{ asset('storage/logo/nissanlogo.png') }}" alt="Nissan">
                    </div>
                    <h5 class="font-semibold text-slate-900">Nissan</h5>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="relative py-24">
        <div class="absolute inset-0 bg-cover bg-center"
             style="background-image: linear-gradient(rgba(0,0,0,.78), rgba(0,0,0,.78)), url('{{ asset('storage/bg/toyota bg.png') }}');">
        </div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
            <p class="section-subtitle text-orange-400 mb-3">Get Started</p>
            <h2 class="text-3xl md:text-5xl font-extrabold mb-4">Ready for Your Next Adventure?</h2>
            <p class="text-white/80 text-lg mb-8">Reserve your car today and enjoy a smooth ride around Dumaguete and beyond.</p>

            <a href="{{ route('register') }}"
               class="inline-block rounded-full bg-brand-orange px-8 py-4 text-white font-semibold hover:opacity-90 transition">
                Create an Account
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid md:grid-cols-3 gap-10">
                <div>
                    <img src="{{ asset('storage/logo/header pic.png') }}" alt="Logo" class="h-16 w-auto mb-4">
                    <p class="text-white/70">
                        Dumaguete EZE Car Rental provides clean, reliable, and affordable vehicles for locals and travelers.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-white/70">
                        <li><a href="#" class="hover:text-orange-400">Home</a></li>
                        <li><a href="#available-cars" class="hover:text-orange-400">Available Cars</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-orange-400">Login</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-orange-400">Register</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4">Contact</h4>
                    <ul class="space-y-2 text-white/70">
                        <li><i class="fas fa-location-dot mr-2 brand-orange"></i>Mangnao Dumaguete City, Philippines</li>
                        <li><i class="fas fa-phone mr-2 brand-orange"></i>0981-225-5442</li>
                        <li><i class="fas fa-envelope mr-2 brand-orange"></i>EZE CAR RENTAL@gmail.com</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 mt-10 pt-6 text-center text-white/60 text-sm">
                © {{ date('Y') }} Dumaguete EZE Car Rental. All Rights Reserved.
            </div>
        </div>
    </footer>
</body>
</html>