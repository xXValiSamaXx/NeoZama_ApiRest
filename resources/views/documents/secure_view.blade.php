<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vista Segura - {{ $document->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Prevent selection */
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Disable print */
        @media print {
            body {
                display: none;
            }
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 5rem;
            color: rgba(0, 0, 0, 0.05);
            pointer-events: none;
            z-index: 50;
        }
    </style>
    <script>
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.addEventListener('keydown', event => {
            if ((event.ctrlKey || event.metaKey) && event.key === 'p') event.preventDefault();
        });
    </script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center relative">
    <div class="watermark">CONFIDENCIAL - {{ Auth::user()->name }}</div>

    <div class="max-w-4xl w-full bg-white shadow-lg rounded-lg overflow-hidden p-8">
        <div class="border-b pb-4 mb-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $document->title }}</h1>
                <p class="text-sm text-gray-500">Propietario: {{ $document->user->name }}</p>
            </div>
            <div class="text-xs text-red-500 font-bold uppercase tracking-wide">Vista Protegida</div>
        </div>

        <div class="prose max-w-none mb-8">
            <p>{{ $document->description ?? 'Sin descripción.' }}</p>

            <div
                class="aspect-w-16 aspect-h-9 bg-gray-200 mt-4 rounded flex items-center justify-center h-64 border-2 border-dashed border-gray-400">
                <!-- If it's an image, show it. If PDF, embed securely without download toolbar if possible -->
                <!-- For proto, just partial preview or mock -->
                <p class="text-gray-500">[Previsualización Segura del Documento]</p>
                <!-- Example image protected by CSS -->
                <!-- <img src="..." class="pointer-events-none" /> -->
            </div>
            <div class="mt-4 text-center">
                <p class="text-xs text-gray-400">Acceso registrado: {{ now() }} - IP: {{ request()->ip() }}</p>
            </div>
        </div>

        <div class="text-center">
            <button onclick="window.close()"
                class="bg-gray-800 text-white px-6 py-2 rounded shadow hover:bg-gray-700">Cerrar Visualizador</button>
        </div>
    </div>
</body>

</html>