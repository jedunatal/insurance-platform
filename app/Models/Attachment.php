<?php

namespace App\Models;

use App\Enums\AttachmentCategoryEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attachments';

    protected $fillable = [
        'tenant_id',
        'attachable_type',
        'attachable_id',
        'created_by',
        'title',
        'category',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'notes',
    ];

    protected $casts = [
        'category'  => AttachmentCategoryEnum::class,
        'file_size' => 'integer',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function formattedFileSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2, ',', '.') . ' KB';
        }

        return $bytes . ' B';
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }
}
