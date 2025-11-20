# Implementação do Toggle do Aside (Menu Lateral)

## Visão Geral

Este documento descreve a implementação completa da funcionalidade de esconder/mostrar o menu lateral (aside) usando um botão FAB (Floating Action Button) no AZClinic v2.

A solução permite que usuários em desktop e tablet alternem a visibilidade do menu lateral para maximizar o espaço de conteúdo, com o estado sendo persistido no localStorage do navegador.

## Componentes da Implementação

### 1. Estrutura HTML

**Localização**: `resources/views/components/layouts/app.blade.php`

```blade
<!--begin::Aside Toggle FAB - Desktop/Tablet-->
<button type="button" class="aside-toggle-fab d-none d-lg-flex" id="kt_aside_toggle_desktop" title="Menu">
    <i class="ki-duotone ki-abstract-14 fs-2" id="kt_aside_toggle_icon">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
</button>
<!--end::Aside Toggle FAB-->
```

**Características**:
- Classe `d-none d-lg-flex`: Esconde o botão em mobile (< 992px) e mostra em desktop/tablet
- ID `kt_aside_toggle_desktop`: Identificador único para o JavaScript
- ID `kt_aside_toggle_icon`: Ícone que rotaciona quando o aside é escondido
- Posição fixa controlada por CSS

---

### 2. Estilos CSS

**Localização**: `resources/views/components/layouts/app.blade.php` (dentro de `<style>`)

#### 2.1 Estilos do Botão FAB

```css
.aside-toggle-fab {
    position: fixed;
    left: 20px;
    top: 20px;
    z-index: 1000;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--bs-primary);
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
}
```

**Estados do Botão**:

```css
/* Hover - aumenta escala e sombra */
.aside-toggle-fab:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    background: var(--bs-primary-hover, #024ca5);
}

/* Active - reduz escala para feedback visual */
.aside-toggle-fab:active {
    transform: scale(0.95);
}

/* Ícone branco com transição */
.aside-toggle-fab i {
    color: #fff !important;
    transition: transform 0.3s ease-in-out;
}
```

#### 2.2 Posicionamento Dinâmico do FAB

```css
/* Quando menu VISÍVEL - FAB ao lado do aside (290px da esquerda) */
body:not(.aside-hidden) .aside-toggle-fab {
    left: 290px;
}

/* Quando menu ESCONDIDO - FAB no canto superior esquerdo */
body.aside-hidden .aside-toggle-fab {
    left: 20px;
}
```

#### 2.3 Estado `aside-hidden` - Escondendo o Menu

```css
/* 1. Esconde completamente o aside */
body.aside-hidden #kt_aside {
    display: none !important;
    width: 0 !important;
    min-width: 0 !important;
    max-width: 0 !important;
}

/* 2. Remove padding/margin/gap da página */
body.aside-hidden .page {
    padding-left: 0 !important;
    margin-left: 0 !important;
    padding-right: 0 !important;
    margin-right: 0 !important;
    gap: 0 !important;
    position: relative !important;
}

/* 3. Força flex-root a ocupar 100% da largura */
body.aside-hidden .flex-root {
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
}

/* 4. Posiciona wrapper de forma absoluta para eliminar espaço em branco */
body.aside-hidden #kt_wrapper {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    right: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    padding-left: 80px !important; /* Espaço para o FAB (50px) + margens */
}

body.aside-hidden .wrapper {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    right: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    padding-left: 80px !important; /* Espaço para o FAB */
}

/* 5. Expande containers de header e content para 100% */
body.aside-hidden #kt_header_container,
body.aside-hidden #kt_content_container {
    max-width: 100% !important;
    width: 100% !important;
    padding-left: 2.25rem;
    padding-right: 2.25rem;
}

/* 6. Força área de conteúdo a expandir */
body.aside-hidden #kt_content {
    width: 100% !important;
    max-width: 100% !important;
}

body.aside-hidden .content {
    width: 100% !important;
    max-width: 100% !important;
}
```

#### 2.4 Rotação do Ícone

```css
/* Ícone em estado normal */
#kt_aside_toggle_icon {
    transition: transform 0.3s ease-in-out;
}

/* Rotaciona 90 graus quando aside escondido */
body.aside-hidden #kt_aside_toggle_icon {
    transform: rotate(90deg);
}
```

#### 2.5 Responsividade Mobile

```css
@media (max-width: 991px) {
    /* Mantém aside visível em mobile */
    body.aside-hidden #kt_aside {
        display: block !important;
    }

    /* Remove margens em mobile */
    body.aside-hidden #kt_wrapper {
        margin-left: 0 !important;
    }

    body.aside-hidden .page {
        padding-left: 0 !important;
    }

    /* Esconde FAB em mobile */
    .aside-toggle-fab {
        display: none !important;
    }
}
```

#### 2.6 Animações Suaves

```css
@media (min-width: 992px) {
    .aside,
    .wrapper,
    .page,
    .container {
        transition: all 0.3s ease-in-out;
    }
}

#kt_wrapper {
    transition: all 0.3s ease-in-out;
}

.page {
    transition: all 0.3s ease-in-out;
}

#kt_header_container,
#kt_content_container {
    transition: max-width 0.3s ease-in-out;
}
```

---

### 3. JavaScript - Lógica de Toggle

**Localização**: `resources/views/components/layouts/app.blade.php` (final do arquivo, antes de `</body>`)

```javascript
<script>
    (function() {
        'use strict';

        // Aguarda DOM estar pronto
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('kt_aside_toggle_desktop');
            const body = document.body;
            const STORAGE_KEY = 'aside_hidden_state';

            if (!toggleBtn) return;

            // 1. CARREGA ESTADO SALVO DO LOCALSTORAGE
            const savedState = localStorage.getItem(STORAGE_KEY);
            if (savedState === 'true') {
                body.classList.add('aside-hidden');

                // Dispara resize após carregar página com aside escondido
                setTimeout(function() {
                    window.dispatchEvent(new Event('resize'));

                    // Ajusta DataTables se presente
                    if (typeof $.fn.DataTable !== 'undefined') {
                        $.fn.DataTable.tables({ visible: true, api: true }).columns.adjust();
                    }
                }, 500);
            }

            // 2. FUNCIONALIDADE DE TOGGLE
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Alterna classe no body
                body.classList.toggle('aside-hidden');

                // Salva estado no localStorage
                const isHidden = body.classList.contains('aside-hidden');
                localStorage.setItem(STORAGE_KEY, isHidden.toString());

                // Atualiza tooltip do botão
                const tooltip = isHidden ? 'Abrir Menu' : 'Fechar Menu';
                toggleBtn.setAttribute('title', tooltip);

                // 3. FORÇA RECÁLCULO DE LAYOUT PARA CHARTS/DATATABLES/CARDS
                setTimeout(function() {
                    // Dispara múltiplos eventos resize para melhor compatibilidade
                    window.dispatchEvent(new Event('resize'));

                    // Recalcula DataTables se presente
                    if (typeof $.fn.DataTable !== 'undefined') {
                        $.fn.DataTable.tables({ visible: true, api: true }).columns.adjust();
                    }

                    // Redesenha gráficos (ApexCharts, ChartJS, etc)
                    if (typeof ApexCharts !== 'undefined') {
                        window.dispatchEvent(new Event('resize'));
                    }
                }, 350); // Tempo para animação CSS completar
            });
        });
    })();
</script>
```

---

## Fluxo de Funcionamento

### Ao Carregar a Página

1. **JavaScript verifica localStorage**: Busca chave `aside_hidden_state`
2. **Se `true`**: Adiciona classe `aside-hidden` ao `<body>`
3. **CSS reage**: Aplica estilos de aside escondido
4. **Recálculo de layout**: Dispara evento `resize` após 500ms para ajustar componentes

### Ao Clicar no Botão FAB

1. **Event listener captura clique**
2. **Toggle da classe**: `body.classList.toggle('aside-hidden')`
3. **Salva no localStorage**: `localStorage.setItem('aside_hidden_state', isHidden)`
4. **Atualiza tooltip**: Muda texto do botão entre "Abrir Menu" e "Fechar Menu"
5. **CSS transiciona**: Animação de 0.3s move elementos
6. **Recálculo de componentes**: Após 350ms (tempo da animação), dispara:
   - `window.resize` para gráficos
   - `DataTable.columns.adjust()` para tabelas
   - Redraws de charts (ApexCharts, ChartJS)

---

## Soluções para Problemas Encontrados

### Problema 1: Espaço em Branco à Esquerda
**Causa**: Framework Metronic reservava espaço para o aside mesmo quando escondido

**Solução**:
```css
body.aside-hidden #kt_wrapper {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    right: 0 !important;
}
```
Forçar `position: absolute` elimina o espaço reservado.

### Problema 2: FAB Sobrepondo Título
**Causa**: FAB fixo em `left: 20px` cobria conteúdo da página

**Solução**:
```css
body.aside-hidden #kt_wrapper,
body.aside-hidden .wrapper {
    padding-left: 80px !important;
}
```
Adiciona espaço de 80px (50px FAB + 20px left + 10px margem) para o conteúdo.

### Problema 3: DataTables Desalinhadas
**Causa**: Mudança de largura não recalculava colunas

**Solução**:
```javascript
if (typeof $.fn.DataTable !== 'undefined') {
    $.fn.DataTable.tables({ visible: true, api: true }).columns.adjust();
}
```
Força recálculo de todas as tabelas visíveis.

### Problema 4: Estado Não Persistia Entre Páginas
**Causa**: Sem armazenamento do estado

**Solução**:
```javascript
const STORAGE_KEY = 'aside_hidden_state';
localStorage.setItem(STORAGE_KEY, isHidden.toString());
```
Salva estado no localStorage do navegador.

### Problema 5: Mobile Quebrava Layout
**Causa**: Lógica de desktop aplicada em mobile

**Solução**:
```css
@media (max-width: 991px) {
    body.aside-hidden #kt_aside {
        display: block !important;
    }
    .aside-toggle-fab {
        display: none !important;
    }
}
```
Em mobile, sempre mostra aside (drawer) e esconde FAB.

---

## Breakpoints de Responsividade

- **Mobile**: `< 992px` (lg)
  - FAB escondido
  - Aside funciona como drawer mobile
  - Toggle desabilitado

- **Desktop/Tablet**: `>= 992px`
  - FAB visível
  - Toggle funcional
  - Animações ativas

---

## Arquivos Modificados

1. **`resources/views/components/layouts/app.blade.php`**
   - Adicionado botão FAB
   - Adicionado CSS inline (linhas 26-268)
   - Adicionado JavaScript (final do arquivo)

2. **`resources/views/components/layouts/sidebar.blade.php`**
   - Nenhuma modificação necessária (componente independente)

---

## Considerações de Performance

1. **Transições CSS**: Todas animações em 0.3s para suavidade sem lag
2. **setTimeout**: 350ms para recálculo após animação CSS completar
3. **localStorage**: Acesso mínimo (apenas load e save)
4. **Event listeners**: Único listener no botão FAB
5. **Passive listeners**: Não necessário, pois apenas um listener

---

## Testes Recomendados

- [ ] Toggle funciona em desktop (>= 992px)
- [ ] FAB escondido em mobile (< 992px)
- [ ] Estado persiste ao recarregar página
- [ ] DataTables recalculam colunas corretamente
- [ ] Gráficos (ApexCharts/ChartJS) redimensionam
- [ ] Sem espaço em branco à esquerda quando escondido
- [ ] FAB não sobrepõe conteúdo
- [ ] Tooltip muda entre "Abrir Menu" e "Fechar Menu"
- [ ] Ícone rotaciona 90 graus ao esconder
- [ ] Animações suaves (0.3s)

---

## Manutenção Futura

### Para Ajustar Largura do Aside
Modificar em 3 locais:

1. **CSS - Posição do FAB (menu visível)**:
```css
body:not(.aside-hidden) .aside-toggle-fab {
    left: [LARGURA_ASIDE + 20px];
}
```

2. **Largura do Aside** (não modificar se usando framework):
```css
/* Gerenciado pelo Metronic em #kt_aside */
```

3. **Padding do Wrapper**:
```css
body.aside-hidden #kt_wrapper {
    padding-left: 80px; /* Ajustar se mudar tamanho do FAB */
}
```

### Para Ajustar Tamanho do FAB
```css
.aside-toggle-fab {
    width: 50px;    /* Modificar */
    height: 50px;   /* Modificar */
}

/* Recalcular padding-left do wrapper */
body.aside-hidden #kt_wrapper {
    padding-left: calc([LARGURA_FAB] + [LEFT] + [MARGEM]);
}
```

### Para Adicionar Atalho de Teclado
```javascript
document.addEventListener('keydown', function(e) {
    // Ctrl + B para toggle
    if (e.ctrlKey && e.key === 'b') {
        e.preventDefault();
        toggleBtn.click();
    }
});
```

---

## Referências

- **Framework**: Metronic 9
- **Bootstrap**: v5.3
- **Ícones**: Keenicons (ki-duotone)
- **Responsividade**: Bootstrap breakpoints (lg = 992px)

---

**Última Atualização**: 2025-01-15
**Autor**: Claude Code
**Versão**: 1.0.0
