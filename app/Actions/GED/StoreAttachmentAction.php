<?php

namespace App\Actions\GED;

use App\Enums\AttachmentCategoryEnum;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoreAttachmentAction
{
    /**
     * Salva um arquivo anexo no storage e cria o registro no GED.
     */
    public function execute(
        Model $attachable,
        UploadedFile|string $file,
        string $title,
        AttachmentCategoryEnum|string $category = AttachmentCategoryEnum::Other,
        ?string $notes = null
    ): Attachment {
        return DB::transaction(function () use ($attachable, $file, $title, $category, $notes) {
            $tenantId = $attachable->tenant_id ?? (auth()->user()?->tenant_id ?? 1);
            $year = now()->year;
            $month = now()->format('m');
            $directory = "attachments/{$tenantId}/{$year}/{$month}";

            if ($file instanceof UploadedFile) {
                $fileName = $file->getClientOriginalName();
                $fileType = $file->getClientMimeType();
                $fileSize = $file->getSize();
                $filePath = $file->store($directory, 'public');
            } else {
                $fileName = basename($file);
                $fileType = 'application/octet-stream';
                $fileSize = 0;
                $filePath = $file;
            }

            $catValue = $category instanceof AttachmentCategoryEnum ? $category : (AttachmentCategoryEnum::tryFrom($category) ?? AttachmentCategoryEnum::Other);

            /** @var Attachment $attachment */
            $attachment = Attachment::create([
                'tenant_id'       => $tenantId,
                'attachable_type' => $attachable->getMorphClass(),
                'attachable_id'   => $attachable->getKey(),
                'created_by'      => auth()->id(),
                'title'           => $title,
                'category'        => $catValue,
                'file_path'       => $filePath,
                'file_name'       => $fileName,
                'file_type'       => $fileType,
                'file_size'       => $fileSize,
                'notes'           => $notes,
            ]);

            return $attachment;
        });
    }
}
