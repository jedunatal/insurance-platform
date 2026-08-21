<?php

namespace App\Actions\GED;

use App\Models\Attachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteAttachmentAction
{
    /**
     * Remove o arquivo do storage e realiza o soft delete do registro no GED.
     */
    public function execute(Attachment $attachment): bool
    {
        return DB::transaction(function () use ($attachment) {
            if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            return (bool) $attachment->delete();
        });
    }
}
