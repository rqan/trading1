<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Club: {{ $club->name }}</h1>
            <a href="{{ route('clubs.show', $club) }}" class="text-sm text-slate-500 hover:text-orange-600">&larr; Kembali ke Club</a>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded relative">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Pengaturan Profil --}}
            <div class="md:col-span-1 bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h2 class="font-bold text-slate-800 mb-4">Pengaturan Dasar</h2>
                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Club</label>
                        <input type="text" wire:model="name" class="w-full border-slate-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                        <textarea wire:model="description" rows="3" class="w-full border-slate-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Peraturan (Rules)</label>
                        <textarea wire:model="rules" rows="4" class="w-full border-slate-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500"></textarea>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" wire:model="is_private" id="is_private" class="rounded text-orange-600 focus:ring-orange-500 mr-2">
                        <label for="is_private" class="text-sm font-medium text-slate-700">Privat (Hanya undangan)</label>
                    </div>
                    <button type="submit" class="w-full bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-orange-600 transition-colors">Simpan Perubahan</button>
                </form>
            </div>

            {{-- Manajemen Anggota --}}
            <div class="md:col-span-2 bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h2 class="font-bold text-slate-800 mb-4">Daftar Anggota</h2>
                <div class="overflow-hidden border border-slate-200 rounded-lg">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Bergabung</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($members as $member)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-slate-900">
                                        {{ $member->user->name }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @if($member->club_role === 'founder')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Founder</span>
                                        @elseif($member->club_role === 'co_founder')
                                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-semibold">Co-founder</span>
                                        @elseif($member->club_role === 'club_moderator')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">Moderator</span>
                                        @else
                                            <span class="px-2 py-1 bg-slate-100 text-slate-800 rounded-full text-xs">Member</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-500">
                                        {{ $member->joined_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        @if($member->club_role !== 'founder')
                                            <button wire:click="removeMember({{ $member->id }})" wire:confirm="Yakin ingin mengeluarkan user ini?" class="text-red-600 hover:text-red-900 text-xs">Keluarkan</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
