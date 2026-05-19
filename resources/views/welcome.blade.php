<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Spa Mascotas - Cuidado Profesional para tu Mejor Amigo</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="relative min-h-screen flex flex-col justify-center items-center">
            
            @if (Route::has('login'))
                <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10 w-full flex justify-end gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">
                            <x-primary-button>
                                Iniciar Sesión
                            </x-primary-button>
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Registrarse
                            </a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="w-full max-w-7xl mx-auto p-6 lg:p-8 flex flex-col items-center gap-12 text-center">
                
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 mx-auto text-indigo-500 drop-shadow-lg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.5c-2.48 0-4.5 2.02-4.5 4.5s2.02 4.5 4.5 4.5 4.5-2.02 4.5-4.5-2.02-4.5-4.5-4.5zm-6 4c-1.93 0-3.5 1.57-3.5 3.5s1.57 3.5 3.5 3.5 3.5-1.57 3.5-3.5-1.57-3.5-3.5-3.5zm12 0c-1.93 0-3.5 1.57-3.5 3.5s1.57 3.5 3.5 3.5 3.5-1.57 3.5-3.5-1.57-3.5-3.5-3.5zM7.5 15c-1.88 0-3.66.69-5.12 1.83C3.04 17.51 4 18.5 5 19.5c1.88-1.5 4.14-2.5 6.5-2.5s4.62 1 6.5 2.5c1-1 1.96-1.99 2.62-2.67C19.16 15.69 17.38 15 15.5 15 13.01 15 12 16 12 16s-1.01-1-4.5-1z"/>
                    </svg>
                </div>
                <div class="flex flex-col gap-4">
                    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white leading-tight">
                        Bienvenido a <span class="text-indigo-500">Spa Mascotas</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto font-medium">
                        El lugar donde el bienestar y el cuidado profesional se unen para mimar a tu mejor amigo. Cortes de pelo, baños, masajes y mucho más.
                    </p>
                </div>

                @guest
                    <div class="mt-8">
                        <a href="{{ route('register') }}">
                            <x-primary-button class="py-4 px-8 text-lg font-bold">
                                ¡Reserva tu Primera Cita!
                            </x-primary-button>
                        </a>
                    </div>
                @endguest

            </div>

            <div class="flex justify-center mt-16 p-6 w-full text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} Spa Mascotas. Todos los derechos reservados.
            </div>
        </div>
    </body>
</html>