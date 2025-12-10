<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipient;
use App\Models\User;

class FilkomRecipientSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil SEMUA User yang ada di database
        $users = User::all();

        // Cek validasi kalau belum ada user sama sekali
        if ($users->isEmpty()) {
            $this->command->error('Belum ada user! Silakan register minimal 1 akun dulu di website.');
            return;
        }

        // 2. Data Dosen Departemen Sistem Informasi (SI/TI/PTI)
        $lecturers = [
            ['name' => 'Rizal Setya Perdana, S.Kom., M.Kom., Ph.D.', 'wa' => '081299990001'],
            ['name' => 'Buce Trias Hanggara, S.Kom., M.Kom.', 'wa' => '081299990002'],
            ['name' => 'Putra Pandu Adikara, S.Kom., M.Kom.', 'wa' => '081299990003'],
            ['name' => 'Djoko Pramono, S.T., M.Kom.', 'wa' => '081299990004'],
            ['name' => 'Ir. Tri Astoto Kurniawan, S.T., M.T., Ph.D., IPM.', 'wa' => '081299990005'],
            ['name' => 'Bayu Rahayudi, S.T., M.T.', 'wa' => '081299990006'],
            ['name' => 'Uun Hariyanti, S.Pd., M.Pd., Ph.D.', 'wa' => '081299990007'],
            ['name' => 'Suprapto, S.T., M.T.', 'wa' => '081299990008'],
            ['name' => 'Dr. Eng. Herman Tolle, S.T., M.T.', 'wa' => '081200000009'], 
            ['name' => 'Ismiarta Aknuranda, S.T., M.Sc., Ph.D.', 'wa' => '081200000010'], 
            ['name' => 'Retno Indah Rokhmawati, S.Pd., M.Pd.', 'wa' => '081200000011'], 
            ['name' => 'Admaja Dwi Herlambang, S.Pd., M.Pd.', 'wa' => '081200000012'], 
            ['name' => 'Issa Arwani, S.Kom., M.Sc.', 'wa' => '081200000013'],
            ['name' => 'Drs. Marji, M.T.', 'wa' => '081200000014'],
            ['name' => 'Komang Candra Brata, S.Kom., M.T., M.Sc.', 'wa' => '081200000015'],
            ['name' => 'Wibisono Sukmo Wardhono, S.T., M.T.', 'wa' => '081200000016'],
            ['name' => 'Dr. Eng. Ahmad Afif Supianto, S.Si., M.Kom.', 'wa' => '081200000017'],
            ['name' => 'Dr. Ir. Fajar Pradana, S.ST., M.Eng.', 'wa' => '081200000018'],
            ['name' => 'Yusi Tyroni Mursityo, S.Kom., M.AB.', 'wa' => '081200000019'],
            ['name' => 'Satrio Hadi Wijoyo, S.Si., S.Pd., M.Kom.', 'wa' => '081200000020'],
            ['name' => 'Diah Priharsari, S.T., M.T., Ph.D.', 'wa' => '081200000021'],
            ['name' => 'Intan Sartika Eris Maghfiroh, S.T., M.Kom.', 'wa' => '081200000022'],
            ['name' => 'Fatwa Ramdani, S.Si., M.Sc., D.Sc.', 'wa' => '081200000023'],
            ['name' => 'Tri Afirianto, S.T., M.T.', 'wa' => '081200000024'],
            ['name' => 'Heru Nurwarsito, S.Kom., M.Kom.', 'wa' => '081200000025'],
            ['name' => 'Widhy Hayuhardhika N. P., S.Kom., M.Kom.', 'wa' => '081200000026'],
            ['name' => 'Nanang Yudi Setiawan, S.T., M.Kom.', 'wa' => '081200000027'],
            ['name' => 'Edy Santoso, S.Si., M.Kom.', 'wa' => '081200000028'],
        ];

        $this->command->info('Mulai memasukkan data untuk ' . $users->count() . ' user...');

        // 3. Loop ke SETIAP User (Ini bagian terpenting!)
        foreach ($users as $user) {
            
            $this->command->info('Processing User: ' . $user->name);

            // A. Hapus data lama milik user ini (RESET per user)
            // Ini mencegah duplikasi data jika seeder dijalankan berulang kali
            Recipient::where('user_id', $user->id)->delete();

            // B. Masukkan Data Baru ke User ini
            foreach ($lecturers as $dosen) {
                Recipient::create([
                    'user_id' => $user->id,
                    'name'    => $dosen['name'],
                    'whatsapp_number' => $dosen['wa']
                ]);
            }
        }
        
        $this->command->info('Selesai! Semua user sekarang punya data dosen.');
    }
}