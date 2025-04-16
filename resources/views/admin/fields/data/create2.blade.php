@extends('admin.layouts.main')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Data Lapak</h1>
    </div>
    {{-- @if (session('error'))
    <div class="alert alert-danger d-flex justify-content-between align-items-center">
        {{ session('error') }}
        <button type="button" class="btn-close flex-end" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif --}}
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form class="form-card p-4" action="{{ route('admin.fieldStore') }}" method="POST" onsubmit="removeFormattingBeforeSubmit(this)" enctype="multipart/form-data">
                    @csrf
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-12 flex-column d-flex">
                            <label class="form-control-label">Nama Lapak<span class="text-danger"> *</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan Nama Lapak">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-12 flex-column d-flex">
                            <label class="form-control-label">Deskripsi Lapak<span class="text-danger"> *</span>
                            </label>
                            <textarea class="form-control" placeholder="Masukkan Deskripsi Lapak" id="floatingTextarea2" name="description"
                                style="height: 100px">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-sm-3 flex-column d-flex">
                            <label class="form-control-label">Jenis Kolam<span class="text-danger"> *</span></label>
                            <select class="form-select @error('field_type') is-invalid @enderror" aria-label="field_type" id="field_type"
                                name="field_type">
                                <option selected disabled>- Pilih jenis kolam -</option>
                                <option value="Kilo Jebur" {{ old('field_type') == 'Kilo Jebur' ? 'selected' : '' }}>Kilo Jebur</option>
                                <option value="Kilo Angkat" {{ old('field_type') == 'Kilo Angkat' ? 'selected' : '' }}>Kilo Angkat</option>
                            </select>
                            @error('field_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-sm-3 flex-column d-flex">
                            <label class="form-control-label">Pilihan Ikan<span class="text-danger"> *</span></label>
                            <select class="form-select @error('field_material') is-invalid @enderror" aria-label="field_material" id="field_material"
                                name="field_material">
                                <option selected disabled>- Pilih jenis ikan -</option>
                                <option value="Mas" {{ old('field_material') == 'Mas' ? 'selected' : '' }}>Mas</option>
                                <option value="Mujaer" {{ old('field_material') == 'Mujaer' ? 'selected' : '' }}>Mujaer</option>
                                <option value="Jambal" {{ old('field_material') == 'Jambal' ? 'selected' : '' }}>Jambal</option>
                                <option value="Bawal" {{ old('field_material') == 'Bawal' ? 'selected' : '' }}>Bawal</option>
                            </select>
                            @error('field_material')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- tes -->
                        <div class="form-group col-sm-3 flex-column d-flex">
                            <label class="form-control-label">Jumlah Ikan (kg)<span class="text-danger"> *</span></label>
                            <select class="form-select @error('field_location') is-invalid @enderror" aria-label="field_location" id="field_location"
                                name="field_location">
                                <option selected disabled>- Pilih berapa kilo -</option>
                                <option value="1 kg" {{ old('field_location') == '1 kg' ? 'selected' : '' }}>1 kg</option>
                                <option value="2 kg" {{ old('field_location') == '2 kg' ? 'selected' : '' }}>2 kg</option>
                            </select>
                            @error('field_location')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>                                             
                    </div>
                    <div class="row justify-content-between">
                        <div class="form-group col-sm-3 flex-column d-flex">
                            <label class="form-control-label">Harga Sesi Pagi<span class="text-danger"> *</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" id="morning_price" name="morning_price" class="form-control" value="{{ old('morning_price') }}" placeholder="Masukkan Harga" oninput="formatCurrency(this)">
                            </div>
                            @error('morning_price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row justify-content-between">
                        <div class="form-group col-sm-3 flex-column d-flex">
                            <label class="form-control-label">Harga Sesi Malam<span class="text-danger"> *</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" id="night_price" name="night_price" value="{{ old('night_price') }}" class="form-control" placeholder="Masukkan Harga" oninput="formatCurrency(this)">
                            </div>
                            @error('night_price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>   
                    </div>
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-12 flex-column d-flex">
                            <label class="form-control-label">Thumbnail<span class="text-danger"> *</span>
                            </label>
                            <input class="form-control" type="file" name="thumbnail" id="thumbnail">
                            @error('thumbnail')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row justify-content-end py-4 px-4">
                        <div class="form-group col-sm-2">
                            <button type="submit" class="btn-block btn-primary">Create</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function formatCurrency(input) {
            // Remove non-numeric characters
            let numericValue = input.value.replace(/[^0-9]/g, '');
    
            // Format the value with commas for thousands
            let formattedValue = new Intl.NumberFormat('id-ID').format(numericValue);
    
            // Add the "Rp" prefix
            formattedValue = formattedValue;
    
            // Update the input value
            input.value = formattedValue;
        }
    
        // Remove formatting before submitting the form
        function removeFormattingBeforeSubmit(form) {
            let priceInput = form.elements['morning_price'];
            let priceInput2 = form.elements['night_price'];
            priceInput.value = priceInput.value.replace(/[^0-9]/g, '');
            priceInput2.value = priceInput2.value.replace(/[^0-9]/g, '');
        }
    </script>        
@endsection
