<?php

namespace App\Livewire\Insured;

use App\Enums\LeadStatusEnum;
use App\Models\Insured;
use App\Models\Lead;
use App\Models\Tenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;

class BaseForm
{
    public static function getFields(): array
    {
        return (new self)->fields();
    }

    public function fields(): array
    {
        return [
            Grid::make(12)
                ->schema([

                    // 1. Vínculo com Lead (Opcional - Autocomplete)
                    Section::make('Origem e Vínculo CRM')
                        ->description('Vincule um Lead existente para importar os dados automaticamente.')
                        ->schema([
                            Select::make('lead_id')
                                ->label('Lead / Cliente em Potencial')
                                ->placeholder('Selecione para puxar os dados do Lead...')
                                ->options(
                                    Lead::where('status', '!=', LeadStatusEnum::Converted->value)
                                        ->orWhere('status', '!=', 'Convertido')
                                        ->pluck('name', 'id')
                                )
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(function (?string $state, $set) {
                                    if (! $state) {
                                        return;
                                    }

                                    $lead = Lead::find($state);
                                    if (! $lead) {
                                        return;
                                    }

                                    $set('name', $lead->name);
                                    $set('email', $lead->email);
                                    $set('phone', $lead->phone);
                                    $set('document', $lead->document);
                                    $set('notes', $lead->notes ? "Convertido do Lead #{$lead->id}.\nObservações do Lead:\n{$lead->notes}" : null);

                                    // Define PF ou PJ baseado no tamanho do documento do Lead
                                    if ($lead->document) {
                                        $cleanDoc = preg_replace('/\D/', '', (string) $lead->document);
                                        $set('person_type', strlen($cleanDoc) > 11 ? 'PJ' : 'PF');
                                    }
                                })
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->columnSpan(12),

                    // 2. Dados Principais
                    Section::make('Identificação do Segurado')
                        ->schema([
                            Select::make('person_type')
                                ->label('Tipo de Pessoa')
                                ->options([
                                    'PF' => 'Pessoa Física (CPF)',
                                    'PJ' => 'Pessoa Jurídica (CNPJ)',
                                ])
                                ->default('PF')
                                ->required()
                                ->live()
                                ->columnSpan(['default' => 12, 'md' => 3]),

                            TextInput::make('document')
                                ->label(fn ($get) => $get('person_type') === 'PJ' ? 'CNPJ' : 'CPF')
                                ->placeholder(fn ($get) => $get('person_type') === 'PJ' ? '00.000.000/0000-00' : '000.000.000-00')
                                ->mask(fn ($get) => $get('person_type') === 'PJ' ? '99.999.999/9999-99' : '999.999.999-99')
                                ->required()
                                ->columnSpan(['default' => 12, 'md' => 5]),

                            \Filament\Forms\Components\DatePicker::make('birth_date')
                                ->label(fn ($get) => $get('person_type') === 'PJ' ? 'Data de Fundação' : 'Data de Nascimento')
                                ->native(false)
                                ->displayFormat('d/m/Y')
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('name')
                                ->label(fn ($get) => $get('person_type') === 'PJ' ? 'Razão Social' : 'Nome Completo')
                                ->placeholder('Informe o nome completo ou razão social')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(12),

                            TextInput::make('email')
                                ->label('E-mail Principal')
                                ->email()
                                ->placeholder('cliente@email.com')
                                ->maxLength(255)
                                ->columnSpan(['default' => 12, 'md' => 6]),

                            TextInput::make('phone')
                                ->label('Telefone / WhatsApp')
                                ->placeholder('(21) 99999-9999')
                                ->tel()
                                ->mask('(99) 99999-9999')
                                ->columnSpan(['default' => 12, 'md' => 6]),
                        ])
                        ->columns(12)
                        ->columnSpan(12),

                    // 3. Endereço Completo
                    Section::make('Endereço e Localização')
                        ->schema([
                            TextInput::make('zip_code')
                                ->label('CEP')
                                ->placeholder('00000-000')
                                ->mask('99999-999')
                                ->extraInputAttributes([
                                    'x-on:blur' => 'buscarCep($event.target.value)',
                                ])
                                ->columnSpan(['default' => 12, 'md' => 3]),

                            TextInput::make('address')
                                ->label('Logradouro / Rua')
                                ->placeholder('Ex: Av. Atlântica')
                                ->columnSpan(['default' => 12, 'md' => 7]),

                            TextInput::make('number')
                                ->label('Número')
                                ->placeholder('Ex: 100')
                                ->columnSpan(['default' => 12, 'md' => 2]),

                            TextInput::make('complement')
                                ->label('Complemento')
                                ->placeholder('Ex: Apto 302, Bloco B')
                                ->columnSpan(['default' => 12, 'md' => 4]),

                            TextInput::make('neighborhood')
                                ->label('Bairro')
                                ->placeholder('Bairro')
                                ->columnSpan(['default' => 12, 'md' => 3]),

                            TextInput::make('city')
                                ->label('Cidade')
                                ->placeholder('Cidade')
                                ->columnSpan(['default' => 12, 'md' => 3]),

                            TextInput::make('state')
                                ->label('UF')
                                ->placeholder('RJ')
                                ->maxLength(2)
                                ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                                ->columnSpan(['default' => 12, 'md' => 2]),
                        ])
                        ->columns(12)
                        ->columnSpan(12),

                    // 4. Observações
                    Section::make('Anotações Adicionais')
                        ->schema([
                            Textarea::make('notes')
                                ->label('Observações do Cliente')
                                ->placeholder('Informações de perfil, preferências de contato, referências...')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->columnSpan(12),
                ]),
        ];
    }

    public static function create(array $data): Insured
    {
        return DB::transaction(function () use ($data) {
            $tenant = Tenant::firstOrCreate(
                ['id' => 1],
                [
                    'name' => 'Empresa Padrão',
                    'slug' => 'empresa-padrao',
                    'email' => 'contato@empresa.com',
                    'document' => '00000000000191',
                ]
            );

            $data['tenant_id'] = auth()->user()?->tenant_id ?? $tenant->id;
            $data['created_by'] = auth()->id();

            $insured = Insured::create($data);

            if (! empty($data['lead_id'])) {
                Lead::where('id', $data['lead_id'])->update([
                    'status' => LeadStatusEnum::Converted->value,
                ]);
            }

            return $insured;
        });
    }
}