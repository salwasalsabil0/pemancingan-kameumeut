@extends('user.layouts.main')

@section('content')
<!-- Header -->
<header class="ex-header">
    <div class="container">
        <div class="row">
            <div class="col-xl-10 offset-xl-1">
                <h1>Daftar Antrean</h1> 
            </div> <!-- end of col -->
        </div> <!-- end of row -->
    </div> <!-- end of container -->
</header> <!-- end of ex-header -->
<!-- end of header -->

<div class="container pt-2">

    @if ($queue->isEmpty())
        <table class="table">
            <thead>
                <tr>
                    <th>No. Antrean</th>
                    <th>Nama</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="3" class="alert alert-warning text-center">Belum ada antrean.</td>
                </tr>
            </tbody>
        </table>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>No. Antrean</th>
                    <th>Waktu Kedatangan</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($queue as $q)
                    <tr>
                        <td>{{ $q->queue_number }}</td>
                        <td>{{ $q->created_at }}</td>

                        <!-- Menyamarkan username dan nama -->
                        <td>
                            @if (Auth::id() == $q->user->id)
                                {{ $q->user->username }}
                            @else
                                {{ substr($q->user->username, 0, 1) . str_repeat('*', strlen($q->user->username) - 3) . substr($q->user->username, -2) }}
                            @endif
                        </td>

                        <td>
                            @if (Auth::id() == $q->user->id)
                                {{ $q->user->name }}
                            @else
                                @php
                                    $nama = explode(' ', $q->user->name);
                                    $namaDepan = $nama[0];
                                    $namaBelakang = isset($nama[1]) ? substr($nama[1], 0, 1) . '.' : '';
                                @endphp
                                {{ $namaDepan }} {{ $namaBelakang }}
                            @endif
                        </td>

                        <!-- Status Antrian -->
                        <td>
                            @if ($q->status == 'Sedang Dilayani')
                                <span class="badge bg-primary text-white">Sedang Dilayani</span>
                            @elseif ($q->status == 'Selesai')
                                <span class="badge bg-success text-white">Selesai</span>
                            @else
                                <span class="badge bg-warning text-white">Menunggu</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a style="display: inline-block; margin-bottom: 10px; font-style: italic; color: #16a085; font-size: 1.1rem;">
        *** Dapatkan Nomor Antrean – Langkah Awal Menuju Pelayanan!
    </a>

    <!-- Notifikasi & Tombol Booking (di kanan) + Tombol Ambil Antrian (di kiri) -->
    <div class="mt-4 d-flex justify-content-between">

        <!-- Tombol Ambil Nomor Antrian -->
        <form action="{{ route('user.queue.store') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-solid-small" {{ !$canTakeQueue ? 'disabled' : '' }}>
                Ambil Nomor Antrean
            </button>
        </form>

        <!-- Tombol Lanjut ke Booking -->
        @php
            $latestQueue = $queue->where('user_id', Auth::id())->last();
            //$isQueueFinished = $latestQueue && $latestQueue->status == 'Sedang Dilayani';
            //$isQueueValid = $latestQueue && $latestQueue->updated_at >= now()->subMinutes(1);
            $canProceedToBooking = $latestQueue && $latestQueue->status == 'Sedang Dilayani';
        @endphp

        @if ($canProceedToBooking)
            <a href="{{ route('user.booking') }}" class="btn btn-solid-small">Lanjut ke Booking</a>
        @else
            <button class="btn btn-solid-small" disabled>Lanjut ke Booking</button>
        @endif

    </div>

    <!-- Pesan Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif

    <!-- Pesan Tunggu 30 detik -->
    @if(!$canTakeQueue)
        <div class="alert alert-warning mt-3">Harap tunggu sebelum mengambil antrean lagi.</div>
    @endif

</div>

    <!-- Batas -->
    <header class="ex-header">
        <div class="container">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                    <h1></h1>
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </header> <!-- end of ex-header -->

</div>
@endsection



@section('script')
<!-- AJAX untuk Update Status Otomatis -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    setInterval(function() {
        $.ajax({
            url: "{{ route('user.queue.autoUpdate') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    location.reload(); // Reload untuk memuat status baru
                }
            }
        });
    }, 5000); // Jalankan setiap 5 detik
</script>
@endsection
