# Whisper small e webhook Uazapi na VPS

Esta integração foi preparada para `whisper.cpp` com o modelo multilíngue `small`. O áudio recebido não é enviado à OpenAI nem à transcrição da Uazapi: o sistema baixa a mídia com o token da instância e processa tudo localmente na VPS.

## Controles de segurança implementados

- Uma única instância e um único número Uazapi atendem todo o SaaS.
- O telefone remetente é vinculado a um usuário e à sua clínica por código temporário descartável.
- Token da Uazapi criptografado pelo `APP_KEY` no banco.
- Segredo de webhook aleatório com 64 caracteres; somente o SHA-256 é armazenado.
- HTTPS obrigatório para cadastrar o callback em produção.
- Payload e transcrição criptografados no banco.
- Idempotência global por instância e ID da mensagem.
- Job de fila criptografado e três tentativas com espera progressiva.
- Download de mídia pelo endpoint autenticado `/message/download`.
- URLs alternativas de mídia aceitas somente de hosts permitidos, sem redirecionamento automático.
- Áudio em armazenamento privado e exclusão automática depois da transcrição por padrão.
- Mensagens de saída de todas as clínicas usam a única credencial central; o `clinic_id` permanece nos logs para isolamento e auditoria.

> A Uazapi não documenta uma assinatura HMAC enviada no callback. Por isso, a autenticação compatível usa um segredo de alta entropia na URL, armazenado apenas como hash. HTTPS fornece a criptografia em trânsito. Não chame isso de criptografia do payload pelo provedor: a criptografia do conteúdo em repouso é feita pelo Laravel.

## 1. Instalar o whisper.cpp depois

Exemplo para Ubuntu/Debian. Execute como usuário com `sudo` e ajuste permissões conforme o usuário PHP do site no CloudPanel.

```bash
sudo apt update
sudo apt install -y build-essential cmake git ffmpeg
sudo git clone --depth 1 https://github.com/ggml-org/whisper.cpp.git /opt/whisper.cpp
sudo cmake -S /opt/whisper.cpp -B /opt/whisper.cpp/build -DCMAKE_BUILD_TYPE=Release
sudo cmake --build /opt/whisper.cpp/build --config Release -j 4
sudo bash /opt/whisper.cpp/models/download-ggml-model.sh small
sudo chmod 0755 /opt/whisper.cpp/build/bin/whisper-cli
sudo chmod 0644 /opt/whisper.cpp/models/ggml-small.bin
```

O modelo correto para português é `small`, não `small.en`.

## 2. Configurar o Laravel

Inclua no `.env` da VPS:

```dotenv
APP_URL=https://clinicas.seudominio.com
QUEUE_CONNECTION=database

WHATSAPP_DRIVER=uazapi
WHATSAPP_PUBLIC_NUMBER=5511999999999
WHATSAPP_ALERT_EMAIL=operacao@seudominio.com
# Deixe vazio em novas instalações; use somente para migração explícita.
WHATSAPP_WEBHOOK_SECRET=
WHATSAPP_ACTIVATION_CODE_TTL_MINUTES=10
WHATSAPP_ACTIVATION_MAX_ATTEMPTS=10

TRANSCRIPTION_ENABLED=true
TRANSCRIPTION_DRIVER=whisper_cpp
TRANSCRIPTION_DISK=local
TRANSCRIPTION_MAX_AUDIO_BYTES=26214400
TRANSCRIPTION_DELETE_AUDIO=true
FFMPEG_BINARY=/usr/bin/ffmpeg
FFMPEG_TIMEOUT=120
WHISPER_CPP_BINARY=/opt/whisper.cpp/build/bin/whisper-cli
WHISPER_CPP_MODEL=/opt/whisper.cpp/models/ggml-small.bin
WHISPER_CPP_LANGUAGE=pt
WHISPER_CPP_THREADS=4
WHISPER_CPP_TIMEOUT=600

UAZAPI_BASE_URL=https://sua-instancia.uazapi.com
UAZAPI_TOKEN=token-da-instancia
UAZAPI_INSTANCE_ID=
UAZAPI_WEBHOOK_EVENTS=messages,messages_update,connection
UAZAPI_WEBHOOK_EXCLUDE_MESSAGES=wasSentByApi,fromMeYes,isGroupYes
UAZAPI_WEBHOOK_MAX_PAYLOAD_BYTES=26214400
UAZAPI_ALLOWED_MEDIA_HOSTS=
UAZAPI_ALLOW_HTTP_MEDIA=false
UAZAPI_HTTP_TIMEOUT=30
```

Depois:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan whisper:check
php artisan whisper:transcribe /caminho/para/audio-de-teste.ogg --language=pt
```

Se a Uazapi devolver a mídia por um CDN diferente do host configurado em `UAZAPI_BASE_URL`, adicione somente o hostname necessário em `UAZAPI_ALLOWED_MEDIA_HOSTS`, separado por vírgulas. Não use curingas.

## 3. Criar ou rotacionar o webhook seguro

O comando configura a única instância do SaaS; não recebe clínica:

```bash
php artisan whatsapp:uazapi-webhook --dry-run
php artisan whatsapp:uazapi-webhook
php artisan whatsapp:uazapi-webhook-status
```

Também é possível informar a URL e o identificador sem editar o `.env`:

```bash
php artisan whatsapp:uazapi-webhook \
  --base-url=https://sua-instancia.uazapi.com \
  --instance-id=identificador-da-instancia
```

O token deve vir do `.env` ou será solicitado de forma oculta no terminal. Ele não deve ser colocado como opção na linha de comando, pois isso o deixaria no histórico do shell. No modo recomendado da Uazapi o comando omite `action`, criando ou atualizando automaticamente o único webhook da instância.

Para migrar temporariamente um segredo já usado por outro sistema, configure `WHATSAPP_WEBHOOK_SECRET` e execute:

```bash
php artisan whatsapp:uazapi-webhook --use-configured-secret
```

Para uma instalação SaaS nova, prefira não definir essa variável: o comando criará o segredo global. Sem `--use-configured-secret`, executar novamente rotaciona esse segredo e atualiza o único webhook.

## 4. Vincular os profissionais

Cada profissional acessa **Automação pelo WhatsApp**, gera um código como `FIN-482917` e o envia, pelo telefone que deseja vincular, ao número definido em `WHATSAPP_PUBLIC_NUMBER`. O código:

- expira em 10 minutos por padrão;
- é armazenado somente como HMAC, nunca em texto puro;
- pode ser usado uma única vez;
- limita tentativas por telefone durante a janela de validade;
- associa o telefone ao usuário autenticado e à clínica desse usuário.
- resolve o proprietário financeiro pela clínica (`clinics.owner_id`), sem duplicar nem congelar essa informação no vínculo.

Mensagens de remetentes sem vínculo, de grupos ou identificadores `@lid` sem telefone direto são ignoradas e não disparam automações.

## 5. Evitar que o segredo apareça no access log

Como a Uazapi não oferece header customizado documentado para autenticar callbacks, o segredo faz parte do caminho da URL. No CloudPanel/Nginx, desative o access log especificamente para esse prefixo, preservando os logs do restante do sistema. Adapte o bloco ao vhost existente e valide com `nginx -t` antes de recarregar:

```nginx
location ^~ /api/webhooks/uazapi/ {
    access_log off;
    try_files $uri $uri/ /index.php?$query_string;
}
```

Não substitua todo o arquivo do vhost por esse trecho. Adicione-o pela configuração do site no CloudPanel ou peça ao administrador da VPS para mesclá-lo com o bloco Laravel existente.

## 6. Worker de fila

O webhook responde rapidamente e delega download/transcrição à fila. Mantenha um worker supervisionado:

```bash
php artisan queue:work --queue=default --sleep=2 --tries=3 --timeout=660
```

Depois de instalar ou ativar o Whisper, áudios que chegaram enquanto ele estava desabilitado podem ser reenfileirados:

```bash
php artisan whatsapp:transcribe-pending --limit=100
php artisan whatsapp:transcribe-pending --clinic=42 --limit=100
```

Após deploy ou alteração de código/configuração:

```bash
php artisan queue:restart
```

O mesmo worker processa as solicitações assíncronas dos pacientes. O scheduler cria lembretes e recupera tarefas interrompidas; mantenha também uma única entrada `php artisan schedule:run` por minuto. Os comandos de ativação por clínica e os parâmetros dos lembretes estão documentados em `docs/IMPLEMENTAÇÃO_UAZAPI_CLIENTES.md`.

## 7. Fluxo resultante

1. A Uazapi envia todos os eventos para a única URL secreta e HTTPS do SaaS.
2. O sistema valida o segredo e a instância central.
3. O telefone remetente é normalizado e resolvido no vínculo de usuário e clínica; mensagens sem vínculo não executam automações.
4. O evento é gravado criptografado e de forma idempotente já com `user_id` e `clinic_id`.
5. A fila solicita a mídia à Uazapi com o token global criptografado.
6. O FFmpeg converte o áudio para WAV mono de 16 kHz e o `whisper-cli` usa `ggml-small.bin` em português.
7. O texto fica criptografado e o evento `WhatsAppAudioTranscribed` é disparado com o contexto correto do profissional e da clínica.
8. O arquivo de áudio temporário é apagado, salvo se `TRANSCRIPTION_DELETE_AUDIO=false`.
