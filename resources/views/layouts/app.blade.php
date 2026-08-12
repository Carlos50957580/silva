<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SIVA - {{ $title ?? 'Sistema de Inventario' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {
            transition: width 0.3s ease, transform 0.3s ease;
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: 80px;
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

        /* =====================================================
           CONTENIDO PRINCIPAL
        ===================================================== */

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .main-content.sidebar-collapsed {
            margin-left: 80px;
        }

        /* =====================================================
           LINKS
        ===================================================== */

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
            min-width: 24px;
            text-align: center;
        }

        /* =====================================================
           ELEMENTOS QUE SE OCULTAN AL CONTRAER
        ===================================================== */

        .sidebar.collapsed .sidebar-text,
        .sidebar.collapsed .sidebar-title,
        .sidebar.collapsed .user-info,
        .sidebar.collapsed .menu-section-title,
        .sidebar.collapsed .sidebar-badge,
        .sidebar.collapsed .submenu {
            display: none !important;
        }

        /* Centrar iconos */
        .sidebar.collapsed .sidebar-link {
            justify-content: center;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .sidebar.collapsed .sidebar-link i {
            margin-right: 0 !important;
        }

        /* Logo */
        .sidebar.collapsed .logo-container {
            justify-content: center;
        }

        .sidebar.collapsed .logo-icon {
            margin-right: 0;
        }

        /* Usuario */
        .sidebar.collapsed .user-container {
            justify-content: center;
        }

        /* Footer */
        .sidebar.collapsed .sidebar-footer {
            display: none;
        }

        /* =====================================================
           BADGES
        ===================================================== */

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

        .sidebar.collapsed .badge-notification {
            display: block;
            right: 8px;
            top: 5px;
            width: 7px;
            height: 7px;
            padding: 0;
            font-size: 0;
        }

        /* =====================================================
           SUBMENUS
        ===================================================== */

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

        .sidebar.collapsed .submenu-toggle .fa-chevron-down {
            display: none;
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

        /* =====================================================
           BOTÓN CONTRAER
        ===================================================== */

        .sidebar-toggle {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-toggle i {
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed ~ .main-content .sidebar-toggle i {
            transform: rotate(180deg);
        }

        /* =====================================================
           MOBILE
        ===================================================== */

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
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .overlay.active {
            display: block;
        }

        @media (max-width: 768px) {

            .sidebar {
                width: 280px;
                transform: translateX(-100%);
            }

            .sidebar.collapsed {
                width: 280px;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content,
            .main-content.sidebar-collapsed {
                margin-left: 0;
            }

            .hamburger {
                display: block !important;
            }

            /* En móvil nunca se aplica el modo colapsado */
            .sidebar.collapsed .sidebar-text,
            .sidebar.collapsed .sidebar-title,
            .sidebar.collapsed .user-info,
            .sidebar.collapsed .menu-section-title,
            .sidebar.collapsed .sidebar-badge {
                display: block !important;
            }

            .sidebar.collapsed .sidebar-link {
                justify-content: flex-start;
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .sidebar.collapsed .sidebar-link i {
                margin-right: 0.75rem !important;
            }

            .sidebar.collapsed .logo-container {
                justify-content: flex-start;
            }

            .sidebar.collapsed .logo-icon {
                margin-right: 0.75rem;
            }

            .sidebar.collapsed .user-container {
                justify-content: flex-start;
            }

            .sidebar.collapsed .sidebar-footer {
                display: block;
            }

            .sidebar.collapsed .submenu {
                display: block !important;
            }

            .sidebar.collapsed .submenu-toggle .fa-chevron-down {
                display: block;
            }
        }

        /* =====================================================
           TOOLTIP SIDEBAR CONTRAÍDA
        ===================================================== */

        .sidebar.collapsed .sidebar-link[title] {
            position: relative;
        }

        /* =====================================================
           ANIMACIONES
        ===================================================== */

        .sidebar-content {
            transition: opacity 0.2s ease;
        }

        .sidebar.collapsed .sidebar-content {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- =====================================================
         OVERLAY MOBILE
    ====================================================== -->

    <div
        class="overlay"
        id="overlay"
        onclick="toggleMobileSidebar()">
    </div>


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside
        id="sidebar"
        class="sidebar bg-gray-900 text-white shadow-xl">

        <!-- =================================================
             LOGO
        ================================================== -->

        <div class="p-6 border-b border-gray-800">

            <div class="logo-container flex items-center space-x-3">

                <div class="logo-icon bg-blue-600 p-2 rounded-lg flex-shrink-0">
                    <i class="fas fa-boxes text-white text-xl"></i>
                </div>

                <div class="sidebar-title">
                    <h1 class="text-xl font-bold text-white">
                        SIVA
                    </h1>

                    <p class="text-xs text-gray-400">
                        Sistema de Inventario
                    </p>
                </div>

            </div>

        </div>


        <!-- =================================================
             USUARIO
        ================================================== -->

        <div class="p-4 border-b border-gray-800">

            <div class="user-container flex items-center space-x-3">

                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">

                    <span class="text-white font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>

                </div>

                <div class="flex-1 user-info min-w-0">

                    <p class="text-sm font-medium text-white truncate">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="text-xs text-gray-400 truncate">
                        {{ auth()->user()->email }}
                    </p>

                </div>

            </div>

        </div>


        <!-- =================================================
             MENÚ
        ================================================== -->

        <nav class="p-4 flex-1 overflow-y-auto">

            <!-- Título sección -->

            <p class="menu-section-title text-xs text-gray-500 uppercase tracking-wider mb-3 sidebar-text">
                Menú Principal
            </p>


            <ul class="space-y-1">

                <!-- =================================================
                     DASHBOARD
                ================================================== -->

                <li>

                    <a
                        href="{{ route('dashboard') }}"
                        title="Panel Principal"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white
                        {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                        <i class="fas fa-home mr-3"></i>

                        <span class="sidebar-text">
                            Panel Principal
                        </span>

                    </a>

                </li>


                <!-- =================================================
                     INVENTARIO
                ================================================== -->

                <li>

                    <a
                        href="{{ route('inventario.index') }}"
                        title="Inventario"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white
                        {{ request()->routeIs('inventario.*') ? 'active' : '' }}">

                        <i class="fas fa-box mr-3"></i>

                        <span class="sidebar-text">
                            Inventario
                        </span>

                        <span class="sidebar-badge ml-auto bg-blue-600 text-white text-xs px-2 py-1 rounded-full">
                            {{ \App\Models\Articulo::count() }}
                        </span>

                    </a>

                </li>


                <!-- =================================================
                     MOVIMIENTOS
                ================================================== -->

                <li class="has-submenu">

                    <div
                        title="Movimientos"
                        class="submenu-toggle sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white
                        {{ request()->routeIs('entradas.*') || request()->routeIs('salidas.*') ? 'active open' : '' }}"
                        onclick="toggleSubmenu(this)">

                        <i class="fas fa-exchange-alt mr-3"></i>

                        <span class="flex-1 sidebar-text">
                            Movimientos
                        </span>

                        <i class="fas fa-chevron-down text-xs"></i>

                    </div>


                    <ul
                        class="submenu
                        {{ request()->routeIs('entradas.*') || request()->routeIs('salidas.*') ? 'open' : '' }}">

                        <!-- ENTRADAS -->

                        <li>

                            <a
                                href="{{ route('entradas.index') }}"
                                title="Entradas"
                                class="sidebar-link submenu-link flex items-center px-4 py-2 text-gray-300 hover:text-white
                                {{ request()->routeIs('entradas.*') ? 'active' : '' }}">

                                <i class="fas fa-arrow-down mr-3 text-green-400"></i>

                                <span class="sidebar-text">
                                    Entradas
                                </span>

                                <span class="sidebar-badge ml-auto bg-green-600 text-white text-xs px-2 py-1 rounded-full">
                                    {{ \App\Models\Entrada::count() }}
                                </span>

                            </a>

                        </li>


                        <!-- SALIDAS -->

                        <li>

                            <a
                                href="{{ route('salidas.index') }}"
                                title="Salidas"
                                class="sidebar-link submenu-link flex items-center px-4 py-2 text-gray-300 hover:text-white
                                {{ request()->routeIs('salidas.*') ? 'active' : '' }}">

                                <i class="fas fa-arrow-up mr-3 text-red-400"></i>

                                <span class="sidebar-text">
                                    Salidas
                                </span>

                                <span class="sidebar-badge ml-auto bg-red-600 text-white text-xs px-2 py-1 rounded-full">
                                    {{ \App\Models\Salida::count() }}
                                </span>

                            </a>

                        </li>

                    </ul>

                </li>


                <!-- =================================================
                     PROVEEDORES
                ================================================== -->

                <li>

                    <a
                        href="{{ route('proveedores.index') }}"
                        title="Proveedores"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white
                        {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">

                        <i class="fas fa-truck mr-3"></i>

                        <span class="sidebar-text">
                            Proveedores
                        </span>

                    </a>

                </li>


                <!-- =================================================
                     SUCURSALES
                ================================================== -->

                <li>

                    <a
                        href="{{ route('sucursales.index') }}"
                        title="Sucursales"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white
                        {{ request()->routeIs('sucursales.*') ? 'active' : '' }}">

                        <i class="fas fa-store mr-3"></i>

                        <span class="sidebar-text">
                            Sucursales
                        </span>

                    </a>

                </li>


                <!-- =================================================
                     REPORTES
                ================================================== -->

                <li>

                    <a
                        href="{{ route('reportes.index') }}"
                        title="Reportes"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white
                        {{ request()->routeIs('reportes.*') ? 'active' : '' }}">

                        <i class="fas fa-chart-bar mr-3"></i>

                        <span class="sidebar-text">
                            Reportes
                        </span>

                    </a>

                </li>

            </ul>


            <!-- =================================================
                 ALERTAS
            ================================================== -->

            <p class="menu-section-title text-xs text-gray-500 uppercase tracking-wider mt-6 mb-3 sidebar-text">
                Alertas
            </p>


            <ul class="space-y-1">

                <!-- STOCK BAJO -->

                <li>

                    <a
                        href="{{ route('inventario.index', ['estado' => 'stock_bajo']) }}"
                        title="Stock Bajo"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white">

                        <i class="fas fa-exclamation-triangle mr-3 text-yellow-500"></i>

                        <span class="sidebar-text">
                            Stock Bajo
                        </span>

                        <span class="sidebar-badge ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">

                            {{ \App\Models\Articulo::whereRaw('stock_actual <= minimo_requerido AND stock_actual > 0')->count() }}

                        </span>

                    </a>

                </li>


                <!-- AGOTADOS -->

                <li>

                    <a
                        href="{{ route('inventario.index', ['estado' => 'agotado']) }}"
                        title="Agotados"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white">

                        <i class="fas fa-times-circle mr-3 text-red-500"></i>

                        <span class="sidebar-text">
                            Agotados
                        </span>

                        <span class="sidebar-badge ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">

                            {{ \App\Models\Articulo::where('stock_actual', 0)->count() }}

                        </span>

                    </a>

                </li>


                <!-- ENTRADAS PENDIENTES -->

                <li>

                    <a
                        href="{{ route('entradas.index', ['estado' => 'pendiente']) }}"
                        title="Entradas Pendientes"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white">

                        <i class="fas fa-clock mr-3 text-yellow-500"></i>

                        <span class="sidebar-text">
                            Entradas Pendientes
                        </span>

                        <span class="sidebar-badge ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">

                            {{ \App\Models\Entrada::where('estado', 'pendiente')->count() }}

                        </span>

                    </a>

                </li>


                <!-- SALIDAS PENDIENTES -->

                <li>

                    <a
                        href="{{ route('salidas.index', ['estado' => 'pendiente']) }}"
                        title="Salidas Pendientes"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white">

                        <i class="fas fa-clock mr-3 text-yellow-500"></i>

                        <span class="sidebar-text">
                            Salidas Pendientes
                        </span>

                        <span class="sidebar-badge ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">

                            {{ \App\Models\Salida::where('estado', 'pendiente')->count() }}

                        </span>

                    </a>

                </li>

            </ul>


            <!-- =================================================
                 CONFIGURACIÓN
            ================================================== -->

            <p class="menu-section-title text-xs text-gray-500 uppercase tracking-wider mt-6 mb-3 sidebar-text">
                Configuración
            </p>


            <ul class="space-y-1">

                <!-- CONFIGURACIÓN -->

                <li>

                    <a
                        href="#"
                        title="Configuración"
                        class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white">

                        <i class="fas fa-cog mr-3"></i>

                        <span class="sidebar-text">
                            Configuración
                        </span>

                    </a>

                </li>


                <!-- LOGOUT -->

                <li>

                    <form method="POST" action="{{ route('logout') }}">

                        @csrf

                        <button
                            type="submit"
                            title="Salir"
                            class="sidebar-link flex items-center px-4 py-3 text-gray-300 hover:text-white w-full text-left">

                            <i class="fas fa-sign-out-alt mr-3"></i>

                            <span class="sidebar-text">
                                Salir
                            </span>

                        </button>

                    </form>

                </li>

            </ul>

        </nav>


        <!-- =================================================
             FOOTER
        ================================================== -->

        <div class="sidebar-footer p-4 border-t border-gray-800">

            <p class="text-xs text-gray-500 text-center">
                SIVA v1.0
            </p>

            <p class="text-xs text-gray-500 text-center">
                © 2026 ICRAPAT, SRL
            </p>

        </div>

    </aside>


    <!-- =====================================================
         CONTENIDO PRINCIPAL
    ====================================================== -->

    <div
        id="mainContent"
        class="main-content">


        <!-- =================================================
             HEADER
        ================================================== -->

        <header class="bg-white shadow-sm sticky top-0 z-50">

            <div class="px-6 py-4 flex items-center justify-between">

                <div class="flex items-center space-x-4">

                    <!-- BOTÓN MÓVIL -->

                    <button
                        onclick="toggleMobileSidebar()"
                        class="hamburger text-gray-600 hover:text-gray-900 text-2xl">

                        <i class="fas fa-bars"></i>

                    </button>


                    <!-- BOTÓN CONTRAER -->

                    <button
                        id="sidebarCollapseButton"
                        onclick="toggleDesktopSidebar()"
                        class="sidebar-toggle text-gray-600 hover:text-gray-900 hidden md:flex"
                        title="Contraer menú">

                        <i class="fas fa-chevron-left"></i>

                    </button>


                    <h2 class="text-xl font-semibold text-gray-800">

                        @yield('page-title', 'Dashboard')

                    </h2>

                </div>


                <!-- DERECHA -->

                <div class="flex items-center space-x-4">

                    <!-- FECHA -->

                    <span class="text-sm text-gray-500 hidden md:block">

                        {{ now()->format('d/m/Y H:i') }}

                    </span>


                    <!-- NOTIFICACIONES -->

                   

                </div>

            </div>

        </header>


        <!-- =================================================
             CONTENIDO
        ================================================== -->

        <main class="p-6">

            <!-- SUCCESS -->

            @if(session('success'))

                <div
                    class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">

                    <i class="fas fa-check-circle mr-2"></i>

                    {{ session('success') }}

                </div>

            @endif


            <!-- ERROR -->

            @if(session('error'))

                <div
                    class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">

                    <i class="fas fa-exclamation-circle mr-2"></i>

                    {{ session('error') }}

                </div>

            @endif


            <!-- INFO -->

            @if(session('info'))

                <div
                    class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded">

                    <i class="fas fa-info-circle mr-2"></i>

                    {{ session('info') }}

                </div>

            @endif


            @yield('content')

        </main>

    </div>


    <!-- =====================================================
         JAVASCRIPT
    ====================================================== -->

    <script>

        /* =====================================================
           REFERENCIAS
        ===================================================== */

        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const overlay = document.getElementById('overlay');


        /* =====================================================
           SIDEBAR DESKTOP
        ===================================================== */

        function toggleDesktopSidebar() {

            // No ejecutar en móvil
            if (window.innerWidth <= 768) {
                return;
            }

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');

            const collapsed =
                sidebar.classList.contains('collapsed');

            localStorage.setItem(
                'siva_sidebar_collapsed',
                collapsed ? 'true' : 'false'
            );

            updateSidebarButton();

        }


        /* =====================================================
           ACTUALIZAR BOTÓN
        ===================================================== */

        function updateSidebarButton() {

            const button =
                document.getElementById('sidebarCollapseButton');

            if (!button) {
                return;
            }

            const icon =
                button.querySelector('i');

            if (sidebar.classList.contains('collapsed')) {

                icon.classList.remove('fa-chevron-left');
                icon.classList.add('fa-chevron-right');

                button.setAttribute(
                    'title',
                    'Expandir menú'
                );

            } else {

                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-left');

                button.setAttribute(
                    'title',
                    'Contraer menú'
                );

            }

        }


        /* =====================================================
           MOBILE SIDEBAR
        ===================================================== */

        function toggleMobileSidebar() {

            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');

        }


        /* =====================================================
           SUBMENUS
        ===================================================== */

        function toggleSubmenu(element) {

            const submenu =
                element.nextElementSibling;

            if (!submenu) {
                return;
            }

            element.classList.toggle('open');

            submenu.classList.toggle('open');

        }


        /* =====================================================
           CARGAR ESTADO SIDEBAR
        ===================================================== */

        function loadSidebarState() {

            if (window.innerWidth <= 768) {

                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('sidebar-collapsed');

                return;

            }

            const collapsed =
                localStorage.getItem(
                    'siva_sidebar_collapsed'
                );

            if (collapsed === 'true') {

                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');

            } else {

                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('sidebar-collapsed');

            }

            updateSidebarButton();

        }


        /* =====================================================
           CERRAR SIDEBAR AL HACER CLICK FUERA EN MOBILE
        ===================================================== */

        document.addEventListener(
            'click',
            function(event) {

                if (window.innerWidth > 768) {
                    return;
                }

                const hamburger =
                    document.querySelector('.hamburger');

                if (
                    sidebar.contains(event.target) ||
                    hamburger?.contains(event.target)
                ) {
                    return;
                }

                sidebar.classList.remove('open');
                overlay.classList.remove('active');

            }
        );


        /* =====================================================
           ESCAPE
        ===================================================== */

        document.addEventListener(
            'keydown',
            function(event) {

                if (event.key === 'Escape') {

                    if (window.innerWidth <= 768) {

                        sidebar.classList.remove('open');
                        overlay.classList.remove('active');

                    }

                }

            }
        );


        /* =====================================================
           RESIZE
        ===================================================== */

        window.addEventListener(
            'resize',
            function() {

                if (window.innerWidth <= 768) {

                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('sidebar-collapsed');

                } else {

                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');

                    loadSidebarState();

                }

            }
        );


        /* =====================================================
           DOM READY
        ===================================================== */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                loadSidebarState();


                /*
                 * Mantener abierto el submenu
                 * de Entradas / Salidas
                 */

                const currentRoute =
                    window.location.pathname;

                if (
                    currentRoute.includes('/entradas') ||
                    currentRoute.includes('/salidas')
                ) {

                    const submenuToggle =
                        document.querySelector('.submenu-toggle');

                    if (submenuToggle) {

                        submenuToggle.classList.add('open');

                        const submenu =
                            submenuToggle.nextElementSibling;

                        if (submenu) {

                            submenu.classList.add('open');

                        }

                    }

                }

            }
        );

    </script>


    <!-- =====================================================
         SCRIPTS DE LAS VISTAS
         IMPORTANTE PARA @push('scripts')
    ====================================================== -->

    @stack('scripts')

</body>
</html>