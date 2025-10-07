# Como Funciona Este Projeto

Este documento explica a arquitetura e funcionamento interno do template Laravel.

## 📐 Arquitetura do Projeto

### Estrutura de Diretórios

```
laravel/
├── .docker/                    # Ambiente Docker isolado
│   ├── Dockerfile             # Imagem PHP 8.3 + Nginx + Supervisor
│   ├── docker-compose.yml     # Configuração produção (porta 8090)
│   ├── docker-compose.dev.yml # Configuração desenvolvimento (porta 80)
│   ├── nginx/                 # Servidor web
│   │   ├── nginx.conf        # Configuração principal Nginx
│   │   └── default.conf      # Virtual host Laravel
│   ├── php/                   # Configurações PHP
│   │   ├── php.ini           # php.ini customizado
│   │   └── php-fpm.conf      # PHP-FPM pool configuration
│   ├── supervisor/            # Process manager
│   │   └── supervisord.conf  # Mantém Nginx e PHP-FPM rodando
│   └── start.sh              # Script de inicialização
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/          # Controllers de autenticação
│   │       │   ├── LoginController.php
│   │       │   └── RegisterController.php
│   │       ├── Controller.php # Base controller
│   │       └── DashboardController.php
│   └── Models/
│       └── User.php           # Modelo de usuário Eloquent
│
├── bootstrap/
│   ├── app.php               # Bootstrap da aplicação Laravel 12
│   ├── cache/                # Cache de configuração
│   └── providers.php         # Service providers
│
├── config/                    # Arquivos de configuração
│   ├── app.php
│   ├── auth.php              # Configuração de autenticação
│   ├── session.php           # Configuração de sessão
│   └── ...
│
├── database/
│   ├── migrations/           # Migrations do banco
│   │   └── 0001_01_01_000000_create_users_table.php
│   └── factories/            # Factories para testes
│
├── public/                   # Ponto de entrada web
│   ├── index.php            # Front controller Laravel
│   └── assets/              # Assets frontend estáticos
│       ├── css/             # Estilos CSS
│       ├── js/              # JavaScript
│       ├── media/           # Imagens, vídeos, etc
│       └── plugins/         # Bibliotecas JS de terceiros
│
├── resources/
│   ├── views/               # Templates Blade
│   │   ├── auth/           # Views de autenticação
│   │   │   ├── login.blade.php
│   │   │   └── register.blade.php
│   │   ├── components/     # Componentes reutilizáveis
│   │   └── dashboard.blade.php
│   └── css/                # CSS para compilação (Tailwind)
│
├── routes/
│   ├── web.php             # Rotas web com autenticação
│   └── console.php         # Comandos Artisan customizados
│
├── storage/                # Arquivos gerados
│   ├── app/               # Uploads, arquivos da aplicação
│   ├── framework/         # Cache, sessões, views compiladas
│   └── logs/              # Logs da aplicação
│
├── tests/                 # Testes Pest
│   ├── Feature/          # Testes de integração
│   └── Unit/             # Testes unitários
│
├── .env                  # Variáveis de ambiente
├── .env.example         # Template de variáveis
├── composer.json        # Dependências PHP
├── package.json         # Dependências Node.js
├── artisan             # CLI do Laravel
├── CLAUDE.md           # Diretrizes para Claude Code
└── README.md           # Documentação do usuário
```

## 🔄 Fluxo de Requisição

### 1. Requisição HTTP Chega ao Nginx

```
Cliente → Nginx (porta 80/8090) → PHP-FPM → Laravel
```

**Nginx** (`/.docker/nginx/default.conf`):
- Recebe requisição na porta 80 (dev) ou 8090 (prod)
- Serve arquivos estáticos diretamente de `/public/`
- Encaminha requisições PHP para `public/index.php` via FastCGI

### 2. Bootstrap da Aplicação

**`public/index.php`**:
1. Carrega autoloader do Composer
2. Bootstrap da aplicação via `bootstrap/app.php`
3. Cria instância da aplicação Laravel

**`bootstrap/app.php`** (Laravel 12):
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',  // Define rotas web
        commands: __DIR__.'/../routes/console.php',
        health: '/up',                       // Health check endpoint
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middlewares globais e aliases
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tratamento de exceções
    })->create();
```

### 3. Roteamento

**`routes/web.php`**:
```php
// Rotas para visitantes (guest)
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Rotas protegidas (autenticadas)
Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
```

### 4. Middleware

O middleware `auth` e `guest` são fornecidos nativamente pelo Laravel:

- **`guest`**: Redireciona usuários autenticados para dashboard
- **`auth`**: Redireciona usuários não autenticados para login
- Configurados em `config/auth.php`

### 5. Controllers

#### LoginController (`app/Http/Controllers/Auth/LoginController.php`)

```php
public function login(Request $request): RedirectResponse
{
    // 1. Valida credenciais
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 2. Tenta autenticar usando Auth facade
    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        // 3. Regenera sessão (segurança contra session fixation)
        $request->session()->regenerate();

        // 4. Redireciona para destino pretendido ou dashboard
        return redirect()->intended(route('dashboard'));
    }

    // 5. Retorna erro de credenciais inválidas
    return back()->withErrors([
        'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
    ])->onlyInput('email');
}
```

#### RegisterController

```php
public function register(Request $request): RedirectResponse
{
    // 1. Valida dados do formulário
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    // 2. Cria usuário no banco
    $user = User::query()->create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']), // Hash bcrypt
    ]);

    // 3. Autentica automaticamente
    Auth::login($user);

    // 4. Redireciona para dashboard
    return redirect()->route('dashboard');
}
```

### 6. Models

**User Model** (`app/Models/User.php`):
```php
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Auto-hash no Laravel 11+
        ];
    }
}
```

### 7. Views Blade

**`resources/views/auth/login.blade.php`**:
```blade
<form method="POST" action="{{ route('login') }}">
    @csrf

    <input type="email" name="email" value="{{ old('email') }}" required>
    @error('email')
        <span>{{ $message }}</span>
    @enderror

    <input type="password" name="password" required>

    <button type="submit">Login</button>
</form>
```

### 8. Sessões

- **Driver**: `file` (padrão), armazena em `storage/framework/sessions/`
- Configurável para `database`, `redis`, etc em `config/session.php`
- Cookie de sessão: `laravel_session`
- Dados do usuário autenticado armazenados na sessão

## 🔐 Sistema de Autenticação

### Como Funciona o Auth::attempt()

```php
Auth::attempt(['email' => $email, 'password' => $password])
```

1. Busca usuário por email no banco de dados
2. Compara hash da senha usando `Hash::check()`
3. Se válido, armazena ID do usuário na sessão
4. Retorna `true` ou `false`

### Guards e Providers

**`config/auth.php`**:
```php
'defaults' => [
    'guard' => 'web',           // Guard padrão (sessão)
    'passwords' => 'users',
],

'guards' => [
    'web' => [
        'driver' => 'session',   // Usa sessões PHP
        'provider' => 'users',   // Provider de usuários
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',  // Usa Eloquent ORM
        'model' => App\Models\User::class,
    ],
],
```

## 🐳 Ambiente Docker

### Dockerfile (`.docker/Dockerfile`)

```dockerfile
FROM php:8.3-fpm-alpine

# Instala dependências do sistema
RUN apk add nginx supervisor

# Instala extensões PHP necessárias
RUN docker-php-ext-install mbstring pdo pdo_mysql

# Copia configurações customizadas
COPY .docker/nginx/ /etc/nginx/
COPY .docker/php/ /usr/local/etc/php/
COPY .docker/supervisor/ /etc/supervisor/

# Instala dependências do Composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Copia código da aplicação
COPY . .

# Otimizações Laravel
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
```

### Supervisor (`.docker/supervisor/supervisord.conf`)

Mantém múltiplos processos rodando no container:

```ini
[supervisord]
nodaemon=true

[program:nginx]
command=nginx -g 'daemon off;'
autostart=true
autorestart=true

[program:php-fpm]
command=php-fpm
autostart=true
autorestart=true
```

### Docker Compose

**Desenvolvimento** (`docker-compose.dev.yml`):
- Monta código como volume (hot reload)
- Porta 80
- Debug habilitado

**Produção** (`docker-compose.yml`):
- Código embutido na imagem
- Porta 8090
- Otimizações ativadas

## 🎨 Assets Frontend

### Estrutura

```
public/assets/
├── css/           # Estilos compilados
├── js/            # JavaScript vanilla ou compilado
├── media/         # Imagens, ícones, fontes
└── plugins/       # jQuery, Bootstrap, etc
```

### Usando nos Blades

```blade
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
<script src="{{ asset('assets/js/app.js') }}"></script>
<img src="{{ asset('assets/media/logo.png') }}" alt="Logo">
```

### Tailwind CSS v4

**`resources/css/app.css`**:
```css
@import "tailwindcss";
```

Compile com:
```bash
npm run build  # Produção
npm run dev    # Desenvolvimento com watch
```

## 🧪 Sistema de Testes (Pest)

### Estrutura de Testes

```php
// tests/Feature/Auth/LoginTest.php
it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

it('cannot login with invalid credentials', function () {
    $response = $this->post('/login', [
        'email' => 'wrong@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});
```

## 🔧 Configurações Importantes

### `.env`

```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Banco de dados (opcional)
DB_CONNECTION=null

# Sessão
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

### Laravel 12 - Mudanças Estruturais

- **Sem `app/Http/Kernel.php`**: Middleware em `bootstrap/app.php`
- **Sem `app/Console/Kernel.php`**: Schedule em `routes/console.php`
- **Autoload de commands**: `app/Console/Commands/` auto-registrado
- **Providers otimizados**: Apenas essenciais em `bootstrap/providers.php`

## 🚀 Fluxo Completo de Autenticação

```
1. Usuário acessa /login
   ↓
2. LoginController::showLoginForm()
   ↓
3. Retorna view('auth.login')
   ↓
4. Usuário preenche formulário e envia POST /login
   ↓
5. LoginController::login($request)
   ↓
6. Valida dados (email, password)
   ↓
7. Auth::attempt($credentials)
   ↓
8. Laravel busca User::where('email', $email)->first()
   ↓
9. Hash::check($password, $user->password)
   ↓
10. Se válido: Session::put('auth_user_id', $user->id)
    ↓
11. Redirect para /dashboard
    ↓
12. Middleware 'auth' verifica Auth::check()
    ↓
13. DashboardController::index()
    ↓
14. Retorna view('dashboard', ['user' => $request->user()])
```

## 📝 Boas Práticas Implementadas

✅ **Segurança**:
- CSRF protection em formulários (`@csrf`)
- Session regeneration após login
- Password hashing com bcrypt
- Input validation

✅ **Código Limpo**:
- Type hints em todos métodos
- Return types explícitos
- Controllers organizados por feature
- Rotas agrupadas por middleware

✅ **Performance**:
- Config/route/view caching em produção
- Autoloader otimizado
- Assets estáticos servidos pelo Nginx

✅ **Desenvolvimento**:
- Laravel Pint para code style
- Pest para testes
- Docker para ambiente consistente
- MCP para AI assistance

## 🔍 Debug e Troubleshooting

### Ver logs
```bash
tail -f storage/logs/laravel.log
```

### Verificar rotas
```bash
php artisan route:list
```

### Limpar caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Verificar usuário autenticado
```php
dd(Auth::user());
dd(Auth::check());
dd($request->user());
```

---

Este documento serve como referência completa do funcionamento interno do projeto. Para instruções de uso, consulte o `README.md`.
