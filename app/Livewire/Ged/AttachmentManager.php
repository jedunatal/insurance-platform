<?php

namespace App\Livewire\Ged;

use App\Actions\GED\DeleteAttachmentAction;
use App\Actions\GED\StoreAttachmentAction;
use App\Enums\AttachmentCategoryEnum;
use App\Models\Attachment;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class AttachmentManager extends Component
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

    public function uploadAttachment(): void
    {
        $this->validate([
            'file'     => 'required|file|max:10240', // max 10MB
            'title'    => 'required|string|min:3|max:255',
            'category' => 'required|string',
        ]);

        app(StoreAttachmentAction::class)->execute(
            attachable: $this->model,
            file: $this->file,
            title: $this->title,
            category: $this->category,
            notes: $this->notes ?: null
        );

        $this->reset(['file', 'title', 'category', 'notes', 'uploadModalOpen']);

        Notification::make()
            ->title('Documento Anexado com Sucesso!')
            ->success()
            ->send();
    }

    public function deleteAttachment(int $attachmentId): void
    {
        $attachment = Attachment::findOrFail($attachmentId);
        app(DeleteAttachmentAction::class)->execute($attachment);

        Notification::make()
            ->title('Documento Removido')
            ->warning()
            ->send();
    }

    public function getAttachmentsProperty(): Collection
    {
        return $this->model->attachments()->latest()->get();
    }

    public function render()
    {
        return view('livewire.ged.attachment-manager', [
            'attachments' => $this->attachments,
            'categories'  => AttachmentCategoryEnum::options(),
        ]);
    }
}
