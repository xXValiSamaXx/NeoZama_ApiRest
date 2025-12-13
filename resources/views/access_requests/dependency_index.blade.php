@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900">Mis Solicitudes de Acceso</h1>
                <a href="{{ route('web.access-requests.create') }}"
                    class="bg-indigo-600 px-4 py-2 text-white rounded-md hover:bg-indigo-700">Nueva Solicitud</a>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul role="list" class="divide-y divide-gray-200">
                    @forelse($requests as $request)
                                <li class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-900">{{ $request->document->title }}</p>
                                            <p class="text-gray-500">Estado:
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' :
                        ($request->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div>
                                            @if($request->status === 'approved')
                                                <a href="{{ url('/documents/' . $request->document_id . '/secure-view') }}" target="_blank"
                                                    class="text-indigo-600 hover:text-indigo-900">Ver Documento</a>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                    @empty
                        <li class="px-4 py-4 sm:px-6 text-center text-gray-500">No has realizado solicitudes.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection