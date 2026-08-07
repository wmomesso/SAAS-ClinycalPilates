# Auditoria funcional e de segurança

Revisão realizada em 6 de agosto de 2026 para o contexto de clínicas de fisioterapia e estúdios de Pilates.

## Cobertura atual

- Pacientes, prontuário, anamnese, evolução e documentos clínicos.
- Agenda individual, recorrente e em grupo por capacidade da sala.
- Controle de presença, faltas, pacotes e consumo de sessões.
- Serviços, salas, profissionais, recepção e permissões.
- Faturamento, recebíveis, pagáveis, contas bancárias, conciliação e relatórios.
- Convênios, guias, autorização e faturamento associado.
- Assinatura SaaS, limites por plano e período de teste.
- Notificações e automações por WhatsApp.
- Estoque de insumos, alerta de reposição, aparelhos e histórico de manutenção.
- Consentimentos versionados, revogação e trilha de auditoria.

## Controles implantados nesta revisão

- Validação de clínica em referências de paciente, profissional, sala, serviço, pacote e dados financeiros.
- CPF e SKU únicos por clínica, sem impedir o mesmo cadastro legítimo em tenants diferentes.
- Documentos de pacientes no disco privado, servidos apenas após autorização.
- Bloqueio de login e encerramento de sessão para usuários inativos.
- Verificação real de e-mail, cabeçalhos HTTP defensivos e proteção da conta proprietária.
- Prontuário inacessível ao perfil de paciente ou a usuários sem permissão clínica.
- Evoluções arquivadas com soft delete e exclusão clínica limitada a administradores.
- Logs de alteração sem copiar valores de saúde, metadados criptografados e registro de leituras sensíveis.
- Jobs de WhatsApp criptografados, logs minimizados e automações isoladas por clínica.
- Uma instância Uazapi global, webhook autenticado e idempotente, vínculo descartável de telefone por usuário/clínica, mídia privada e transcrição local com `whisper.cpp` preparada.
- Bloqueio de pagamentos acima do saldo da fatura.
- Dependências PHP e JavaScript atualizadas até não restarem advisories conhecidos.

## Operação após deploy

1. Execute `php artisan migrate --force`.
2. Execute `php artisan clinic:secure-patient-documents` uma vez para mover anexos antigos do disco público.
3. Mantenha um worker de fila ativo; mensagens contêm dados transitórios e agora usam payload criptografado.
4. Em produção, use HTTPS, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, chaves fora do repositório e backup criptografado testado periodicamente.
5. Defina retenção de prontuário e descarte com assessoria jurídica conforme profissão, estado e finalidade de tratamento. Soft delete não substitui uma política formal de retenção.
6. Para ativar áudio por WhatsApp e cadastrar webhooks, siga `docs/WHISPER_E_UAZAPI_NA_VPS.md`.

## Próximos módulos dependentes de decisão de produto

Estes itens são úteis, mas variam por operação, contrato e integrações escolhidas:

- Portal/app do paciente, autoagendamento, lista de espera e confirmação por link assinado.
- Prescrição de exercícios domiciliares com vídeos e acompanhamento de adesão.
- CRM de leads, campanhas e funil de matrícula.
- Jornada, disponibilidade, comissão/repasse e folha de profissionais.
- Emissão fiscal/NFS-e, cobrança bancária e integração contábil.
- Assinatura eletrônica do texto integral dos termos e avaliações posturais com imagens.
- BI avançado: ocupação, evasão, LTV, inadimplência e resultado por profissional/unidade.
- Plano formal de continuidade: RPO/RTO, restauração ensaiada, resposta a incidente e canal para solicitações LGPD.

Esses módulos não devem ser implementados sem definir regras comerciais, provedor, responsabilidade legal e fluxo de aprovação; a base criada nesta revisão já oferece tenant, autorização e auditoria para recebê-los.
