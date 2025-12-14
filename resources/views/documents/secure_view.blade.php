@extends('layouts.app')

@section('content')
    <div class="h-screen flex flex-col">
        <!-- Toolbar -->
        <div class="bg-gray-800 text-white p-4 shadow-md flex justify-between items-center z-10">
            <div class="flex items-center space-x-4">
                <a href="{{ url()->previous() }}" class="text-gray-300 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-lg font-semibold truncate">{{ $document->title }}</h1>
            </div>

            <div class="flex space-x-3">
                <a href="{{ route('documents.stream', $document) }}" download
                    class="flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-md text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Descargar
                </a>
            </div>
        </div>

        <!-- Viewer Container -->
        <div class="flex-1 bg-gray-100 dark:bg-gray-900 relative">
            <iframe src="{{ route('documents.stream', $document) }}#toolbar=0" class="w-full h-full border-none"
                type="application/pdf">
            </iframe>

            <!-- Watermark / Security overlay (Optional visual deterrent) -->
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center opacity-5 select-none">
                <div class="transform -rotate-45 text-6xl font-black text-gray-500">
                    CONFIDENCIAL
                </div>
            </div>
        </div>
    </div>
@endsection