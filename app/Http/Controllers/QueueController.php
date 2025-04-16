<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    // Tampilkan halaman antrian berdasarkan role
    public function index()
    {
        $user = Auth::user();

        if ($user->role->name == 'admin') {
            $queue = Queue::with('user')->orderBy('id')->get();
            return view('admin.queue.list', compact('queue'));
        
        } elseif ($user->role->name == 'user') {
            // Ambil semua antrian dengan relasi user
            $queue = Queue::with('user')->orderBy('id')->get();
    
            // Ambil antrian terakhir milik user
            $userQueue = Queue::where('user_id', $user->id)->latest()->first();
    
            // Ambil antrian terakhir secara global
            $lastQueue = Queue::latest()->first();
    
            // Cek apakah user bisa mengambil antrian lagi (jeda 30 detik)
            $canTakeQueue = !$userQueue || $userQueue->created_at->diffInSeconds(now()) >= 30;
    
            // Cek apakah user dapat melanjutkan ke booking
            $canBook = $userQueue && $userQueue->status == 'Sedang Dilayani'; 
            //&& $userQueue->updated_at->gt(now()->subMinutes(1));
    
            // Tambahkan logika apakah antrian terakhir adalah milik user saat ini
            $isLastQueueUser = $lastQueue && $lastQueue->user_id == $user->id;
    
            return view('user.queue.list', compact('queue', 'canTakeQueue', 'canBook', 'isLastQueueUser'));
        }

        return abort(403, 'Unauthorized action.');
    }

    // Ambil nomor antrian baru (Khusus User)
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name != 'user') {
            return redirect()->route('admin.queue.index')->with('error', 'Akses ditolak.');
        }

        // Cek apakah user sudah memiliki antrian aktif dalam 2 menit terakhir
        $activeQueue = Queue::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes(2))
            ->first();


        if ($activeQueue) {
            return redirect()->route('user.queue.index')->with('error', 'Anda sudah memiliki nomor antrian aktif. Tunggu beberapa saat.');
        }

        // Ambil nomor antrian terakhir
        $lastQueue = Queue::orderBy('id', 'desc')->first();

        // Buat nomor antrian baru (PK001, PK002, dst.)
        $newQueueNumber = 'A-' . str_pad(($lastQueue ? intval(substr($lastQueue->queue_number, 2)) + 1 : 1), 3, '0', STR_PAD_LEFT);

        // Cek jika tidak ada yang sedang dilayani, maka antrian baru otomatis dilayani
        $status = Queue::where('status', 'Sedang Dilayani')->exists() ? 'Menunggu' : 'Sedang Dilayani';

        // Simpan ke database
        $queue = Queue::create([
            'user_id' => $user->id,
            'queue_number' => $newQueueNumber,
            'status' => $status,
            'created_at' => now() // Simpan waktu pembuatan antrian
        ]);

        return redirect()->route('user.queue.index')->with('success', 'Nomor antrian Anda: ' . $newQueueNumber);
    }

    // Fungsi AJAX untuk update status otomatis
    public function autoUpdateStatus()
    {
        // Cek antrian yang sedang dilayani (FCFS)
        $currentQueue = Queue::where('status', 'Sedang Dilayani')->orderBy('id')->first();

        if ($currentQueue) {
            // Ubah status menjadi "Selesai" setelah 2 Menit dari status "Sedang Dilayani"
            if ($currentQueue->updated_at->diffInMinutes(now()) >= 2) {
                $currentQueue->update(['status' => 'Selesai']);
                //$currentQueue->status = 'Selesai';
                //$currentQueue->save();

                // Ambil antrian berikutnya yang "Menunggu"
                $nextQueue = Queue::where('status', 'Menunggu')->orderBy('id')->first();
                if ($nextQueue) {
                    $nextQueue->update(['status' => 'Sedang Dilayani']);
                    //$nextQueue->status = 'Sedang Dilayani';
                    //$nextQueue->save();
                }
            }
        }

        return response()->json(['success' => true]);
    }

    // Update status antrian (Khusus Admin)
    public function update($id, Request $request)
    {
        $user = Auth::user();

        if ($user->role->name != 'admin') {
            return redirect()->route('user.queue.index')->with('error', 'Akses ditolak.');
        }

        $queue = Queue::findOrFail($id);

        if ($queue->status == 'Menunggu') {
            $queue->status = 'Sedang Dilayani';
        } elseif ($queue->status == 'Sedang Dilayani') {
            $queue->status = 'Selesai';

            // Ambil antrian berikutnya yang masih menunggu
            $nextQueue = Queue::where('status', 'Menunggu')->orderBy('id')->first();
            if ($nextQueue) {
                $nextQueue->status = 'Sedang Dilayani';
                $nextQueue->save();
            }
        }

        $queue->save();

        return redirect()->route('admin.queue.index')->with('success', 'Status antrian diperbarui.');
    }

    // Hapus antrian (Khusus Admin)
    public function destroyField($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->delete();

        return redirect()->route('admin.queue.index')->with('success', 'Data antrian berhasil dihapus.');
    }
}
