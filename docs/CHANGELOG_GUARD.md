# 🛡️ CHANGELOG & DIRETRIZES DE SALVAGUARDA (GUARD RULES)

> **Objetivo deste Documento**: Garantir que o sistema permaneça 100% íntegro, funcional e sem quebras silenciosas mesmo após grandes refatorações ou inclusão de novos recursos.

---

## 🛑 As 7 Regras de Ouro para Qualquer Alteração

1. **Nunca quebre o redirecionador de rotas (`/dashboard`)**:
   - Todo usuário passa pelo GPS em `routes/web.php`. A integridade dos setores (`vendas`, `pcp`, `compras`, `almoxarifado`, `producao`, `embalagem`, `expedicao`) deve ser mantida.
2. **Respeite a multi-inquilinato por `turma_id`**:
   - Quase todas as entidades operacionais pertencem a uma turma (`materia_primas`, `produtos_acabados`, `clientes`, `fornecedores`, `pedido_vendas`, `ordens_compra`, `locais_estoque`). Sempre garanta a passagem e filtragem de `turma_id`.
3. **Consistência de Datas Virtuais (`data_jogo`)**:
   - Operações industriais (chegada de pedidos, ordens de compra, apontamentos, prazos de entrega) dependem da data virtual da turma (`turma->data_jogo`), e não da data física do servidor (`now()`), a menos que especificado explicitamente para logs de auditoria.
4. **Proteção por Senha Mestra em Ações Destrutivas**:
   - Todas as rotas de exclusão de turmas, alunos globais, mapas de estoque, matrizes de curso e cadastros-base devem manter o middleware `ProtecaoSenhaMestra`.
5. **Preservação de Helpers e Acessibilidade**:
   - Textos de botões, títulos de tabelas e labels das telas operacionais de alunos devem continuar usando `{{ traduz('...') }}` para manter a acessibilidade cognitiva funcionando.
6. **Integridade da Ficha Técnica (BOM / `estrutura_produto`)**:
   - O cálculo do MRP no PCP e o apontamento de refugo na produção dependem diretamente do relacionamento pivô entre `produtos_acabados` e `materia_primas`. Nunca altere essa relação sem atualizar os dois controladores correspondentes (`AlunoController` e `ProfessorController`).
7. **Documentação Contínua**:
   - Sempre que adicionar uma nova tabela, coluna crítica ou módulo, atualize os arquivos correspondentes na pasta `docs/` (`DATABASE_SCHEMA.md`, `BUSINESS_RULES_FLOWS.md`, etc.).

---

## 📝 Histórico de Marcos e Evoluções Arquiteturais

| Versão / Data Base | Marco / Recurso Implementado | Impacto no Sistema |
| :--- | :--- | :--- |
| **Set/2026** | Criação do Sistema de Memória Contínua (`docs/`) | Centralização da documentação para salvaguarda de refatorações. |
| **Jun/2026** | Sistema de Avaliação por Competências + Importador Gemini IA | Adição de tabelas de competências, upload de PDFs pedagógicos e notas socioemocionais. |
| **Fev/2026** | Painel do Caos & Acessibilidade Avançada | Injeção de bloqueios, quebra de máquinas, atrasos e suporte a neurodivergências/dislexia. |
| **Jan/2026** | Módulo de Embalagem & Refugo Programado | Fluxo completo fechando a esteira: Produção -> Embalagem -> Expedição. |
| **Dez/2025** | Estruturação do Núcleo WMS, MRP e GPS de Alunos | Separação física de estoques, cálculo automático de compras e roteamento por setor. |
