@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Stats -->
        <!-- Stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @if(isset($dependenciesCount))
                <!-- Admin View: Dependencies Count -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Dependencias</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ $dependenciesCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- User/Dependency View: Documents Count -->
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Documentos</dt>
                                    <dd class="text-3xl font-semibold text-gray-900">{{ $documentsCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Categorías</dt>
                                <dd class="text-3xl font-semibold text-gray-900">{{ $categoriesCount }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Documents -->
        <!-- Recent Documents (Only for Non-Admins or if exists) -->
    @if($recentDocuments->isNotEmpty())
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Documentos Recientes</h3>
        </div>
        <ul role="list" class="divide-y divide-gray-200">
            @foreach($recentDocuments as $doc)
                <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-100">
                                    <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-indigo-600 truncate">{{ $doc->title }}</div>
                                <div class="flex items-center text-sm text-gray-500">
                                    @if(Auth::user()->isAdmin())
                                        <span class="font-semibold text-gray-700 mr-1">{{ $doc->user->name }}</span>
                                        <span class="mx-1">&bull;</span>
                                    @endif
                                    <span class="truncate">{{ $doc->category->name ?? 'Sin Categoría' }}</span>
                                    <span class="mx-1">&bull;</span>
                                    <span>{{ $doc->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            @if(Auth::user()->isDependency() && !in_array($doc->category_id, Auth::user()->accessibleCategories->pluck('id')->toArray()) )
                                <!-- Logic for special request view if needed, or simple view -->
                                <!-- Usually dependency only sees what they can view. -->
                            @endif
                            <a href="#" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-full shadow-sm text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                Ver
                            </a>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="bg-gray-50 px-4 py-4 sm:px-6 rounded-b-lg">
            <div class="text-sm">
                <a href="{{ route('documents.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Ver todos los documentos <span aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
    </div>
    @endif
        </div>
    </div>
@endsection