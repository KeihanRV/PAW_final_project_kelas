<x-app-layout title="Invitation">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orator+Std&family=Reenie+Beanie&display=swap');
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .paper-texture { background-image: linear-gradient(#EBE2D1 1px, transparent 1px); background-size: 100% 24px; }
        .cursor-zoom-in { cursor: zoom-in; }
        .cursor-zoom-out { cursor: zoom-out; }
    </style>

    <div class="min-h-screen w-full bg-[#dcdcdc] flex flex-col items-center py-8 px-4 font-sans">
        
        <h1 class="font-orator text-4xl md:text-5xl text-[#4A3B32] tracking-[0.2em] uppercase mb-8 text-center">
            INVITATION &nbsp; MANAGER
        </h1>

        <div class="flex flex-col lg:flex-row w-full max-w-7xl h-[85vh] gap-6">
            
            <div class="w-full lg:w-1/3 bg-[#EBE2D1] rounded-xl shadow-xl border-l-[8px] border-[#3e3226] p-6 flex flex-col relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none font-orator text-6xl rotate-12">CONTACTS</div>
                <h2 class="font-orator text-xl text-[#4A3B32] border-b-2 border-[#4A3B32] pb-2 mb-4">ADDRESS BOOK</h2>

                <div class="flex gap-2 mb-4 z-10 relative">
                    <div class="relative flex-1">
                        <input type="text" id="searchRecipient" placeholder="Search Name..." 
                               class="w-full bg-[#dcd0bc]/40 border border-[#4A3B32]/30 rounded px-2 py-1 text-sm font-orator text-[#4A3B32] focus:border-[#4A3B32] focus:ring-0 placeholder-[#4A3B32]/40">
                    </div>
                    <button id="sortButton" onclick="toggleSort()" 
                            class="bg-[#4A3B32]/10 hover:bg-[#4A3B32]/20 text-[#4A3B32] px-3 py-1 rounded border border-[#4A3B32]/20 transition flex items-center gap-1 font-orator text-xs whitespace-nowrap"
                            title="Sort Alphabetically">
                        <span>AZ</span> ↓
                    </button>
                </div>

                <form action="{{ route('recipient.store') }}" method="POST" class="mb-6 bg-[#dcd0bc]/50 p-4 rounded border border-[#4A3B32]/20">
                    @csrf
                    <div class="grid gap-2">
                        <input type="text" name="name" placeholder="Full Name" required class="bg-transparent border-b border-[#4A3B32] focus:border-[#4A3B32] focus:ring-0 placeholder-[#4A3B32]/50 text-sm font-orator text-[#4A3B32]">
                        <div class="flex gap-2">
                            <input type="number" name="whatsapp_number" placeholder="62812..." required class="w-full bg-transparent border-b border-[#4A3B32] focus:border-[#4A3B32] focus:ring-0 placeholder-[#4A3B32]/50 text-sm font-orator text-[#4A3B32]">
                            <button type="submit" class="bg-[#4A3B32] text-[#EBE2D1] px-3 py-1 rounded text-xs font-orator hover:bg-[#2c231e] transition">ADD</button>
                        </div>
                    </div>
                </form>

                <div id="recipientList" class="flex-1 overflow-y-auto no-scrollbar space-y-2 pr-2">
                    @forelse($recipients as $recipient)
                        <div class="recipient-item group flex justify-between items-center p-2 hover:bg-[#dcd0bc] rounded transition cursor-default">
                            <div>
                                <div class="recipient-name font-bold text-[#4A3B32] font-reenie text-xl">{{ $recipient->name }}</div>
                                <div class="text-[10px] font-orator text-[#4A3B32]/70">{{ $recipient->whatsapp_number }}</div>
                            </div>
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                                <button onclick="openEditRecipientModal({{ $recipient->id }}, '{{ $recipient->name }}', '{{ $recipient->whatsapp_number }}')" class="text-[#4A3B32] hover:text-[#2c231e]" title="Edit">✎</button>
                                <form action="{{ route('recipient.destroy', $recipient->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-600" onclick="return confirm('Delete contact?')" title="Delete">✕</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-[#4A3B32]/40 font-reenie text-xl mt-10">No contacts yet...</div>
                    @endforelse
                </div>
            </div>

            <div class="w-full lg:w-2/3 flex flex-col gap-6">
                <div class="bg-[#EBE2D1] rounded-xl shadow-lg p-6 relative">
                    <button onclick="document.getElementById('createTemplateModal').classList.remove('hidden')" class="w-full h-16 border-2 border-dashed border-[#4A3B32]/40 rounded-lg flex items-center justify-center gap-2 text-[#4A3B32]/60 hover:bg-[#dcd0bc] hover:text-[#4A3B32] transition font-orator">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        CREATE NEW INVITATION TEMPLATE
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto no-scrollbar grid grid-cols-1 md:grid-cols-2 gap-4 pb-10">
                    @forelse($invitations as $inv)
                        <div class="bg-[#EBE2D1] rounded-xl shadow-md overflow-hidden flex flex-col border border-[#4A3B32]/10 relative group">
                            
                            <div class="absolute top-2 right-2 z-10 flex gap-2 opacity-0 group-hover:opacity-100 transition">
                                <button onclick="openEditModal({{ json_encode($inv) }})" class="bg-[#4A3B32] text-[#EBE2D1] w-6 h-6 rounded-full flex items-center justify-center text-xs hover:bg-[#2c231e]">✎</button>
                                <form action="{{ route('invitation.destroy', $inv->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="bg-red-800 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs" onclick="return confirm('Delete template?')">✕</button>
                                </form>
                            </div>

                            <div class="h-40 bg-[#dcdcdc] relative overflow-hidden flex items-center justify-center cursor-zoom-in" 
                                 onclick="inspectImage('{{ $inv->poster_path ? asset('storage/'.$inv->poster_path) : '' }}')">
                                @if($inv->poster_path)
                                    <img src="{{ asset('storage/'.$inv->poster_path) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="font-orator text-[#4A3B32]/30 text-xs">NO POSTER</span>
                                @endif
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition flex items-center justify-center">
                                    <span class="text-white font-orator text-xs opacity-0 group-hover:opacity-100 bg-black/50 px-2 py-1 rounded">Click to Inspect</span>
                                </div>
                            </div>

                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="font-orator text-lg font-bold text-[#4A3B32] leading-tight">{{ $inv->event_name }}</h3>
                                <div class="text-[10px] font-orator text-[#4A3B32]/60 mt-0.5">
                                    FROM: {{ $inv->sender_name ?: Auth::user()->name }}
                                </div>

                                <div class="text-xs font-orator text-[#4A3B32]/70 mt-2 flex gap-2">
                                    <span>{{ $inv->event_date->format('d M Y') }}</span> • <span>{{ \Carbon\Carbon::parse($inv->event_time)->format('H:i') }}</span>
                                </div>
                                <div class="text-xs font-orator text-[#4A3B32]/70 mt-1 truncate">📍 {{ $inv->location }}</div>

                                <button onclick='openSendModal(@json($inv))' class="mt-4 w-full bg-[#4A3B32] text-[#EBE2D1] py-2 rounded text-xs font-orator tracking-widest hover:bg-[#2c231e] transition flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
                                    SEND INVITATION
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center text-[#4A3B32]/30 h-40">
                            <span class="font-reenie text-2xl">No invitation templates yet...</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div id="createTemplateModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-[#EBE2D1] w-full max-w-lg rounded-xl shadow-2xl p-6 relative border-t-[8px] border-[#4A3B32] max-h-[90vh] overflow-y-auto">
            <button onclick="document.getElementById('createTemplateModal').classList.add('hidden')" class="absolute top-4 right-4 text-[#4A3B32] hover:text-red-600">✕</button>
            <h2 class="font-orator text-2xl text-[#4A3B32] mb-6 text-center underline decoration-2 underline-offset-4">DRAFT INVITATION</h2>
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <strong class="font-bold">Error:</strong>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('invitation.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">EVENT NAME</label>
                    <input type="text" name="event_name" required class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-reenie text-xl px-0 placeholder-[#4A3B32]/30">
                </div>
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">SENDER NAME (Optional)</label>
                    <input type="text" name="sender_name" class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-orator text-sm px-0 placeholder-[#4A3B32]/30" placeholder="Default: {{ Auth::user()->name }}">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-orator text-[#4A3B32] mb-1">DATE</label>
                        <input type="date" name="event_date" required class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-orator text-sm px-0">
                    </div>
                    <div>
                        <label class="block text-xs font-orator text-[#4A3B32] mb-1">TIME</label>
                        <input type="time" name="event_time" required class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-orator text-sm px-0">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">LOCATION</label>
                    <input type="text" name="location" required class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-reenie text-xl px-0 placeholder-[#4A3B32]/30">
                </div>
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">POSTER (Optional)</label>
                    <input type="file" name="poster" class="w-full text-xs text-[#4A3B32]">
                </div>
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">MESSAGE / LETTER (Optional)</label>
                    <textarea name="message_body" rows="3" class="w-full bg-[#dcd0bc]/30 border-none rounded font-reenie text-lg text-[#4A3B32] focus:ring-0 placeholder-[#4A3B32]/30"></textarea>
                </div>
                <button type="submit" class="w-full bg-[#4A3B32] text-[#EBE2D1] py-3 rounded mt-4 font-orator tracking-widest hover:bg-[#2c231e] transition shadow-lg">CREATE TEMPLATE</button>
            </form>
        </div>
    </div>

    <div id="editTemplateModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-[#EBE2D1] w-full max-w-lg rounded-xl shadow-2xl p-6 relative border-t-[8px] border-[#4A3B32] max-h-[90vh] overflow-y-auto">
            <button onclick="document.getElementById('editTemplateModal').classList.add('hidden')" class="absolute top-4 right-4 text-[#4A3B32] hover:text-red-600">✕</button>
            <h2 class="font-orator text-2xl text-[#4A3B32] mb-6 text-center underline decoration-2 underline-offset-4">EDIT INVITATION</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">EVENT NAME</label>
                    <input type="text" name="event_name" id="edit_event_name" required class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-reenie text-xl px-0">
                </div>
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">SENDER NAME (Optional)</label>
                    <input type="text" name="sender_name" id="edit_sender_name" class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-orator text-sm px-0" placeholder="Default: {{ Auth::user()->name }}">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-orator text-[#4A3B32] mb-1">DATE</label>
                        <input type="date" name="event_date" id="edit_event_date" required class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-orator text-sm px-0">
                    </div>
                    <div>
                        <label class="block text-xs font-orator text-[#4A3B32] mb-1">TIME</label>
                        <input type="time" name="event_time" id="edit_event_time" required class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-orator text-sm px-0">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">LOCATION</label>
                    <input type="text" name="location" id="edit_location" required class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-reenie text-xl px-0">
                </div>
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">CHANGE POSTER (Optional)</label>
                    <input type="file" name="poster" class="w-full text-xs text-[#4A3B32]">
                </div>
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">MESSAGE</label>
                    <textarea name="message_body" id="edit_message_body" rows="3" class="w-full bg-[#dcd0bc]/30 border-none rounded font-reenie text-lg text-[#4A3B32] focus:ring-0"></textarea>
                </div>
                <button type="submit" class="w-full bg-[#4A3B32] text-[#EBE2D1] py-3 rounded mt-4 font-orator tracking-widest hover:bg-[#2c231e] transition shadow-lg">UPDATE TEMPLATE</button>
            </form>
        </div>
    </div>

    <div id="sendModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
         <div class="bg-[#EBE2D1] w-full max-w-3xl rounded-xl shadow-2xl p-6 relative border-t-[8px] border-[#4A3B32] flex flex-col md:flex-row gap-6 max-h-[90vh] overflow-hidden">
            <button onclick="closeSendModal()" class="absolute top-4 right-4 text-[#4A3B32] hover:text-red-600 z-10">✕</button>
            
            <div class="w-full md:w-1/3 flex flex-col">
                <h2 class="font-orator text-xl text-[#4A3B32] mb-1">SEND TO...</h2>
                <div class="mb-2 text-xs font-orator text-[#4A3B32]/70">SELECT RECIPIENTS</div>
                <div class="flex-1 overflow-y-auto border border-[#4A3B32]/20 rounded p-2 bg-white/50 mb-4 custom-scrollbar min-h-[200px]">
                    @forelse($recipients as $r)
                        <label class="flex items-center gap-3 p-2 hover:bg-[#4A3B32]/5 cursor-pointer rounded">
                            <input type="checkbox" value="{{ $r->id }}" class="recipient-checkbox w-4 h-4 text-[#4A3B32] border-[#4A3B32] focus:ring-[#4A3B32] rounded">
                            <div>
                                <div class="font-bold text-[#4A3B32] text-sm">{{ $r->name }}</div>
                                <div class="text-xs text-[#4A3B32]/60">{{ $r->whatsapp_number }}</div>
                            </div>
                        </label>
                    @empty
                        <div class="text-center text-xs p-4">No contacts found.</div>
                    @endforelse
                </div>
                <button onclick="processSending()" id="btnSendAction" class="w-full bg-[#4A3B32] text-[#EBE2D1] py-3 rounded font-orator tracking-widest hover:bg-[#2c231e] transition shadow-lg shrink-0">SEND NOW</button>
            </div>

            <div class="w-full md:w-2/3 bg-white/60 p-4 rounded border border-[#4A3B32]/10 flex flex-col">
                <h2 class="font-orator text-sm text-[#4A3B32] mb-2">MESSAGE PREVIEW:</h2>
                <textarea id="messagePreview" class="flex-1 w-full bg-transparent border-none font-sans text-sm text-[#4A3B32] resize-none focus:ring-0 p-0 h-full" readonly style="min-height: 300px;"></textarea>
                <div class="text-[10px] text-[#4A3B32]/50 mt-2 font-orator text-center">*Text generated automatically. Poster will be attached.</div>
            </div>
        </div>
    </div>

    <div id="editRecipientModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-[#EBE2D1] w-full max-w-sm rounded-xl shadow-2xl p-6 relative border-t-[8px] border-[#4A3B32]">
            <button onclick="document.getElementById('editRecipientModal').classList.add('hidden')" class="absolute top-4 right-4 text-[#4A3B32] hover:text-red-600">✕</button>
            
            <h2 class="font-orator text-xl text-[#4A3B32] mb-6 text-center">EDIT CONTACT</h2>
            
            <form id="editRecipientForm" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">FULL NAME</label>
                    <input type="text" name="name" id="edit_recipient_name" required class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-reenie text-xl px-0 text-[#4A3B32]">
                </div>
                <div>
                    <label class="block text-xs font-orator text-[#4A3B32] mb-1">WHATSAPP NUMBER</label>
                    <input type="number" name="whatsapp_number" id="edit_recipient_wa" required class="w-full bg-transparent border-b border-[#4A3B32] focus:ring-0 font-orator text-sm px-0 text-[#4A3B32]">
                </div>
                <button type="submit" class="w-full bg-[#4A3B32] text-[#EBE2D1] py-2 rounded mt-2 font-orator tracking-widest hover:bg-[#2c231e] transition shadow-lg">UPDATE CONTACT</button>
            </form>
        </div>
    </div>

    <div id="imageInspector" class="hidden fixed inset-0 bg-black/90 z-[60] flex items-center justify-center p-4 cursor-zoom-out" onclick="this.classList.add('hidden')">
        <img id="inspectedImage" src="" class="max-w-full max-h-full object-contain shadow-2xl border-4 border-[#EBE2D1]">
    </div>

    <script>
        // --- LOGIC BARU: SEARCH & SORT ---
        
        // 1. Search Logic
        document.getElementById('searchRecipient').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const items = document.querySelectorAll('.recipient-item');

            items.forEach(item => {
                const name = item.querySelector('.recipient-name').innerText.toLowerCase();
                if(name.includes(filter)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // 2. Sort Logic
        let isAscending = true;
        function toggleSort() {
            const list = document.getElementById('recipientList');
            // Ambil semua elemen recipient-item ke dalam Array
            const items = Array.from(list.getElementsByClassName('recipient-item'));
            const btnIcon = document.getElementById('sortButton').querySelector('span');
            const btn = document.getElementById('sortButton');

            items.sort((a, b) => {
                const nameA = a.querySelector('.recipient-name').innerText.toUpperCase();
                const nameB = b.querySelector('.recipient-name').innerText.toUpperCase();
                
                if (isAscending) {
                    return nameA.localeCompare(nameB); // ASC
                } else {
                    return nameB.localeCompare(nameA); // DESC
                }
            });

            // Re-append items (ini akan memindahkan posisi elemen di DOM)
            items.forEach(item => list.appendChild(item));

            // Flip state and update UI
            isAscending = !isAscending;
            if(isAscending) {
                btn.innerHTML = '<span>AZ</span> ↓';
            } else {
                btn.innerHTML = '<span>ZA</span> ↑';
            }
        }

        // --- END LOGIC BARU ---

        @if ($errors->any())
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById('createTemplateModal').classList.remove('hidden');
            });
        @endif

        function inspectImage(src) {
            if(!src) return;
            document.getElementById('inspectedImage').src = src;
            document.getElementById('imageInspector').classList.remove('hidden');
        }

        function openEditRecipientModal(id, name, wa) {
            document.getElementById('edit_recipient_name').value = name;
            document.getElementById('edit_recipient_wa').value = wa;
            document.getElementById('editRecipientForm').action = `/recipients/${id}`;
            document.getElementById('editRecipientModal').classList.remove('hidden');
        }

        function openEditModal(invitation) {
            document.getElementById('edit_event_name').value = invitation.event_name;
            document.getElementById('edit_sender_name').value = invitation.sender_name || ''; 
            document.getElementById('edit_event_date').value = invitation.event_date.split('T')[0];
            let time = invitation.event_time.split('T')[1].substring(0, 5); 
            document.getElementById('edit_event_time').value = time;
            document.getElementById('edit_location').value = invitation.location;
            document.getElementById('edit_message_body').value = invitation.message_body || '';
            document.getElementById('editForm').action = `/invitations/template/${invitation.id}`;
            document.getElementById('editTemplateModal').classList.remove('hidden');
        }

        let currentInvitationId = null;

        function openSendModal(invitation) {
            currentInvitationId = invitation.id;
            
            const dateObj = new Date(invitation.event_date);
            const timeString = invitation.event_time.split('T')[1].substring(0, 5); 

            const optionsHari = { weekday: 'long' };
            const optionsTanggal = { day: 'numeric', month: 'long', year: 'numeric' };
            const hari = dateObj.toLocaleDateString('id-ID', optionsHari);
            const tanggal = dateObj.toLocaleDateString('id-ID', optionsTanggal);

            const senderName = invitation.sender_name ? invitation.sender_name : '{{ Auth::user()->name }}';

            let text = "Kepada Yth,\n";
            text += "Bapak/Ibu/Saudara/i [Nama Penerima]\n";
            text += "di Tempat\n\n";

            text += "Dengan hormat,\n\n";
            text += "Melalui pesan ini, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk dapat menghadiri acara:\n\n";
            
            text += "✨ *" + invitation.event_name.toUpperCase() + "* ✨\n\n";
            
            text += "Yang Insya Allah akan diselenggarakan pada:\n";
            text += `🗓️ Hari/Tanggal : ${hari}, ${tanggal}\n`;
            text += `⏰ Pukul : ${timeString} WIB - Selesai\n`;
            text += `📍 Lokasi : ${invitation.location}\n\n`;

            if(invitation.message_body) {
                text += "Catatan:\n" + invitation.message_body + "\n\n";
            }

            text += "Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan untuk hadir memberikan doa restu.\n\n";
            text += "Atas perhatian dan kehadirannya, kami ucapkan terima kasih.\n\n";
            
            text += "Hormat kami,\n";
            text += "*" + senderName + "*"; 

            document.getElementById('messagePreview').value = text;
            document.querySelectorAll('.recipient-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('sendModal').classList.remove('hidden');
        }

        function closeSendModal() {
            document.getElementById('sendModal').classList.add('hidden');
        }

        async function processSending() {
            const selected = [];
            document.querySelectorAll('.recipient-checkbox:checked').forEach(cb => selected.push(cb.value));

            if(selected.length === 0) {
                alert("Please select at least one recipient!");
                return;
            }

            const btn = document.getElementById('btnSendAction');
            const originalText = btn.innerHTML;
            btn.innerHTML = "SENDING...";
            btn.disabled = true;

            try {
                const response = await fetch(`/invitations/${currentInvitationId}/send`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ recipients: selected })
                });
                const result = await response.json();
                if(response.ok) {
                    alert(result.message);
                    closeSendModal();
                } else {
                    alert("Failed to send.");
                }
            } catch (error) {
                console.error(error);
                alert("Error occurred.");
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    </script>
</x-app-layout>