# 🧩 REFERÊNCIA DE MÓDULOS ESPECIAIS

Este documento descreve os módulos avançados do sistema que possuem regras específicas e inteligência acoplada.

---

## 1. Painel do Professor (Game Master)

- **Controle do Relógio & Simulação**:
  - `POST /professor/simulacao/avancar`: Avança `data_jogo` em 1 dia ou intervalo configurado.
  - `POST /professor/simulacao/resetar`: Reseta a timeline para o início do semestre.
  - `POST /professor/simulacao/status`: Pausa ou retoma o jogo para a turma (`jogo_ativo`).
- **Gestão Global de Alunos e Escalação**:
  - Cadastro global de alunos com importação em massa via CSV (`professor.global.importar`).
  - Vinculação ágil de alunos nas turmas e alocação dinâmica nos 7 setores de trabalho.
- **Gestão de Parâmetros Industriais**:
  - Cadastros de matérias-primas, produtos acabados, ficha técnica (BOM), clientes e fornecedores.
  - Capacidade de produção por turno e limite diário de captação de vendas.
  - Injeção de capital de giro e ajuste de saldo de caixa.

---

## 2. Painel do Caos (`CaosController.php`)

Ferramenta pedagógica para ensinar gestão de crises e resiliência operacional aos alunos:
- **Operações em Lote (Seleção Múltipla)**:
  - Permite ao professor aplicar incidentes pontuais em múltiplas ordens simultaneamente com um único clique (botões de atalho "Marcar/Desmarcar Todas").
- **Bloqueio Setorial Temporário**:
  - Trava temporariamente o Almoxarifado, Expedição ou Faturamento até uma determinada data do jogo (`bloqueio_almoxarifado_ate`, etc.).
- **Quebra de Máquinas em Lote**:
  - Paralisa uma ou múltiplas Ordens de Produção ativas em lote e define a data final de retorno da manutenção.
- **Atraso de Fornecedor em Lote**:
  - Adiciona dias extras de atraso no *Lead Time* de uma ou múltiplas ordens de compra em trânsito.
- **Sabotagem / Inconformidade de Carga em Lote**:
  - Injeta lote avariado ou com quantidade divergente na entrega de uma ou várias OCs a caminho.
- **Refugo Forçado na Produção em Lote**:
  - Força refugo não planejado em uma ou várias OPs (`tem_refugo_forcado`, `qtd_refugo_forcado`, `motivo_refugo_forcado`), exigindo retrabalho ou nova requisição de matéria-prima.
- **Plantão do Caos**:
  - Emissão de avisos urgentes exibidos em banners piscantes nos painéis dos alunos.

---

## 2.1 Chão de Fábrica & Linhas de Produção (`AlunoController.php` / `dashboard.blade.php`)

- **Priorização do Operador ("Sua Linha Primeiro")**:
  - A linha do aluno logado é exibida imediatamente abaixo do Backlog de Ordens com destaque visual para rápida tomada de ação.
- **Accordion / Sanfona para Demais Operadores**:
  - As linhas dos outros colegas ficam recolhidas por padrão em componentes `<details>/<summary>`, mantendo a tela compacta e limpa mesmo em turmas grandes.
- **Scroll Restoration Inteligente**:
  - Formulários de produção (*Pegar Ordem*, *Solicitar Material*, *Ligar Máquina*) preservam a posição exata da rolagem via `sessionStorage`, eliminando o retorno incômodo ao topo da página.
- **Controle de Capacidade por Turma**:
  - O professor define a capacidade máxima de máquinas simultâneas por aluno (ex: 3 OPs ativas).

---

## 3. Gênesis IA (`GenesisController.php`)

- **Objetivo**: Geração automática por inteligência artificial (Google Gemini API) de empresas inteiras, produtos com árvores de componentes estruturadas (BOM) e demandas de mercado customizadas com base no segmento industrial escolhido pelo professor (ex: Automotivo, Moveleiro, Bebidas, Eletrônicos).
- **Fluxo**:
  1. O professor seleciona o segmento e número de produtos.
  2. `POST /professor/genesis/gerar`: O backend formula o prompt pedagógico e requisita a estrutura JSON ao modelo Gemini.
  3. `POST /professor/genesis/salvar`: Persiste atomicamente no banco as matérias-primas, produtos acabados e os relacionamentos na tabela `estrutura_produto`.

---

## 4. Sistema de Acessibilidade Cognitiva e Visual

- **Acessibilidade Cognitiva**:
  - Implementada através do helper `traduz($termo)` em `app/helpers.php`.
  - Se ativado no cadastro do usuário (`users.acessibilidade_cognitiva = 1`), converte jargões industriais complexos em linguagem clara e direta.
- **Acessibilidade Visual**:
  - Suporte nativo a fonte para dislexia (*OpenDyslexic*), modo de alto contraste, ampliação de fontes e espaçamento entre linhas e parágrafos configuráveis individualmente por usuário.

---

## 5. Avaliação Pedagógica por Competências

- **Importador Inteligente de Planos de Curso**:
  - Faz upload de diretrizes curriculares / planos de curso (PDF/DOCX).
  - A IA extrai e classifica as competências técnicas e socioemocionais em uma matriz editável.
- **Lançamento de Avaliações**:
  - O professor avalia o desempenho individual de cada aluno por turma e setor com notas e critérios socioemocionais (Comunicação, Resolução de Problemas, Colaboração, Pontualidade).
