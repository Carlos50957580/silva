<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIVA - {{ $title ?? 'Sistema de Inventario' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar {
            transition: all 0.3s;
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: #1a202c;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: #4a5568;
            border-radius: 2px;
        }
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
        }
        .sidebar-link {
            transition: all 0.2s;
            border-radius: 8px;
            position: relative;
        }
        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: inset 3px 0 0 #60A5FA;
        }
        .sidebar-link i {
            width: 24px;
            text-align: center;
        }
        .badge-notification {
            position: absolute;
            top: 8px;
            right: 12px;
            background: #EF4444;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 10px;
            font-weight: bold;
        }
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .submenu.open {
            max-height: 500px;
            transition: max-height 0.3s ease-in;
        }
        .submenu-link {
            padding-left: 3rem !important;
            font-size: 0.875rem;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .hamburger {
                display: block !important;
            }
        }
        .hamburger {
            display: none;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .overlay.active {
            display: block;
        }
        .has-submenu .submenu-toggle {
            cursor: pointer;
        }
        .has-submenu .submenu-toggle .fa-chevron-down {
            transition: transform 0.3s;
        }
        .has-submenu .submenu-toggle.open .fa-chevron-down {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <!-- Overlay para móvil -->
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar bg-gray-900 text-white shadow-xl">
        <!-- Logo -->
        <div class="p-6 border-b border-gray-800">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-600 p-2 rounded-lg">
                    <i class="fas fa-boxes text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">SIVA</h1>
                    <p class="text-xs text-gray-400">Sistema de Inventario</p>
                </div>
            </div>
        </div>

        <!-- Usuario -->
        <div class="p-4 border-b border-gray-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>

        <!-- Menú -->
        <nav class="p-4 flex-1 overflow-y-auto">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Menú Principal</p>
            <ul class="space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home mr-3"></i>
                        <span>Panel Principal</span>
                    </a>
                </li>

                <!-- Inventario -->
                <li>
                    <a href="{{ route('inventario.index') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('inventario.*') ? 'active' : '' }}">
                        <i class="fas fa-box mr-3"></i>
                        <span>Inventario</span>
                        <span class="ml-auto bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                            {{ \App\Models\Articulo::count() }}
                        </span>
                    </a>
                </li>

                <!-- Movimientos con submenú -->
                <li class="has-submenu">
                    <div class="submenu-toggle sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('entradas.*') || request()->routeIs('salidas.*') ? 'active' : '' }}" 
                         onclick="toggleSubmenu(this)">
                        <i class="fas fa-exchange-alt mr-3"></i>
                        <span class="flex-1">Movimientos</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                    <ul class="submenu {{ request()->routeIs('entradas.*') || request()->routeIs('salidas.*') ? 'open' : '' }}">
                        <li>
                            <a href="{{ route('entradas.index') }}" 
                               class="sidebar-link submenu-link flex items-center px-4 py-2 text-gray-300 hover:text-white {{ request()->routeIs('entradas.*') ? 'active' : '' }}">
                                <i class="fas fa-arrow-down mr-3 text-green-400"></i>
                                <span>Entradas</span>
                                <span class="ml-auto bg-green-600 text-white text-xs px-2 py-1 rounded-full">
                                    {{ \App\Models\Entrada::count() }}
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('salidas.index') }}" 
                               class="sidebar-link submenu-link flex items-center px-4 py-2 text-gray-300 hover:text-white {{ request()->routeIs('salidas.*') ? 'active' : '' }}">
                                <i class="fas fa-arrow-up mr-3 text-red-400"></i>
                                <span>Salidas</span>
                                <span class="ml-auto bg-red-600 text-white text-xs px-2 py-1 rounded-full">
                                    {{ \App\Models\Salida::count() }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Proveedores -->
                <li>
                    <a href="{{ route('proveedores.index') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                        <i class="fas fa-truck mr-3"></i>
                        <span>Proveedores</span>
                    </a>
                </li>

                <!-- Sucursales -->
                <li>
                    <a href="{{ route('sucursales.index') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('sucursales.*') ? 'active' : '' }}">
                        <i class="fas fa-store mr-3"></i>
                        <span>Sucursales</span>
                    </a>
                </li>

                <!-- Reportes -->
                <li>
                    <a href="{{ route('reportes.index') }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar mr-3"></i>
                        <span>Reportes</span>
                    </a>
                </li>
            </ul>

            <p class="text-xs text-gray-500 uppercase tracking-wider mt-6 mb-3">Alertas</p>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('inventario.index', ['estado' => 'stock_bajo']) }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white">
                        <i class="fas fa-exclamation-triangle mr-3 text-yellow-500"></i>
                        <span>Stock Bajo</span>
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ \App\Models\Articulo::whereRaw('stock_actual <= minimo_requerido AND stock_actual > 0')->count() }}
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventario.index', ['estado' => 'agotado']) }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white">
                        <i class="fas fa-times-circle mr-3 text-red-500"></i>
                        <span>Agotados</span>
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ \App\Models\Articulo::where('stock_actual', 0)->count() }}
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('entradas.index', ['estado' => 'pendiente']) }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white">
                        <i class="fas fa-clock mr-3 text-yellow-500"></i>
                        <span>Entradas Pendientes</span>
                        <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ \App\Models\Entrada::where('estado', 'pendiente')->count() }}
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('salidas.index', ['estado' => 'pendiente']) }}" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white">
                        <i class="fas fa-clock mr-3 text-yellow-500"></i>
                        <span>Salidas Pendientes</span>
                        <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ \App\Models\Salida::where('estado', 'pendiente')->count() }}
                        </span>
                    </a>
                </li>
            </ul>

            <p class="text-xs text-gray-500 uppercase tracking-wider mt-6 mb-3">Configuración</p>
            <ul class="space-y-1">
                <li>
                    <a href="#" class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white">
                        <i class="fas fa-cog mr-3"></i>
                        <span>Configuración</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white w-full text-left">
                            <i class="fas fa-sign-out-alt mr-3"></i>
                            <span>Salir</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Footer Sidebar -->
        <div class="p-4 border-t border-gray-800">
            <p class="text-xs text-gray-500 text-center">SIVA v1.0</p>
            <p class="text-xs text-gray-500 text-center">© 2026 ICRAPAT, SRL</p>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <div class="main-content">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Botón hamburguesa para móvil -->
                    <button onclick="toggleSidebar()" class="hamburger text-gray-600 hover:text-gray-900 text-2xl">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2 class="text-xl font-semibold text-gray-800">
                        @yield('page-title', 'Dashboard')
                    </h2>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Fecha actual -->
                    <span class="text-sm text-gray-500 hidden md:block">
                        {{ now()->format('d/m/Y H:i') }}
                    </span>
                    <!-- Notificaciones -->
                    <div class="relative">
                        <button class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bell text-xl"></i>
                            @php
                                $alertasPendientes = \App\Models\AlertaStock::where('estado', 'pendiente')->count();
                                $entradasPendientes = \App\Models\Entrada::where('estado', 'pendiente')->count();
                                $salidasPendientes = \App\Models\Salida::where('estado', 'pendiente')->count();
                                $totalNotificaciones = $alertasPendientes + $entradasPendientes + $salidasPendientes;
                            @endphp
                            @if($totalNotificaciones > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ $totalNotificaciones }}
                                </span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido -->
        <main class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded">
                    <i class="fas fa-info-circle mr-2"></i>
                    {{ session('info') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }

        function toggleSubmenu(element) {
            const submenu = element.nextElementSibling;
            element.classList.toggle('open');
            submenu.classList.toggle('open');
        }

        // Cerrar sidebar al hacer click fuera en móvil
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('overlay');
            const hamburger = document.querySelector('.hamburger');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !hamburger?.contains(event.target)) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                }
            }
        });

        // Cerrar sidebar al redimensionar a desktop
        window.addEventListener('resize', function() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('overlay');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            }
        });

        // Mantener submenú abierto si estamos en una ruta de entradas o salidas
        document.addEventListener('DOMContentLoaded', function() {
            const currentRoute = window.location.pathname;
            if (currentRoute.includes('/entradas') || currentRoute.includes('/salidas')) {
                const submenuToggle = document.querySelector('.submenu-toggle');
                if (submenuToggle) {
                    submenuToggle.classList.add('open');
                    const submenu = submenuToggle.nextElementSibling;
                    if (submenu) {
                        submenu.classList.add('open');
                    }
                }
            }
        });
    </script>
</body>
</html>