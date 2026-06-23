<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Executa os seeders para o banco de dados.
     */
    public function run(): void
    {
        // Limpa o cache de roles e permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- DEFINIÇÃO DAS PERMISSÕES ---
        $permissions = [
            'gerenciar-clinicas', // Para super-admin

            // Salas
            'gerenciar-salas',
            'visualizar-salas',

            // Agenda Profissionais
            'gerenciar-agenda-profissionais',
            'visualizar-agenda-profissionais',

            // Lista de Compras
            'gerenciar-lista-compras-clinica',
            'visualizar-lista-compras-clinica',

            // Convênios
            'gerenciar-convenios',
            'visualizar-convenios',

            // Pacientes
            'gerenciar-pacientes',
            'visualizar-pacientes',

            // Pagamentos
            'gerenciar-pagamentos',
            'visualizar-pagamentos',

            // Assinatura SAAS
            'gerenciar-assinatura-saas',
            'visualizar-assinatura-saas',

            // Financeiro
            'gerenciar-financeiro',
            'visualizar-financeiro',

            // Fluxo de Caixa
            'gerenciar-fluxo-caixa',
            'visualizar-fluxo-caixa',

            // Específicas de profissionais (mantidas ou adaptadas)
            'ver-agenda-propria',
            'gerenciar-agendamentos-proprios',
            'registrar-evolucao-paciente',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // --- DEFINIÇÃO DOS PERFIS (ROLES) E ATRIBUIÇÃO DE PERMISSÕES ---

        // Perfil Super Admin
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // Perfil Admin da Clínica
        $adminClinicaRole = Role::firstOrCreate(['name' => 'admin-clinica']);
        $adminClinicaRole->syncPermissions([
            'gerenciar-salas',
            'visualizar-salas',
            'gerenciar-agenda-profissionais',
            'visualizar-agenda-profissionais',
            'gerenciar-lista-compras-clinica',
            'visualizar-lista-compras-clinica',
            'gerenciar-convenios',
            'visualizar-convenios',
            'gerenciar-pacientes',
            'visualizar-pacientes',
            'gerenciar-pagamentos',
            'visualizar-pagamentos',
            'gerenciar-assinatura-saas',
            'visualizar-assinatura-saas',
            'gerenciar-financeiro',
            'visualizar-financeiro',
            'gerenciar-fluxo-caixa',
            'visualizar-fluxo-caixa',
        ]);

        // Perfil Profissional
        $profissionalRole = Role::firstOrCreate(['name' => 'profissional']);
        $profissionalRole->syncPermissions([
            'visualizar-agenda-profissionais',
            'visualizar-pacientes',
            'registrar-evolucao-paciente',
            'ver-agenda-propria',
            'gerenciar-agendamentos-proprios',
        ]);

        // Perfil Recepcionista
        $recepcionistaRole = Role::firstOrCreate(['name' => 'recepcionista']);
        $recepcionistaRole->syncPermissions([
            'visualizar-salas',
            'gerenciar-agenda-profissionais',
            'visualizar-agenda-profissionais',
            'visualizar-pacientes',
            'gerenciar-pacientes',
            'visualizar-pagamentos',
            'gerenciar-pagamentos',
        ]);

        // Perfil Paciente
        $pacienteRole = Role::firstOrCreate(['name' => 'paciente']);
        // Pacientes podem ter permissões limitadas no futuro para ver seus próprios dados
    }
}
