<?php

namespace App\Actions\Document;

use App\Enums\DocumentCategoryEnum;
use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class StoreDocumentAction
{
    /**
     * Lista de MIME types estritamente permitidos para armazenamento no disco privado.
     */
    public const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/pjpeg',
    ];

    /**
     * Armazena um documento no disco seguro privado e cria o registro polimórfico no banco de dados.
     *
     * @param Model $documentable
     * @param UploadedFile|string $file
     * @param DocumentCategoryEnum|string $category
     * @param string|null $title
     * @param string|null $notes
     * @param int|null $uploadedBy
     * @return Document
     */
    public function execute(
        Model $documentable,
        UploadedFile|string $file,
        DocumentCategoryEnum|string $category = DocumentCategoryEnum::Other,
        ?string $title = null,
        ?string $notes = null,
        ?int $uploadedBy = null
    ): Document {
        return DB::transaction(function () use ($documentable, $file, $category, $title, $notes, $uploadedBy) {
            $tenantId = $documentable->tenant_id ?? (auth()->user()?->tenant_id ?? 1);
            $modelName = strtolower(class_basename($documentable));
            $modelId = $documentable->getKey() ?? 'temp';
            $yearMonth = now()->format('Y/m');
            $directory = "documents/{$tenantId}/{$modelName}/{$modelId}/{$yearMonth}";

            if ($file instanceof UploadedFile) {
                $mimeType = $file->getMimeType() ?: $file->getClientMimeType();

                if (! in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
                    throw new InvalidArgumentException("O tipo de arquivo [{$mimeType}] não é permitido por motivos de segurança.");
                }

                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $filePath = $file->store($directory, 'private');
            } else {
                $originalName = basename($file);
                $mimeType = 'application/pdf';
                $fileSize = Storage::disk('private')->exists($file) ? Storage::disk('private')->size($file) : 0;
                $filePath = $file;
            }

            $categoryEnum = $category instanceof DocumentCategoryEnum 
                ? $category 
                : DocumentCategoryEnum::fromValue($category);

            /** @var Document $document */
            $document = Document::create([
                'tenant_id'         => $tenantId,
                'documentable_type' => $documentable->getMorphClass(),
                'documentable_id'   => $documentable->getKey(),
                'category'          => $categoryEnum,
                'title'             => $title ?: $categoryEnum->getLabel(),
                'original_name'     => $originalName,
                'file_path'         => $filePath,
                'mime_type'         => $mimeType,
                'file_size'         => $fileSize,
                'uploaded_by'       => $uploadedBy ?? auth()->id(),
                'notes'             => $notes,
            ]);

            return $document;
        });
    }
}
