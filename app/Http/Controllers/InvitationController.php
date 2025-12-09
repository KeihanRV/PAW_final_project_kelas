<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Recipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvitationController extends Controller
{
    
    // Halaman Utama (List Undangan & Kontak)
    public function index()
    {
        $invitations = Auth::user()->invitations()->latest()->get();
        $recipients = Auth::user()->recipients()->orderBy('name')->get();
        return view('invitation.index', compact('invitations', 'recipients'));
    }

    // Simpan Kontak Baru
    public function storeRecipient(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'whatsapp_number' => 'required|numeric'
        ]);

        Auth::user()->recipients()->create($request->all());
        return back()->with('success', 'Contact added!');
    }

    public function update(Request $request, Invitation $invitation)
    {
        // Pastikan milik user sendiri
        if ($invitation->user_id !== Auth::id()) abort(403);

        $request->validate([
            'event_name' => 'required',
            'event_date' => 'required|date',
            'sender_name'=> 'nullable|string|max:255',
            'event_time' => 'required',
            'location'   => 'required',
            'poster'     => 'nullable|file|max:40960',
        ]);

        $data = $request->except(['poster', '_token', '_method']);

        // Handle ganti poster
        if ($request->hasFile('poster')) {
            // Hapus poster lama jika ada
            if ($invitation->poster_path) {
                Storage::disk('public')->delete($invitation->poster_path);
            }
            $data['poster_path'] = $request->file('poster')->store('invitation-posters', 'public');
        }

        $invitation->update($data);

        return back()->with('success', 'Template Updated Successfully!');
    }

    // Hapus Kontak
    public function destroyRecipient(Recipient $recipient)
    {
        if ($recipient->user_id == Auth::id()) $recipient->delete();
        return back()->with('success', 'Contact removed.');
    }
    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'location'   => 'required',
            'sender_name'=> 'nullable|string|max:255',
            'poster'     => 'nullable|file|max:40960', 
        ]);

        $path = null;
        
        // Kita tambahkan pengecekan manual sederhana di sini agar tetap aman
        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            
            // Cek ekstensi manual (lebih ramah error di Windows)
            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                return back()->withErrors(['poster' => 'File harus berupa gambar (jpg, png, gif).']);
            }

            $path = $file->store('invitation-posters', 'public');
        }

        Auth::user()->invitations()->create([
            'event_name' => $request->event_name,
            'event_date' => $request->event_date,
            'sender_name'  => $request->sender_name,
            'event_time' => $request->event_time,
            'location'   => $request->location,
            'message_body' => $request->message_body,
            'poster_path' => $path,
        ]);

        return back()->with('success', 'Invitation Template Created!');
    }

    // Hapus Template
    public function destroy(Invitation $invitation)
    {
        if ($invitation->user_id == Auth::id()) $invitation->delete();
        return back()->with('success', 'Template deleted.');
    }

    // Simulasi Kirim (AJAX)
   // --- UPDATE: SEND (FORMAT PESAN FORMAL) ---
    public function send(Request $request, Invitation $invitation)
    {
        $recipients = Recipient::whereIn('id', $request->recipients ?? [])->get();
        $count = $recipients->count();

        // 1. Ambil Nama Penerima (Contoh satu orang untuk preview)
        $sampleName = $count > 0 ? $recipients->first()->name : "[Nama Penerima]";
        
        // 2. Format Waktu Indonesia yang Lengkap & Formal
        // Pastikan locale Carbon diset ke ID (biasanya di AppServiceProvider, tapi kita paksa disini biar aman)
        \Carbon\Carbon::setLocale('id');
        $dateObj = \Carbon\Carbon::parse($invitation->event_date);
        $timeObj = \Carbon\Carbon::parse($invitation->event_time);

        $hari = $dateObj->translatedFormat('l');       // Senin
        $tanggal = $dateObj->translatedFormat('d F Y'); // 14 Agustus 2025
        $jam = $timeObj->format('H:i');                // 19:30

        // 3. Ambil Nama Pengirim (User yang login)
        $senderName = $invitation->sender_name ? $invitation->sender_name : Auth::user()->name;

        // 4. SUSUN FORMAT PESAN FORMAL
        $text  = "Kepada Yth,\n";
        $text .= "Bapak/Ibu/Saudara/i *$sampleName*\n";
        $text .= "di Tempat\n\n";

        $text .= "Dengan hormat,\n\n";
        $text .= "Melalui pesan ini, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk dapat menghadiri acara:\n\n";
        
        $text .= "✨ *" . strtoupper($invitation->event_name) . "* ✨\n\n"; // Nama acara bold & uppercase
        
        $text .= "Yang Insya Allah akan diselenggarakan pada:\n";
        $text .= "🗓️ Hari/Tanggal : $hari, $tanggal\n";
        $text .= "⏰ Pukul : $jam WIB - Selesai\n";
        $text .= "📍 Lokasi : $invitation->location\n\n";

        // Tambahkan Pesan Tambahan User (Jika ada)
        if($invitation->message_body) {
            $text .= "Catatan:\n" . $invitation->message_body . "\n\n";
        }

        $text .= "Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan untuk hadir memberikan doa restu.\n\n";
        $text .= "Atas perhatian dan kehadirannya, kami ucapkan terima kasih.\n\n";
        
        $text .= "Hormat kami,\n";
        $text .= "*$senderName*"; // Nama User Pengirim

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil mengirim simulasi ke $count penerima!",
            'preview_text' => $text 
        ]);
    }

    // Update Kontak
    public function updateRecipient(Request $request, Recipient $recipient)
    {
        // Pastikan milik user sendiri
        if ($recipient->user_id !== Auth::id()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'required|numeric'
        ]);

        $recipient->update([
            'name' => $request->name,
            'whatsapp_number' => $request->whatsapp_number
        ]);

        return back()->with('success', 'Contact updated successfully!');
    }
}