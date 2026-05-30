<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function toggleStatus($id)
    {
        // Buscamos al responsable (sea docente o auxiliar)
        $borrower = Borrower::findOrFail($id);

        // Cambiamos el estado al contrario del que tenga
        $borrower->update([
            'activo' => ! $borrower->activo,
        ]);

        $mensaje = $borrower->activo ? 'activado' : 'desactivado';

        return back()->with('success', "El usuario ha sido {$mensaje} correctamente.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        //
    }
}
