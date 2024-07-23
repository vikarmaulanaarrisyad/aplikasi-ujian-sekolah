<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    /**
     * Generate HTML for action buttons.
     *
     * @param AcademicYear $academicYear
     * @return string
     */
    private function actionButtons(AcademicYear $academicYear)
    {
        return '
            <button class="btn btn-link text-primary" onclick="editForm(`' . route('academic-years.show', $academicYear->id) . '`, `' . 'Tahun Ajaran ' . $academicYear->name . ' ' . $academicYear->semester . '`)"><i class="fas fa-edit"></i></button>
            <button class="btn btn-link text-danger" onclick="deleteData(`' . route('academic-years.destroy', $academicYear->id) . '`, `' . 'Tahun Ajaran ' . $academicYear->name . ' ' . $academicYear->semester . '`)"><i class="fas fa-trash-alt"></i></button>
        ';
    }

    public function data(Request $request)
    {
        $query = AcademicYear::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('year')) {
            $query->where('name', 'like', '%' . $request->year . '%');
        }
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Order by start_date
        $query->orderBy('start_date', 'asc'); // Use 'desc' for descending order

        return datatables($query)
            ->addIndexColumn()
            ->addColumn('status', function ($query) {
                return $query->statusBadge();
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

        return view('admin.tahunpelajaran.index', compact('academicYears'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $rules = [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'semester' => 'required|in:Ganjil,Genap',
            'status' => 'required|in:Sedang Berlangsung,Telah Berakhir,Belum Terlaksana',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'message' => 'Data tidak valid'], 422);
        }

        // Cek jika status yang akan ditambahkan adalah "Sedang Berlangsung"
        if ($request->status === 'Sedang Berlangsung') {
            $existingActive = AcademicYear::where('status', 'Sedang Berlangsung')
                ->Orwhere(function ($query) use ($request) {
                    $query->where('start_date', '<=', $request->end_date)
                        ->where('end_date', '>=', $request->start_date);
                })
                ->exists();

            if ($existingActive) {
                return response()->json(['message' => 'Tahun Ajaran yang sedang berlangsung sudah ada dalam rentang waktu yang diberikan.'], 422);
            }
        }

        // Simpan data tahun ajaran baru
        $academicYear = new AcademicYear();
        $academicYear->name = $request->name;
        $academicYear->slug = Str::slug($request->name);
        $academicYear->semester = $request->semester;
        $academicYear->start_date = $request->start_date;
        $academicYear->end_date = $request->end_date;
        $academicYear->status = $request->status;
        $academicYear->save();

        return response()->json(['message' => 'Tahun Ajaran ' . $academicYear->name . ' ' . $academicYear->semester . ' ditambahkan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        return response()->json(['data' => $academicYear]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        // Validasi input
        $rules = [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'semester' => 'required|in:Ganjil,Genap',
            'status' => 'required|in:Sedang Berlangsung,Telah Berakhir,Belum Terlaksana',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'message' => 'Data tidak valid'], 422);
        }

        // Cek jika status yang akan diubah menjadi "Sedang Berlangsung"
        if ($request->status === 'Sedang Berlangsung') {
            $existingActive = AcademicYear::where('status', 'Sedang Berlangsung')
                ->where('id', '!=', $academicYear->id) // Kecualikan tahun ajaran yang sedang diperbarui
                ->where(function ($query) use ($request) {
                    $query->where('start_date', '<=', $request->end_date)
                        ->where('end_date', '>=', $request->start_date);
                })
                ->exists();

            if ($existingActive) {
                return response()->json(['message' => 'Tahun Ajaran yang sedang berlangsung sudah ada dalam rentang waktu yang diberikan.'], 422);
            }
        }

        // Perbarui data tahun ajaran
        $academicYear->name = $request->name;
        $academicYear->slug = Str::slug($request->name);
        $academicYear->semester = $request->semester;
        $academicYear->start_date = $request->start_date;
        $academicYear->end_date = $request->end_date;
        $academicYear->status = $request->status;
        $academicYear->save();

        return response()->json(['message' => 'Tahun Ajaran ' . $academicYear->name . ' ' . $academicYear->semester . ' diperbarui.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AcademicYear  $academicYear
     * @return \Illuminate\Http\Response
     */
    public function destroy(AcademicYear $academicYear)
    {
        // Cek jika status adalah "Sedang Berlangsung"
        if ($academicYear->status === 'Sedang Berlangsung') {
            return response()->json([
                'success' => false,
                'message' => 'Data dengan status "Sedang Berlangsung" tidak dapat dihapus.'
            ], 403); // 403 Forbidden
        }

        try {
            // Hapus record dari database
            $academicYear->delete();

            // Kembalikan respons sukses
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.'
            ], 200);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, kembalikan respons error
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
