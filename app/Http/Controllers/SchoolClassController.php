<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchoolClassController extends Controller
{

    /**
     * Generate HTML for action buttons.
     *
     * @param SchoolClass $schoolClass
     * @return string
     */
    private function actionButtons(SchoolClass $schoolClass)
    {
        return '
            <button class="btn btn-link text-primary" onclick="editForm(`' . route('schoolClasses.show', $schoolClass->id) . '`, `' . 'Data ' . $schoolClass->name . ' ' . $schoolClass->academicYear->name . ' ' . $schoolClass->academicYear->semester . '`)"><i class="fas fa-edit"></i></button>
            <button class="btn btn-link text-danger" onclick="deleteData(`' . route('schoolClasses.destroy', $schoolClass->id) . '`, `' . 'Data ' . $schoolClass->name . ' ' . $schoolClass->academicYear->name . ' ' . $schoolClass->academicYear->semester . '`)"><i class="fas fa-trash-alt"></i></button>
        ';
    }

    public function data(Request $request)
    {
        // Build the query with optional filters
        $query = SchoolClass::query()
            ->when($request->has('year') && $request->year != "", function ($query) use ($request) {
                $query->whereHas('academicYear', function ($q) use ($request) {
                    $q->where('id', $request->year);
                });
            })
            ->when($request->has('level') && $request->level != "", function ($query) use ($request) {
                $query->where('level', $request->level);
            });

        // Return DataTables response
        return datatables($query)
            ->addIndexColumn()
            ->editColumn('academic_year_id', function ($query) {
                return $query->academicYear->name . ' ' . $query->academicYear->semester;
            })
            ->addColumn('action', function ($query) {
                return $this->actionButtons($query);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicYears = AcademicYear::all();

        return view('admin.schoolClasses.index', compact('academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'name' => 'required',
            'level' => 'required',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Handle validation failure
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Data tidak valid'
            ], 422);
        }

        // Check for active academic year
        $academicYearActive = AcademicYear::where('status', 'Sedang Berlangsung')->first();
        if (!$academicYearActive) {
            return response()->json([
                'message' => 'Tidak ada data tahun ajaran yang aktif, silakan atur di menu tahun pelajaran'
            ], 422);
        }

        // Create and save new school class
        $schoolClass = new SchoolClass();
        $schoolClass->name = $request->name;
        $schoolClass->level = $request->level;
        $schoolClass->academic_year_id = $academicYearActive->id;
        $schoolClass->save();

        // Return success response
        return response()->json([
            'message' => 'Data kelas berhasil ditambahkan.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolClass $schoolClass)
    {
        return response()->json(['data' => $schoolClass]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchoolClass $schoolClass)
    {
        // Define validation rules
        $rules = [
            'name' => 'required',
            'level' => 'required',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        // Handle validation failure
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Data tidak valid'
            ], 422);
        }

        // Check for active academic year
        $academicYearActive = AcademicYear::where('status', 'Sedang Berlangsung')->first();
        if (!$academicYearActive) {
            return response()->json([
                'message' => 'Tidak ada data tahun ajaran yang aktif, silakan atur di menu tahun pelajaran'
            ], 422);
        }

        // Update the SchoolClass instance
        $schoolClass->name = $request->input('name');
        $schoolClass->level = $request->input('level');
        $schoolClass->academic_year_id = $academicYearActive->id;
        $schoolClass->save();

        // Return success response
        return response()->json([
            'message' => 'Data kelas berhasil diperbarui.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolClass $schoolClass)
    {
        try {
            // Attempt to delete the SchoolClass instance
            $schoolClass->delete();

            // Return success response
            return response()->json([
                'message' => 'Data kelas berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors that occur during deletion
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus data kelas.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
