<div>
    {{-- Trigger Button --}}
    <button wire:click="openModal"
            class="mt-2 w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-medium transition-colors">
        🤝 Propose Trade
    </button>

    {{-- Modal Overlay --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
             x-data @keydown.escape.window="$wire.closeModal()">
            <div class="bg-white rounded-lg w-full max-w-lg mx-4 max-h-[85vh] flex flex-col shadow-xl">

                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-bold text-slate-900">
                        @if($showConfirmation)
                            Konfirmasi Trade
                        @else
                            Pilih Item untuk Ditawarkan
                        @endif
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Flash Messages --}}
                @if(session()->has('error'))
                    <div class="mx-6 mt-4 p-3 text-sm text-red-800 rounded-lg bg-red-50">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- STEP 1: Item Selection --}}
                @if(!$showConfirmation)
                    <div class="px-6 py-4 overflow-y-auto flex-1">
                        @error('selectedItems')
                            <p class="text-red-600 text-sm mb-3">{{ $message }}</p>
                        @enderror

                        @if($myCollection && $myCollection->count() > 0)
                            <div class="space-y-2">
                                @foreach($myCollection as $item)
                                    <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors
                                                  {{ in_array($item->id, $selectedItems) ? 'border-orange-400 bg-orange-50' : 'border-slate-200 hover:bg-slate-50' }}">
                                        <input type="checkbox"
                                               wire:model="selectedItems"
                                               value="{{ $item->id }}"
                                               class="mt-1 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-sm text-slate-900">
                                                {{ $item->casting->name ?? 'Unknown' }}
                                                <span class="text-slate-500 font-normal">({{ $item->casting->year ?? '-' }})</span>
                                            </p>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                Kondisi: {{ $item->condition }} · Card: {{ $item->card_condition ?? '-' }}
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-slate-500">
                                <p class="text-sm">Kamu belum memiliki item yang tersedia di koleksi.</p>
                                <a href="{{ route('dashboard') }}" class="text-orange-600 hover:underline text-sm mt-2 inline-block">
                                    Tambah ke Koleksi →
                                </a>
                            </div>
                        @endif

                        {{-- Note Field --}}
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Catatan (opsional)</label>
                            <textarea wire:model="note"
                                      rows="2"
                                      maxlength="500"
                                      placeholder="Tulis pesan untuk pemilik item..."
                                      class="w-full rounded-lg border-slate-300 text-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                            <p class="text-xs text-slate-400 mt-1">{{ strlen($note) }}/500 karakter</p>
                        </div>
                    </div>

                    {{-- Footer: Review Button --}}
                    <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-2 shrink-0">
                        <button wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                            Batal
                        </button>
                        <button wire:click="reviewProposal"
                                class="px-4 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 transition-colors disabled:opacity-50"
                                @if(count($selectedItems) === 0) disabled @endif>
                            Review Proposal →
                        </button>
                    </div>
                @endif

                {{-- STEP 2: Confirmation --}}
                @if($showConfirmation)
                    <div class="px-6 py-4 overflow-y-auto flex-1">
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                            <p class="text-sm font-medium text-orange-800 mb-2">Kamu akan menawarkan {{ count($selectedItems) }} item:</p>
                            <ul class="space-y-1">
                                @foreach($myCollection->whereIn('id', $selectedItems) as $item)
                                    <li class="text-sm text-orange-700 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-400 shrink-0"></span>
                                        {{ $item->casting->name ?? 'Unknown' }} ({{ $item->casting->year ?? '-' }})
                                        — {{ $item->condition }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @if(!empty(trim($note)))
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Catatan</p>
                                <p class="text-sm text-slate-700 italic">"{{ $note }}"</p>
                            </div>
                        @endif

                        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <p class="text-sm text-amber-800">
                                ⚠️ <strong>Apakah kamu yakin?</strong> Setelah dikirim, pemilik item akan menerima notifikasi dan bisa mereview penawaranmu.
                            </p>
                        </div>
                    </div>

                    {{-- Footer: Confirm Buttons --}}
                    <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-2 shrink-0">
                        <button wire:click="backToSelection"
                                class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                            ← Kembali
                        </button>
                        <button wire:click="propose"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50">
                            <span wire:loading.remove wire:target="propose">✅ Confirm & Send</span>
                            <span wire:loading wire:target="propose">Mengirim...</span>
                        </button>
                    </div>
                @endif

            </div>
        </div>
    @endif
</div>
