<?php

// Define o namespace onde este arquivo do Model está localizado na arquitetura do Laravel
namespace App\Models;

// Importação dos Enums utilizados para tipar a Origem (source) e o Status do Lead no sistema
use App\Enums\LeadSourceEnum;
use App\Enums\LeadStatusEnum;

// Importação das classes base do Eloquent para manipulação de consultas no banco de dados
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// Classe que representa a tabela 'leads' (Clientes em Potencial) no banco de dados
class Lead extends Model
{
    // Trait para permitir o uso de Factories na geração de dados de teste e Seeders
    use HasFactory;

    // Trait para exclusão lógica (não apaga o registro fisicamente do banco, apenas preenche a coluna deleted_at)
    use SoftDeletes;

    // Lista de campos autorizados para preenchimento em massa (Mass Assignment via Lead::create ou $lead->update)
    protected $fillable = [
        'tenant_id',       // Identificador da corretora/empresa proprietária do registro (Multi-tenant)
        'product_id',      // Identificador do ramo/produto de interesse do cliente (ex: Auto, Vida, Residencial)
        'assigned_to',     // Identificador do corretor/consultor responsável pelo atendimento
        'created_by',      // Identificador do usuário que cadastrou o cliente (nulo quando cadastrado pelo site)
        'name',            // Nome completo do cliente em potencial
        'email',           // E-mail de contato do cliente
        'phone',           // Telefone / WhatsApp de contato do cliente
        'document',        // CPF ou CNPJ do cliente em potencial (opcional)
        'source',          // Origem de onde o cliente veio (ex: site, manual, whatsapp)
        'status',          // Status atual no funil (ex: novo, em_atendimento, cotado, ganho, perdido)
        'next_contact_at', // Data e hora agendada para o próximo contato do corretor com o cliente
        'notes',           // Anotações e histórico de atendimento mantidos pelo corretor
    ];

    // Define a conversão automática (casting) de tipos nativos do banco para tipos/objetos específicos do PHP
    protected function casts(): array
    {
        return [
            'source' => LeadSourceEnum::class,                 // Converte o valor do campo 'source' para o Enum LeadSourceEnum
            'status' => LeadStatusEnum::class,                 // Converte o valor do campo 'status' para o Enum LeadStatusEnum
            'next_contact_at' => 'immutable_datetime',          // Converte o campo para um objeto Carbon de data/hora imutável
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relacionamentos (Relationships)
    |--------------------------------------------------------------------------
    */

    // Relacionamento de pertencimento (BelongsTo) com a Corretora / Empresa (Tenant)
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class); // Indica que este Lead pertence a uma empresa/tenant específica
    }

    // Relacionamento de pertencimento (BelongsTo) com o Produto / Ramo de Seguro de interesse
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class); // Permite acessar os dados do ramo de seguro (ex: $lead->product->name)
    }

    // Relacionamento de pertencimento (BelongsTo) com o Corretor / Consultor responsável pelo atendimento
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to'); // Indica qual usuário é o responsável atual pelo lead
    }

    // Relacionamento de pertencimento (BelongsTo) com o Usuário que realizou o cadastro
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by'); // Indica o usuário criador do registro
    }

    /*
    |--------------------------------------------------------------------------
    | Escopos de Consulta (Query Scopes)
    |--------------------------------------------------------------------------
    */

    // Escopo para filtrar registros restritos apenas à corretora (tenant) do usuário logado
    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId); // Adiciona a instrução WHERE tenant_id = $tenantId na query
    }

    // Escopo para filtrar os registros por um status específico do funil utilizando o Enum
    public function scopeByStatus(Builder $query, LeadStatusEnum $status): Builder
    {
        return $query->where('status', $status->value); // Adiciona a instrução WHERE status = $status->value na query
    }

    // Escopo para filtrar apenas os clientes atribuídos a um corretor específico
    public function scopeByConsultant(Builder $query, int $userId): Builder 
    {
        return $query->where('assigned_to', $userId); // Adiciona a instrução WHERE assigned_to = $userId na query
    }

    // Escopo para realizar buscas por texto parcial nos campos de nome, e-mail ou telefone
    public function scopeSearch(Builder $query, string $term): Builder 
    {
        $term = "%{$term}%"; // Monta o padrão da busca em texto com curinga (%) nas pontas

        return $query->where(
            static function (Builder $query) use ($term): void {
                $query
                    ->where('name', 'like', $term)       // Busca o trecho informado dentro da coluna 'name'
                    ->orWhere('email', 'like', $term)   // Ou busca o trecho dentro da coluna 'email'
                    ->orWhere('phone', 'like', $term);  // Ou busca o trecho dentro da coluna 'phone'
            }
        );
    }

    // Escopo para filtrar apenas os clientes com negociação em andamento (ignorando fechados e perdidos)
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            LeadStatusEnum::Converted->value, // Exclui do resultado os clientes convertidos em venda (ganhos)
            LeadStatusEnum::Lost->value,      // Exclui do resultado os clientes perdidos
        ]);
    }

    // Escopo para identificar atendimentos atrasados (leads ativos cujo horário agendado de contato já passou)
    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->active()                                              // Aplica o filtro de negociações ativas
            ->whereNotNull('next_contact_at')                      // Garante que o lead possui uma data de agendamento definida
            ->where('next_contact_at', '<', now());                // Seleciona apenas quando o horário agendado for menor que a hora atual
    }

    // Escopo para buscar apenas os clientes em potencial cadastrados na data de hoje
    public function scopeCreatedToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today()); // Compara apenas a parte da data da coluna created_at com o dia de hoje
    }
}