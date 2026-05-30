<?php

namespace App\Http\Controllers;

use App\Imports\SubjectImport;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('subject/Index', [
            'subjects' => Subject::all(),
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new SubjectImport, $request->file('archivo'));

            return back()->with('success', 'Catálogo de materias actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['archivo' => 'Error al importar materias: '.$e->getMessage()]);
        }
    }

    public function toggleStatus(Subject $subject)
    {
        $subject->update([
            'activo' => ! $subject->activo,
        ]);

        $estado = $subject->activo ? 'habilitada' : 'deshabilitada';

        // Usamos $subject->nombre_materia y comillas dobles
        return back()->with('success', "La materia {$subject->nombre_materia} ha sido {$estado}.");
    }

    public function deshabilitarPensumAntiguo(Request $request)
    {
        Subject::where('pensum', 'Plan 1998')->update(['activo' => false]);

        return back()->with('success', 'Materias del pensum anterior deshabilitadas.');
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

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        //
    }
}
