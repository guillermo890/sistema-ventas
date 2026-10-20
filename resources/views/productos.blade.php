
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de productos</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f2f2f2;
        }

        h1, h2 {
            color: #222;
        }

        form, .producto {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        input, textarea, button {
            padding: 8px;
            margin: 5px 0;
        }

        input, textarea {
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

    <h1>Gestión de productos</h1>
    <p>
        <a href="{{ route('productos.index') }}">Productos</a> |
        <a href="{{ route('clientes.index') }}">Clientes</a> |
        <a href="{{ route ('ventas.index') }}">Ventas</a>
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

    <h2>Registrar producto</h2>

    <form action="{{ route('productos.guardar') }}" method="POST">
        @csrf

        <input type="text" name="nombre" placeholder="Nombre del producto"
            value="{{ old('nombre') }}" required>

        <textarea name="descripcion" placeholder="Descripción">{{ old('descripcion') }}</textarea>

        <input type="number" name="precio" placeholder="Precio"
            step="0.01" min="0" value="{{ old('precio') }}" required>

        <input type="number" name="stock" placeholder="Existencias"
            min="0" value="{{ old('stock', 0) }}" required>

        <button type="submit">Registrar producto</button>
    </form>

    <h2>Lista de productos</h2>

    @forelse ($productos as $producto)
        <div class="producto">
            <form action="{{ route('productos.actualizar', $producto->id) }}"
                method="POST">
                @csrf
                @method('PUT')

                <p><strong>ID:</strong> {{ $producto->id }}</p>

                <label>Nombre</label>
                <input type="text" name="nombre"
                    value="{{ $producto->nombre }}" required>

                <label>Descripción</label>
                <textarea name="descripcion">{{ $producto->descripcion }}</textarea>

                <label>Precio</label>
                <input type="number" name="precio" step="0.01" min="0"
                    value="{{ $producto->precio }}" required>

                <label>Stock</label>
                <input type="number" name="stock" min="0"
                    value="{{ $producto->stock }}" required>

                <button type="submit">Actualizar</button>
            </form>

            <form action="{{ route('productos.eliminar', $producto->id) }}"
                method="POST"
                onsubmit="return confirm('¿Deseas eliminar este producto?')">
                @csrf
                @method('DELETE')

                <button type="submit">Eliminar</button>
            </form>
        </div>
    @empty
        <p>Aún no hay productos registrados.</p>
    @endforelse

</body>
</html>
