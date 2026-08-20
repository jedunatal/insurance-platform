<?php

namespace App\Actions\Insured;

use App\Enums\LeadStatusEnum;
use App\Enums\PersonTypeEnum;
use App\Models\Insured;
use App\Models\Lead;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Responsável por converter um Lead em Segurado dentro de uma transação do banco de dados.
 */
final class ConvertLeadToInsuredAction
{
    /**
     * Converte o Lead em um Segurado e atualiza o status do Lead para 'Convertido'.
     *
     * @param Lead|int $lead
     * @param array<string, mixed> $additionalData Dados adicionais/sobrescritos para o segurado
     */
    public function execute(Lead|int $lead, array $additionalData = []): Insured
    {
        return DB::transaction(function () use ($lead, $additionalData) {
            $leadModel = $lead instanceof Lead ? $lead : Lead::findOrFail($lead);

            // Garante um tenant válido
            $tenantId = $additionalData['tenant_id'] 
                ?? $leadModel->tenant_id 
                ?? auth()->user()?->tenant_id;

            if (! $tenantId) {
                $tenant = Tenant::firstOrCreate(
                    ['id' => 1],
                    [
                        'name' => 'Empresa Padrão',
                        'slug' => 'empresa-padrao',
                        'email' => 'contato@empresa.com',
                        'document' => '00000000000191',
                    ]
                );
                $tenantId = $tenant->id;
            }

            // Identifica tipo de pessoa baseado no documento se não fornecido
            $document = $additionalData['document'] ?? $leadModel->document;
            $personType = $additionalData['person_type'] ?? null;

            if (! $personType && $document) {
                $cleanDoc = preg_replace('/\D/', '', (string) $document);
                $personType = strlen($cleanDoc) > 11 ? PersonTypeEnum::Legal : PersonTypeEnum::Individual;
            }

            $personTypeValue = $personType instanceof PersonTypeEnum 
                ? $personType->value 
                : ($personType ?? 'PF');

            $insuredData = array_merge([
                'tenant_id'    => $tenantId,
                'lead_id'      => $leadModel->id,
                'assigned_to'  => $additionalData['assigned_to'] ?? $leadModel->assigned_to,
                'created_by'   => $additionalData['created_by'] ?? auth()->id() ?? $leadModel->created_by,
                'name'         => $leadModel->name,
                'email'        => $leadModel->email,
                'phone'        => $leadModel->phone,
                'document'     => $document,
                'person_type'  => $personTypeValue,
                'notes'        => $leadModel->notes ? "Convertido do Lead #{$leadModel->id}.\nObservações do Lead:\n{$leadModel->notes}" : null,
            ], $additionalData);

            // Cria o registro do segurado
            $insured = Insured::create($insuredData);

            // Atualiza o status do Lead para Convertido
            $leadModel->update([
                'status' => LeadStatusEnum::Converted->value,
            ]);

            return $insured;
        });
    }
}
