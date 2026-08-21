<?php

namespace App\Models;

use App\Enums\DocumentCategoryEnum;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'documents';

    protected $fillable = [
        'tenant_id',
        'documentable_type',
        'documentable_id',
        'category',
        'title',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
        'notes',
    ];

    protected $casts = [
        'category'  => DocumentCategoryEnum::class,
        'file_size' => 'integer',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Retorna o tamanho do arquivo formatado em KB ou MB.
     */
    public function formattedSize(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1, ',', '.') . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Verifica se o arquivo é um PDF.
     */
    public function isPdf(): bool
    {
        return str_contains(strtolower($this->mime_type), 'pdf') 
            || str_ends_with(strtolower($this->original_name), '.pdf');
    }

    /**
     * Verifica se o arquivo é uma imagem suportada.
     */
    public function isImage(): bool
    {
        return str_starts_with(strtolower($this->mime_type), 'image/');
    }

    /**
     * Retorna a rota segura de visualização prévia (preview).
     */
    public function previewUrl(): string
    {
        return route('documents.preview', $this);
    }

    /**
     * Retorna a rota segura de download.
     */
    public function downloadUrl(): string
    {
        return route('documents.download', $this);
    }
}
