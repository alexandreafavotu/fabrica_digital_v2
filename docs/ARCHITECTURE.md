# 🏛️ ARQUITETURA TÉCNICA DO SISTEMA

> **Repositório**: `fabrica-escola_18-09-2026`  
> **Framework**: Laravel 11.x (PHP 8.2+)  
> **Frontend**: Blade Templates, Vanilla JavaScript, Tailwind CSS, Vite  
> **Database**: MySQL / MariaDB (XAMPP)

---

## 1. Estrutura de Diretórios Chave

```
fabrica-escola_18-09-2026/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AjudaController.php          # Gerencia dicas/ajudas dinâmicas por tela
│   │   │   ├── AlunoController.php          # Concentra as operações dos 7 setores operacionais
│   │   │   ├── CaosController.php           # Injeção de anomalias pelo professor
│   │   │   ├── GenesisController.php        # Gerador por IA (Gemini) de estruturas/BOM
│   │   │   ├── MonitoramentoController.php  # Monitoramento em tempo real para o professor
│   │   │   ├── ProfessorController.php      # Administração, turmas, regras, cadastros base
│   │   │   ├── ProfileController.php        # Perfil do usuário
│   │   │   └── RelatoriosController.php     # BI e relatórios estatísticos
│   │   └── Middleware/
│   │       ├── CheckSetor.php               # Bloqueia acessos cruzados entre setores de alunos
│   │       ├── ProtecaoSenhaMestra.php      # Protege exclusões e operações críticas
│   │       └── ...
│   ├── Models/                              # 19 Eloquent Models com relacionamentos estruturados
│   └── helpers.php                          # Helper traduz() e utilitários globais
├── docs/                                    # Base de Memória Viva do Sistema
├── database/
│   └── migrations/                          # 55+ Migrations com histórico evolutivo
├── resources/
│   └── views/
│       ├── aluno/                           # Telas segmentadas por setor (pcp, compras, etc.)
│       ├── professor/                       # Telas do Game Master (turmas, caos, avaliações)
│       └── layouts/                         # Layouts base (app, navigation, etc.)
└── routes/
    ├── web.php                              # GPS e rotas autenticadas
    └── auth.php                             # Breeze / Autenticação
```

---

## 2. GPS Inteligente de Redirecionamento (`/dashboard`)

O sistema utiliza um único ponto de entrada para direcionar cada usuário à sua interface correta:

```php
// Rota /dashboard em routes/web.php
1. Se user->tipo == 'professor' -> redirect('professor.dashboard')
2. Se user->tipo == 'aluno':
   - Busca Aluno onde user_id == user->id
   - Se não tiver turma/setor -> view('dashboard')
   - Conforme $aluno->setor:
       - 'vendas'       -> 'aluno.vendas.index'
       - 'pcp'          -> 'aluno.pcp.dashboard'
       - 'compras'      -> 'aluno.compras.dashboard'
       - 'almoxarifado' -> 'aluno.almoxarifado.dashboard'
       - 'producao'     -> 'aluno.producao.dashboard'
       - 'embalagem'    -> 'aluno.embalagem.dashboard'
       - 'expedicao'    -> 'aluno.expedicao.dashboard'
```

---

## 3. Middlewares e Camada de Segurança

1. **`auth`**: Garante que o usuário está autenticado.
2. **`professor`**: Garante que apenas usuários com `tipo == 'professor'` (ou super admin) acessem rotas gerenciais.
3. **`setor:{nome_setor}`**: Impede que um aluno alocado em `compras` acerte rotas de `producao` ou `pcp`, mantendo o isolamento de papéis gamificados.
4. **`ProtecaoSenhaMestra`**: Intercepta operações perigosas (exclusão de turma, redefinição de mapa de estoque, remoção de matérias-primas).

---

## 4. Helper Global de Acessibilidade (`app/helpers.php`)

- **Função `traduz($termo)`**: Se o usuário autenticado tiver a flag `acessibilidade_cognitiva = true`, os termos técnicos industriais (como *BOM, MRP, SKU, Lead Time*) são convertidos em termos acessíveis e amigáveis para estudantes em desenvolvimento ou com necessidades específicas.
