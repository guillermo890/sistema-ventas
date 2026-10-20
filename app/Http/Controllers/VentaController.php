<?php

namespace App\Http\Controllers;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['cliente', 'detalles.producto'])
            ->latest()
            ->get();

        $clientes = Cliente::all();
        $productos = Producto::where('stock', '>', 0)->get();

        return view('ventas', compact('ventas', 'clientes', 'productos'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_venta' => 'required|date',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|distinct|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request) {
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'fecha_venta' => $request->fecha_venta,
                'estado' => 'pendiente',
                'total' => 0
            ]);

            $totalVenta = 0;

            foreach ($request->productos as $detalle) {
                $producto = Producto::where(
                    'id',
                    $detalle['producto_id']
                )->lockForUpdate()->firstOrFail();

                $cantidad = (int) $detalle['cantidad'];

                if ($producto->stock < $cantidad) {
                    throw new \Exception(
                        'No hay suficiente stock de ' . $producto->nombre
                    );
                }

                $precio = $producto->precio;
                $subtotal = $precio * $cantidad;

                $venta->detalles()->create([
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'total' => $subtotal
                ]);

                $producto->decrement('stock', $cantidad);

                $totalVenta += $subtotal;
            }

            $venta->update(['total' => $totalVenta]);
        });

        return redirect()->route('ventas.index')
            ->with('mensaje', 'Venta registrada correctamente');
    }

    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => [
                'required',
                Rule::in(['pendiente', 'confirmada', 'cancelada'])
            ]
        ]);

        DB::transaction(function () use ($request, $id) {
            $venta = Venta::with('detalles.producto')
                ->lockForUpdate()
                ->findOrFail($id);

            $nuevoEstado = $request->estado;

            if ($venta->estado === $nuevoEstado) {
                return;
            }

            if ($venta->estado !== 'pendiente') {
                throw new \Exception(
                    'Solo se puede cambiar el estado de una venta pendiente.'
                );
            }

            if ($nuevoEstado === 'cancelada') {
                foreach ($venta->detalles as $detalle) {
                    $detalle->producto->increment(
                        'stock',
                        $detalle->cantidad
                    );
                }
            }

            $venta->update(['estado' => $nuevoEstado]);
        });

        return redirect()->route('ventas.index')
            ->with('mensaje', 'Estado de la venta actualizado');
    }

    public function eliminar($id)
    {
        DB::transaction(function () use ($id) {
            $venta = Venta::with('detalles.producto')
                ->lockForUpdate()
                ->findOrFail($id);

            if (!in_array($venta->estado, ['pendiente', 'cancelada'])) {
                throw new \Exception(
                    'Solo se pueden eliminar ventas pendientes o canceladas.'
                );
            }

            if ($venta->estado === 'pendiente') {
                foreach ($venta->detalles as $detalle) {
                    $detalle->producto->increment(
                        'stock',
                        $detalle->cantidad
                    );
                }
            }

            $venta->delete();
        });

        return redirect()->route('ventas.index')
            ->with('mensaje', 'Venta eliminada correctamente');
    }
}
