<nav x-data="{ open: false }" class="bg-[#0A192F] border-b border-white/10 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('catalog.index') }}">
                        <span class="font-serif text-xl font-bold text-gold">Literra</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.*')">
                        {{ __('Katalog') }}
                    </x-nav-link>

                    <x-nav-link :href="route('promo.index')" :active="request()->routeIs('promo.*')">
                        {{ __('Promo') }}
                    </x-nav-link>

                    @if (!auth()->check() || Auth::user()->role === 'customer')
                        <x-nav-link :href="route('wishlist.index')" :active="request()->routeIs('wishlist.*')">
                            {{ __('Wishlist Saya') }}
                        </x-nav-link>

                        <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                            {{ __('Keranjang') }}
                            @if (auth()->check() && Auth::user()->role === 'customer')
                            <span id="cart-badge" class="ml-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full">{{ Auth::user()->carts()->sum('quantity') }}</span>
                            @endif
                        </x-nav-link>

                        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                            {{ __('Pesanan Saya') }}
                        </x-nav-link>
                    @endif

                    @auth
                @if (Auth::user()->role === 'kasir')
                    <x-nav-link :href="route('kasir.pos')" :active="request()->routeIs('kasir.pos')">
                        {{ __('Kasir POS') }}
                    </x-nav-link>
                    <x-nav-link :href="route('kasir.orders.index')" :active="request()->routeIs('kasir.orders.*')">
                        {{ __('Cari Pesanan') }}
                    </x-nav-link>
                @endif

                @if (Auth::user()->role === 'admin')
                    <x-nav-link :href="route('admin.books.index')" :active="request()->routeIs('admin.books.*')">
                                {{ __('Kelola Buku') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                                {{ __('Kelola Kategori') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.coupons.index')" :active="request()->routeIs('admin.coupons.*')">
                                {{ __('Kelola Kupon') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                                {{ __('Semua Pesanan') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                                {{ __('Laporan') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.featured.index')" :active="request()->routeIs('admin.featured.*')">
                                {{ __('Buku Unggulan') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-white/70 hover:text-gold">Log in</a>
                    <a href="{{ route('register') }}" class="ms-4 inline-flex items-center px-4 py-2 bg-gold border border-transparent rounded-md font-semibold text-xs text-navy uppercase tracking-widest hover:opacity-90">
                        Register
                    </a>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.*')">
                    {{ __('Katalog') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('promo.index')" :active="request()->routeIs('promo.*')">
                    {{ __('Promo') }}
                </x-responsive-nav-link>

                @if (!auth()->check() || Auth::user()->role === 'customer')
                <x-responsive-nav-link :href="route('wishlist.index')" :active="request()->routeIs('wishlist.*')">
                    {{ __('Wishlist Saya') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                    {{ __('Keranjang') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                    {{ __('Pesanan Saya') }}
                </x-responsive-nav-link>
            @endif

            @auth
                @if (Auth::user()->role === 'kasir')
                    <x-responsive-nav-link :href="route('kasir.pos')" :active="request()->routeIs('kasir.pos')">
                        {{ __('Kasir POS') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('kasir.orders.index')" :active="request()->routeIs('kasir.orders.*')">
                        {{ __('Cari Pesanan') }}
                    </x-responsive-nav-link>
                @endif

                @if (Auth::user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.books.index')" :active="request()->routeIs('admin.books.*')">
                        {{ __('Kelola Buku') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                        {{ __('Kelola Kategori') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.coupons.index')" :active="request()->routeIs('admin.coupons.*')">
                        {{ __('Kelola Kupon') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                        {{ __('Semua Pesanan') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                        {{ __('Laporan') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.featured.index')" :active="request()->routeIs('admin.featured.*')">
                        {{ __('Buku Unggulan') }}
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Log in') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>
