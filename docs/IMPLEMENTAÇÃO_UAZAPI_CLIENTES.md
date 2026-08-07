# Implementação Uazapi para clientes e pacientes

## 1. Objetivo

Este documento define a evolução da integração central da Uazapi para oferecer autoatendimento aos pacientes de todas as clínicas usando um único número do SaaS.

Não será utilizada inteligência artificial nesta fase. O objetivo é automatizar as solicitações mais frequentes da recepção com palavras-chave e menus determinísticos, regras previsíveis, confirmação explícita e isolamento por clínica:

1. lembrar, confirmar e cancelar agendamentos;
2. consultar agenda e solicitar remarcação;
3. consultar aulas contratadas, realizadas, agendadas e restantes;
4. informar faltas, cancelamentos tardios e créditos de reposição;
5. consultar cobranças e receber link ou Pix para pagamento;
6. transferir a conversa para atendimento humano.

## 2. Estado atual e escopo deste documento

### Já implementado

- uma única instância, um único token, um único número e um único webhook Uazapi para todo o SaaS;
- credencial Uazapi criptografada no banco;
- webhook secreto, idempotente e com payload criptografado;
- envio de mensagens por fila com jobs criptografados;
- vínculo de telefone para profissionais por código temporário `FIN-######`;
- identificação de `user_id` e `clinic_id` nas mensagens dos profissionais vinculados;
- rejeição de grupos, remetentes não vinculados e identificadores telefônicos ambíguos;
- download seguro e transcrição local de áudio com `whisper.cpp` quando habilitado;
- registro de consentimento do paciente para mensagens por WhatsApp;
- índice HMAC do telefone do paciente, sem vincular o telefone globalmente a uma única clínica;
- fila persistente `whatsapp_patient_tasks`, criptografada, idempotente e recuperável;
- worker com retentativas para que o webhook nunca consulte financeiro ou agenda durante o callback;
- interpretação determinística de `confirmar`, `cancelar`, `financeiro`, `agenda`, `pacote/aulas`, `atendente` e `menu`;
- confirmação automática do agendamento a partir de um lembrete recente;
- consultas assíncronas de agenda, pacote/aulas e pendências financeiras;
- pedido de cancelamento e atendimento humano encaminhados para a equipe da clínica;
- painel **Solicitações WhatsApp** para administradores e recepcionistas;
- ativação e parâmetros de lembrete separados por clínica em `clinic_whatsapp_settings`;
- comando de planejamento de lembretes não confirmados, com repetição, limite e horário de parada;
- comando de recuperação e reenvio de tarefas interrompidas;
- agendamento desses comandos no scheduler do Laravel.

### Ainda precisa ser implementado para pacientes

- seleção interativa quando o mesmo telefone representa mais de um paciente ou clínica;
- interpretação dos identificadores nativos de resposta de botões da Uazapi, além dos textos atuais;
- cancelamento automático e remarcação pelo paciente;
- cálculo formal e histórico de créditos de reposição;
- configuração avançada por clínica para cancelamento, reposição, horários de atendimento e textos;
- link Pix, boleto ou portal financeiro;
- resposta da equipe ao paciente a partir do painel de solicitações;
- responsáveis e dependentes.

Não considerar as funcionalidades desta segunda lista ativas somente porque o webhook global foi configurado.

## 3. Princípios do produto

### 3.1 Uma infraestrutura global, contexto sempre por clínica

Todas as mensagens passam pela mesma Uazapi, mas toda ação de negócio deve carregar, validar e registrar:

- `clinic_id`;
- `patient_id` ou `user_id`;
- origem da identificação;
- interação ou conversa que originou a ação;
- usuário ou processo responsável pela alteração;
- regra comercial aplicada naquele momento.

Nenhuma consulta deve localizar agendamentos, faturas ou pacotes apenas pelo telefone, sem delimitar a clínica e o paciente corretos.

### 3.2 O paciente não usa o vínculo permanente do profissional

Um telefone pode representar:

- o mesmo paciente em mais de uma clínica;
- um responsável e seus dependentes;
- dois familiares que compartilham o aparelho;
- mais de um contrato ou modalidade.

Portanto, o telefone do paciente não deve ser único nem ficar permanentemente preso a uma clínica.

O roteamento deve seguir esta ordem:

1. se for resposta a uma mensagem enviada pelo sistema, recuperar o paciente e a clínica da interação original;
2. caso seja uma mensagem espontânea, procurar os pacientes relacionados ao telefone normalizado;
3. se existir somente um paciente em uma clínica, iniciar uma sessão curta nesse contexto;
4. se houver mais de um paciente ou clínica, solicitar uma seleção explícita;
5. se não houver correspondência segura, encaminhar para atendimento humano ou identificação por link assinado.

Cada mensagem de saída deve começar com o nome da clínica para evitar confusão:

```text
Clínica Movimento

Olá, Ana. Você tem uma aula amanhã às 09:00.
```

### 3.3 Automação determinística

Nesta fase, somente palavras-chave e opções conhecidas são aceitas. Exemplos: `confirmar`, `cancelar`, `financeiro`, `minha agenda`, `meu pacote`, `atendente` e `menu`.

- mensagens desconhecidas não executam tarefas de negócio;
- áudio pode continuar sendo transcrito para registro, mas não será interpretado automaticamente;
- cancelamento fica como solicitação para a equipe enquanto as regras de antecedência e reposição não estiverem parametrizadas;
- nenhuma automação escolhe silenciosamente um paciente ou uma clínica;
- respostas ambíguas são encaminhadas para atendimento seguro.

Confirmação e consultas já são processadas por serviços determinísticos. Reposição, cancelamento automático e pagamento deverão seguir a mesma regra quando forem implementados.

## 4. Prioridades de implementação

### Fase 1 — MVP obrigatório

#### 4.1 Lembrete e confirmação de agendamento

Enviar lembrete configurável, inicialmente 24 horas antes, contendo:

- clínica;
- paciente;
- data e hora;
- modalidade ou serviço;
- profissional, quando aplicável;
- botões `Confirmar`, `Cancelar`, `Remarcar` e `Falar com a clínica`.

Regras:

- o botão deve carregar um identificador opaco, assinado, descartável e com validade;
- nunca expor IDs sequenciais no conteúdo da ação;
- uma resposta duplicada deve ser idempotente;
- confirmar somente agendamentos ainda válidos;
- registrar data, origem e resultado da confirmação;
- se o horário já tiver sido alterado, apresentar o estado atual em vez de repetir a operação antiga.

#### 4.2 Cancelamento com consequência explícita

Antes de concluir, apresentar a regra que será aplicada:

```text
Este cancelamento ocorre com mais de 12 horas de antecedência.
Será criado 1 crédito de reposição válido até 30/09/2026.

Deseja confirmar o cancelamento?
```

Ou:

```text
Este horário está dentro da janela de cancelamento tardio.
A aula será contabilizada e não gerará reposição.

Deseja continuar?
```

O cancelamento deve ocorrer em uma transação que:

1. bloqueia e relê o agendamento;
2. verifica clínica, paciente, status e antecedência;
3. salva um retrato da regra aplicada;
4. cancela o agendamento;
5. cria o crédito de reposição quando elegível;
6. registra auditoria;
7. envia a confirmação depois do commit.

#### 4.3 Consulta de agenda

O paciente poderá solicitar:

- próximo horário;
- agenda dos próximos sete dias;
- horários futuros já confirmados;
- detalhes de uma aula específica;
- solicitação de remarcação.

No MVP, `Remarcar` pode cancelar conforme a regra e encaminhar para a recepção. A escolha automática de um novo horário entra na Fase 2.

#### 4.4 Consulta de pacote e aulas

Resumo recomendado:

```text
Plano Pilates — Agosto

Contratadas: 12
Realizadas: 7
Agendadas: 2
Reposições disponíveis: 1
Faltas/cancelamentos tardios: 1
Saldo livre: 1
Vencimento: 31/08/2026
```

Os números precisam ter definições estáveis:

- `contratadas`: quantidade adquirida no pacote vigente;
- `realizadas`: presenças efetivamente registradas;
- `agendadas`: aulas futuras que já reservam saldo;
- `reposição`: crédito separado, com origem e validade próprias;
- `falta/cancelamento tardio`: aula consumida conforme a política;
- `saldo livre`: quantidade ainda disponível para novos agendamentos.

#### 4.5 Créditos de reposição

Criar uma entidade própria para reposições, sem apenas devolver uma unidade ao pacote. Cada crédito precisa registrar:

- clínica e paciente;
- pacote original;
- agendamento cancelado que originou o crédito;
- motivo;
- quantidade;
- data de criação e expiração;
- status `disponível`, `reservado`, `utilizado`, `expirado` ou `cancelado`;
- agendamento no qual foi utilizado;
- regra comercial aplicada.

O paciente poderá consultar quantidade, validade e origem das reposições.

#### 4.6 Financeiro essencial

Disponibilizar:

- cobranças em aberto;
- valor e vencimento;
- pagamentos recentes;
- Pix copia e cola ou link de pagamento;
- solicitação de recibo ou nota por link seguro;
- opção `Falar com o financeiro`.

Não enviar extratos extensos, documentos ou dados clínicos no corpo da conversa. Para conteúdo detalhado, gerar link assinado, de curta duração, com autenticação adicional quando necessário.

#### 4.7 Atendimento humano e preferências

Todo menu deve conter `Falar com a clínica`.

Registrar também:

- horário do pedido;
- setor solicitado;
- clínica e paciente selecionados;
- resumo seguro da intenção;
- status da transferência;
- atendente responsável;
- horário de início e encerramento.

O paciente deve poder desativar mensagens não essenciais e a clínica precisa respeitar consentimentos revogados. Comunicação promocional deve ser separada das mensagens transacionais.

### Fase 2 — Autoagendamento

- pesquisar disponibilidade por data, período, modalidade, profissional e unidade;
- apresentar poucas opções por vez;
- reservar temporariamente a vaga durante a confirmação;
- validar capacidade da sala, conflito, pacote, crédito e regras do plano dentro da transação;
- remarcar sem criar saldo ou reposição em duplicidade;
- lista de espera com aceite por ordem e prazo;
- aviso automático de vaga liberada;
- renovação de pacote e alerta de vencimento.

### Fase 3 — Experiência ampliada

- áudio para intenções de baixo risco;
- responsáveis e dependentes;
- formulários pré-atendimento por link seguro;
- instruções antes da primeira aula;
- pesquisa de satisfação;
- aviso de atraso;
- atualização cadastral;
- endereços, localização, estacionamento, horários e feriados;
- contingência por e-mail ou SMS para avisos críticos.

## 5. Menu inicial sugerido

```text
Clínica Movimento

Como podemos ajudar?

1. Minha agenda
2. Confirmar ou cancelar
3. Meu pacote e aulas
4. Minhas reposições
5. Financeiro
6. Falar com a clínica
```

Para pacientes relacionados a mais de uma clínica, selecionar a clínica antes desse menu. Para responsáveis, selecionar também o dependente.

## 6. Modelo de dados recomendado

Os nomes finais podem seguir as convenções do projeto, mas as responsabilidades devem permanecer separadas.

### `whatsapp_contacts`

Identidade telefônica global:

- telefone criptografado;
- HMAC do telefone normalizado para pesquisa;
- situação e última atividade;
- nunca conter `clinic_id` como vínculo único.

### `whatsapp_contact_patients`

Relacionamento muitos-para-muitos:

- contato;
- paciente;
- clínica;
- tipo `próprio`, `responsável` ou `compartilhado`;
- consentimento e data de verificação;
- situação do vínculo.

### `whatsapp_conversations`

Sessão de curta duração:

- contato;
- clínica e paciente selecionados;
- intenção e etapa atual;
- dados mínimos necessários para concluir o fluxo;
- expiração;
- status de atendimento humano.

Não armazenar texto clínico desnecessário no estado da conversa.

### `whatsapp_interactions`

Correlação e idempotência:

- mensagem enviada e resposta recebida;
- clínica, paciente e agendamento relacionados;
- ação permitida;
- token assinado ou nonce descartável;
- expiração, consumo e resultado.

### `appointment_replacement_credits`

Histórico formal dos créditos de reposição descritos na seção 4.5.

### `clinic_whatsapp_settings`

Configuração por clínica. Nesta entrega já existem:

- automação de pacientes ativa;
- antecedência dos lembretes;
- intervalo entre lembretes;
- quantidade máxima de lembretes;
- antecedência mínima para interromper novos lembretes.

Ainda deverão ser acrescentados quando os respectivos fluxos forem implementados:

- janela mínima de cancelamento;
- validade da reposição;
- limite de reposições;
- horários de atendimento;
- setores e contatos de transferência;
- textos institucionais permitidos;
- funcionalidades liberadas.

## 7. Regras que precisam ser definidas antes da Fase 1

Cada clínica deverá configurar ou aceitar um padrão para:

- quantas horas antes enviar lembrete;
- antecedência mínima para cancelamento com reposição;
- validade do crédito;
- limite de créditos por período;
- se falta e cancelamento tardio consomem aula;
- possibilidade de reposição com outro profissional ou modalidade;
- tratamento de feriados e cancelamentos realizados pela clínica;
- pausa de plano, atestado e exceções administrativas;
- prazo de reserva de uma vaga durante remarcação;
- quais usuários podem alterar essas regras.

Alterações nessas configurações valem para operações futuras. Cada operação deve manter um retrato da regra utilizada para que o histórico continue explicável.

## 8. Segurança e privacidade

- exigir consentimento válido para mensagens pelo WhatsApp;
- registrar finalidade, versão, origem, concessão e revogação do consentimento;
- separar mensagens transacionais de marketing;
- não solicitar CPF completo, prontuário ou diagnóstico em conversa aberta;
- usar links assinados e de curta duração para documentos e dados detalhados;
- nunca aceitar `patient_id`, `clinic_id` ou `appointment_id` informados livremente pelo cliente;
- assinar ações e validar novamente a autorização ao executar;
- limitar tentativas de identificação e expirar sessões;
- criptografar payloads que precisem ser retidos;
- minimizar logs e nunca registrar token, segredo do webhook ou conteúdo clínico;
- auditar cancelamentos, reposições, confirmações e consultas financeiras;
- oferecer saída para atendimento humano;
- não fornecer diagnóstico, conduta clínica ou orientação emergencial por automação.

Dados relacionados à saúde são dados pessoais sensíveis. Consulte o [Glossário da ANPD](https://www.gov.br/anpd/pt-br/documentos-e-publicacoes/glossario-anpd) e o [Guia de Segurança para Agentes de Tratamento de Pequeno Porte](https://www.gov.br/anpd/pt-br/assuntos/noticias/anpd-publica-guia-de-seguranca-para-agentes-de-tratamento-de-pequeno-porte).

A Uazapi declara ser uma integração não oficial e exige consentimento prévio para mensagens. O número central é um ponto único de falha; mantenha monitoramento e plano de contingência. Consulte os [Termos da Uazapi](https://www.uazapi.com/terms).

## 9. Variáveis de ambiente para produção

Configuração mínima da integração já implementada:

```dotenv
APP_URL=https://clinicas.seudominio.com
APP_DEBUG=false
QUEUE_CONNECTION=database
SESSION_SECURE_COOKIE=true

WHATSAPP_DRIVER=uazapi
WHATSAPP_ENABLED=true
WHATSAPP_LOG_ONLY=false
WHATSAPP_DEFAULT_COUNTRY_CODE=55
WHATSAPP_PUBLIC_NUMBER=5511999999999
WHATSAPP_ALERT_EMAIL=operacao@seudominio.com
WHATSAPP_WEBHOOK_SECRET=
WHATSAPP_ACTIVATION_CODE_TTL_MINUTES=10
WHATSAPP_ACTIVATION_MAX_ATTEMPTS=10
WHATSAPP_PATIENT_AUTOMATION_ENABLED=false
WHATSAPP_PATIENT_REMINDER_HOURS_BEFORE=24
WHATSAPP_PATIENT_REMINDER_REPEAT_MINUTES=180
WHATSAPP_PATIENT_REMINDER_MAX_ATTEMPTS=3
WHATSAPP_PATIENT_REMINDER_STOP_MINUTES_BEFORE=60
WHATSAPP_PATIENT_RECENT_REMINDER_HOURS=48
WHATSAPP_PATIENT_TASK_BATCH_SIZE=100
WHATSAPP_PATIENT_TASK_STALE_MINUTES=10
WHATSAPP_PATIENT_MAX_REQUESTS_PER_10_MINUTES=30

UAZAPI_BASE_URL=https://sua-instancia.uazapi.com
UAZAPI_TOKEN=token-da-instancia-central
UAZAPI_INSTANCE_ID=identificador-da-instancia
UAZAPI_WEBHOOK_EVENTS=messages,messages_update,connection
UAZAPI_WEBHOOK_EXCLUDE_MESSAGES=wasSentByApi,fromMeYes,isGroupYes
UAZAPI_WEBHOOK_MAX_PAYLOAD_BYTES=26214400
UAZAPI_ALLOWED_MEDIA_HOSTS=
UAZAPI_ALLOW_HTTP_MEDIA=false
UAZAPI_HTTP_TIMEOUT=30
```

Regras:

- não colocar o token da Uazapi na linha de comando;
- não versionar o `.env`;
- `APP_URL` deve usar HTTPS em produção;
- deixar `WHATSAPP_WEBHOOK_SECRET` vazio em instalações novas para o comando gerar um segredo forte;
- preencher `WHATSAPP_PUBLIC_NUMBER` somente com dígitos e código do país;
- usar o token da instância central, não o token administrativo da conta Uazapi;
- manter `WHATSAPP_LOG_ONLY=true` enquanto estiver validando um ambiente sem desejar envios reais.

Mantenha `WHATSAPP_PATIENT_AUTOMATION_ENABLED=false` durante a migração e a indexação inicial dos telefones. Para habilitar áudio, acrescente as variáveis descritas em `docs/WHISPER_E_UAZAPI_NA_VPS.md`.

`WHATSAPP_PATIENT_AUTOMATION_ENABLED` é a chave mestra de segurança. Os quatro valores de lembrete no `.env` são apenas os padrões usados ao criar a configuração de uma clínica. Depois disso, cada clínica usa os valores persistidos em `clinic_whatsapp_settings`. Portanto, ativar a chave mestra não ativa automaticamente todas as clínicas.

## 10. Comandos após o deploy

Esta seção ativa a infraestrutura, a fila de solicitações e os lembretes de pacientes atualmente implementados.

### 10.1 Instalar e preparar a aplicação

Execute no diretório da aplicação, com o mesmo usuário utilizado pelo PHP/worker:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan whatsapp:backfill-patient-phones
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Se os assets forem compilados na própria VPS:

```bash
npm ci
npm run build
```

Não execute `npm` na VPS se o pipeline já publicar os assets compilados.

### 10.2 Validar sem alterar a Uazapi

```bash
php artisan whatsapp:uazapi-webhook --dry-run
php artisan route:list --name=webhooks.uazapi
```

O callback esperado terá o formato:

```text
https://clinicas.seudominio.com/api/webhooks/uazapi/SEGREDO
```

Existe somente um callback para todas as clínicas. O segredo real não deve ser copiado para tickets, prints ou logs.

### 10.3 Registrar o webhook global

```bash
php artisan whatsapp:uazapi-webhook
php artisan whatsapp:uazapi-webhook-status
```

O primeiro comando cria ou rotaciona o segredo e registra o único webhook na instância. Não passe ID ou subdomínio de clínica.

Para reaproveitar temporariamente um segredo já configurado no `.env`:

```bash
php artisan whatsapp:uazapi-webhook --use-configured-secret
```

### 10.4 Iniciar ou reiniciar a fila

Para uma verificação manual:

```bash
php artisan queue:work --queue=default --sleep=2 --tries=3 --timeout=660
```

Em produção, mantenha o worker sob Supervisor ou serviço equivalente. Exemplo de programa:

```ini
[program:clinycal-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/clinycal/artisan queue:work database --queue=default --sleep=2 --tries=3 --timeout=660
directory=/var/www/clinycal
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/clinycal/storage/logs/worker.log
stopwaitsecs=700
```

Ajuste diretório e usuário para a VPS. Depois de cada deploy:

```bash
php artisan queue:restart
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status clinycal-worker:*
```

### 10.5 Validar e ativar a automação dos pacientes

Primeiro mantenha `WHATSAPP_PATIENT_AUTOMATION_ENABLED=false`, execute a migração e indexe os telefones:

```bash
php artisan whatsapp:backfill-patient-phones
```

Confirme que os pacientes do piloto possuem telefone válido e consentimento ativo do tipo `whatsapp_messages`. Configure somente a clínica piloto, pelo ID ou subdomínio:

```bash
php artisan whatsapp:configure-clinic-patients clinica-piloto \
  --enable \
  --hours-before=24 \
  --repeat-minutes=180 \
  --max-reminders=3 \
  --stop-minutes-before=60
```

Consultar a configuração atual não exige opções:

```bash
php artisan whatsapp:configure-clinic-patients clinica-piloto
```

Somente depois habilite a chave mestra no `.env`:

```dotenv
WHATSAPP_PATIENT_AUTOMATION_ENABLED=true
```

Recarregue a configuração e faça uma simulação que não cria nem envia tarefas:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan whatsapp:plan-patient-reminders --dry-run
```

Se a quantidade estiver correta, planeje e despache:

```bash
php artisan whatsapp:plan-patient-reminders
php artisan whatsapp:dispatch-patient-tasks
php artisan queue:restart
```

O scheduler já executa o planejador a cada cinco minutos e a recuperação de tarefas a cada minuto, com bloqueio contra sobreposição. Configure uma única entrada no cron:

```cron
* * * * * cd /var/www/clinycal && php artisan schedule:run >> /dev/null 2>&1
```

Validação:

```bash
php artisan schedule:list
```

Não crie uma entrada cron individual para cada clínica.

Para interromper uma única clínica sem afetar as demais:

```bash
php artisan whatsapp:configure-clinic-patients clinica-piloto --disable
```

Tarefas automatizadas ainda pendentes dessa clínica são canceladas pelo processador antes de qualquer novo envio. O comando `--enable` pode ser utilizado novamente para retomar a automação.

### 10.6 Como as filas e os crons trabalham

O webhook apenas valida e persiste a solicitação do paciente. Ele não despacha nem executa o job nessa requisição. O cron despachante coloca a tarefa na fila e, somente então, o worker consulta agenda, pacote ou financeiro e envia a resposta pela Uazapi:

```text
Uazapi -> webhook rápido -> whatsapp_patient_tasks -> cron despachante -> worker -> Uazapi
```

- `whatsapp:plan-patient-reminders`: a cada cinco minutos, cria uma tarefa para cada agendamento elegível e ainda não confirmado;
- `whatsapp:dispatch-patient-tasks`: a cada minuto, reenfileira tarefas vencidas, inclusive as interrompidas por queda do servidor;
- `queue:work`: processa as tarefas, consulta os dados e envia as mensagens;
- falhas de rede deixam a tarefa como `retrying`; depois do limite ela fica como `failed` para análise;
- a confirmação do paciente altera o agendamento para `confirmed`, impedindo novos lembretes;
- o pedido de cancelamento não cancela automaticamente: fica como `awaiting_staff` no painel **Solicitações WhatsApp**.

Esses comandos podem ser executados manualmente para diagnóstico, mas em produção o cron único do scheduler e o worker supervisionado devem permanecer ativos.

### 10.7 Ativar transcrição de áudio, se desejado

Depois de instalar `ffmpeg`, `whisper.cpp` e o modelo:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan whisper:check
php artisan whisper:transcribe /caminho/para/audio-de-teste.ogg --language=pt
php artisan whatsapp:transcribe-pending --limit=100
php artisan queue:restart
```

### 10.8 Vincular um profissional e validar o recebimento

1. O profissional entra no SaaS.
2. Acessa **Automação pelo WhatsApp**.
3. Gera o código `FIN-######`.
4. Envia o código, pelo telefone que deseja vincular, ao número central.
5. Confirma que recebeu a resposta de vínculo concluído.
6. A operação consulta o worker e os eventos se a resposta não chegar.

## 11. Ativação gradual das clínicas e pacientes

A ativação deve ser gradual:

1. manter `WHATSAPP_PATIENT_AUTOMATION_ENABLED=false` durante o deploy;
2. escolher uma clínica piloto;
3. executar `whatsapp:configure-clinic-patients` somente para essa clínica;
4. revisar texto dos lembretes e identificação da clínica;
5. garantir consentimento válido dos pacientes selecionados;
6. ativar a chave mestra e validar com `--dry-run`;
7. acompanhar entregas, respostas, cancelamentos e erros por alguns dias;
8. ajustar os parâmetros persistidos da clínica quando necessário;
9. habilitar remarcação automática somente depois da implementação e validação das regras;
10. expandir para outras clínicas com o comando de configuração, sem criar novos webhooks ou crons.

## 12. Checklist de validação

### Infraestrutura

- [ ] HTTPS válido e `APP_URL` correto.
- [ ] Uma única integração ativa em `whatsapp_integrations`.
- [ ] Token central criptografado no banco.
- [ ] `whatsapp:uazapi-webhook-status` retorna o callback esperado.
- [ ] Access log desativado somente para `/api/webhooks/uazapi/`.
- [ ] Worker ativo e reiniciado após o deploy.
- [ ] Uma única entrada `schedule:run` ativa no cron da VPS.
- [ ] Nenhum job falhou após o teste.
- [ ] Alerta operacional configurado em `WHATSAPP_ALERT_EMAIL`.

### Profissionais

- [ ] Código temporário expira e só funciona uma vez.
- [ ] Telefone correto aparece mascarado na tela.
- [ ] Mensagem recebida é atribuída ao usuário e à clínica corretos.
- [ ] Grupo, `@lid` sem telefone e remetente desconhecido não executam automações.

### Pacientes

- [ ] Chave mestra ativa e somente as clínicas desejadas habilitadas em `clinic_whatsapp_settings`.
- [ ] Consentimento ativo antes do primeiro envio.
- [ ] Nome da clínica aparece em todas as mensagens.
- [ ] Resposta ao lembrete recupera o paciente e a clínica originais.
- [ ] Telefone associado a duas clínicas não é atribuído automaticamente.
- [ ] Casos ambíguos recebem orientação segura para atendimento.
- [ ] Confirmação duplicada não duplica alterações.
- [ ] Cancelamento concorrente não cria dois créditos.
- [ ] Consequência do cancelamento é apresentada antes da confirmação.
- [ ] Saldo apresentado confere com pacote, presenças e agenda futura.
- [ ] Link financeiro expira e não expõe IDs internos.
- [ ] Transferência para atendimento humano funciona.
- [ ] Revogação do consentimento interrompe mensagens não permitidas.

## 13. Operação e contingência

Com um número central, indisponibilidade ou bloqueio afeta todas as clínicas. Monitorar:

- estado da conexão Uazapi;
- taxa de entrega e falha;
- tamanho da fila;
- jobs falhos;
- eventos presos em `received`, `retrying` ou `awaiting_transcription`;
- aumento anormal de remetentes ignorados;
- latência entre recebimento e processamento;
- volume por clínica para impedir abuso.

Comandos úteis:

```bash
php artisan whatsapp:uazapi-webhook-status
php artisan queue:failed
php artisan queue:retry all
php artisan queue:restart
php artisan whatsapp:configure-clinic-patients clinica-piloto
php artisan whatsapp:dispatch-patient-tasks
```

Não execute `queue:retry all` sem antes identificar a causa da falha e confirmar que os jobs são idempotentes.

Para interromper rapidamente novos envios sem remover o webhook:

```dotenv
WHATSAPP_ENABLED=false
```

Depois:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan queue:restart
```

O webhook continuará recebendo eventos. Se também for necessário interromper o recebimento, desabilite temporariamente o webhook no painel da Uazapi e registre a ação operacional.

Para interromper somente as automações de pacientes, mantendo o vínculo de profissionais e o webhook, use `WHATSAPP_PATIENT_AUTOMATION_ENABLED=false`. Para interromper apenas uma clínica, prefira `whatsapp:configure-clinic-patients CLINICA --disable`.

## 14. Critérios de conclusão da Fase 1

A Fase 1 estará pronta para expansão quando:

- todas as operações forem isoladas por clínica;
- pacientes com telefone duplicado estiverem cobertos;
- confirmação e cancelamento forem idempotentes e transacionais;
- reposições tiverem histórico próprio e auditável;
- saldos coincidirem com os dados exibidos no sistema web;
- não houver alteração de negócio baseada em texto desconhecido ou interpretação probabilística;
- consentimento e revogação forem respeitados;
- links sensíveis forem assinados e expirarem;
- houver métricas, alertas e atendimento humano;
- testes cobrirem concorrência, duplicidade, múltiplas clínicas e responsáveis.

## 15. Documento relacionado

Para instalação do `whisper.cpp`, segurança de mídia e detalhes do webhook global, consulte `docs/WHISPER_E_UAZAPI_NA_VPS.md`.
