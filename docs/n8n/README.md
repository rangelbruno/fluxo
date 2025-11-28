# Workflow n8n: Processar Comprovantes de Pagamento

Este workflow automatiza a extração de dados de comprovantes de pagamento (PIX, TED, boleto, etc.) usando IA e salva no banco de dados do sistema.

## Arquitetura

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   WEBHOOK    │───▶│  PREPARAR    │───▶│  PDF ou      │───▶│   OCR/IA     │
│   (POST)     │    │  ARQUIVO     │    │  IMAGEM?     │    │   VISÃO      │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
                                                                   │
                                                                   ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   RESPOSTA   │◀───│   GRAVAR     │◀───│   VALIDAR    │◀───│   PARSEAR    │
│   WEBHOOK    │    │   API        │    │   DADOS      │    │   JSON IA    │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
```

## Pré-requisitos

### 1. Configurar Variáveis de Ambiente no n8n

```env
LARAVEL_API_URL=http://seu-sistema.com
OCR_SPACE_API_KEY=sua-chave-ocr-space
```

### 2. Configurar Credenciais no n8n

1. **OpenAI API**: Para os nós de IA
   - Vá em: Settings → Credentials → Add Credential → OpenAI API
   - Cole sua API Key

2. **HTTP Header Auth** (para API Laravel):
   - Vá em: Settings → Credentials → Add Credential → Header Auth
   - Name: `X-API-Token`
   - Value: Seu token de API (mesmo valor de `N8N_API_TOKEN` no Laravel)

### 3. Configurar Laravel

Adicione ao `.env` do Laravel:

```env
N8N_API_TOKEN=seu-token-seguro-aqui
N8N_ALLOWED_IPS=127.0.0.1,::1,172.16.0.0/12
N8N_WEBHOOK_URL=http://localhost:5678
```

Execute a migration:

```bash
php artisan migrate
```

## Importar Workflow

1. No n8n, vá em **Workflows → Import from File**
2. Selecione o arquivo `workflow-processar-comprovantes.json`
3. Atualize as credenciais nos nós marcados
4. Ative o workflow

## Uso

### Endpoint do Webhook

```
POST http://seu-n8n:5678/webhook/comprovante
```

### Opção 1: Enviar arquivo como multipart/form-data

```bash
curl -X POST \
  http://localhost:5678/webhook/comprovante \
  -H "Content-Type: multipart/form-data" \
  -F "file=@comprovante.pdf" \
  -F "user_id=1"
```

### Opção 2: Enviar arquivo como base64

```bash
curl -X POST \
  http://localhost:5678/webhook/comprovante \
  -H "Content-Type: application/json" \
  -d '{
    "file_base64": "JVBERi0xLjQKJeLjz9MKMyAwI...",
    "file_name": "comprovante.pdf",
    "file_type": "pdf",
    "user_id": 1
  }'
```

### Opção 3: Via JavaScript (Frontend)

```javascript
async function enviarComprovante(file, userId) {
  const base64 = await fileToBase64(file);

  const response = await fetch('http://localhost:5678/webhook/comprovante', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      file_base64: base64,
      file_name: file.name,
      file_type: file.type.split('/')[1],
      file_size: file.size,
      user_id: userId
    })
  });

  return response.json();
}

function fileToBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result.split(',')[1]);
    reader.onerror = reject;
  });
}
```

## Exemplos Práticos

### Exemplo 1: Comprovante PIX

**Texto do comprovante (OCR):**
```
COMPROVANTE DE PAGAMENTO
Banco do Brasil

Data: 27/11/2025
Hora: 14:35:22

Tipo: PIX
Valor: R$ 1.500,00

De:
JOAO DA SILVA
CPF: 123.456.789-00
Banco do Brasil
Ag: 1234-5 CC: 12345-6

Para:
EMPRESA ABC LTDA
CNPJ: 12.345.678/0001-90
Chave PIX: empresa@abc.com.br
Itaú Unibanco
Ag: 5678 CC: 98765-4

ID da Transação: E00000000202511271435abc123def
Autenticação: A1B2C3D4E5F6
```

**JSON retornado pela IA:**
```json
{
  "data_pagamento": "2025-11-27",
  "valor": 1500.00,
  "descricao": null,
  "referencia": null,
  "pagador": "JOAO DA SILVA",
  "documento_pagador": "12345678900",
  "favorecido": "EMPRESA ABC LTDA",
  "documento_favorecido": "12345678000190",
  "forma_pagamento": "PIX",
  "identificador_transacao": "E00000000202511271435abc123def",
  "codigo_autenticacao": "A1B2C3D4E5F6",
  "banco_origem": "Banco do Brasil",
  "agencia_origem": "12345",
  "conta_origem": "123456",
  "banco_destino": "Itaú Unibanco",
  "agencia_destino": "5678",
  "conta_destino": "987654",
  "chave_pix": "empresa@abc.com.br",
  "tipo_chave_pix": "Email",
  "observacoes": null,
  "confianca": 95
}
```

**Payload enviado para API Laravel:**
```json
{
  "data_pagamento": "2025-11-27",
  "valor": 1500.00,
  "descricao": null,
  "referencia": null,
  "pagador": "JOAO DA SILVA",
  "documento_pagador": "12345678900",
  "favorecido": "EMPRESA ABC LTDA",
  "documento_favorecido": "12345678000190",
  "forma_pagamento": "PIX",
  "identificador_transacao": "E00000000202511271435abc123def",
  "codigo_autenticacao": "A1B2C3D4E5F6",
  "banco_origem": "Banco do Brasil",
  "agencia_origem": "12345",
  "conta_origem": "123456",
  "banco_destino": "Itaú Unibanco",
  "agencia_destino": "5678",
  "conta_destino": "987654",
  "chave_pix": "empresa@abc.com.br",
  "tipo_chave_pix": "Email",
  "arquivo_original_nome": "comprovante-pix.png",
  "arquivo_tipo": "png",
  "arquivo_tamanho": 125000,
  "confianca_extracao": 95,
  "texto_ocr": "COMPROVANTE DE PAGAMENTO...",
  "dados_brutos_ia": { ... },
  "observacoes_ia": null,
  "status": "processado",
  "user_id": 1,
  "origem": "n8n",
  "workflow_execution_id": "exec_abc123"
}
```

### Exemplo 2: Boleto Bancário

**Texto do comprovante (OCR):**
```
COMPROVANTE DE PAGAMENTO DE BOLETO

Banco Bradesco
Data do Pagamento: 25/11/2025

Código de Barras:
23793.38128 60000.000003 00000.000405 1 96150000015000

Beneficiário: CEMIG DISTRIBUICAO S.A.
CNPJ: 06.981.180/0001-16

Pagador: MARIA SANTOS OLIVEIRA
CPF: 987.654.321-00

Valor do Documento: R$ 150,00
Desconto: R$ 0,00
Juros/Multa: R$ 0,00
Valor Pago: R$ 150,00

Nosso Número: 000000004
Autenticação: 7890ABCD
```

**JSON retornado pela IA:**
```json
{
  "data_pagamento": "2025-11-25",
  "valor": 150.00,
  "descricao": "Pagamento de boleto CEMIG",
  "referencia": "000000004",
  "pagador": "MARIA SANTOS OLIVEIRA",
  "documento_pagador": "98765432100",
  "favorecido": "CEMIG DISTRIBUICAO S.A.",
  "documento_favorecido": "06981180000116",
  "forma_pagamento": "Boleto",
  "identificador_transacao": "23793381286000000000300000000405196150000015000",
  "codigo_autenticacao": "7890ABCD",
  "banco_origem": "Bradesco",
  "agencia_origem": null,
  "conta_origem": null,
  "banco_destino": null,
  "agencia_destino": null,
  "conta_destino": null,
  "chave_pix": null,
  "tipo_chave_pix": null,
  "observacoes": null,
  "confianca": 90
}
```

## SQL de Exemplo (INSERT Direto)

Se preferir usar o nó Postgres diretamente ao invés da API:

```sql
INSERT INTO comprovantes_pagamento (
  data_pagamento,
  valor,
  descricao,
  referencia,
  pagador,
  documento_pagador,
  favorecido,
  documento_favorecido,
  forma_pagamento,
  identificador_transacao,
  codigo_autenticacao,
  banco_origem,
  agencia_origem,
  conta_origem,
  banco_destino,
  agencia_destino,
  conta_destino,
  chave_pix,
  tipo_chave_pix,
  arquivo_original_nome,
  arquivo_tipo,
  arquivo_tamanho,
  confianca_extracao,
  texto_ocr,
  dados_brutos_ia,
  observacoes_ia,
  status,
  user_id,
  origem,
  workflow_execution_id,
  created_at,
  updated_at
) VALUES (
  '2025-11-27',
  1500.00,
  NULL,
  NULL,
  'JOAO DA SILVA',
  '12345678900',
  'EMPRESA ABC LTDA',
  '12345678000190',
  'PIX',
  'E00000000202511271435abc123def',
  'A1B2C3D4E5F6',
  'Banco do Brasil',
  '12345',
  '123456',
  'Itaú Unibanco',
  '5678',
  '987654',
  'empresa@abc.com.br',
  'Email',
  'comprovante.png',
  'png',
  125000,
  95.00,
  'COMPROVANTE DE PAGAMENTO...',
  '{"data_pagamento": "2025-11-27", ...}'::jsonb,
  NULL,
  'processado',
  1,
  'n8n',
  'exec_abc123',
  NOW(),
  NOW()
);
```

## Troubleshooting

### Erro: "Nenhum arquivo encontrado"
- Verifique se está enviando o arquivo como `file` no multipart ou `file_base64` no JSON

### Erro: "Não autorizado"
- Verifique se o header `X-API-Token` está correto
- Verifique se o IP do n8n está na lista de IPs permitidos

### Erro: "Resposta da IA não é JSON válido"
- A IA às vezes retorna texto extra. O nó de validação tenta extrair o JSON automaticamente.
- Se persistir, ajuste o prompt ou use `response_format: json_object`

### OCR retorna texto ilegível
- Experimente usar OCR Engine 2 (mais lento mas mais preciso)
- Considere usar Google Vision ou AWS Textract para melhor qualidade

## Custos Estimados

| Serviço | Uso por Comprovante | Custo Aprox. |
|---------|---------------------|--------------|
| OCR.space | 1 requisição | Gratuito (até 25k/mês) |
| GPT-4o | ~1000 tokens | ~$0.01 |
| GPT-4o Vision | ~2000 tokens | ~$0.02 |

**Custo médio por comprovante: $0.01 - $0.03**

## Alternativas de OCR

1. **OCR.space** (usado no workflow): Gratuito até 25k req/mês
2. **Google Cloud Vision**: Mais preciso, $1.50/1000 imagens
3. **AWS Textract**: Bom para documentos estruturados, $1.50/1000 páginas
4. **Azure Document Intelligence**: Similar ao Textract

## Alternativas de IA

1. **GPT-4o** (usado no workflow): Melhor custo-benefício
2. **Claude 3.5 Sonnet**: Excelente para análise de documentos
3. **Gemini 1.5 Pro**: Boa alternativa gratuita para testes
