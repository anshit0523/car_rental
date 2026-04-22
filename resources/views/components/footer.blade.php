<footer class="bg-slate-950 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid md:grid-cols-3 gap-10">
            <div>
                <img src="{{ asset('storage/cars/ezelogo.png') }}" alt="Dumaguete EZE Car Rental Logo" class="h-16 w-auto mb-4">

                <p class="text-white/70 mb-5 leading-7">
                    Dumaguete EZE Car Rental provides clean, reliable, and affordable vehicles for locals and travelers.
                </p>

                <h6 class="font-semibold text-white mb-3">Follow Us On</h6>
                <div class="flex items-center gap-3">
                    <a href="https://web.facebook.com/profile.php?id=100095109516642"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="Facebook"
                       class="inline-flex items-center justify-center w-11 h-11 rounded-full border border-white/10 bg-white/5 text-white hover:bg-orange-500 hover:border-orange-500 transition">
                        <i class="fab fa-square-facebook text-xl"></i>
                    </a>

                    <a href="https://wa.me/639812255442"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="WhatsApp"
                       class="inline-flex items-center justify-center w-11 h-11 rounded-full border border-white/10 bg-white/5 text-white hover:bg-orange-500 hover:border-orange-500 transition">
                        <i class="fab fa-whatsapp text-xl"></i>
                    </a>

                    <a href="https://www.instagram.com/zeyn_capitan/?igsh=MWdudDJvMXVqbTBvcA%3D%3D&fbclid=IwY2xjawRVwuhleHRuA2FlbQIxMABicmlkETFiZVozYXBJZ25XNmVrVFhZc3J0YwZhcHBfaWQQMjIyMDM5MTc4ODIwMDg5MgABHl4v3Tt6afFPom7k8v2Z-h3Ib6nZXhHuDCU3v7NI5sxQQkD80hu-TZ7PLtZD_aem_M37qmig-wTdBgBPh1Pksxg"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="Instagram"
                       class="inline-flex items-center justify-center w-11 h-11 rounded-full border border-white/10 bg-white/5 text-white hover:bg-orange-500 hover:border-orange-500 transition">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-lg mb-4">Quick Links</h4>
                <ul class="space-y-2 text-white/70">
                    <li>
                        <a href="{{ route('landing') }}" class="hover:text-orange-400 transition">Home</a>
                    </li>
                    <li>
                        <a href="{{ route('user.browse') }}" class="hover:text-orange-400 transition">Browse Cars</a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="hover:text-orange-400 transition">Contact</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.login') }}" class="hover:text-orange-400 transition">Admin / Staff Login</a>
                    </li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-lg mb-4">Contact</h4>
                <ul class="space-y-3 text-white/70">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-location-dot text-orange-400 mt-1"></i>
                        <span>Noreco Rd, Mangnao, Dumaguete City</span>
                    </li>

                    <li class="flex items-start gap-3">
                        <i class="fas fa-phone text-orange-400 mt-1"></i>
                        <span>
                            <span class="block text-white/90 font-medium">SMART / TNT</span>
                            <a href="tel:09812255442" class="hover:text-orange-400 transition">09812255442</a>
                        </span>
                    </li>

                    <li class="flex items-start gap-3">
                        <i class="fas fa-phone text-orange-400 mt-1"></i>
                        <span>
                            <span class="block text-white/90 font-medium">TM / GLOBE</span>
                            <a href="tel:09552603041" class="hover:text-orange-400 transition">09552603041</a>
                        </span>
                    </li>

                    <li class="flex items-start gap-3">
                        <i class="fas fa-envelope text-orange-400 mt-1"></i>
                        <a href="mailto:EZECARRENTAL@gmail.com" class="hover:text-orange-400 transition">
                            EZECARRENTAL@gmail.com
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-white/10 mt-10 pt-6 text-center text-white/60 text-sm">
            © {{ date('Y') }} Dumaguete EZE Car Rental. All Rights Reserved.
        </div>
    </div>
</footer>