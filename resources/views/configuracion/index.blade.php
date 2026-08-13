@extends('layouts.app')

@section('page-title', 'Configuración de mi cuenta')

@section('content')

<div class="max-w-5xl mx-auto space-y-6">

    {{-- ENCABEZADO --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Configuración de mi cuenta
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Administra tu información personal y la seguridad de tu cuenta.
        </p>
    </div>


    {{-- MENSAJE DE ÉXITO --}}
    @if(session('success'))

        <div class="flex items-center p-4 text-green-800 rounded-lg bg-green-50 border border-green-200">

            <i class="fas fa-check-circle text-lg mr-3"></i>

            <span class="text-sm font-medium">
                {{ session('success') }}
            </span>

        </div>

    @endif


    {{-- ERRORES GENERALES --}}
    @if($errors->any())

        <div class="p-4 rounded-lg bg-red-50 border border-red-200">

            <div class="flex items-center text-red-800 mb-2">

                <i class="fas fa-exclamation-circle mr-2"></i>

                <span class="font-semibold">
                    Se encontraron algunos errores:
                </span>

            </div>

            <ul class="list-disc list-inside text-sm text-red-700">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- INFORMACIÓN DE LA CUENTA --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">

        <div class="px-6 py-5 border-b border-gray-200">

            <div class="flex items-center">

                <div class="bg-blue-100 text-blue-600 rounded-lg p-3 mr-4">
                    <i class="fas fa-user text-xl"></i>
                </div>

                <div>

                    <h2 class="text-lg font-semibold text-gray-900">
                        Información personal
                    </h2>

                    <p class="text-sm text-gray-500">
                        Actualiza tus datos personales.
                    </p>

                </div>

            </div>

        </div>


        <form method="POST"
              action="{{ route('configuracion.perfil.update') }}"
              class="p-6">

            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                {{-- NOMBRE --}}
                <div>

                    <label for="name"
                           class="block text-sm font-medium text-gray-700 mb-2">

                        Nombre completo

                    </label>

                    <div class="relative">

                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                            <i class="fas fa-user text-gray-400"></i>

                        </div>

                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full pl-10 rounded-lg border-gray-300
                                      focus:border-blue-500
                                      focus:ring focus:ring-blue-200">

                    </div>

                    @error('name')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- CORREO --}}
                <div>

                    <label for="email"
                           class="block text-sm font-medium text-gray-700 mb-2">

                        Correo electrónico

                    </label>

                    <div class="relative">

                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                            <i class="fas fa-envelope text-gray-400"></i>

                        </div>

                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full pl-10 rounded-lg border-gray-300
                                      focus:border-blue-500
                                      focus:ring focus:ring-blue-200">

                    </div>

                    @error('email')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </div>


            <div class="mt-6 flex justify-end">

                <button type="submit"
                        class="inline-flex items-center px-5 py-2.5
                               bg-blue-600 text-white rounded-lg
                               hover:bg-blue-700">

                    <i class="fas fa-save mr-2"></i>

                    Guardar cambios

                </button>

            </div>

        </form>

    </div>


    {{-- SEGURIDAD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">

        <div class="px-6 py-5 border-b border-gray-200">

            <div class="flex items-center">

                <div class="bg-red-100 text-red-600 rounded-lg p-3 mr-4">
                    <i class="fas fa-lock text-xl"></i>
                </div>

                <div>

                    <h2 class="text-lg font-semibold text-gray-900">
                        Seguridad
                    </h2>

                    <p class="text-sm text-gray-500">
                        Cambia la contraseña de acceso a tu cuenta.
                    </p>

                </div>

            </div>

        </div>


        <form method="POST"
              action="{{ route('configuracion.password.update') }}"
              class="p-6">

            @csrf
            @method('PUT')


            {{-- CONTRASEÑA ACTUAL --}}
            <div class="mb-5">

                <label for="current_password"
                       class="block text-sm font-medium text-gray-700 mb-2">

                    Contraseña actual

                </label>

                <div class="relative">

                    <input type="password"
                           id="current_password"
                           name="current_password"
                           required
                           autocomplete="current-password"
                           class="w-full rounded-lg border-gray-300 pr-10
                                  focus:border-blue-500
                                  focus:ring focus:ring-blue-200">

                    <button type="button"
                            onclick="togglePassword('current_password', 'icon-current')"
                            class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600">

                        <i id="icon-current" class="fas fa-eye"></i>

                    </button>

                </div>

                @error('current_password')

                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                {{-- NUEVA CONTRASEÑA --}}
                <div>

                    <label for="password"
                           class="block text-sm font-medium text-gray-700 mb-2">

                        Nueva contraseña

                    </label>

                    <div class="relative">

                        <input type="password"
                               id="password"
                               name="password"
                               required
                               autocomplete="new-password"
                               class="w-full rounded-lg border-gray-300 pr-10
                                      focus:border-blue-500
                                      focus:ring focus:ring-blue-200">

                        <button type="button"
                                onclick="togglePassword('password', 'icon-password')"
                                class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600">

                            <i id="icon-password" class="fas fa-eye"></i>

                        </button>

                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        La contraseña debe tener al menos 8 caracteres.
                    </p>

                    @error('password')

                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                {{-- CONFIRMAR CONTRASEÑA --}}
                <div>

                    <label for="password_confirmation"
                           class="block text-sm font-medium text-gray-700 mb-2">

                        Confirmar nueva contraseña

                    </label>

                    <div class="relative">

                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               required
                               autocomplete="new-password"
                               class="w-full rounded-lg border-gray-300 pr-10
                                      focus:border-blue-500
                                      focus:ring focus:ring-blue-200">

                        <button type="button"
                                onclick="togglePassword('password_confirmation', 'icon-confirm')"
                                class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600">

                            <i id="icon-confirm" class="fas fa-eye"></i>

                        </button>

                    </div>

                </div>

            </div>


            <div class="mt-6 flex justify-end">

                <button type="submit"
                        class="inline-flex items-center px-5 py-2.5
                               bg-red-600 text-white rounded-lg
                               hover:bg-red-700">

                    <i class="fas fa-key mr-2"></i>

                    Cambiar contraseña

                </button>

            </div>

        </form>

    </div>


    {{-- INFORMACIÓN DE LA CUENTA --}}
    <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">

        <div class="flex items-start">

            <div class="bg-gray-200 text-gray-600 rounded-lg p-3 mr-4">

                <i class="fas fa-info-circle"></i>

            </div>

            <div>

                <h3 class="font-semibold text-gray-900">
                    Información de la cuenta
                </h3>

                <div class="mt-2 text-sm text-gray-600 space-y-1">

                    <p>
                        <strong>Usuario:</strong>
                        {{ $user->name }}
                    </p>

                    <p>
                        <strong>Correo:</strong>
                        {{ $user->email }}
                    </p>

                    <p>
                        <strong>Cuenta creada:</strong>
                        {{ $user->created_at?->format('d/m/Y H:i') }}
                    </p>

                    <p>
                        <strong>Última actualización:</strong>
                        {{ $user->updated_at?->format('d/m/Y H:i') }}
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

function togglePassword(inputId, iconId)
{
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === 'password') {

        input.type = 'text';

        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');

    } else {

        input.type = 'password';

        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');

    }
}

</script>

@endsection