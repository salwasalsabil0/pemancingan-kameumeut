@extends('user.layouts.main')
@section('content')

    <!-- Header -->
    <header class="ex-header"style="padding: 20px 15px; ">
        
    </header> <!-- end of ex-header -->
    <!-- end of header -->

    
    <!-- Blog Start -->
        <div class="container pt-2">
            <div class="card p-3">
<div class="container" style="padding: 8px 15px; " >
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                    <h1>Kolam Kilo Angkat</h1>
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
                
                @php
                    $chunks = $kiloAngkat->chunk(12); // Membagi data menjadi kelompok 12
                @endphp

                @if ($kiloAngkat->isEmpty())
                    <div class="col-md-12 text-center">
                        <p>Data not available</p>
                    </div>
                @else

                {{-- 12 Data Pertama --}}
                 <div class="row pb-3 justify-content-center">
                    @foreach ($chunks->first() as $data)
                        <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm text-center">
            
                                    {{--<div class="text-center">
                                        <a style="border-radius: 5px; width: auto; min-width: 80px; max-width: 100%;
                                        padding: 30px 12px; white-space: nowrap; display: inline-block;" 
                                        href="{{ route('user.bookingCreate', 
                                        ['id' => $data->id, 'type' => 'single']) }}" 
                                            class="btn btn-secondary">
                                             {{ $data->name }} <!-- A1, A2, A3, dst -->
                                         </a>
                                          href="{{ route('user.bookingCreate', 
                                        $data->id) }}" class="btn-solid-small">  {{ $data->name }}</a>
                                    </div> --}}
                                    <div class="btn-group custom-btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                        
                                        <input type="checkbox" class="btn-check field-checkbox kilo-angkat" id="btncheck1{{ $data->id }}" data-id="{{ $data->id }}" data-field-type="kilo-angkat" autocomplete="off">
                                        <label for="btncheck1{{ $data->id }}" class="btn custom-checkbox-label">
                                            {{ $data->name }}
                                        </label>
                                        
                                    </div>
                        
                            </div>
                        </div>
                    @endforeach
                 </div>

                <!-- GAMBAR KOLAM -->
                <nav class="navbar navbar-light text-center mt-0 mb-4" style="background-color: #B7B7B7;">
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="navbar-brand mb-0 h1 text-white w-50 d-flex justify-content-between custom-title" 
                        style="height: 20px; line-height: 0px; font-size: 15px;">
                            <span>K</span> <span>O</span> <span>L</span> <span>A</span> <span>M</span>
                        </span>
                    </div>
                </nav>
            {{-- 12 Data Berikutnya (Jika Ada) --}}
            <div class="row pb-3 justify-content-center">
                @if ($chunks->count() > 1)
                    @foreach ($chunks[1] as $data)
                        <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm text-center">
                                {{--<div class="text-center">
                                    <a style="border-radius: 5px; min-width: 80px; padding: 30px 12px; display: inline-block;"
                                       href="{{ route('user.bookingCreate', $data->id) }}" 
                                       class="btn btn-secondary">  
                                       {{ $data->name }}
                                    </a>
                                </div>--}}
                                <div class="btn-group custom-btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                        
                                    <input type="checkbox" class="btn-check field-checkbox kilo-angkat" id="btncheck1{{ $data->id }}" data-id="{{ $data->id }}" data-field-type="kilo-angkat" autocomplete="off">
                                    <label for="btncheck1{{ $data->id }}" class="btn custom-checkbox-label">
                                        {{ $data->name }}
                                    </label>
                                    
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @endif
            </div>
            <div id="bookingMessage1" class="alert" style="display: none;"></div>
            <hr style="border-top: 2px solid #000;">
            <!-- Tombol Book Now -->
<div class="row pb-3 justify-content-center">
    <div class="col-md-12 text-center">
        <a id="bookNowBtn1" class="btn-solid-small" href="#" >Book Now</a>
    </div>
</div>
</div>
        </div>
            @endif
        <!-- Blog End -->



        
                <!-- Header Kilo Jebur-->
                <header class="ex-header" style="padding: 20px 15px; ">
                    
                </header> <!-- end of ex-header -->
                <!-- end of header -->
            
                <!-- Blog Start -->
            <div class="container pt-2">
                <div class="card p-3">
                    <div class="container" style="padding: 8px 15px;">
                        <div class="row">
                            <div class="col-xl-10 offset-xl-1 ">
                                <h1>Kolam Kilo Jebur</h1>
                            </div> <!-- end of col -->
                        </div> <!-- end of row -->
                    </div> <!-- end of container -->
                    @php
                        $chunks = $kiloJebur->chunk(12); // Membagi data menjadi kelompok 12
                    @endphp
    
                    @if ($kiloJebur->isEmpty())
                        <div class="col-md-12 text-center">
                            <p>Data not available</p>
                        </div>
                    @else
    
                    {{-- 12 Data Pertama --}}
                     <div class="row pb-3 justify-content-center">
                        @foreach ($chunks->first() as $data)
                            <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm text-center">
                
                                        {{--<div class="text-center" type="checkbox">
                                            <a style="border-radius: 5px; width: auto; min-width: 80px; max-width: 100%;
                                            padding: 30px 12px; white-space: nowrap; display: inline-block;" 
                                            href="{{ route('user.bookingCreate', 
                                            ['id' => 'all', 'type' => 'group']) }}" 
                                            class="btn-solid-small">
                                             Booking Semua Lapak J (1-24)
                                            </a>
                                              href="{{ route('user.bookingCreate', 
                                            $data->id) }}" class="btn btn-secondary">  {{ $data->name }}</a>
                                        </div> --}}
                                        <div class="btn-group custom-btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                        
                                            <input type="checkbox" class="btn-check field-checkbox kilo-jebur" id="btncheck2{{ $data->id }}" data-id="{{ $data->id }}" data-field-type="kilo-jebur" autocomplete="off">
                                            <label for="btncheck2{{ $data->id }}" class="btn custom-checkbox-label">
                                                {{ $data->name }}
                                            </label>
                                            
                                        </div>
                                          
                                </div>
                            
                            </div>
                        @endforeach
                     </div>
    
                    <!-- GAMBAR KOLAM -->
                    <nav class="navbar navbar-light text-center mt-0 mb-4" style="background-color: #B7B7B7;">
                        <div class="container-fluid d-flex justify-content-center">
                            <span class="navbar-brand mb-0 h1 text-white w-50 d-flex justify-content-between custom-title" 
                            style="height: 20px; line-height: 0px; font-size: 15px;">
                                <span>K</span> <span>O</span> <span>L</span> <span>A</span> <span>M</span>
                            </span>
                        </div>
                    </nav>
                {{-- 12 Data Berikutnya (Jika Ada) --}}
                <div class="row pb-3 justify-content-center">
                    @if ($chunks->count() > 1)
                        @foreach ($chunks[1] as $data)
                            <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm text-center">
                                    {{--<div class="text-center">
                                        <a style="border-radius: 5px; min-width: 80px; padding: 30px 12px; display: inline-block;"
                                           href="{{ route('user.bookingCreate', $data->id) }}" 
                                           class="btn btn-secondary">  
                                           {{ $data->name }}
                                        </a>
                                    </div>--}}
                                    <div class="btn-group custom-btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                        
                                        <input type="checkbox" class="btn-check field-checkbox kilo-jebur" id="btncheck2{{ $data->id }}" data-id="{{ $data->id }}" data-field-type="kilo-jebur" autocomplete="off">
                                        <label for="btncheck2{{ $data->id }}" class="btn custom-checkbox-label">
                                            {{ $data->name }}
                                        </label>
                                        
                                    </div>
            </div>
        </div>
    @endforeach
    @endif
</div>
<div id="bookingMessage2" class="alert" style="display: none;"></div>
            <hr style="border-top: 2px solid #000;">
<!-- Tombol Book Now -->
<div class="row pb-3 justify-content-center">
    <div class="col-md-12 text-center">
        <a id="bookNowBtn2" class="btn-solid-small" href="#" >Book Now</a>
    </div>
</div></div>
@endif 
            </div>
            <!-- Footer -->
    <header class="ex-header">
        <div class="container">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                    <h1></h1>
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </header> <!-- end of ex-header -->
    <!-- end of header -->
@endsection

@section('script')
<script> 
    
    //Kolam Kilo Angkat
    document.addEventListener("DOMContentLoaded", function () {
        const bookNowButton1 = document.getElementById("bookNowBtn1");
        const messageBox = document.getElementById("bookingMessage1");

        bookNowButton1.addEventListener("click", function (event) {
            event.preventDefault(); // Mencegah redirect sebelum validasi

            // Ambil semua checkbox yang dipilih
            //const checkedBoxes1 = document.querySelectorAll(".btn-check:checked");
            const checkedBoxes1 = document.querySelectorAll('.btn-check[data-field-type="kilo-angkat"]:checked');


            // Reset pesan sebelumnya
            messageBox.style.display = "none";
            messageBox.classList.remove("alert");

            if (checkedBoxes1.length === 0) {
                messageBox.innerHTML = "Silakan pilih lapak terlebih dahulu!";
                messageBox.classList.add("alert");
                messageBox.style.display = "block";
            } else if (checkedBoxes1.length > 1) {
                messageBox.innerHTML = "Hanya boleh booking 1 lapak saja!";
                messageBox.classList.add("alert");
                messageBox.style.display = "block";
            } else {
                // Ambil ID lapak yang dipilih
                const lapakId = checkedBoxes1[0].id.replace("btncheck1", ""); // Ambil ID dari checkbox

                // Simpan status booking di sessionStorage
                sessionStorage.setItem("bookingSuccess", "true");

                

                // Redirect setelah 1.5 detik agar pesan terlihat
                setTimeout(() => {
                    window.location.href = `/user/booking/choose-field/${lapakId}?type=single`;
                }, 1500);
            }
        });
    });

    //Kolam Kilo Jebur
    document.addEventListener("DOMContentLoaded", function () {
        const bookNowButton2 = document.getElementById("bookNowBtn2");
        const messageBox = document.getElementById("bookingMessage2");

        bookNowButton2.addEventListener("click", function (event) {
            event.preventDefault(); // Mencegah redirect sebelum validasi

            // Ambil semua checkbox dengan data-field-type="kilo-jebur"
            const allKiloJeburBoxes = document.querySelectorAll('.btn-check[data-field-type="kilo-jebur"]');
            const checkedBoxes2 = document.querySelectorAll('.btn-check[data-field-type="kilo-jebur"]:checked');


            // Reset pesan sebelumnya
            messageBox.style.display = "none";
            messageBox.classList.remove("alert");

            if (checkedBoxes2.length === 0) {
                messageBox.innerHTML = "Silakan pilih lapak terlebih dahulu!";
                messageBox.classList.add("alert");
                messageBox.style.display = "block";
            } else if (checkedBoxes2.length < allKiloJeburBoxes.length) {
                messageBox.innerHTML = `Anda harus memilih semua lapak (${allKiloJeburBoxes.length} lapak) untuk melanjutkan!`;
                messageBox.classList.add("alert");
                messageBox.style.display = "block";
            } else {
                // Semua lapak sudah dipilih
                sessionStorage.setItem("bookingSuccess", "true");

                // Redirect setelah 1.5 detik agar pesan terlihat
                setTimeout(() => {
                    window.location.href = `/user/booking/choose-field/all?kilo-angkat=true`;
                }, 1500);
            }
        });
    });

    
</script>



@endsection
