<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <style>
        body {
            font-family: monospace;
            background: #1a1a1a;
            color: #00ff00;
            padding: 20px;
        }

        .error-box {
            background: #2a2a2a;
            border: 2px solid #ff0000;
            padding: 20px;
            margin: 20px 0;
        }

        h1 {
            color: #ff0000;
        }

        pre {
            background: #000;
            padding: 15px;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <h1>🚨 Error Detallado</h1>
    <div class="error-box">
        <h2>Mensaje:</h2>
        <p>{{ $error }}</p>
    </div>
    <div class="error-box">
        <h2>Stack Trace:</h2>
        <pre>{{ $trace }}</pre>
    </div>
    <p><a href="{{ route('dashboard') }}" style="color: #00ff00;">← Volver al Dashboard</a></p>
</body>

</html>