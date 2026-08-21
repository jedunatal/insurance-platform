<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Visualização segura (Preview / Stream inline) de documento privado no navegador.
     */
    public function preview(Document $document): Response|StreamedResponse
    {
        $disk = Storage::disk('private');

        if (! $disk->exists($document->file_path)) {
            abort(404, 'O arquivo solicitado não foi encontrado no armazenamento seguro.');
        }

        return $disk->response(
            $document->file_path,
            $document->original_name,
            [
                'Content-Type'        => $document->mime_type ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . addslashes($document->original_name) . '"',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    /**
     * Download seguro do arquivo com nome original preservado.
     */
    public function download(Document $document): StreamedResponse
    {
        $disk = Storage::disk('private');

        if (! $disk->exists($document->file_path)) {
            abort(404, 'O arquivo solicitado não foi encontrado no armazenamento seguro.');
        }

        return $disk->download(
            $document->file_path,
            $document->original_name,
            [
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    /**
     * Exclusão segura do arquivo físico e do registro no banco.
     */
    public function destroy(Document $document): RedirectResponse
    {
        $disk = Storage::disk('private');

        if ($disk->exists($document->file_path)) {
            $disk->delete($document->file_path);
        }

        $document->delete();

        return back()->with('status', 'Documento removido com sucesso.');
    }
}
