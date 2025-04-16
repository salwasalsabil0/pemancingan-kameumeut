@extends('user.layouts.main')

@section('content')
    <!-- Header -->
    <header class="ex-header">
        <div class="container">
            <div class="row">
                <div class="col-xl-10 offset-xl-1">
                    <h1>Kolam Kilo Angkat</h1>
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </header> <!-- end of ex-header -->
    <!-- end of header -->

    <!--
    <div class="container py-5">
        <div class="d-flex justify-content-end">
            <form method="GET" action="{{ route('user.search') }}">
                <div class="input-group">
                    <input class="form-control" name="search" placeholder="Search Field Type" aria-label="Search">
                    <button class="btn-solid-small" type="submit">Search</button>
                </div>
            </form>
        </div>
    </div>
    -->

    <!-- Blog Start -->
        <div class="container pt-2">
            <div class="row pb-3 justify-content-center">
                @if ($fieldDatas->isEmpty())
                    <div class="col-md-12 text-center">
                        <p>Data not available</p>
                    </div>
                @else
                    @foreach ($fieldDatas as $data)
                        <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-3">
                            <div class="card h-100 border-0 shadow-sm text-center">
                                <!--
                                <img class="card-img-top p-2 rounded-4" src="{{ asset('storage/' . $data->thumbnail) }}"
                                    alt="" />
                                
                                <div class="card-body p-2 d-flex align-items-center justify-content-center">
                                    
                                    <h4 class="text-center">{{ $data->name }}</h4>
                                    <div class="text-center py-2">
                                        <p>{{ $data->field_type }}</p>
                                    </div>
                                    <table>
                                        <tr>
                                            <td>Pilihan Ikan</td>
                                            <td>:</td>
                                            <td class="text-capitalize">{{ $data->field_material }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Ikan (kg) </td>
                                            <td>:</td>
                                            <td>{{ $data->field_location }}</td>
                                        </tr>
                                        <tr>
                                            <td>Harga</td>
                                            <td>:</td>
                                            <td>
                                                @if ($data->field_type == 'Futsal')
                                                    Rp. {{ number_format($data->morning_price, 0, ',', '.') }} - Rp.
                                                    {{ number_format($data->night_price, 0, ',', '.') }}
                                                @else
                                                    Rp. {{ number_format($data->night_price, 0, ',', '.') }}
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                -->
                                <div class="">
                                    <div class="text-center">
                                        <a style="border-radius: 5px; border-radius: 5px; width: auto; min-width: 80px; max-width: 100%;
                                        padding: 30px 12px; white-space: nowrap; display: inline-block;" href="{{ route('user.bookingCreate', 
                                        $data->id) }}" class="btn-solid-small">  {{ $data->name }}</a>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <nav class="navbar navbar-light text-center mt-4 mb-4" style="background-color: #B7B7B7;">  
                    <div class="container-fluid d-flex justify-content-center">
                        <span class="navbar-brand mb-0 h1 text-white w-50 d-flex justify-content-between custom-title" 
                        style="height: 20px; line-height: 0px; font-size: 15px;">
                            <span>K</span> <span>O</span> <span>L</span> <span>A</span> <span>M</span>
                        </span></div>
                </nav></div>
            </div>
        <!-- Blog End -->




                <!-- Header Kilo Jebur-->
                <header class="ex-header border-top">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-10 offset-xl-1 ">
                                <h1>Kolam Kilo Jebur</h1>
                            </div> <!-- end of col -->
                        </div> <!-- end of row -->
                    </div> <!-- end of container -->
                </header> <!-- end of ex-header -->
                <!-- end of header -->
            <!-- Blog Start -->
            <div class="container pt-2">
                <div class="row pb-3 justify-content-center">
                    @if ($fieldDatas->isEmpty())
                        <div class="col-md-12 text-center">
                            <p>Data not available</p>
                        </div>
                    @else
                        @foreach ($fieldDatas as $data)
                            <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm text-center">
                                    <div class="">
                                        <div class="text-center">
                                            <a style="border-radius: 5px; border-radius: 5px; width: auto; min-width: 80px; max-width: 100%;
                                            padding: 30px 12px; white-space: nowrap; display: inline-block;" href="{{ route('user.bookingCreate', 
                                            $data->id) }}" class="btn-solid-small">  {{ $data->name }}</a>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
            
                    <nav class="navbar navbar-light text-center mt-4 mb-4" style="background-color: #B7B7B7;">  
                        <div class="container-fluid d-flex justify-content-center">
                            <span class="navbar-brand mb-0 h1 text-white w-50 d-flex justify-content-between custom-title" 
                            style="height: 20px; line-height: 0px; font-size: 15px;">
                                <span>K</span> <span>O</span> <span>L</span> <span>A</span> <span>M</span>
                            </span></div>
                    </nav>
                    
                <div class="col-md-12 mb-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            {{-- {{ $fieldDatas->links() }} --}}

                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    <!-- Blog End -->
@endsection
