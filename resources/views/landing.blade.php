<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">CarRental</a>
            <div class="d-flex">
                <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-warning">Register</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-dark text-white text-center py-5">
        <div class="container">
            <h1 class="display-4 fw-bold">Rent Your Dream Car Today</h1>
            <p class="lead">Flexible rentals, real-time availability, and hassle-free booking.</p>
            <a href="#available-cars" class="btn btn-warning btn-lg mt-3">Browse Cars</a>
        </div>
    </section>

    <!-- Available Cars Section -->
    <section id="available-cars" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Available Cars</h2>
            <div class="row">
                @forelse ($cars as $car)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            
                            <!-- Car Image -->
                            <div class="position-relative" style="height: 200px; overflow: hidden;">
                                @php
                                    $images = is_string($car->images) ? json_decode($car->images, true) : $car->images;
                                @endphp
                                @if($images && is_array($images) && count($images) > 0)
                                    <img src="{{ asset('storage/' . $images[0]) }}" 
                                         class="card-img-top w-100 h-100" 
                                         alt="{{ $car->brand->name ?? 'Car' }} {{ $car->model }}"
                                         style="object-fit: cover;">
                                @else
                                    <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                                        <i class="fas fa-car fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">{{ $car->brand->name ?? 'Unknown Brand' }} {{ $car->model }}</h5>
                                
                                <p class="card-text text-muted mb-2">
                                    <small>
                                        <i class="fas fa-user"></i> {{ $car->seats }} seats • 
                                        <i class="fas fa-cogs"></i> {{ $car->transmission->type ?? '' }} • 
                                        <i class="fas fa-gas-pump"></i> {{ $car->fuelType->type ?? '' }}
                                    </small>
                                </p>
                                <p class="fw-bold text-dark mb-3">₱{{ number_format($car->price_per_day, 2) }} / day</p>
                                <a href="{{ route('login') }}" class="btn btn-warning w-100">
                                    <i class="fas fa-calendar-check"></i> Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center text-muted">No cars available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p class="mb-0">© {{ date('Y') }} CarRental. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>