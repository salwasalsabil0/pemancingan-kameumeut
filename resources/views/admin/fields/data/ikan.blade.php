@extends('admin.layouts.main')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Tempat Pemancingan</h1>
    </div>
    @if (session('success'))
        <div class="alert alert-success d-flex justify-content-between align-items-center">
            {{ session('success') }}
            <button type="button" class="btn-close flex-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger d-flex justify-content-between align-items-center">
            {{ session('error') }}
            <button type="button" class="btn-close flex-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="py-3">
        <a href="{{ route('admin.createIkan') }}" class="btn btn-primary">Tambah</a>
    </div>
    <!-- Content Row -->
    <div class="row">

        <!-- Area Chart -->
        <div class="col">
            <table class="table table-hover text-nowrap" id="myTable" style="width: 100%">
                <thead>
                    <tr>
                        <th class="text-center align-middle" rowspan="2">#</th>
                        <th class="text-center align-middle" rowspan="2">Aksi</th>
                        <th class="text-center align-middle" rowspan="2">Jenis Kolam</th>
                        <th class="text-center align-middle" rowspan="2">Jenis Ikan</th>
                        <th class="text-center align-middle" rowspan="2">Stock</th>
                        <th class="text-center align-middle" colspan="2">Harga (/kg)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ikan as $i => $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#detailModal{{ $data->id }} "><i class="fa fa-eye"></i></a>

                                    {{-- Modal --}}

                                    <a href="" data-bs-toggle="tooltip"
                                        data-bs-placement="top" data-bs-title="Edit" class="btn btn-sm btn-warning"><i
                                            class="fa fa-edit"></i>
                                    </a>

                                    <form class="deleteForm" action="{{ route('admin.deleteIkan', $item->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete"
                                            type="button" class="btn btn-sm btn-danger deleteButton">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="text-center">{{ $data->field_type }}</td>
                            <td class="text-center">{{ $item->type_ikan }}</td>
                            <td class="text-center">{{ $item->perkg_stock }}</td>
                            <td class="text-center">Rp {{ number_format($item->perkg_price, 0, ',', '.') }}</td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endsection
    @section('script')
        <script>
            var deleteButtons = document.querySelectorAll('.deleteButton');

            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var form = this.closest('.deleteForm');

                    Swal.fire({
                        title: 'Hapus Request',
                        text: "Apakah Anda Yakin Untuk Menghapus?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endsection
