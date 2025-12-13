@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6">Administración de Solicitudes de Acceso</h1>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul role="list" class="divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <li class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="text-sm truncate">
                                    <p class="font-medium text-indigo-600 truncate">{{ $request->dependency->name }}</p>
                                    <p class="text-gray-900">Propietario: {{ $request->user->name }}</p>
                                    <p class="text-gray-500">Solicita ver: <strong>{{ $request->document->title }}</strong></p>
                                    <p class="text-gray-500">Motivo: {{ $request->reason }}</p>
                                </div>
                                <div class="ml-2 flex-shrink-0 flex">
                                    <form action="{{ route('web.access-requests.update', $request) }}" method="POST"
                                        class="inline-flex">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit"
                                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 mr-2">
                                            Aprobar
                                        </button>
                                    </form>
                                    <form action="{{ route('web.access-requests.update', $request) }}" method="POST"
                                        class="inline-flex">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit"
                                            class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                                            Rechazar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-4 sm:px-6 text-center text-gray-500">No hay solicitudes pendientes.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection