@extends('admin.layouts.main')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Data Lapak</h1>
    </div>
    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form class="form-card p-4" action="{{ route('admin.fieldsStore') }}" method="POST" onsubmit="removeFormattingBeforeSubmit(this)" enctype="multipart/form-data">
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

                    <!-- Jenis Kolam (Pond Type) -->
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-12 flex-column d-flex">
                            <label for="pond_type_id" class="form-control-label">Jenis Kolam <span class="text-danger">*</span></label>
                            <select id="pond_type_id" name="pond_type_id" class="form-select" required>
                                <option value="" disabled selected>- Pilih jenis kolam -</option>
                                @foreach($pondTypes as $pondType)
                                    <option value="{{ $pondType->id }}" {{ old('pond_type_id') == $pondType->id ? 'selected' : '' }}>
                                        {{ $pondType->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pond_type_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row justify-content-between">
                        <div class="form-group col-sm-3 flex-column d-flex">
                            <label class="form-control-label">Harga Jam Pagi<span class="text-danger"> *</span></label>
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
                            <label class="form-control-label">Harga Jam Malam<span class="text-danger"> *</span></label>
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
