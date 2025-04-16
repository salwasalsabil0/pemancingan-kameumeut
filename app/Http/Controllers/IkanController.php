<?php

namespace App\Http\Controllers;

use App\Models\Ikan;
use Illuminate\Http\Request;
use App\Models\FieldData; // Import model Kolam

class IkanController extends Controller
{
    // Tampilkan semua data ikan
    public function index() {
        $ikan = Ikan::all();
        return view('admin.fields.data.ikan', compact('ikan'));
    }

    // Form tambah ikan
    public function create() {
        $field_data = FieldData::all(); // Ambil semua kolam
    return view('admin.fields.data.create_ikan', compact('field_data'));
    }

    // Simpan data ikan
    public function store(Request $request) {
        $request->validate([
            'type_ikan' => 'required',
            'perkg_stock' => 'required',
            'perkg_price' => 'required|gt:0',
            'field_data_id' => 'required|integer|exists:field_data,id',
        ],

        [
            'type_ikan.required' => 'Jenis ikan harus diisi',
            'perkg_stock.required' => 'Stock tersedia harus diisi',
            'perkg_price.required' => 'Harga /kg harus diisi',
            'perkg_price.gt' => 'Harga /kg tidak boleh 0',
        ]);

        Ikan::create([
            'type_ikan' => $request->type_ikan,
            'perkg_stock' => $request->perkg_stock,
            'perkg_price' => $request->perkg_price* 1000, // Konversi ke rupiah
            'field_data_id' => $request->field_data_id,
        ]);

        return redirect()->route('admin.indexIkan')->with('success', 'Data ikan berhasil ditambahkan!');
    }

    // Form edit ikan
    public function edit(Ikan $ikan) {
        return view('admin.fields.data.edit_ikan', compact('ikan'));
    }

    // Update data ikan
    public function update(Request $request, Ikan $ikan) {
        $request->validate([
            'type_ikan' => 'required|string|max:100',
            'perkg_stock' => 'required|integer|min:0',
            'perkg_price' => 'required|numeric|min:0',
        ]);

        $ikan->update($request->all());

        return redirect()->route('admin.fields.data.ikan')->with('success', 'Data ikan berhasil diperbarui!');
    }

    // Hapus data ikan
    public function destroy($id) {
        $ikan = Ikan::find($id);
        $ikan->delete();

        return redirect()->route('admin.indexIkan')->with('success', 'Data ikan berhasil dihapus!');
    }
}
