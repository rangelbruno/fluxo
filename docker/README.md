# Docker Setup for Laravel

## Serviços

- **app**: Laravel application (PHP 8.3.6 + Nginx)
- **postgres**: PostgreSQL 16

## Como usar

1. Configure o arquivo `.env` na raiz do projeto com as credenciais do banco:

```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

2. Build e inicie os containers:

```bash
cd docker
docker-compose up -d --build
```

3. Acesse a aplicação em: http://localhost:8000

## Comandos úteis

```bash
# Ver logs
docker-compose logs -f

# Parar containers
docker-compose down

# Rebuild containers
docker-compose up -d --build

# Acessar o container da aplicação
docker-compose exec app bash

# Executar comandos artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan tinker

# Acessar PostgreSQL
docker-compose exec postgres psql -U laravel -d laravel
```

## Volumes

- `postgres_data`: Dados persistentes do PostgreSQL

## Portas

- **8000**: Aplicação Laravel
- **5432**: PostgreSQL
