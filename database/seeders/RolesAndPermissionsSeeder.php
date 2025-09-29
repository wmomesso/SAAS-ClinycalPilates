<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
        // (Exemplos baseados no plano. Adicionar todas as permissões necessárias)
        Permission::create(['name' => 'gerenciar-clinicas']); // Para super-admin

        Permission::create(['name' => 'gerenciar-profissionais']);
        Permission::create(['name' => 'gerenciar-salas']);
        Permission::create(['name' => 'ver-todos-agendamentos']);
        Permission::create(['name' => 'gerenciar-pacientes']);
        Permission::create(['name' => 'ver-relatorios-financeiros']);
        Permission::create(['name' => 'gerenciar-assinatura-saas']);

        Permission::create(['name' => 'ver-agenda-propria']);
        Permission::create(['name' => 'gerenciar-agendamentos-proprios']);
        Permission::create(['name' => 'registrar-evolucao-paciente']);

        // --- DEFINIÇÃO DOS PERFIS (ROLES) E ATRIBUIÇÃO DE PERMISSÕES ---

        // Perfil Super Admin
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all()); // O super-admin pode tudo

        // Perfil Admin da Clínica
        $adminClinicaRole = Role::create(['name' => 'admin-clinica']);
        $adminClinicaRole->givePermissionTo([
            'gerenciar-profissionais',
            'gerenciar-salas',
            'ver-todos-agendamentos',
            'gerenciar-pacientes',
            'ver-relatorios-financeiros',
            'gerenciar-assinatura-saas',
        ]);

        // Perfil Profissional
        $profissionalRole = Role::create(['name' => 'profissional']);
        $profissionalRole->givePermissionTo([
            'ver-agenda-propria',
            'gerenciar-agendamentos-proprios',
            'registrar-evolucao-paciente',
        ]);

        // Perfil Recepcionista
        $recepcionistaRole = Role::create(['name' => 'recepcionista']);
        // Adicionar permissões específicas da recepcionista aqui

        // Perfil Paciente
        $pacienteRole = Role::create(['name' => 'paciente']);
        // Adicionar permissões específicas do paciente aqui
    }
}
