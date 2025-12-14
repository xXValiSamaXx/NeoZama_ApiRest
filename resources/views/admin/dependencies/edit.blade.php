<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Dependencia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.dependencies.update', $dependency) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre de la Dependencia:</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $dependency->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Correo Electrónico:</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $dependency->email) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            @error('email') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Nueva Contraseña (Dejar en blanco para mantener actual):</label>
                            <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            @error('password') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Nueva Contraseña:</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <!-- Permissions (Categories) -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Permisos de Acceso a Categorías:</label>
                            <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($categories as $category)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" id="category_{{ $category->id }}" class="mr-2 leading-tight"
                                            {{ in_array($category->id, $assignedCategories) ? 'checked' : '' }}>
                                        <label for="category_{{ $category->id }}" class="text-sm">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-gray-500 text-xs mt-1">Selecciona las categorías cuyos documentos podrá ver esta dependencia.</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Actualizar Dependencia
                            </button>
                            <a href="{{ route('admin.dependencies.index') }}" class="text-gray-600 hover:text-gray-800">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
