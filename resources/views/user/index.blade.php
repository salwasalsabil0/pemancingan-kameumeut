@extends('user.layouts.main')
@section('content')
    <!-- Header -->
    <div id="header" class="header" style="padding-top: 4rem;">
        <div class="text-container">
            <h3 class="title-hero"></h3>
        </div>

        <div class="background-container">
            <div class="overlay">
                    <div class="text-containerr">
                        <div class="section-title">SELAMAT DATANG DI PEMANCINGAN KAMEUMEUT</div>
                        <h3 style="color: white; font-weight: bold;" class="title-hero py-3">Booking Mudah, Mancing Tanpa Ribet!</h3>
                        <p style="color: white;" class="p-large">Sistem booking online dengan antrean memastikan urutan pelayanan dalam pemesanan tempat pemancingan. 
                            Dapatkan pengalaman memancing terbaik dengan kemudahan booking kapan saja!</p>
                        <a class="btn-solid-lg" href="{{ route('user.queue.index') }}">Pesan sekarang </a>
                    </div> <!-- end of text-container -->
                
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of header -->
    <!-- Details 2 -->
    <div class="counter" >
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-xl-7 pb-4">
                    <div class="image-container">
                        <img class="img-fluid" src="{{ asset('userLib2/images/detailfishing.svg') }}" alt="alternative">
                    </div> <!-- end of image-container -->
                </div> <!-- end of col -->
                <div class="col-lg-6 col-xl-5 ps-5 px-5">
                    <div class="text-container">
                        <h2><span>Kenapa Memilih </span><br> Pemancingan Kameumeut?</h2>
                        <p>Suasana nyaman, cocok untuk mancing santai.  
                            Booking pakai sistem antrean — ambil nomor dulu, lalu pilih spot.  
                            Adil dan tanpa rebutan tempat!</p>

                        <!-- Counter -->
                        <div class="counter-container">
                            <div class="counter-cell">
                                <div data-purecounter-start="0" data-purecounter-end="{{ $countTransactions }}"
                                    data-purecounter-duration="2" class="purecounter">1</div>
                                <div class="counter-info">Total Booking</div>
                            </div> <!-- end of counter-cell -->
                            <div class="counter-cell red">
                                <div data-purecounter-start="0" data-purecounter-end="{{ $countUsers }}"
                                    data-purecounter-duration="2" class="purecounter">1</div>
                                <div class="counter-info">Jumlah Pemancing</div>
                            </div> <!-- end of counter-cell -->
                        </div> <!-- end of counter-container -->
                        <!-- end of counter -->

                    </div> <!-- end of text-container -->
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of counter -->
    <!-- end of details 2 -->

    <div id="projects" class="filter bg-gray" >
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="h2-heading">Kabar Terbaru</h2>
                </div> <!-- end of col -->
            </div> <!-- end of row -->
            @if ($posts->isEmpty())
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-container">
                            <h4 class="h4-heading">Yah... kabar terkini tidak tersedia <i class="bi bi-emoji-frown"></i></h4>
                        </div> <!-- end of text-container -->
                    </div> <!-- end of col -->
                </div> <!-- end of row -->
            @else
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Filter -->
                        <div class="button-group filters-button-group">
                            <!-- Button untuk filter "All" -->
                            <button class="button is-checked" data-filter="*">Semua</button>
                            <!-- Button untuk filter masing-masing kategori -->
                            @foreach ($categories as $category)
                                <button class="button" data-filter=".{{ Str::slug($category) }}">{{ $category }}</button>
                            @endforeach
                        </div> <!-- end of button group -->

                        <div class="grid">
                            @foreach ($posts as $post)
                                <div class="element-item {{ Str::slug($post->category) }}">

                                    <a href="@if ($post->category == 'Berita') {{ route('detailBerita', $post->id) }} 
                                        @else {{ route('detailEvent', $post->id) }} @endif"
                                        onclick="openModal()">
                                        <img class="img-fluid" style="height: 300px; width: 100%"
                                            src="{{ asset('storage/' . $post->thumbnail) }}" alt="alternative">
                                        <p><strong>{{ $post->title }}</strong> - {!! Str::limit($post->description, 200) !!}</p>
                                    </a>
                                </div>
                            @endforeach
                        </div> <!-- end of grid -->
                        
                        <!-- end of filter -->
                    </div> <!-- end of col -->
                </div> <!-- end of row -->
            @endif
        </div> <!-- end of container -->
    </div> <!-- end of filter -->

    <!-- Invitation -->
    <div class="basic-2 pb-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-container">
                        <h4>Siap Memancing? Booking Sekarang!</h4>
                        <p class="p-large">Segera lakukan pemesanan lapak untuk mendapatkan spot terbaik
                            dengan reservasi mudah di tempat pemancingan bersama kami.
                        </p>
                    </div> <!-- end of text-container -->
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of basic-2 -->
    <!-- end of invitation -->
@endsection
