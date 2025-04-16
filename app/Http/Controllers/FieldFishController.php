<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Field;
use App\Models\Fish;
use App\Models\PondType;
use App\Models\FieldSchedule;
use App\Models\ScheduleAvailability;

use Illuminate\Support\Facades\Storage;

class FieldFishController extends Controller
{
    public function indexFields()
    {
        $user = auth()->user();
        $field = Field::all();
        $fish = Fish::all();

        return view('admin.fields.data.index', compact('field', 'fish'));
    }

// Data lapak 
    public function createFields()
    {
        $pondTypes = PondType::all();
        return view('admin.fields.data.create',compact('pondTypes'));
    }

    public function storeFields(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'pond_type_id' => 'required|exists:pond_types,id',
            'morning_price' => 'required|gt:0',
            'night_price' => 'required|gt:0',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:10240', // Adjust the validation rules as needed
        ], [
            'name.required' => 'Nama harus diisi',
            'description.required' => 'Deskripsi harus diisi',
            'pond_type_id.required' => 'Jenis kolam harus diisi',
            'morning_price.required' => 'Harga jam pagi harus diisi',
            'morning_price.gt' => 'Harga jam pagi tidak boleh 0',
            'night_price.required' => 'Harga jam malam harus diisi',
            'night_price.gt' => 'Harga jam malam tidak boleh 0',
            'thumbnail.required' => 'Thumbnail harus diisi',
            'thumbnail.image' => 'File harus berupa gambar',
            'thumbnail.mimes' => 'File harus berupa jpeg, png, atau jpg',
            'thumbnail.max' => 'File tidak boleh lebih dari 2 MB',
        ]);

        $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public'); // Assuming 'thumbnails' is the storage folder

        // Ambil ID kolam berdasarkan pond_type_id
        $pondType = PondType::findOrFail($request->pond_type_id);

        Field::create([
            'name' => $request->name,
            'description' => $request->description,
            'pond_type_id' => $pondType->id,
            'morning_price' => $request->morning_price,
            'night_price' => $request->night_price,
            'thumbnail' => $thumbnailPath,
        ]);

        session()->flash('success_lapak', 'Data lapak berhasil ditambahkan');
        return redirect()->route('admin.fieldsIndex');
    }

    public function editFields($id)
    {
        $field = Field::find($id);
        $pondTypes = PondType::all();
        if (!$field) {
            return view('admin.404');
        }

        return view('admin.fields.data.edit', compact('field','pondTypes'));
    }
    public function updateFields(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'pond_type_id' => 'required|exists:pond_types,id',
            'morning_price' => 'required|gt:0',
            'night_price' => 'required|gt:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // Allow empty or image files
        ], [
            'name.required' => 'Nama harus diisi',
            'description.required' => 'Deskripsi harus diisi',
            'pond_type_id.required' => 'Jenis kolam harus diisi',
            'morning_price.required' => 'Harga jam pagi harus diisi',
            'morning_price.gt' => 'Harga jam pagi tidak boleh 0',
            'night_price.required' => 'Harga jam malam harus diisi',
            'night_price.gt' => 'Harga jam malam tidak boleh 0',
            'thumbnail.image' => 'File harus berupa gambar',
            'thumbnail.mimes' => 'File harus berupa jpeg, png, atau jpg',
            'thumbnail.max' => 'File tidak boleh lebih dari 2 MB',
        ]);

        $field = Field::find($id);

        // Hapus gambar lama jika ada gambar baru yang diberikan
        if ($request->hasFile('thumbnail')) {
            // Hapus gambar lama dari sistem penyimpanan
            if ($field->thumbnail) {
                Storage::disk('public')->delete($field->thumbnail);
            }

            // Simpan gambar baru dan dapatkan path-nya
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $field->thumbnail = $thumbnailPath;
        }
        
        // Ambil ID kolam berdasarkan pond_type_id
        $pondType = PondType::findOrFail($request->pond_type_id);
        $field->update([
            'name' => $request->name,
            'description' => $request->description,
            'pond_type_id' => $pondType->id,
            'morning_price' => $request->morning_price,
            'night_price' => $request->night_price,
            //'thumbnail' => $thumbnailPath,
        ]);

        session()->flash('success_lapak', 'Data lapak berhasil diubah');
        return redirect()->route('admin.fieldsIndex');
    }
    public function destroyFields($id)
    {
        $field = Field::find($id);
        if (!$field) {
            session()->flash('error_lapak', 'Data lapak tidak ditemukan');
            return redirect()->back();
        }

        $fieldUsed = $field->bookings()->exists();
        if($fieldUsed){
            session()->flash('error_lapak', 'Data lapak telah digunakan');
            return redirect()->back();
        }

        // Hapus file gambar dari penyimpanan
        Storage::disk('public')->delete($field->thumbnail);

        $field->delete();
        session()->flash('success_lapak', 'Data lapak berhasil dihapus');
        return redirect()->route('admin.fieldsIndex');
    }

    
//Data ikan
        public function create() {
            $pondTypes = PondType::all();
            $field = Field::all(); // Ambil semua kolam
        return view('admin.fields.data.create_ikan', compact('field','pondTypes'));
        }
    
        // Simpan data ikan
        public function store(Request $request)
    {
        $request->validate([
            'type_ikan' => 'required',
            'perkg_stock' => 'required',
            'perkg_price' => 'required|numeric|gt:0',
            'pond_type_id' => 'required|exists:pond_types,id',
        ], [
            'type_ikan.required' => 'Jenis ikan harus diisi',
            'perkg_stock.required' => 'Stock tersedia harus diisi',
            'perkg_price.required' => 'Harga per kilogram harus diisi',
            'perkg_price.gt' => 'Harga per kilogram tidak boleh 0',
            'pond_type_id.required' => 'Jenis kolam harus diisi',
        ]);
        // Ambil ID kolam berdasarkan pond_type_id
        $pondType = PondType::findOrFail($request->pond_type_id);
        Fish::create([
            'type_ikan' => $request->type_ikan,
            'perkg_stock' => $request->perkg_stock,
            'perkg_price' => $request->perkg_price,
            'pond_type_id' => $pondType->id,
        ]);

        session()->flash('success_ikan', 'Data ikan berhasil ditambahkan');
        return redirect()->route('admin.fieldsIndex');
    }
    public function edit($id)
    {
        $fish = Fish::find($id);
        $pondTypes = PondType::all();
        if (!$fish) {
            return view('admin.404');
        }

        return view('admin.fields.data.edit_ikan', compact('fish','pondTypes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type_ikan' => 'required',
            'perkg_stock' => 'required',
            'perkg_price' => 'required|numeric|gt:0',
            'pond_type_id' => 'required|exists:pond_types,id',
        ], [
            'type_ikan.required' => 'Jenis ikan harus diisi',
            'perkg_stock.required' => 'Stock tersedia harus diisi',
            'perkg_price.required' => 'Harga per kilogram harus diisi',
            'perkg_price.gt' => 'Harga per kilogram tidak boleh 0',
            'pond_type_id.required' => 'Jenis kolam harus diisi',
        ]);
        $fish = Fish::find($id);
        // Ambil ID kolam berdasarkan pond_type_id
        $pondType = PondType::findOrFail($request->pond_type_id);
        $fish->update([
            'type_ikan' => $request->type_ikan,
            'perkg_stock' => $request->perkg_stock,
            'perkg_price' => $request->perkg_price,
            'pond_type_id' => $pondType->id,
        ]);


        session()->flash('success_ikan', 'Data ikan berhasil diubah');
        return redirect()->route('admin.fieldsIndex');
    }
    
        // Hapus data ikan
        public function destroy($id) {
            $fish = Fish::find($id);
            
            $fishUsed = $fish->bookings()->exists();
            if($fishUsed){
                session()->flash('error_ikan', 'Data ikan telah digunakan');
                return redirect()->back();
            }
            $fish->delete();
            session()->flash('success_ikan', 'Data ikan berhasil dihapus');
        return redirect()->route('admin.fieldsIndex');
            
    }

// Field Schedule
public function indexSchedule()
{
    $fieldSchedule = FieldSchedule::all();
    return view('admin.fields.schedule.index', compact('fieldSchedule'));
}

public function updateSchedule(Request $request, $id)
{
    $fieldSchedule = FieldSchedule::find($id);

    // Menggunakan operasi ternary untuk menetapkan nilai is_active sesuai dengan checkbox
    $isActive = $request->has('is_active') ? 1 : 0;

    $fieldSchedule->update([
        'is_active' => $isActive,
    ]);

    session()->flash('success', 'Jadwal lapak berhasil diubah');
    return redirect()->route('admin.scheduleIndex');
}

public function indexScheduleActive()
{
    $scheduleAvailable = ScheduleAvailability::with(['booking', 'field', 'fieldSchedule'])
        ->whereHas('booking', function ($query) {
            $query->where(function ($query) {
                $query->where('is_member', 0)
                    ->orWhere(function ($query) {
                        $query->where('is_member', 1)
                            ->where('booking_status', 0);
                    });
            });
        })
        ->orderBy('id', 'desc')
        ->latest()
        ->get();

    return view('admin.fields.schedule.scheduleActive', compact('scheduleAvailable'));
}

public function destroyScheduleActive(Request $request)
{
    $ids = $request->input('ids', []);

    // Pastikan $ids adalah array yang valid dan tidak kosong
    if (!is_array($ids) || empty($ids)) {
        return response()->json(['error' => 'Invalid or empty IDs array'], 400);
    }

    try {
        // Periksa apakah ada jadwal yang terkait dengan booking yang memiliki status 4
        $bookingsWithPaidStatus = ScheduleAvailability::whereIn('id', $ids)
            ->whereHas('booking', function ($query) {
                $query->where('booking_status', 4);
            })->exists();

        if ($bookingsWithPaidStatus) {
            return response()->json(['error' => 'Schedule cannot be deleted because booking status is already paid'], 400);
        }

        // Hapus data berdasarkan ID yang diberikan
        ScheduleAvailability::whereIn('id', $ids)->delete();

        // Beri respons sukses
        return response()->json(['message' => 'Data successfully removed'], 200);
    } catch (\Exception $e) {
        // Tangani jika terjadi kesalahan saat menghapus data
        return response()->json(['error' => 'Failed to remove data'], 500);
    }
}
}