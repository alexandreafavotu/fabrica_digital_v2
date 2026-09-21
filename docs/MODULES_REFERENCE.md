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
- **Bloqueio Setorial Temporário**:
  - Trava temporariamente o Almoxarifado, Expedição ou Faturamento até uma determinada data do jogo (`bloqueio_almoxarifado_ate`, etc.).
- **Quebra de Máquina**:
  - Paralisa uma Ordem de Produção ativa e define uma `previsao_conserto`.
- **Atraso de Fornecedor**:
  - Adiciona dias extras de atraso no *Lead Time* de uma ordem de compra em trânsito.
- **Sabotagem / Inconformidade de Carga**:
  - Injeta lote avariado ou com quantidade divergente na entrega do fornecedor.
- **Refugo Forçado na Produção**:
  - Força refugo não planejado em uma OP (`tem_refugo_forcado`, `qtd_refugo_forcado`, `motivo_refugo_forcado`), exigindo retrabalho ou nova requisição de matéria-prima.
- **Plantão do Caos**:
  - Emissão de avisos urgentes exibidos em banners piscantes nos painéis dos alunos.

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
