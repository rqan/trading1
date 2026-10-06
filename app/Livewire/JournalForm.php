<?php

namespace App\Livewire;

use App\Models\Casting;
use App\Models\Journal;
use App\Models\JournalPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class JournalForm extends Component
{
    use WithFileUploads;

    // ── Mode edit ───────────────────────────────────────────
    public ?Journal $journal = null;

    // ── Bagian 1: Momennya ──────────────────────────────────
    public string $title = '';

    public ?int $castingId = null;

    public string $castingSearch = '';

    public array $castingSuggestions = [];

    public string $acquisitionType = 'beli';

    public string $acquiredAt = '';

    public ?string $price = null;

    public string $story = '';

    // ── Bagian 2: Lokasi ────────────────────────────────────
    public ?float $latitude = null;

    public ?float $longitude = null;

    public string $placeName = '';

    public string $placeAddress = '';

    // ── Bagian 3: Foto ──────────────────────────────────────
    public array $newPhotos = [];          // TemporaryUploadedFile[]

    public array $existingPhotos = [];          // [{id, url, order, delete}]

    public string $photoWarning = '';

    // ── Visibilitas ─────────────────────────────────────────
    public string $visibility = 'private';

    public bool $showInFeed = false;

    // ── Autocomplete casting ─────────────────────────────────
    public bool $showCastingDropdown = false;

    public function mount(?Journal $journal = null): void
    {
        if ($journal && $journal->exists) {
            // Mode edit — pastikan hanya pemilik
            if (! $journal->isOwnedBy(Auth::id())) {
                abort(403);
            }

            $this->journal = $journal;
            $this->title = $journal->title;
            $this->castingId = $journal->casting_id;
            $this->castingSearch = $journal->casting?->name ?? '';
            $this->acquisitionType = $journal->acquisition_type;
            $this->acquiredAt = $journal->acquired_at->format('Y-m-d');
            $this->price = $journal->price ? (string) $journal->price : null;
            $this->story = $journal->story ?? '';
            $this->latitude = (float) $journal->latitude;
            $this->longitude = (float) $journal->longitude;
            $this->placeName = $journal->place_name ?? '';
            $this->placeAddress = $journal->place_address ?? '';
            $this->visibility = $journal->visibility;
            $this->showInFeed = (bool) $journal->show_in_feed;

            $this->existingPhotos = $journal->photos->map(fn ($p) => [
                'id' => $p->id,
                'url' => $p->url,
                'order' => $p->order,
                'delete' => false,
            ])->all();
        } else {
            $this->acquiredAt = now()->format('Y-m-d');
        }
    }

    // ── Autocomplete casting ─────────────────────────────────

    public function updatedCastingSearch(string $value): void
    {
        if (strlen($value) < 2) {
            $this->castingSuggestions = [];
            $this->showCastingDropdown = false;

            return;
        }

        $this->castingSuggestions = Casting::where('name', 'like', "%{$value}%")
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'year', 'series'])
            ->toArray();

        $this->showCastingDropdown = ! empty($this->castingSuggestions);
    }

    public function selectCasting(int $id, string $name): void
    {
        $this->castingId = $id;
        $this->castingSearch = $name;
        $this->castingSuggestions = [];
        $this->showCastingDropdown = false;
    }

    public function clearCasting(): void
    {
        $this->castingId = null;
        $this->castingSearch = '';
        $this->castingSuggestions = [];
        $this->showCastingDropdown = false;
    }

    // ── Lokasi (dipanggil dari JS via @this.call) ────────────

    public function setLocation(float $lat, float $lng, string $placeName = '', string $placeAddress = ''): void
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->placeName = $placeName;
        $this->placeAddress = $placeAddress;
    }

    // ── Foto ─────────────────────────────────────────────────

    public function updatedNewPhotos(): void
    {
        $this->photoWarning = '';

        // Hitung slot yang tersisa
        $kept = collect($this->existingPhotos)->where('delete', false)->count();
        $slots = 3 - $kept;

        if (count($this->newPhotos) > $slots) {
            $this->photoWarning = "Hanya {$slots} slot foto tersisa. ".(count($this->newPhotos) - $slots).' foto terakhir diabaikan.';
            $this->newPhotos = array_slice($this->newPhotos, 0, $slots);
        }
    }

    public function markPhotoDelete(int $index): void
    {
        if (isset($this->existingPhotos[$index])) {
            $this->existingPhotos[$index]['delete'] = ! $this->existingPhotos[$index]['delete'];
        }
    }

    public function removeNewPhoto(int $index): void
    {
        array_splice($this->newPhotos, $index, 1);
        $this->photoWarning = '';
    }

    // ── Simpan ───────────────────────────────────────────────

    public function save(): void
    {
        $this->validate($this->rules());

        // Lokasi wajib
        if ($this->latitude === null || $this->longitude === null) {
            $this->addError('latitude', 'Pilih lokasi di peta sebelum menyimpan.');

            return;
        }

        $data = [
            'user_id' => Auth::id(),
            'casting_id' => $this->castingId,
            'title' => $this->title,
            'acquisition_type' => $this->acquisitionType,
            'acquired_at' => $this->acquiredAt,
            'price' => $this->acquisitionType === 'beli' ? ($this->price ?: null) : null,
            'story' => $this->story ?: null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'place_name' => $this->placeName ?: null,
            'place_address' => $this->placeAddress ?: null,
            'visibility' => $this->visibility,
            // show_in_feed hanya berlaku saat publik; reset ke false jika private
            'show_in_feed' => $this->visibility === 'public' && $this->showInFeed,
        ];

        if ($this->journal) {
            // Mode edit
            $this->journal->update($data);
            $journal = $this->journal;

            // Hapus foto yang ditandai
            foreach ($this->existingPhotos as $ep) {
                if ($ep['delete']) {
                    $photo = JournalPhoto::find($ep['id']);
                    if ($photo && $photo->journal_id === $this->journal->id) {
                        Storage::delete($photo->path);
                        $photo->delete();
                    }
                }
            }
        } else {
            // Mode buat baru
            $journal = Journal::create($data);
        }

        // Simpan foto baru
        $kept = collect($this->existingPhotos)->where('delete', false)->count();
        foreach ($this->newPhotos as $i => $photo) {
            $path = $photo->store('journal-photos');
            $journal->photos()->create([
                'path' => $path,
                'order' => $kept + $i,
            ]);
        }

        session()->flash('message', $this->journal ? 'Jurnal berhasil diperbarui.' : 'Jurnal berhasil disimpan.');
        $this->redirect(route('journals.show', $journal), navigate: true);
    }

    // ── Validasi ─────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'castingId' => ['nullable', 'integer', Rule::exists('castings', 'id')],
            'acquisitionType' => ['required', Rule::in(['beli', 'hadiah', 'tukar', 'temuan'])],
            'acquiredAt' => ['required', 'date', 'before_or_equal:today'],
            'price' => ['nullable', 'integer', 'min:0'],
            'story' => ['nullable', 'string', 'max:2000'],
            'visibility' => ['required', Rule::in(['private', 'public'])],
            'showInFeed' => ['boolean'],
            'newPhotos' => ['array', 'max:3'],
            'newPhotos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ];
    }

    protected $messages = [
        'acquiredAt.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
        'newPhotos.*.image' => 'File harus berupa gambar.',
        'newPhotos.*.max' => 'Ukuran foto tidak boleh melebihi 4 MB.',
    ];

    // ── Render ───────────────────────────────────────────────

    public function render()
    {
        return view('livewire.journal-form');
    }
}
