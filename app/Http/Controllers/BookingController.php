<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\User;
use App\Models\Booking;

use App\Models\Field;
use App\Models\Fish;
use App\Models\Pondtype;
use Illuminate\Support\Str;

use App\Mail\ValidPayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\BookingDetail;
use App\Models\FieldSchedule;
use App\Models\PaymentMethod;
use App\Mail\InvalidatePayment;
use App\Mail\ConfirmationBooking;
use Illuminate\Support\Facades\DB;
use App\Models\ScheduleAvailability;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('bookingDetails', 'field', 'transactions.paymentMethodDP', 'transactions.paymentMethodRemaining')
            ->orderBy('id', 'desc')
            ->latest() // Jika id adalah timestamp Unix, Anda bisa menggunakan metode latest()
            ->get();
        $paymentMethods = PaymentMethod::all();

        return view('admin.booking.index', compact('bookings', 'paymentMethods',));
    }

    public function chooseField()
    {
        $user = auth()->user();
        // Ambil data sesuai dengan jenis kolam
        $kiloAngkat = Field::where('pond_type_id', '1')->get();
        $kiloJebur = Field::where('pond_type_id', '2')->get();

        if ($user->role_id == 1) {
            $fieldDatas = Field::all();
            return view('admin.booking.chooseField', compact('fieldDatas'));
        } elseif ($user->role_id == 2) {
            $fieldDatas = Field::all();
            
            return view('user.booking.chooseField', compact('kiloAngkat', 'kiloJebur'));
        }
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $fieldDatas = Field::where('field_type', 'like', '%' . $search . '%')->paginate(6);
        return view('user.booking.chooseField', compact('fieldDatas'));
    }

    public function create(Request $request, $id)
    {
        $user = auth()->user();
        
        $field = Field::find($id);

        if ($user->role_id == 1) {
            if (!$field) {
                return view('admin.404');
            }
        } elseif ($user->role_id == 2) {
            if (!$field) {
                return abort(404);
            }
        }
        // Ambil jenis ikan berdasarkan pond_type_id
        $fish = Fish::where('pond_type_id', $field->pond_type_id)->get();
        // Ambil jadwal berdasarkan field yang dipilih dan tanggal hari ini
        $schedule_play = Carbon::now()->format('Y-m-d');
        $fieldSchedules = FieldSchedule::with(['scheduleAvailabilities' => function ($query) use ($field, $schedule_play) {
            $query->where('field_id', $field->id)
                ->where('schedule_date', $schedule_play);
        }])->get();
        
        $type = $request->query('type'); // sekarang ini bisa digunakan!

        if ($user->role_id == 1) {
            return view('admin.booking.create', compact('field', 'fieldSchedules', 'schedule_play','fish','type'));
        } elseif ($user->role_id == 2) {
            return view('user.booking.create', compact('field', 'fieldSchedules', 'schedule_play','fish','type'));
        }
    }


    public function chooseAllKiloJebur(Request $request)
    {
        $user = auth()->user();

    // Ambil semua field dengan pond_type = kilo-jebur
    $fields = Field::where('pond_type_id', '2')->get();


    if ($fields->isEmpty()) {
        return abort(404, 'Tidak ada field dengan pond_type kilo-jebur.');
    }

    // Ambil pond_type_id dari salah satu field untuk ambil data ikan
    $pondTypeId = $fields->first()->pond_type_id;

    // Ambil data ikan berdasarkan pond_type_id
    $fish = Fish::where('pond_type_id', $pondTypeId)->get();

    // Ambil semua schedule hari ini untuk field2 tersebut
    $schedule_play = Carbon::now()->format('Y-m-d');

    $fieldSchedules = FieldSchedule::with(['scheduleAvailabilities' => function ($query) use ($fields, $schedule_play) {
        $fieldIds = $fields->pluck('id'); // Ambil semua ID field kilo jebur
        $query->whereIn('field_id', $fieldIds)
              ->where('schedule_date', $schedule_play);
    }])->get();

    // Pilih view sesuai role user
    if ($user->role_id == 1) {
        return view('admin.booking.create', compact('fields', 'fieldSchedules', 'schedule_play', 'fish'));
    } elseif ($user->role_id == 2) {
        return view('user.booking.createPond', compact('fields', 'fieldSchedules', 'schedule_play', 'fish'));
    }
    }

    public function checkAvailability(Request $request)
    {
        $selectedDate = $request->schedule_play;
        $fieldDataId = $request->field_id;

        // Ubah input field_data_id menjadi integer
        $fieldDataId = (int) $fieldDataId;

        // Ambil data FieldData berdasarkan id
        $field = Field::find($fieldDataId);

        // Pastikan FieldData ditemukan
        if (!$field) {
            return response()->json(['error' => 'Field not found'], 404);
        }

        $fieldSchedules = FieldSchedule::with(['scheduleAvailabilities' => function ($query) use ($field, $selectedDate) {
            $query->where('field_id', $field->id)
                ->where('schedule_date', $selectedDate);
        }])->get();

        return response()->json($fieldSchedules);
    }

    public function getSession(Request $request)
    {
        $user = auth()->
        user();
        
        // Validasi input
        $request->validate([
            'customer_name' => 'required',
            'fish_id' => 'required',
            'weight' => 'required|numeric|min:0.1', //pesanan ikan yang diambil
            'selected_schedules' => 'required',
            'schedule_play' => 'required|date',
        ], [
            'customer_name.required' => 'Nama Pelanggan harus diisi.',
            'fish_id.required' => 'Ikan harus dipilih',
            'weight.required' => 'Pesanan ikan harus diisi',
            'schedule_play.required' => 'Anda harus memilih jadwal tanggal bermain',
            'selected_schedules.required' => 'Anda harus memilih jam bermain.',
        ]);
        
//awal

        // Tentukan apakah pelanggan adalah member
        $isMember = $request->has('is_member') ? true : false;

        // Jika pelanggan adalah member, pastikan ada 3 jadwal yang dipilih
        if ($isMember && count($request->selected_schedules) != 3) {
            return back()->with('error', 'Jika Anda ingin menjadi member, Anda harus memilih 3 jadwal.');
        }
        
        
        $fish = Fish::findOrFail($request->fish_id);

        if ($fish->perkg_stock < $request->weight) {
            return back()->with('error', 'Stok ikan tidak mencukupi!');
        }
        // Hitung total biaya
        $fishPrice = $fish->perkg_price * $request->weight; // Harga berdasarkan berat ikan
        // Simpan detail ikan yang dipesan
        $orderedFish = [
            'type_ikan' => $fish->type_ikan,
            'price' => $fish->perkg_price,
            'quantity' => $request->weight,
            'total' => $fishPrice,
        ];

        // Kurangi stok ikan
        $fish->decrement('perkg_stock', $request->weight);

        // Simpan data booking ke dalam sesi
        $bookingData = [
            'field_id' => $request->field_id,
            'customer_name' => $request->customer_name,
            'is_member' => $isMember,
            'discount' => $isMember ? 25000 : 0,
            'fish_id' => $request->fish_id, 
            'weight' => $request->weight, 
            'selected_schedules' => [],
            'total_fish_price'=> $fishPrice,
            'total_before_discount' => 0, 
            'total_after_discount' => 0,
            'ordered_fishes' => [$orderedFish], // Tambahkan detail ikan ke dalam bookingData
        ];

        // Jika pelanggan adalah member, lakukan logika untuk member
        if ($isMember) {
            // Iterasi melalui tanggal jadwal
            $selectedDates = [];
            $scheduleDate = Carbon::parse($request->schedule_play);
            while (count($selectedDates) < 4) {
                $selectedDates[] = $scheduleDate->format('Y-m-d');
                $scheduleDate->addDays(7);
            }

            // Iterasi melalui tanggal jadwal yang dipilih
            foreach ($selectedDates as $date) {
                foreach ($request->selected_schedules as $scheduleId) {
                    $fieldSchedule = FieldSchedule::find($scheduleId);

                    if ($fieldSchedule) {
                        $field = Field::find($request->field_id);
                        $subTotal = ($scheduleId <= 11) ? $field->morning_price : $field->night_price;
                        
                        // Tambahkan harga ke total sebelum diskon
                        $bookingData['total_before_discount'] += $subTotal;

                        // Tambahkan detail jadwal ke dalam data booking
                        $bookingData['selected_schedules'][] = [
                            'schedule_play' => $date,
                            'field_schedule_id' => $scheduleId,
                            'start_time' => $fieldSchedule->start_time, // Tambahkan start_time
                            'end_time' => $fieldSchedule->end_time,
                            'sub_total' => $subTotal,
                        ];
                    }
                }
            }

            // Hitung total setelah diskon
            $bookingData['total_after_discount'] = $bookingData['total_before_discount'] + $bookingData['total_fish_price'] - $bookingData['discount'];

            // Jika pelanggan bukan member, lakukan logika untuk non-member
        } else {
            // Iterasi melalui jadwal yang dipilih
            foreach ($request->selected_schedules as $scheduleId) {
                $fieldSchedule = FieldSchedule::find($scheduleId);

                if ($fieldSchedule) {
                    $field = Field::find($request->field_id);
                    $subTotal = ($scheduleId <= 11) ? $field->morning_price : $field->night_price;
                        
                    // Tambahkan harga ke total sebelum diskon
                    $bookingData['total_before_discount'] += $subTotal; 

                    // Tambahkan detail jadwal ke dalam data booking
                    $bookingData['selected_schedules'][] = [
                        'schedule_play' => $request->schedule_play,
                        'field_schedule_id' => $scheduleId,
                        'start_time' => $fieldSchedule->start_time, // Tambahkan start_time
                        'end_time' => $fieldSchedule->end_time,
                        'sub_total' => $subTotal,
                    ];
                }
            }

            // Hitung total setelah diskon
            $bookingData['total_after_discount'] = $bookingData['total_before_discount'] + $bookingData['total_fish_price'] - $bookingData['discount'];
        }

        // Simpan data booking ke dalam sesi
        session()->put('booking_data', $bookingData);
        
        // Redirect pengguna ke halaman transaksi
        if ($user->role_id == 1) {
            return redirect()->route('admin.transaction');
        } elseif ($user->role_id == 2) {
            return redirect()->route('user.transaction');
        }
    }

    public function transaction()
    {
        $user = auth()->user();
        $bookingData = session('booking_data');
  
        $field = Field::find($bookingData['field_id']);
        $paymentMethods = PaymentMethod::all();

        if ($user->role_id == 1) {
            return view('admin.booking.transaction', compact('bookingData', 'field', 'paymentMethods'));
        } elseif ($user->role_id == 2) {
            return view('user.transaction.transaction', compact('bookingData', 'field', 'paymentMethods'));
        }
    }

    public function storeTransaction(Request $request)
    {
        $user = auth()->user();
        $bookingData = session('booking_data');
        
        if (!$bookingData) {
            return back()->with('error', 'Data booking tidak ditemukan.');
        }

        $totalAfterDiscount = $bookingData['total_after_discount'] - $request->discount;
        if ($request->down_payment > $bookingData['total_after_discount'] || $request->down_payment > $totalAfterDiscount) {
            return back()->with('error', 'Nominal DP melebihi total yang harus dibayarkan');
        }

        $paymentProofPath = null; // Inisialisasi variable

        // Validasi input berdasarkan kondisi
        if ($bookingData['is_member'] == true) {
            $request->validate([
                'payment_method' => 'required',
            ], [
                'payment_method.required' => 'Pilih metode pembayaran',
            ]);
        } else {
        // Ambil total harga dari session bookingData
        $total = $bookingData['total_after_discount'];
        $minDp = $total * 0.3; // Hitung minimal DP (30%)
            $request->validate([
                'payment_method' => 'required',
                'down_payment' => ['required', 'numeric', 'min:' . $minDp],
            ], [
                'payment_method.required' => 'Pilih metode pembayaran',
                'down_payment.required' => 'Masukkan nominal DP',
                'down_payment.min' => 'Pembayaran DP minimal adalah 30% dari total harga, yaitu Rp. ' . number_format($minDp, 0, ',', '.'),
            ]);
        }

        if ($user->role_id == 1 && $request->payment_method != 1) {
            $request->validate([
                'payment_proof' => 'required|mimes:jpg,jpeg,png|max:2048',
                'account_name' => 'required',
            ], [
                'payment_proof.required' => 'Masukkan bukti pembayaran',
                'account_name.required' => 'Masukkan nama akun pembayaran',
            ]);

            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }


// Ambil data ikan berdasarkan fish_id
$fish = Fish::find($request->fish_id);
if (!$fish) {
    return back()->with('error', 'Data ikan tidak ditemukan.');
}
// Ambil field berdasarkan field_id yang dipilih oleh user
$field = Field::findOrFail($request->field_id);



        // Menentukan apakah pelanggan adalah member
        $isMember = $request->is_member;


        if ($isMember) {
            // Buat booking jika pelanggan adalah member
            $booking = Booking::create([
                'field_id' => $request->field_id,
                'fish_id' => $fish->id,
                'pond_type_id' => $field->pond_type_id, // Ambil pond_type_id dari tabel fields
                'weight' => $request->weight,
                'customer_name' => $request->customer_name,
                'is_member' => $isMember, // Atur status member
                'discount' => $isMember ? 25000 : 0,
            ]);

            // Iterasi melalui tanggal jadwal
            $selectedDates = [];
            $scheduleDate = Carbon::parse($request->schedule_play);
            while (count($selectedDates) < 4) {
                $selectedDates[] = $scheduleDate->format('Y-m-d');
                $scheduleDate->addDays(7);
            }

            // Iterasi melalui tanggal jadwal yang dipilih
            foreach ($selectedDates as $date) {
                foreach ($request->selected_schedules as $scheduleId) {
                    // Periksa apakah jadwal ini sudah pernah dimasukkan sebelumnya
                    $existingBookingDetail = BookingDetail::where('booking_id', $booking->id)
                        ->where('schedule_play', $date)
                        ->where('field_schedule_id', $scheduleId)
                        ->exists();

                    if (!$existingBookingDetail) {
                        $fieldSchedule = FieldSchedule::find($scheduleId);

                        if ($fieldSchedule) {
                            $field = Field::find($request->field_id);
                            $subTotal = ($scheduleId <= 11) ? $field->morning_price : $field->night_price;
                        
                            // Tambahkan detail booking
                            $booking->bookingDetails()->create([
                                'booking_id' => $booking->id,
                                'schedule_play' => $date,
                                'field_schedule_id' => $scheduleId,
                                'sub_total' => $subTotal,
                            ]);

                            // Tandai jadwal sebagai tidak tersedia pada tanggal tersebut di ScheduleAvailability
                            ScheduleAvailability::create([
                                'booking_id' => $booking->id,
                                'field_id' => $request->field_id,
                                'field_schedule_id' => $scheduleId,
                                'schedule_date' => $date,
                                'is_available' => false,
                            ]);
                        }
                    }
                }
            }
            // Hitung total setelah diskon
            $fishPrice = $fish->perkg_price * $request->weight; // Harga ikan dihitung terpisah
            $totalBeforeDiscount = $booking->bookingDetails()->sum('sub_total');
            $totalAfterDiscount = $fishPrice + $totalBeforeDiscount - $booking->discount;

            // Simpan total_subtotal (total setelah diskon) ke dalam tabel booking
            $booking->total_subtotal = $totalAfterDiscount;
            $booking->save();
            $transaction = Transaction::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'payment_method_dp' => $request->payment_method,
                'payment_proof_dp' => $paymentProofPath,
                'account_name_dp' => $request->account_name,
                'down_payment' => $totalAfterDiscount,
            ]);
        } else {
            // Buat booking jika pelanggan bukan member
            $discount = $request->discount ?? 0;
            $booking = Booking::create([
                'field_id' => $request->field_id,
                'fish_id' => $fish->id,
                'pond_type_id' => $field->pond_type_id, // Ambil pond_type_id dari tabel fields
                'weight' => $request->weight,
                'customer_name' => $request->customer_name,
                'discount' => $discount,
            ]);

            // Hitung total sebelum diskon
            $totalBeforeDiscount = 0;

            foreach ($request->selected_schedules as $scheduleId) {
                $fieldSchedule = FieldSchedule::find($scheduleId);

                if ($fieldSchedule) {
                    $field = Field::find($request->field_id);
                    $subTotal = ($scheduleId <= 11) ? $field->morning_price : $field->night_price;
                        
                    // Tambahkan harga ke total sebelum diskon
                    $totalBeforeDiscount += $subTotal;

                    // Buat booking detail
                    $bookingDetail = $booking->bookingDetails()->create([
                        'booking_id' => $booking->id,
                        'schedule_play' => $request->schedule_play,
                        'field_schedule_id' => $scheduleId,
                        'sub_total' => $subTotal,
                    ]);

                    // Tandai jadwal sebagai tidak tersedia pada tanggal tersebut di ScheduleAvailability
                    ScheduleAvailability::create([
                        'booking_id' => $booking->id,
                        'field_id' => $request->field_id,
                        'field_schedule_id' => $scheduleId,
                        'schedule_date' => $request->schedule_play,
                        'is_available' => false,
                    ]);
                }
            }
    // Hitung total biaya
        $fishPrice = $fish->perkg_price * $request->weight; // Harga berdasarkan berat ikan

            // Hitung total setelah diskon
            $totalAfterDiscount = $fishPrice + $totalBeforeDiscount - $booking->discount;

            // Simpan total_subtotal (total setelah diskon) ke dalam tabel booking
            $booking->total_subtotal = $totalAfterDiscount;
            $booking->save();

            // Buat transaksi dengan pembayaran DP
            $transaction = Transaction::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'payment_method_dp' => $request->payment_method,
                'payment_proof_dp' => $paymentProofPath,
                'account_name_dp' => $request->account_name,
                'down_payment' => $request->down_payment,
            ]);
            
        }

        session()->forget('booking_data');
        // Update status booking berdasarkan peran pengguna
        if ($user->role_id == 1) {
            if ($isMember) {
                $booking->update([
                    'booking_status' => 4,
                ]);
            } else {
                $booking->update([
                    'booking_status' => 2,
                ]);
            }

            session()->flash('success', 'Data booking berhasil ditambahkan');
            // Redirect pengguna ke halaman transaksi
            return redirect()->route('admin.bookingIndex');
            
        } elseif ($user->role_id == 2) {
            if ($transaction->payment_method_dp != 1) {
                return redirect()->route('user.paymentTransaction', $transaction->id);
            } else {
                $booking->update([
                    'booking_status' => 1,
                ]);

                $recipients = User::where('role_id', 1)
                    ->pluck('email');

                Mail::to($recipients)->send(new ConfirmationBooking($transaction));
                return redirect()->route('user.noticeTransaction', $transaction->id);
            }
        }
    }


    public function paymentTransaction($id)
    {
        $user = auth()->user();
        $transaction = Transaction::with('paymentMethodDP', 'booking.field')->find($id);
        if (!$transaction) {
            return abort(404); // Atau bisa juga return redirect()->route('route_name')->with('error', 'Transaksi tidak ditemukan');
        }

        if ($transaction->payment_method_dp == 1 || $transaction->payment_proof_dp != null) {
            return abort(403);
        }

        if ($transaction->user_id != auth()->user()->id) {
            return abort(403);
        }

        return view('user.transaction.payment', compact('transaction'));
    }

    public function paymentTransactionStore($id, Request $request)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'account_name' => 'required',
        ], [
            'payment_proof.required' => 'Bukti pembayaran harus diisi',
            'payment_proof.image' => 'File harus berupa gambar',
            'payment_proof.mimes' => 'File harus berupa jpeg, png, atau jpg',
            'payment_proof.max' => 'File tidak boleh lebih dari 2 MB',
            'account_name.required' => 'Nama harus diisi',
        ]);

        $transaction = Transaction::with('paymentMethodDP', 'booking.field')->find($id);
        $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        $transaction->update([
            'payment_proof_dp' => $paymentProofPath,
            'account_name_dp' => $request->account_name,
        ]);

        $transaction->booking->update([
            'booking_status' => 1,
        ]);

        $recipients = User::where('role_id', 1)
            ->pluck('email');

        Mail::to($recipients)->send(new ConfirmationBooking($transaction));
        return redirect()->route('user.noticeTransaction', $transaction->id);
    }

    public function noticeTransaction($id)
    {

        $transaction = Transaction::with('paymentMethodDP', 'booking.field')->find($id);
        if (!$transaction) {
            return abort(404);
        }

        if ($transaction->user_id != auth()->user()->id) {
            return abort(403);
        }

        return view('user.transaction.getProcess', compact('transaction'));
    }

    public function confirmPaymentDP($id)
    {
        $booking = Booking::with('transactions')->find($id);
        if ($booking->is_member == 1) {
            $booking->update([
                'booking_status' => 4,
            ]);
        } else {
            $booking->update([
                'booking_status' => 2,
            ]);
        }

        $recipients = User::where('id', $booking->transactions->first()->user_id)
            ->pluck('email');

        Mail::to($recipients)->send(new ValidPayment($booking));

        session()->flash('success', 'Data booking berhasil diperbarui');
        // Redirect pengguna ke halaman transaksi
        return redirect()->route('admin.bookingIndex');
    }

    public function invalidatePaymentDP($id)
    {
        $booking = Booking::with('transactions')->find($id);
        $booking->update([
            'booking_status' => 0,
        ]);

        $recipients = User::where('id', $booking->transactions->first()->user_id)
            ->pluck('email');

        Mail::to($recipients)->send(new InvalidatePayment($booking));

        session()->flash('success', 'Data booking berhasil diperbarui');
        return redirect()->route('admin.bookingIndex');
    }

    public function canceledBooking($id)
    {
        $booking = Booking::find($id);
        $booking->update([
            'booking_status' => 3,
        ]);

        session()->flash('success', 'Data booking berhasil diperbarui');
        // Redirect pengguna ke halaman transaksi
        return redirect()->route('admin.bookingIndex');
    }

    public function confirmPaymentRemaining($id, Request $request)
    {
        if ($request->payment_method != 1) {
            $request->validate([
                'payment_method' => 'required',
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'account_name' => 'required',
            ], [
                'payment_method.required' => 'Metode pembayaran harus dipilih',
                'payment_proof.required' => 'Bukti pembayaran harus diisi',
                'payment_proof.image' => 'File harus berupa gambar',
                'payment_proof.mimes' => 'File harus berupa jpeg, png, atau jpg',
                'payment_proof.max' => 'File tidak boleh lebih dari 2 MB',
                'account_name.required' => 'Nama harus diisi',
            ]);
        } else {
            $request->validate([
                'payment_method' => 'required',
            ], [
                'payment_method.required' => 'Metode pembayaran harus dipilih',
            ]);
        }

        if ($request->payment_method != 1) {
            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        } else {
            $paymentProofPath = null;
        }

        if ($request->payment_method != 1) {
            $account_name_remaining = $request->account_name;
        } else {
            $account_name_remaining = null;
        }

        $transaction = Transaction::find($id);
        $transaction->update([
            'payment_method_remaining' => $request->payment_method,
            'payment_proof_remaining' => $paymentProofPath,
            'account_name_remaining' => $account_name_remaining,
            'remaining_payment' => $request->remaining_payment,
        ]);

        $transaction->booking->update([
            'booking_status' => 4,
        ]);

        // Redirect ke halaman pemberitahuan transaksi sedang diproses
        session()->flash('success', 'Data booking berhasil diperbarui');
        // Redirect pengguna ke halaman transaksi
        return redirect()->route('admin.bookingIndex');
    }

    public function transactionIndex()
    {
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        // Mengambil semua PaymentMethod
        $paymentMethods = PaymentMethod::all();

        // Array untuk menyimpan total untuk setiap PaymentMethod
        $totals = [];
        $totalTransaction = 0;

        // Menghitung total down_payment dan remaining_payment untuk setiap PaymentMethod
        foreach ($paymentMethods as $paymentMethod) {
            // Menghitung total down_payment dengan payment_method_dp = id PaymentMethod saat ini
            $totalDownPayment = Transaction::where('payment_method_dp', $paymentMethod->id)
                ->whereHas('booking', function ($query) use ($startDate, $endDate) {
                    $query->where('booking_status', '>=', 2)
                        ->whereBetween('created_at', [$startDate, $endDate]);
                })->sum('down_payment');

            // Menghitung total remaining_payment dengan payment_method_remaining = id PaymentMethod saat ini
            $totalRemainingPayment = Transaction::where('payment_method_remaining', $paymentMethod->id)
                ->whereHas('booking', function ($query) use ($startDate, $endDate) {
                    $query->where('booking_status', '>=', 2)
                        ->whereBetween('updated_at', [$startDate, $endDate]);
                })->sum('remaining_payment');

            // Total untuk PaymentMethod saat ini
            $totals[$paymentMethod->name] = $totalDownPayment + $totalRemainingPayment;
            $totalTransaction += $totalDownPayment + $totalRemainingPayment;
        }

        $transactions = Transaction::join('bookings', 'transactions.booking_id', '=', 'bookings.id')
            ->join('fields', 'bookings.field_id', '=', 'fields.id')
            ->select('transactions.*')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereHas('booking', function ($query) {
                    $query->where('bookings.booking_status', '>=', 2);
                })
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('bookings.created_at', [$startDate, $endDate])
                            ->orWhereBetween('bookings.updated_at', [$startDate, $endDate]);
                    });
            })
            ->orderBy(DB::raw('(SELECT pond_type_id FROM fields WHERE id = bookings.field_id)'))
            ->orderBy(DB::raw('(SELECT name FROM fields WHERE id = bookings.field_id)'))
            ->with('booking.field')
            ->get();


        foreach ($transactions as $transaction) {
            // Mengambil down payment dari transaksi pertama
            $downPayment = $transaction->down_payment;

            // Mengambil remaining payment dari transaksi pertama
            $remainingPayment = $transaction->remaining_payment;

            $dateStart = $startDate->format('Y-m-d');
            $dateEnd = $endDate->format('Y-m-d');

            $forDP = $transaction->created_at->format('Y-m-d') == $dateStart &&  $transaction->created_at->format('Y-m-d') == $dateEnd;
            $forRemaining = $transaction->updated_at->format('Y-m-d') == $dateStart &&  $transaction->updated_at->format('Y-m-d') == $dateEnd;

            $createdAt = $transaction->created_at->format('Y-m-d');
            $updatedAt = $transaction->updated_at->format('Y-m-d');

            if ($transaction->booking->booking_status == 2) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Bayar DP';
                $transaction->bg_color = 'primary';
            } elseif ($transaction->booking->booking_status == 3) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Batal';
                $transaction->bg_color = 'secondary';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $forDP) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Bayar DP';
                $transaction->bg_color = 'primary';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $forRemaining) {
                $transaction->total_payment = $remainingPayment;
                $transaction->status = 'Pelunasan';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $dateStart != $dateEnd && $dateEnd == $createdAt) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Bayar DP';
                $transaction->bg_color = 'primary';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $dateStart != $dateEnd && $dateStart == $updatedAt) {
                $transaction->total_payment = $remainingPayment;
                $transaction->status = 'Pelunasan';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $dateStart != $dateEnd) {
                $transaction->total_payment = $downPayment + $remainingPayment;
                $transaction->status = 'Lunas';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt == $updatedAt && $dateStart == $dateEnd) {
                $transaction->total_payment = $downPayment + $remainingPayment;
                $transaction->status = 'Pembayaran Lunas';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt == $updatedAt && $dateStart != $dateEnd) {
                $transaction->total_payment = $downPayment + $remainingPayment;
                $transaction->status = 'Lunas';
                $transaction->bg_color = 'success';
            }
        }

        return view('admin.transaction.index', compact('transactions', 'startDate', 'endDate', 'totals', 'totalTransaction'));
    }

    public function loadTransactions(Request $request)
    {
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $paymentMethods = PaymentMethod::all();

        // Array untuk menyimpan total untuk setiap PaymentMethod
        $totals = [];
        $totalTransaction = 0;

        // Menghitung total down_payment dan remaining_payment untuk setiap PaymentMethod
        foreach ($paymentMethods as $paymentMethod) {
            // Menghitung total down_payment dengan payment_method_dp = id PaymentMethod saat ini
            $totalDownPayment = Transaction::where('payment_method_dp', $paymentMethod->id)
                ->whereHas('booking', function ($query) use ($startDate, $endDate) {
                    $query->where('booking_status', '>=', 2)
                        ->whereBetween('created_at', [$startDate, $endDate]);
                })->sum('down_payment');

            // Menghitung total remaining_payment dengan payment_method_remaining = id PaymentMethod saat ini
            $totalRemainingPayment = Transaction::where('payment_method_remaining', $paymentMethod->id)
                ->whereHas('booking', function ($query) use ($startDate, $endDate) {
                    $query->where('booking_status', '>=', 2)
                        ->whereBetween('updated_at', [$startDate, $endDate]);
                })->sum('remaining_payment');

            // Total untuk PaymentMethod saat ini
            $totals[$paymentMethod->name] = $totalDownPayment + $totalRemainingPayment;
            $totalTransaction += $totalDownPayment + $totalRemainingPayment;
        }

        // Query transactions within date range
        $transactions = Transaction::join('bookings', 'transactions.booking_id', '=', 'bookings.id')
            ->join('fields', 'bookings.field_id', '=', 'fields.id')
            ->select('transactions.*')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereHas('booking', function ($query) {
                    $query->where('bookings.booking_status', '>=', 2);
                })
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('bookings.created_at', [$startDate, $endDate])
                            ->orWhereBetween('bookings.updated_at', [$startDate, $endDate]);
                    });
            })
            ->orderBy(DB::raw('(SELECT pond_type_id FROM fields WHERE id = bookings.field_id)'))
            ->orderBy(DB::raw('(SELECT name FROM fields WHERE id = bookings.field_id)'))
            ->with('booking.field')
            ->get();


        foreach ($transactions as $transaction) {
            // Mengambil down payment dari transaksi pertama
            $downPayment = $transaction->down_payment;

            // Mengambil remaining payment dari transaksi pertama
            $remainingPayment = $transaction->remaining_payment;

            $dateStart = $startDate->format('Y-m-d');
            $dateEnd = $endDate->format('Y-m-d');

            $forDP = $transaction->created_at->format('Y-m-d') == $dateStart &&  $transaction->created_at->format('Y-m-d') == $dateEnd;
            $forRemaining = $transaction->updated_at->format('Y-m-d') == $dateStart &&  $transaction->updated_at->format('Y-m-d') == $dateEnd;

            $createdAt = $transaction->created_at->format('Y-m-d');
            $updatedAt = $transaction->updated_at->format('Y-m-d');

            if ($transaction->booking->booking_status == 2) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Bayar DP';
                $transaction->bg_color = 'primary';
            } elseif ($transaction->booking->booking_status == 3) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Batal';
                $transaction->bg_color = 'secondary';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $forDP) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Bayar DP';
                $transaction->bg_color = 'primary';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $forRemaining) {
                $transaction->total_payment = $remainingPayment;
                $transaction->status = 'Pelunasan';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $dateStart != $dateEnd && $dateEnd == $createdAt) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Bayar DP';
                $transaction->bg_color = 'primary';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $dateStart != $dateEnd && $dateStart == $updatedAt) {
                $transaction->total_payment = $remainingPayment;
                $transaction->status = 'Pelunasan';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $dateStart != $dateEnd) {
                $transaction->total_payment = $downPayment + $remainingPayment;
                $transaction->status = 'Lunas';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt == $updatedAt && $dateStart == $dateEnd) {
                $transaction->total_payment = $downPayment + $remainingPayment;
                $transaction->status = 'Pembayaran Lunas';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt == $updatedAt && $dateStart != $dateEnd) {
                $transaction->total_payment = $downPayment + $remainingPayment;
                $transaction->status = 'Lunas';
                $transaction->bg_color = 'success';
            }
        }

        return response()->json(
            [
                'transactions' => $transactions,
                'totals' => $totals,
                'totalTransaction' => $totalTransaction
            ]
        );
    }

    public function transactionExportPDF(Request $request)
    {
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Mengambil semua PaymentMethod
        $paymentMethods = PaymentMethod::all();

        // Array untuk menyimpan total untuk setiap PaymentMethod
        $totals = [];
        $totalTransaction = 0;

        // Menghitung total down_payment dan remaining_payment untuk setiap PaymentMethod
        foreach ($paymentMethods as $paymentMethod) {
            // Menghitung total down_payment dengan payment_method_dp = id PaymentMethod saat ini
            $totalDownPayment = Transaction::where('payment_method_dp', $paymentMethod->id)
                ->whereHas('booking', function ($query) use ($startDate, $endDate) {
                    $query->where('booking_status', '>=', 2)
                        ->whereBetween('created_at', [$startDate, $endDate]);
                })->sum('down_payment');

            // Menghitung total remaining_payment dengan payment_method_remaining = id PaymentMethod saat ini
            $totalRemainingPayment = Transaction::where('payment_method_remaining', $paymentMethod->id)
                ->whereHas('booking', function ($query) use ($startDate, $endDate) {
                    $query->where('booking_status', '>=', 2)
                        ->whereBetween('updated_at', [$startDate, $endDate]);
                })->sum('remaining_payment');

            // Total untuk PaymentMethod saat ini
            $totals[$paymentMethod->name] = $totalDownPayment + $totalRemainingPayment;
            $totalTransaction += $totalDownPayment + $totalRemainingPayment;
        }

        // Query transactions within date range
        $transactions = Transaction::join('bookings', 'transactions.booking_id', '=', 'bookings.id')
            ->join('fields', 'bookings.field_id', '=', 'fields.id')
            ->select('transactions.*')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereHas('booking', function ($query) {
                    $query->where('bookings.booking_status', '>=', 2);
                })
                    ->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('bookings.created_at', [$startDate, $endDate])
                            ->orWhereBetween('bookings.updated_at', [$startDate, $endDate]);
                    });
            })
            ->orderBy(DB::raw('(SELECT pond_type_id FROM fields WHERE id = bookings.field_id)'))
            ->orderBy(DB::raw('(SELECT name FROM fields WHERE id = bookings.field_id)'))
            ->with('booking.field')
            ->get();


        foreach ($transactions as $transaction) {
            // Mengambil down payment dari transaksi pertama
            $downPayment = $transaction->down_payment;

            // Mengambil remaining payment dari transaksi pertama
            $remainingPayment = $transaction->remaining_payment;

            $dateStart = $startDate->format('Y-m-d');
            $dateEnd = $endDate->format('Y-m-d');

            $forDP = $transaction->created_at->format('Y-m-d') == $dateStart &&  $transaction->created_at->format('Y-m-d') == $dateEnd;
            $forRemaining = $transaction->updated_at->format('Y-m-d') == $dateStart &&  $transaction->updated_at->format('Y-m-d') == $dateEnd;

            $createdAt = $transaction->created_at->format('Y-m-d');
            $updatedAt = $transaction->updated_at->format('Y-m-d');

            if ($transaction->booking->booking_status == 2) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Bayar DP';
                $transaction->bg_color = 'primary';
            } elseif ($transaction->booking->booking_status == 3) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Batal';
                $transaction->bg_color = 'secondary';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $forDP) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Bayar DP';
                $transaction->bg_color = 'primary';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $forRemaining) {
                $transaction->total_payment = $remainingPayment;
                $transaction->status = 'Pelunasan';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $dateStart != $dateEnd && $dateEnd == $createdAt) {
                $transaction->total_payment = $downPayment;
                $transaction->status = 'Bayar DP';
                $transaction->bg_color = 'primary';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $dateStart != $dateEnd && $dateStart == $updatedAt) {
                $transaction->total_payment = $remainingPayment;
                $transaction->status = 'Pelunasan';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt != $updatedAt && $dateStart != $dateEnd) {
                $transaction->total_payment = $downPayment + $remainingPayment;
                $transaction->status = 'Lunas';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt == $updatedAt && $dateStart == $dateEnd) {
                $transaction->total_payment = $downPayment + $remainingPayment;
                $transaction->status = 'Pembayaran Lunas';
                $transaction->bg_color = 'success';
            } elseif ($transaction->booking->booking_status == 4 && $createdAt == $updatedAt && $dateStart != $dateEnd) {
                $transaction->total_payment = $downPayment + $remainingPayment;
                $transaction->status = 'Lunas';
                $transaction->bg_color = 'success';
            }
        }

        // Load view template
        $pdfView = view('admin.transaction.transaction-pdf', compact('transactions', 'startDate', 'endDate', 'totals', 'totalTransaction'));

        // Create PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($pdfView->render());

        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render PDF (optional: save to file)
        $dompdf->render();

        // Set PDF filename
        $dateStart = $startDate->format('Y-m-d');
        $dateEnd = $endDate->format('Y-m-d');
        $fileName = 'transaction' . $startDate->format('Ymd');
        if ($dateStart == $dateEnd) {
            $fileName .= '.pdf';
        } else {
            $fileName .= '-' . $endDate->format('Ymd') . '.pdf';
        }

        // Output PDF to browser
        return $dompdf->stream($fileName);
    }
}
