@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white overflow-hidden shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Nueva Categoría
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Crea una nueva categoría para organizar tus documentos.
                </p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
                <form action="{{ route('categories.store') }}" method="POST">
                    @include('categories.form')
                </form>
            </div>
        </div>
    </div>
@endsection