@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6">Solicitar Acceso a Documento</h1>

            <div class="bg-white shadow sm:rounded-lg p-6">
                <form action="{{ route('web.access-requests.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="document_id" class="block text-sm font-medium text-gray-700">Documento</label>
                        <select id="document_id" name="document_id"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($documents as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->title }} ({{ $doc->user->name ?? 'Usuario' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700">Motivo de la solicitud</label>
                        <textarea id="reason" name="reason" rows="3"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            required></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Enviar
                            Solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection