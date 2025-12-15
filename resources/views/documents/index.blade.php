@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ Auth::user()->isAdmin() ? 'Todos los Documentos' : 'Mis Documentos' }}
        </h1>
        @if(!Auth::user()->isAdmin())
            <div class="mt-4 sm:mt-0">
                <button type="button" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Subir Documento
                </button>
            </div>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($documents as $doc)
                <li class="px-4 py-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center min-w-0">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-12 w-12 rounded-md bg-indigo-100 dark:bg-indigo-900 text-indigo-500 dark:text-indigo-300">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                                <div>
                                    <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400 truncate">{{ $doc->title }}</p>
                                    <p class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        @if(Auth::user()->isAdmin())
                                            <span class="font-semibold text-gray-700 dark:text-gray-300 mr-1">{{ $doc->user->name }}</span>
                                            <span class="mx-1">&bull;</span>
                                        @endif
                                        <span class="truncate">{{ $doc->original_filename }}</span>
                                    </p>
                                </div>
                                <div class="hidden md:block">
                                    <div>
                                        <p class="text-sm text-gray-900 dark:text-gray-300">
                                            Subido el <time datetime="{{ $doc->created_at }}">{{ $doc->created_at->format('d/m/Y') }}</time>
                                        </p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $doc->readable_file_size }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('documents.download', $doc) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                Descargar
                            </a>
                            @if(!Auth::user()->isAdmin())
                                <form action="{{ route('documents.destroy', $doc) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este documento?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Eliminar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-gray-500">
                    No tienes documentos aún. ¡Sube el primero!
                </li>
            @endforelse
        </ul>
    </div>
    
    <div class="mt-4">
        {{ $documents->links() }}
    </div>
</div>
@endsection
