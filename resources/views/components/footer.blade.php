<footer class="bg-gray-800 text-white mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ config('app.name', 'E-Commerce') }}</h3>
                <p class="text-gray-400 text-sm">
                    Your trusted Nigerian e-commerce platform for physical and digital products.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm">Home</a></li>
                    <li><a href="{{ route('shop.index') }}" class="text-gray-400 hover:text-white text-sm">Shop</a></li>
                    @auth
                        <li><a href="{{ route('orders.index') }}" class="text-gray-400 hover:text-white text-sm">My Orders</a></li>
                    @endauth
                </ul>
            </div>

            <!-- Categories -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Categories</h3>
                <ul class="space-y-2">
                    @php
                        $categories = \App\Models\Category::whereNull('parent_id')->where('is_active', true)->orderBy('display_order')->limit(5)->get();
                    @endphp
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('shop.category', $category->slug) }}" class="text-gray-400 hover:text-white text-sm">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                <ul class="space-y-2 text-gray-400 text-sm">
                    @php
                        $contactEmail = \App\Models\Setting::where('key', 'contact_email')->value('value');
                        $contactPhone = \App\Models\Setting::where('key', 'contact_phone')->value('value');
                    @endphp
                    @if($contactEmail)
                        <li>
                            <a href="mailto:{{ $contactEmail }}" class="hover:text-white">
                                {{ $contactEmail }}
                            </a>
                        </li>
                    @endif
                    @if($contactPhone)
                        <li>
                            <a href="tel:{{ $contactPhone }}" class="hover:text-white">
                                {{ $contactPhone }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'E-Commerce') }}. All rights reserved.</p>
        </div>
    </div>
</footer>
