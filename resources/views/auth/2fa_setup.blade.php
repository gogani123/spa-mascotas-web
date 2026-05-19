<x-guest-layout>
    <div class="text-center mb-4 text-sm text-gray-600 dark:text-gray-400">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Configurar Seguridad (2FA)</h2>
        <p>Por políticas de seguridad, el Administrador debe utilizar autenticación de dos factores.</p>
    </div>

    <div class="flex justify-center mb-6">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($QR_Image) }}" alt="Código QR" class="rounded-lg shadow-lg border-4 border-white">
    </div>

    <div class="text-center mb-6 text-sm text-gray-600 dark:text-gray-400">
        <p><strong>1.</strong> Descarga la app <strong>Google Authenticator</strong> en tu celular.</p>
        <p><strong>2.</strong> Escanea el código QR de arriba.</p>
        <p class="mt-2 text-xs">Si no puedes escanearlo, ingresa esta clave manual: <br> <span class="font-mono text-indigo-500">{{ $secret }}</span></p>
    </div>

    <div class="flex items-center justify-center mt-4">
        <a href="{{ route('2fa.index') }}">
            <x-primary-button>
                Ya lo escaneé, continuar
            </x-primary-button>
        </a>
    </div>
</x-guest-layout>