<?php

namespace App\Http\Controllers;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();

        return view('clientes', compact('clientes'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20'
        ]);

        Cliente::create($request->all());

        return redirect()->route('clientes.index')
            ->with('mensaje', 'Cliente registrado correctamente');
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $id,
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20'
        ]);

        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());

        return redirect()->route('clientes.index')
            ->with('mensaje', 'Cliente actualizado correctamente');
    }

    public function eliminar($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('mensaje', 'Cliente eliminado correctamente');
    }
}
