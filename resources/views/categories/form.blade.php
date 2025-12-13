@csrf
<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
        <div class="mt-1">
            <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                required>
        </div>
        @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
        <div class="mt-1">
            <textarea id="description" name="description" rows="3"
                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description', $category->description ?? '') }}</textarea>
        </div>
        <p class="mt-2 text-sm text-gray-500">Breve descripción de la categoría.</p>
        @error('description')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end">
        <a href="{{ route('categories.index') }}"
            class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
            Cancelar
        </a>
        <button type="submit"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Guardar
        </button>
    </div>
</div>