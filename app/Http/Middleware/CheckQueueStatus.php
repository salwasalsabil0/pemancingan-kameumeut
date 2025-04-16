<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Queue;

class CheckQueueStatus
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $queue = Queue::where('user_id', $user->id)->latest()->first();

        // Redirect jika antrian belum selesai
        if (!$queue || $queue->status != 'Selesai') {
            return redirect()->route('user.queue.index')->with('error', 'Selesaikan antrian sebelum booking.');
        }

        return $next($request);
    }
}
