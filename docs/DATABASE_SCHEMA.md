# 🗄️ ESQUEMA E DICIONÁRIO DO BANCO DE DADOS

Este documento sintetiza os modelos, tabelas, chaves primárias/estrangeiras e colunas essenciais do ecossistema.

---

## 1. Núcleo de Usuários, Acessibilidade e Turmas

### `users`
*Representa os operadores (alunos), professores e administradores.*
- `id` (PK)
- `name`, `email`, `password`
- `tipo`: `'professor'` ou `'aluno'`
- `ativo`: Flag booleana (bloqueio de login individual)
- `matricula`, `rg`, `cpf`, `data_nascimento`
- **Acessibilidade**:
  - `acessibilidade_cognitiva`: habilita o dicionário `traduz()`
  - `fonte_dislexia`, `alto_contraste`, `tamanho_fonte`, `espacamento_linhas`, etc.

### `turmas`
*O container central de uma empresa simulada no jogo.*
- `id` (PK)
- `professor_id` (FK -> `users.id`): Professor responsável (ID 1 = Super Admin com visão global)
- `nome`: Nome da turma escolar (ex: "Logística 2026/1")
- `nome_empresa`: Nome fantasia da indústria simulada
- `setor`, `segmento`, `resumo`, `cnpj`, `telefone`, `rua`, `numero`, `bairro`, `cidade`, `estado`
- `data_jogo`: Data virtual da simulação (avanço controlado pelo professor)
- `jogo_ativo`: Booleano (Pausado / Em execução)
- `capacidade_producao`: Capacidade produtiva diária/turno
- `limite_vendas_diarias`: Teto de pedidos que o setor comercial pode fechar por dia
- `arquivada`: Turma ativa ou adormecida
- **Parâmetros do Painel do Caos**:
  - `bloqueio_almoxarifado_ate` (datetime)
  - `bloqueio_expedicao_ate` (datetime)
  - `bloqueio_faturamento_ate` (datetime)
  - `mensagem_plantao_caos` (string/text)
- **Financeiro e Relógio**:
  - `saldo_caixa`: Dinheiro em caixa da empresa
  - `relogio_velocidade`, `relogio_auto_avancar`

### `alunos`
*A ligação do usuário aluno à turma e ao seu setor de trabalho.*
- `id` (PK)
- `user_id` (FK -> `users.id`)
- `turma_id` (FK -> `turmas.id`)
- `setor`: `'vendas'`, `'pcp'`, `'compras'`, `'almoxarifado'`, `'producao'`, `'embalagem'`, `'expedicao'`
- `softDeletes`

---

## 2. Cadastros Base e Estrutura de Produtos

### `materia_primas`
*Insumos comprados e armazenados no WMS.*
- `id` (PK), `turma_id` (FK)
- `codigo`, `nome`, `unidade_medida` (ex: UN, KG, M)
- `preco_custo`, `estoque_minimo`, `lead_time_dias`

### `fornecedores`
- `id` (PK), `turma_id` (FK)
- `nome`, `cnpj`, `lead_time_padrao`, `telefone`, `email`

### `clientes`
- `id` (PK), `turma_id` (FK)
- `nome`, `cnpj_cpf`, `email`, `telefone`, `endereco`

### `produtos_acabados`
*Bens finais fabricados e comercializados.*
- `id` (PK), `turma_id` (FK)
- `codigo`, `nome`, `preco_venda`, `tempo_fabricacao_minutos`

### `estrutura_produto` (BOM - Bill of Materials)
*Tabela pivô que define quais insumos compõem um produto acabado.*
- `id` (PK)
- `produto_acabado_id` (FK -> `produtos_acabados.id`)
- `materia_prima_id` (FK -> `materia_primas.id`)
- `quantidade_necessaria`: Proporção de insumo por unidade de produto

---

## 3. Fluxo de Operações Industriais

### `demandas_mercado` & `demandas_mercado_itens`
*Oportunidades geradas pelo professor ou simulador para os alunos de vendas captarem.*
- `id` (PK), `turma_id` (FK), `cliente_id` (FK), `aluno_id` (FK de quem assumiu)
- `status`: `'aberta'`, `'assumida'`, `'convertida'`
- `data_jogo_emissao`, `data_limite_entrega`

### `pedido_vendas` & `pedido_venda_items`
- `id` (PK), `turma_id` (FK), `cliente_id` (FK), `aluno_id` (FK)
- `data_emissao`, `data_entrega_prometida`, `valor_total`
- `status`: `'pendente'`, `'em_analise_pcp'`, `'em_producao'`, `'pronto_embalagem'`, `'aguardando_expedicao'`, `'faturado'`

### `ordens_compra`
- `id` (PK), `turma_id` (FK), `fornecedor_id` (FK), `materia_prima_id` (FK)
- `quantidade`, `valor_unitario`, `valor_total`
- `data_pedido`, `data_entrega_prevista`, `data_entrega_real`
- `status`: `'cotacao'`, `'solicitado'`, `'em_transito'`, `'entregue'`, `'recusado'`
- `motivo_recusa`, `inconformidade_tipo`

### `locais_estoque` & `estoque_movimentacoes` (WMS)
- `id` (PK), `turma_id` (FK), `codigo` (ex: "RUA-A-01-02"), `tipo` (`'materia_prima'`, `'produto_acabado'`)
- Mapeamento de ocupação, quantidade e lote.

### `ordens_producao` (OP)
- `id` (PK), `pedido_venda_id` (FK), `produto_acabado_id` (FK), `aluno_id` (FK)
- `quantidade`, `quantidade_perda` (refugo)
- `status`: `'planejada'`, `'solicitando_material'`, `'em_producao'`, `'concluida'`, `'embalada'`
- `status_material`: `'aguardando_separacao'`, `'separado'`, `'entregue_linha'`
- `tem_refugo_forcado`, `qtd_refugo_forcado`, `motivo_refugo_forcado` (Injeção do Caos)
- `data_inicio_real`, `data_fim`

### `solicitacoes_separacao` (Picking)
- `id` (PK), `ordem_producao_id` (FK), `materia_prima_id` (FK), `quantidade`
- `status`: `'pendente'`, `'em_separacao'`, `'concluida'`

### `apontamentos_producao`
- Registro de horas e quantidades apontadas linha a linha pelos operadores.

### `notas_fiscais`
- `id` (PK), `pedido_venda_id` (FK), `numero_nf`, `chave_acesso`, `valor_total`, `data_emissao_jogo`

---

## 4. Matriz Pedagógica e Avaliações

### `competencias_cursos`, `competencias_itens` e `avaliacoes_alunos`
- Estrutura que armazena os planos de curso (importados via PDF com Google Gemini ou cadastrados manualmente).
- Registra notas técnicas e avaliações socioemocionais (Comunicação, Trabalho em Equipe, Resolução de Problemas, Pontualidade).
