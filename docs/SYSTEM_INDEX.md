# 🧠 MAPA CENTRAL DE MEMÓRIA DO SISTEMA - FÁBRICA DIGITAL (Fábrica-Escola)

> **Finalidade**: Este documento e a pasta `docs/` servem como a **Fonte Única da Verdade** da arquitetura, regras de negócio, fluxos industriais e banco de dados deste sistema.  
> **Regra de Ouro**: Sempre que fizer qualquer alteração no código, consulte estes arquivos para não quebrar integrações e atualize a documentação correspondente.

---

## 🗂️ Índice dos Documentos de Memória

| Arquivo | Descrição |
| :--- | :--- |
| [ARCHITECTURE.md](file:///c:/xampp/htdocs/fabrica-escola_18-09-2026/docs/ARCHITECTURE.md) | Arquitetura técnica, stack (Laravel 11, Blade, Tailwind, MySQL), GPS de Rotas e Middlewares. |
| [DATABASE_SCHEMA.md](file:///c:/xampp/htdocs/fabrica-escola_18-09-2026/docs/DATABASE_SCHEMA.md) | Dicionário de dados de todas as 55+ migrations e tabelas, relacionamentos e colunas críticas. |
| [BUSINESS_RULES_FLOWS.md](file:///c:/xampp/htdocs/fabrica-escola_18-09-2026/docs/BUSINESS_RULES_FLOWS.md) | Fluxo completo de simulação: Vendas -> PCP (MRP) -> Compras -> Almoxarifado (WMS) -> Produção -> Embalagem -> Expedição. |
| [MODULES_REFERENCE.md](file:///c:/xampp/htdocs/fabrica-escola_18-09-2026/docs/MODULES_REFERENCE.md) | Módulos detalhados: Painel do Professor (Game Master), Painel do Caos, Gênesis IA, Acessibilidade Cognitiva, Avaliações por Competências. |
| [CHANGELOG_GUARD.md](file:///c:/xampp/htdocs/fabrica-escola_18-09-2026/docs/CHANGELOG_GUARD.md) | Histórico de alterações e diretrizes de salvaguarda para novas implementações sem quebra. |

---

## 🧭 Visão Geral do Sistema

O **Fábrica Digital / Fábrica-Escola** é um simulador pedagógico-industrial gamificado em tempo real onde:
- **Professores (Game Masters)** criam turmas, configuram parâmetros industriais (BOM/Ficha Técnica, Capacidade, Tempo/Relógio Virtual, Limite de Vendas), injetam eventos de imprevisto (**Painel do Caos**), geram estruturas com IA (**Gênesis**) e avaliam o desempenho socioemocional e técnico dos alunos.
- **Alunos (Operadores)** assumem setores específicos em equipes industriais e executam as rotinas reais de uma indústria manufatureira através de telas customizadas por perfil e adaptadas com recursos de acessibilidade.

---

## 🔄 Ciclo Operacional Integrado

```
[MERCADO / PROFESSOR / VENDAS]
             │
             ▼ (Demanda / Pedido de Venda)
        [PCP - MRP]
       /           \
      ▼             ▼
[COMPRAS]      [ORDEM DE PRODUÇÃO]
      │                 │
      ▼                 ▼
[ALMOXARIFADO] ──► [SEPARAÇÃO (PICKING)]
(WMS / Recebimento)     │
                        ▼
                  [PRODUÇÃO] (Start/Apontamento/Refugo)
                        │
                        ▼
                  [EMBALAGEM]
                        │
                        ▼
                  [EXPEDIÇÃO] (Conferência / Faturamento / NF)
```

---

## 🛡️ Diretrizes Rápidas para Manutenção

1. **Nunca remova campos sem verificar migrations e models** (ex: `turma_id`, `data_jogo`, `jogo_ativo`, `aluno_id`).
2. **Respeite o GPS de Redirecionamento de Rotas** em `/dashboard` baseado no tipo de usuário e setor do aluno.
3. **Mantenha os Helpers de Acessibilidade** (`traduz()`) e compatibilidade nos formulários Blade.
4. **Proteção de Ações Críticas**: Rotas destrutivas (exclusões de turmas, cadastros-base, mapa de estoque) exigem o middleware `ProtecaoSenhaMestra`.
