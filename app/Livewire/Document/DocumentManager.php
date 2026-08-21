<?php

namespace App\Livewire\Document;

use App\Actions\Document\StoreDocumentAction;
use App\Enums\DocumentCategoryEnum;
use App\Models\Document;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DocumentManager extends Component
{
    use WithFileUploads;

    public Model $model;

    public $file = null;

    public string $title = '';

    public string $category = 'other';

    public string $notes = '';

    public bool $uploadModalOpen = false;

    public function mount(Model $model): void
    {
        $this->model = $model;
    }

    public function uploadDocument(): void
    {
        $this->validate([
            'file'     => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240', // max 10MB
            'title'    => 'nullable|string|max:255',
            'category' => 'required|string',
            'notes'    => 'nullable|string|max:500',
        ], [
            'file.required' => 'Selecione um arquivo para upload.',
            'file.mimes'    => 'Formato de arquivo não permitido. Aceito apenas PDF, JPG, PNG e WEBP.',
            'file.max'      => 'O arquivo não pode ultrapassar o tamanho limite de 10MB.',
        ]);

        app(StoreDocumentAction::class)->execute(
            documentable: $this->model,
            file: $this->file,
            category: $this->category,
            title: $this->title ?: null,
            notes: $this->notes ?: null
        );

        $this->reset(['file', 'title', 'category', 'notes', 'uploadModalOpen']);

        Notification::make()
            ->title('Documento Armazenado com Sucesso!')
            ->body('Arquivo gravado com segurança no storage privado e protegido por LGPD.')
            ->success()
            ->send();
    }

    public function deleteDocument(int $documentId): void
    {
        $document = Document::findOrFail($documentId);

        // Deleta o arquivo físico do disco privado
        if (Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }

        $document->delete();

        Notification::make()
            ->title('Documento Removido')
            ->warning()
            ->send();
    }

    public function getDocumentsProperty(): Collection
    {
        return $this->model->documents()->latest()->get();
    }

    public function render()
    {
        return view('livewire.document.document-manager', [
            'documents'  => $this->documents,
            'categories' => DocumentCategoryEnum::options(),
        ]);
    }
}
