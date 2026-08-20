<?php

namespace Database\Seeders;

use App\Enums\InsuranceBranchEnum;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::firstOrCreate(
            ['id' => 1],
            [
                'name'     => 'Empresa Padrão',
                'slug'     => 'empresa-padrao',
                'email'    => 'contato@empresa.com',
                'document' => '00000000000191',
            ]
        );

        $catalog = [
            // 1. Automóvel
            [
                'branch' => InsuranceBranchEnum::Auto->value,
                'name' => 'Seguro Auto Compreensivo (Casco / Roubo / Colisão)',
                'description' => 'Cobertura completa contra colisão, incêndio, roubo e furto para veículos leves e utilitários.',
            ],
            [
                'branch' => InsuranceBranchEnum::Auto->value,
                'name' => 'Seguro Auto RCF-V (Danos Materiais e Corporais a Terceiros)',
                'description' => 'Garante a indenização de danos involuntários corporais e materiais causados a terceiros.',
            ],
            [
                'branch' => InsuranceBranchEnum::Auto->value,
                'name' => 'Seguro Auto Roubo e Furto + Rastreador',
                'description' => 'Opção simplificada e econômica com instalação de rastreador e indenização integral 100% FIPE.',
            ],
            [
                'branch' => InsuranceBranchEnum::Auto->value,
                'name' => 'Seguro Frota Automotiva',
                'description' => 'Proteção corporativa unificada para gestão de veículos de empresas.',
            ],
            [
                'branch' => InsuranceBranchEnum::Auto->value,
                'name' => 'Seguro Moto / Duas Rodas',
                'description' => 'Cobertura exclusiva para motocicletas, com assistência 24h e cobertura para terceiros.',
            ],

            // 2. Vida
            [
                'branch' => InsuranceBranchEnum::Life->value,
                'name' => 'Seguro de Vida Individual',
                'description' => 'Proteção financeira para você e sua família em casos de morte acidental ou natural e invalidez.',
            ],
            [
                'branch' => InsuranceBranchEnum::Life->value,
                'name' => 'Seguro de Vida em Grupo / Coletivo',
                'description' => 'Solução corporativa para proteger funcionários e sócios com coberturas customizáveis.',
            ],
            [
                'branch' => InsuranceBranchEnum::Life->value,
                'name' => 'Seguro de Vida Mulher',
                'description' => 'Plano especializado com diagnóstico precoce de câncer feminino e suporte emocional.',
            ],
            [
                'branch' => InsuranceBranchEnum::Life->value,
                'name' => 'Seguro Acidentes Pessoais (AP)',
                'description' => 'Indenização rápida e despesas médico-hospitalares (DMHO) para eventos acidentais.',
            ],
            [
                'branch' => InsuranceBranchEnum::Life->value,
                'name' => 'Seguro DIT (Diária por Incapacidade Temporária)',
                'description' => 'Garante a renda mensal do profissional liberal ou autônomo durante afastamento médico.',
            ],

            // 3. Residencial
            [
                'branch' => InsuranceBranchEnum::Home->value,
                'name' => 'Seguro Residencial Básico',
                'description' => 'Proteção essencial contra incêndio, queda de raio, explosão e fumaça.',
            ],
            [
                'branch' => InsuranceBranchEnum::Home->value,
                'name' => 'Seguro Residencial Premium Multirrisco',
                'description' => 'Coberturas adicionais de roubo de bens, danos elétricos, vendaval, vidros e assistência emergencial 24h.',
            ],
            [
                'branch' => InsuranceBranchEnum::Home->value,
                'name' => 'Seguro Condomínio Obrigatório',
                'description' => 'Proteção para a edificação e áreas comuns de condomínios residenciais e comerciais.',
            ],

            // 4. Empresarial
            [
                'branch' => InsuranceBranchEnum::Business->value,
                'name' => 'Seguro Empresarial Multirrisco (Comércio / Serviços)',
                'description' => 'Garante a continuidade dos negócios contra incêndio, roubo de mercadorias e lucros cessantes.',
            ],
            [
                'branch' => InsuranceBranchEnum::Business->value,
                'name' => 'Seguro D&O (Directors and Officers Liability)',
                'description' => 'Protege o patrimônio de executivos e administradores contra processos civis e trabalhistas.',
            ],
            [
                'branch' => InsuranceBranchEnum::Business->value,
                'name' => 'Seguro Riscos Cibernéticos (Cyber Security)',
                'description' => 'Cobertura contra vazamento de dados (LGPD), extorsão por ransomware e recuperação de sistemas.',
            ],

            // 5. Saúde
            [
                'branch' => InsuranceBranchEnum::Health->value,
                'name' => 'Plano de Saúde Individual / Familiar',
                'description' => 'Ampla rede credenciada de hospitais, clínicas e laboratórios para você e seus dependentes.',
            ],
            [
                'branch' => InsuranceBranchEnum::Health->value,
                'name' => 'Plano de Saúde Empresarial / PME',
                'description' => 'Benefício de assistência médica para colaboradores com coparticipação inteligente.',
            ],
            [
                'branch' => InsuranceBranchEnum::Health->value,
                'name' => 'Plano Odontológico (Dental)',
                'description' => 'Tratamentos preventivos, ortodontia e procedimentos odontológicos essenciais.',
            ],

            // 6. Equipamentos Portáteis
            [
                'branch' => InsuranceBranchEnum::Electronics->value,
                'name' => 'Seguro Smartphone e Tablets',
                'description' => 'Proteção contra roubo, furto qualificado e danos acidentais por líquidos ou impacto.',
            ],
            [
                'branch' => InsuranceBranchEnum::Electronics->value,
                'name' => 'Seguro Notebooks e Equipamentos Fotográficos',
                'description' => 'Ideal para trabalho remoto e produtores de conteúdo com cobertura nacional e internacional.',
            ],

            // 7. Agrícola / Rural
            [
                'branch' => InsuranceBranchEnum::Rural->value,
                'name' => 'Seguro Agrícola / Lavoura',
                'description' => 'Mitigação de perdas na safra causadas por seca, geada, granizo e chuvas excessivas.',
            ],
            [
                'branch' => InsuranceBranchEnum::Rural->value,
                'name' => 'Seguro de Máquinas e Equipamentos Agrícolas',
                'description' => 'Proteção para tratores, colheitadeiras e implementos no campo ou em trânsito.',
            ],

            // 8. Responsabilidade Civil
            [
                'branch' => InsuranceBranchEnum::Liability->value,
                'name' => 'Responsabilidade Civil Profissional (E&O)',
                'description' => 'Proteção para médicos, dentistas, engenheiros, contadores e advogados contra reclamações de clientes.',
            ],
            [
                'branch' => InsuranceBranchEnum::Liability->value,
                'name' => 'Responsabilidade Civil Geral e Operações',
                'description' => 'Cobertura para incidentes que ocorram dentro do estabelecimento comercial.',
            ],

            // 9. Outros
            [
                'branch' => InsuranceBranchEnum::Other->value,
                'name' => 'Seguro Viagem Nacional e Internacional',
                'description' => 'Assistência médica no exterior, extravio de bagagem e repatriação.',
            ],
            [
                'branch' => InsuranceBranchEnum::Other->value,
                'name' => 'Seguro Garantia Contratual e Judicial',
                'description' => 'Substituto de fiança bancária e caução em licitações e contratos públicos ou privados.',
            ],
            [
                'branch' => InsuranceBranchEnum::Other->value,
                'name' => 'Seguro Transporte de Cargas Nacional',
                'description' => 'Garante a segurança de mercadorias durante o trajeto rodoviário, aéreo ou marítimo.',
            ],
        ];

        foreach ($catalog as $item) {
            Product::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name'      => $item['name'],
                ],
                [
                    'branch'      => $item['branch'],
                    'description' => $item['description'],
                    'is_active'   => true,
                ]
            );
        }
    }
}