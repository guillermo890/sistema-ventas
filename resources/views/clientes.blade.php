
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de clientes</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f2f2f2;
        }

        h1, h2 {
            color: #222;
        }

        form, .cliente {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        input, button {
            padding: 8px;
            margin: 5px 0;
        }

        input {
            width: 95%;
        }

        button {
            cursor: pointer;
        }

        .error {
            color: red;
        }

        .mensaje {
            color: green;
        }
    </style>
</head>
<body>

    <h1>Gestión de clientes</h1>

    <p>
        <a href="{{ route('productos.index') }}">Ir a productos</a>
    </p>

    @if (session('mensaje'))
        <p class="mensaje">{{ session('mensaje') }}</p>
    @endif

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2>Registrar cliente</h2>

    <form action="{{ route('clientes.guardar') }}" method="POST">
        @csrf

        <input type="text" name="nombre"
            placeholder="Nombre completo"
            value="{{ old('nombre') }}" required>

        <input type="email" name="email"
            placeholder="Correo electrónico"
            value="{{ old('email') }}" required>

        <input type="text" name="direccion"
            placeholder="Dirección"
            value="{{ old('direccion') }}" required>

        <input type="text" name="telefono"
            placeholder="Teléfono"
            value="{{ old('telefono') }}" required>

        <button type="submit">Registrar cliente</button>
    </form>

    <h2>Lista de clientes</h2>

    @forelse ($clientes as $cliente)
        <div class="cliente">
            <form action="{{ route('clientes.actualizar', $cliente->id) }}"
                method="POST">
                @csrf
                @method('PUT')

                <p><strong>ID:</strong> {{ $cliente->id }}</p>

                <label>Nombre</label>
                <input type="text" name="nombre"
                    value="{{ $cliente->nombre }}" required>

                <label>Correo electrónico</label>
                <input type="email" name="email"
                    value="{{ $cliente->email }}" required>

                <label>Dirección</label>
                <input type="text" name="direccion"
                    value="{{ $cliente->direccion }}" required>

                <label>Teléfono</label>
                <input type="text" name="telefono"
                    value="{{ $cliente->telefono }}" required>

                <button type="submit">Actualizar cliente</button>
            </form>

            <form action="{{ route('clientes.eliminar', $cliente->id) }}"
                method="POST"
                onsubmit="return confirm('¿Deseas eliminar este cliente?')">
                @csrf
                @method('DELETE')

                <button type="submit">Eliminar cliente</button>
            </form>
        </div>
    @empty
        <p>Aún no hay clientes registrados.</p>
    @endforelse

</body>
</html>
