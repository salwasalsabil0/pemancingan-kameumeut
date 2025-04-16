@extends('admin.layouts.main')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Data Antrean</h1>
    </div>
    @if (session('success'))
        <div class="alert alert-success d-flex justify-content-between align-items-center">
            {{ session('success') }}
            <button type="button" class="btn-close flex-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    
    <!-- Content Row -->
    <div class="row">

        <!-- Area Chart -->
        <div class="col">
    @if ($queue->isEmpty())
        <table class="table table-hover text-nowrap" id="myTable" style="width: 100%">
            <thead>
                <tr>
                    <th>No. Antrean</th>
                    <th>Waktu Kedatangan</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Aksi</th></tr>
            </thead>
            
        </table>
    @else
        <table class="table table-hover text-nowrap" id="myTable" style="width: 100%">
            <thead>
                <tr>
                    <th>No. Antrean</th>
                    <th>Waktu Kedatangan</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($queue as $q)
                    <tr>
                        <td>{{ $q->queue_number }}</td>
                        <td>{{ $q->created_at }}</td>
                        <td>{{ $q->user->username }}</td>
                        <td>{{ $q->user->name }}</td>
                            <td>
                                @if ($q->served == 'Sedang Dilayani')
                                    <span class="badge bg-primary text-white">Sedang Dilayani</span>
                                @elseif ($q->status == 'Selesai')
                                    <span class="badge bg-success text-white">Selesai</span>
                                @else
                                    <span class="badge bg-warning text-white">Menunggu</span>
                                @endif
                            </td>

                        
                        <td class="text-center">
                            <div class="d-flex justify-content gap-1">
                                <!-- Form untuk mengupdate status menjadi "Selesai" -->
                                <form action="{{ route('admin.queue.update', $q->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('POST')
                                    <button data-bs-toggle="tooltip" data-bs-placement="top" title="Tandai Selesai"
                                            type="submit" class="btn btn-sm btn-success">
                                        <i class="fa fa-check"></i>
                                    </button>
                            </form>
                            <!-- Form untuk menghapus antrian -->
                            <form class="deleteForm" action="{{ route('admin.queue.delete', $q->id) }}"
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
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
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
