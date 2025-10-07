# Laravel Template

Template Laravel pré-configurado com autenticação, Docker e assets frontend otimizados.

## 🚀 Características

- **Laravel 12** com PHP 8.3
- **Autenticação** completa (login, registro, logout)
- **Docker** configurado para desenvolvimento e produção
- **Assets frontend** prontos em `/public/assets/`
- **Tailwind CSS v4** para estilização
- **Pest** para testes
- **Laravel Pint** para formatação de código
- **Laravel MCP** para desenvolvimento assistido

## 📋 Pré-requisitos

- Docker e Docker Compose
- Git

## 🔧 Instalação

### 1. Clone o repositório

```bash
git clone <seu-repositorio>
cd laravel
```

### 2. Configure o ambiente

```bash
# Copie o arquivo de ambiente
cp .env.example .env

# Ou use o ambiente Docker
cp .docker/.env.docker .env
```

### 3. Inicie o ambiente Docker

#### Desenvolvimento

```bash
# Entre no diretório Docker
cd .docker

# Inicie os containers
docker-compose -f docker-compose.dev.yml up -d

# Volte ao diretório raiz
cd ..
```

#### Produção

```bash
# Entre no diretório Docker
cd .docker

# Inicie os containers
docker-compose up -d

# Volte ao diretório raiz
cd ..
```

### 4. Instale as dependências

```bash
# Dentro do container
docker exec -it laravel-app-dev composer install

# Gere a chave da aplicação
docker exec -it laravel-app-dev php artisan key:generate

# Execute as migrations (se houver banco de dados configurado)
docker exec -it laravel-app-dev php artisan migrate
```

## 🌐 Acesso

- **Desenvolvimento**: http://localhost
- **Produção**: http://localhost:8090

## 📁 Estrutura do Projeto

```
.
├── .docker/                 # Configurações Docker
│   ├── Dockerfile
│   ├── docker-compose.yml
│   ├── docker-compose.dev.yml
│   ├── nginx/              # Configurações Nginx
│   ├── php/                # Configurações PHP
│   └── supervisor/         # Configurações Supervisor
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Auth/       # Controllers de autenticação
│   └── Models/
├── public/
│   └── assets/             # Assets frontend (CSS, JS, imagens)
├── resources/
│   └── views/
│       └── auth/           # Views de autenticação
└── routes/
    └── web.php             # Rotas web
```

## 🔐 Autenticação

O template inclui autenticação completa:

- **Login**: `/login`
- **Registro**: `/register`
- **Dashboard**: `/dashboard` (protegida)
- **Logout**: `POST /logout`

### Criando um usuário via tinker

```bash
docker exec -it laravel-app-dev php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
]);
```

## 🎨 Frontend

Os assets estão organizados em `/public/assets/`:

- **CSS**: `/public/assets/css/`
- **JavaScript**: `/public/assets/js/`
- **Imagens**: `/public/assets/media/`
- **Plugins**: `/public/assets/plugins/`

## 🧪 Testes

```bash
# Execute todos os testes
docker exec -it laravel-app-dev php artisan test

# Execute um arquivo específico
docker exec -it laravel-app-dev php artisan test tests/Feature/Auth/LoginTest.php

# Execute com filtro
docker exec -it laravel-app-dev php artisan test --filter=login
```

## 🎨 Formatação de Código

```bash
# Formate o código com Pint
docker exec -it laravel-app-dev vendor/bin/pint

# Verifique apenas arquivos modificados
docker exec -it laravel-app-dev vendor/bin/pint --dirty
```

## 🔧 Comandos Úteis

```bash
# Limpar cache
docker exec -it laravel-app-dev php artisan cache:clear
docker exec -it laravel-app-dev php artisan config:clear
docker exec -it laravel-app-dev php artisan route:clear
docker exec -it laravel-app-dev php artisan view:clear

# Otimizar para produção
docker exec -it laravel-app php artisan optimize
docker exec -it laravel-app php artisan config:cache
docker exec -it laravel-app php artisan route:cache
docker exec -it laravel-app php artisan view:cache
```

## 🚀 Adicionando API Externa (Opcional)

Se você deseja integrar uma API externa:

1. Crie um Service em `app/Services/`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExternalApiService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.external_api.url');
    }

    public function get(string $endpoint): array
    {
        try {
            $response = Http::timeout(30)
                ->get($this->baseUrl . $endpoint);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Erro ao buscar dados',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro na comunicação com o servidor',
                'error' => $e->getMessage(),
            ];
        }
    }
}
```

2. Configure em `config/services.php`:

```php
'external_api' => [
    'url' => env('EXTERNAL_API_URL'),
],
```

3. Adicione ao `.env`:

```env
EXTERNAL_API_URL=https://api.example.com
```

## 📝 Banco de Dados

Por padrão, o template usa `DB_CONNECTION=null` (sem banco). Para adicionar:

1. Configure no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=secret
```

2. Adicione o serviço MySQL ao `docker-compose.yml`:

```yaml
mysql:
  image: mysql:8.0
  container_name: laravel-mysql
  environment:
    MYSQL_ROOT_PASSWORD: secret
    MYSQL_DATABASE: laravel
  volumes:
    - mysql_data:/var/lib/mysql
  ports:
    - "3306:3306"

volumes:
  mysql_data:
```

3. Execute as migrations:

```bash
docker exec -it laravel-app-dev php artisan migrate
```

## 📄 Licença

Este projeto é open-source e está disponível sob a licença MIT.

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues e pull requests.
