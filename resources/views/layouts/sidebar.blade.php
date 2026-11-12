<!-- Navigasi Bar Mobile (Layar Handphone/Tablet) -->
<div class="md:hidden bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between z-30">
    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
        <span class="font-bold text-gray-900 text-lg">Armada<span class="text-gray-500">Nikel</span></span>
    </a>
    <button @click="sidebarOpen = !sidebarOpen"
        class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<!-- Latar Belakang Redup Saat Menu Mobile Dibuka -->
<div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
    class="fixed inset-0 z-40 bg-gray-600/50 backdrop-blur-xs md:hidden" style="display: none;"></div>

<!-- Sidebar Navigasi Utama Desktop -->
<aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transition-transform duration-200 ease-in-out md:translate-x-0 md:static md:inset-auto md:min-h-screen shrink-0">
    
    <!-- Logo Identitas Aplikasi -->
    <div class="h-16 px-6 border-b border-gray-200 flex items-center space-x-3">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            <span class="font-bold text-gray-800 text-lg tracking-tight">Armada<span class="text-gray-500">Nikel</span></span>
        </a>
    </div>

    <!-- Menu Utama Aplikasi (3 Fitur Inti) -->
    <nav class="p-4 space-y-2">
        <!-- 1. Menu Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-4 py-2.5 rounded-md text-sm font-semibold transition ease-in-out duration-150 {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white shadow-sm hover:bg-gray-700 active:bg-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- 2. Menu Pemesanan & Persetujuan Berjenjang -->
        <a href="{{ route('bookings.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-md text-sm font-semibold transition ease-in-out duration-150 {{ request()->routeIs('bookings.index') ? 'bg-gray-800 text-white shadow-sm hover:bg-gray-700 active:bg-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span>Pemesanan</span>
        </a>

        <!-- 3. Menu Monitoring Kondisi Armada -->
        <a href="{{ route('vehicles.index') }}" 
           class="flex items-center px-4 py-2.5 rounded-md text-sm font-semibold transition ease-in-out duration-150 {{ request()->routeIs('vehicles.index') ? 'bg-gray-800 text-white shadow-sm hover:bg-gray-700 active:bg-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
            </svg>
            <span>Monitoring Armada</span>
        </a>
    </nav>
</aside>
