
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de ventas</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f2f2f2;
        }

        form, .venta {
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        input, select, button {
            padding: 8px;
            margin: 5px 0;
        }

        .mensaje {
            color: green;
        }

        .error {
            color: red;
        }

        .detalle {
            margin: 10px 0;
        }
    </style>
</head>
<body>

    <h1>Gestión de ventas</h1>

    <p>
        <a href="{{ route('productos.index') }}">Productos</a> |
        <a href="{{ route('clientes.index') }}">Clientes</a>
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

    <h2>Registrar venta</h2>

    <form action="{{ route('ventas.guardar') }}" method="POST">
        @csrf

        <label>Cliente</label><br>
        <select name="cliente_id" required>
            <option value="">Selecciona un cliente</option>
            @foreach ($clientes as $cliente)
                <option value="{{ $cliente->id }}">
                    {{ $cliente->nombre }}
                </option>
            @endforeach
        </select>

        <br>

        <label>Fecha de venta</label><br>
        <input type="date" name="fecha_venta"
            value="{{ date('Y-m-d') }}" required>

        <h3>Productos de la venta</h3>

        @for ($i = 0; $i < 3; $i++)
            <div class="detalle">
                <label>Producto {{ $i + 1 }}</label><br>

                <select name="productos[{{ $i }}][producto_id]">
                    <option value="">Selecciona un producto</option>

                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}">
                            {{ $producto->nombre }}
                            - ${{ number_format($producto->precio, 2) }}
                            (Stock: {{ $producto->stock }})
                        </option>
                    @endforeach
                </select>

                <input type="number"
                    name="productos[{{ $i }}][cantidad]"
                    min="1"
                    placeholder="Cantidad">
            </div>
        @endfor

        <button type="submit">Registrar venta</button>
    </form>

    <h2>Ventas registradas</h2>

    @forelse ($ventas as $venta)
        <div class="venta">
            <h3>Venta #{{ $venta->id }}</h3>

            <p><strong>Cliente:</strong> {{ $venta->cliente->nombre }}</p>
            <p><strong>Fecha:</strong> {{ $venta->fecha_venta }}</p>
            <p><strong>Estado:</strong> {{ ucfirst($venta->estado) }}</p>

            <h4>Detalle de productos</h4>

            <table border="1" cellpadding="8" cellspacing="0">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                </tr>

                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>${{ number_format($detalle->total, 2) }}</td>
                    </tr>
                @endforeach
            </table>

            <p>
                <strong>Total:</strong>
                ${{ number_format($venta->total, 2) }}
            </p>

            @if ($venta->estado === 'pendiente')
                <form action="{{ route('ventas.estado', $venta->id) }}"
                    method="POST">
                    @csrf
                    @method('PUT')

                    <select name="estado" required>
                        <option value="confirmada">Confirmar venta</option>
                        <option value="cancelada">Cancelar venta</option>
                    </select>

                    <button type="submit">Actualizar estado</button>
                </form>
            @endif

            @if (in_array($venta->estado, ['pendiente', 'cancelada']))
                <form action="{{ route('ventas.eliminar', $venta->id) }}"
                    method="POST"
                    onsubmit="return confirm('¿Deseas eliminar esta venta?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit">Eliminar venta</button>
                </form>
            @endif
        </div>
    @empty
        <p>Aún no hay ventas registradas.</p>
    @endforelse

</body>
</html>
