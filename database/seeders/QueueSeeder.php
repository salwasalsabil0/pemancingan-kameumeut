<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Queue;
use App\Models\User;

class QueueSeeder extends Seeder
{
    public function run()
    {
        // Pastikan ada user yang valid
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'], // Cek berdasarkan email
            [
                'name' => 'User Contoh',
                'username' => 'usercontoh',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'phone' => '081234567890',
            ]
        );

        // Cek apakah antrian sudah ada, jika belum maka tambahkan
        $queueData = [
            ['queue_number' => 1, 'served' => false],
            ['queue_number' => 2, 'served' => true],
            ['queue_number' => 3, 'served' => false],
        ];

        foreach ($queueData as $data) {
            Queue::firstOrCreate(
                ['user_id' => $user->id, 'queue_number' => $data['queue_number']],
                ['served' => $data['served']]
            );
        }

        echo "Seeder Queue berhasil dijalankan.\n";
    }
}
