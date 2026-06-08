<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-b-indigo-500 border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Enlaces de Navegación Dinámicos por Rol (Punto 11 Estricto) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    
                    <!-- ==================== VISTA: ADMINISTRADOR (Rol 1) ==================== -->
                    @if(Auth::user()->rol_id == 1)
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            📊 {{ __('Dashboard de Reportes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.cierre_caja')" :active="request()->routeIs('admin.cierre_caja')">
                            💰 {{ __('Ventas (Cierre de Caja)') }}
                        </x-nav-link>
                        <x-nav-link :href="route('citas.calendario')" :active="request()->routeIs('citas.calendario')">
                            📈 {{ __('Ocupación Global') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.servicios.index')" :active="request()->routeIs('admin.servicios.*')">
                            💵 {{ __('Configuración de Precios') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            👥 {{ __('Gestión de Personal') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.inventario.index')" :active="request()->routeIs('admin.inventario.*')">
                            📦 {{ __('Inventario') }}
                        </x-nav-link>
                        <!-- Acceso extra para que el administrador pueda crear citas operativas si lo desea -->
                        <x-nav-link :href="route('citas.index')" :active="request()->routeIs('citas.*') && !request()->routeIs('citas.calendario')">
                            📅 {{ __('Control de Citas') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.reporte.insumos')" :active="request()->routeIs('admin.reporte.insumos')">
                            📋 {{ __('Auditoría de Insumos') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.reporte.satisfaccion')" :active="request()->routeIs('admin.reporte.satisfaccion')">
                            ⭐ {{ __('Satisfacción / NPS') }}
                        </x-nav-link>
                    @endif

                    <!-- ==================== VISTA: RECEPCIÓN (Rol 2) ==================== -->
                    @if(Auth::user()->rol_id == 2)
                        <x-nav-link :href="route('citas.calendario')" :active="request()->routeIs('citas.calendario')">
                            📅 {{ __('Calendario Maestro') }}
                        </x-nav-link>
                        <x-nav-link :href="route('mascotas.index')" :active="request()->routeIs('mascotas.*')">
                            🐾 {{ __('Registro Clientes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('recepcion.cronograma')" :active="request()->routeIs('recepcion.cronograma')">
                            📋 {{ __('Cronograma Diario') }}
                        </x-nav-link>
                        <x-nav-link :href="route('recepcion.cancelaciones')" :active="request()->routeIs('recepcion.cancelaciones')">
                            🚨 {{ __('Cancelaciones / No-Show') }}
                        </x-nav-link>
                        <x-nav-link :href="route('recepcion.inventario_critico')" :active="request()->routeIs('recepcion.inventario_critico')">
                            📦 {{ __('Inventario Crítico') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.cierre_caja')" :active="request()->routeIs('admin.cierre_caja')">
                            💰 {{ __('Cierre de Caja') }}
                        </x-nav-link>
                    @endif

                    <!-- ==================== VISTA: GROOMER (Rol 3) ==================== -->
                    @if(Auth::user()->rol_id == 3)
                        <x-nav-link :href="route('groomer.agenda')" :active="request()->routeIs('groomer.agenda')">
                            📅 {{ __('Mi Agenda de Hoy') }}
                        </x-nav-link>
                        <x-nav-link :href="route('groomer.reporte.rendimiento')" :active="request()->routeIs('groomer.reporte.rendimiento')">
                            📊 {{ __('Mi Rendimiento') }}
                        </x-nav-link>
                    @endif
                    <!-- ==================== VISTA: CLIENTE (Rol 4) ==================== -->
                    @if(Auth::user()->rol_id == 4)
                        <x-nav-link :href="route('tienda.index')" :active="request()->routeIs('tienda.*')">
                            🛍 {{ __('Catálogo de Productos') }}
                        </x-nav-link>
                        <x-nav-link :href="route('cliente.citas.create')" :active="request()->routeIs('cliente.citas.create')">
                            📅 {{ __('Solicitar Cita') }}
                        </x-nav-link>
                        <x-nav-link :href="route('cliente.mascotas.index')" :active="request()->routeIs('cliente.mascotas.*')">
                            🐶 {{ __('Mis Mascotas') }}
                        </x-nav-link>
                        <x-nav-link :href="route('cliente.citas.index')" :active="request()->routeIs('cliente.citas.index')">
                            📜 {{ __('Historial de Servicios') }}
                        </x-nav-link>
                    @endif

                </div>
            </div>

            <!-- Menú Desplegable de Usuario (Derecha) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                
                <!-- Ajustes avanzados de Horarios/Logs para Admin y Recepción -->
                @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <span class="text-indigo-400 font-bold">⚙ Parámetros</span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('admin.horarios.index')">🕒 Horarios Atendidos</x-dropdown-link>
                            <x-dropdown-link :href="route('admin.bloqueos.index')">🚫 Bloqueos Activos</x-dropdown-link>
                            @if(Auth::user()->rol_id == 1)
                                <x-dropdown-link :href="route('admin.auditoria.index')">📑 Auditoría (Logs)</x-dropdown-link>
                            @endif
                        </x-slot>
                    </x-dropdown>
                @endif

                <!-- Perfil del Usuario Firmado -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>👤 {{ Auth::user()->name }}</div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Mi Perfil') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburguesa Móvil -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 14h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->rol_id == 1)
                <x-responsive-nav-link :href="route('dashboard')">{{ __('Dashboard de Reportes') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.cierre_caja')">{{ __('Ventas') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.inventario.index')">{{ __('Inventario') }}</x-responsive-nav-link>
            @endif
            @if(Auth::user()->rol_id == 2)
                <x-responsive-nav-link :href="route('citas.calendario')">{{ __('Calendario Maestro') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('mascotas.index')">{{ __('Registro Clientes') }}</x-responsive-nav-link>
            @endif
            @if(Auth::user()->rol_id == 3)
                <x-responsive-nav-link :href="route('groomer.agenda')">{{ __('Mi Agenda de Hoy') }}</x-responsive-nav-link>
            @endif
            @if(Auth::user()->rol_id == 4)
                <x-responsive-nav-link :href="route('tienda.index')">{{ __('Catálogo') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cliente.mascotas.index')">{{ __('Mis Mascotas') }}</x-responsive-nav-link>
            @endif
        </div>
    </div>
</nav>