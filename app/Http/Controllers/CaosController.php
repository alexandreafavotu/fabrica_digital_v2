<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turma;
use App\Models\OrdemProducao;
use App\Models\OrdemCompra;
use App\Models\PedidoVenda;
use Carbon\Carbon;

class CaosController extends Controller
{
    // 1. Exibe o Painel do Caos
    public function index($turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        
        // Carrega listas para os incidentes pontuais
        // Carrega OPs para sabotagem (Agora aceita 'Aberta' e 'Em Produção')
        $opsEmAndamento = OrdemProducao::with('produto')
            ->whereHas('pedido', fn($q) => $q->where('turma_id', $turmaId))
            ->whereIn('status', ['Aberta', 'Em Produção']) // <--- AQUI ESTÁ A MUDANÇA
            ->where('em_manutencao', false)
            ->get();

        $ocsPendentes = OrdemCompra::with('materiaPrima')
            ->whereHas('pedido', fn($q) => $q->where('turma_id', $turmaId))
            ->whereIn('status', ['Pendente', 'Aguardando Entrega'])
            ->get();

        $pedidosAtivos = PedidoVenda::with('cliente')
            ->where('turma_id', $turmaId)
            ->whereIn('status', ['Novo', 'Em Produção'])
            ->get();

        return view('professor.caos.index', compact('turma', 'opsEmAndamento', 'ocsPendentes', 'pedidosAtivos'));
    }

    // 2. Aplica Bloqueios Globais (Almoxarifado, Expedição, Sistema)
    public function aplicarBloqueio(Request $request, $turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        $dias = (int) $request->dias;
        $tipo = $request->tipo; // 'almoxarifado', 'expedicao', 'faturamento'

        // Calcula até quando vai o bloqueio (Data Jogo + Dias escolhidos)
        $dataFim = Carbon::parse($turma->data_jogo)->addDays($dias);

        if ($tipo == 'almoxarifado') $turma->bloqueio_almoxarifado_ate = $dataFim;
        if ($tipo == 'expedicao') $turma->bloqueio_expedicao_ate = $dataFim;
        if ($tipo == 'faturamento') $turma->bloqueio_faturamento_ate = $dataFim;

        $turma->save();

        return back()->with('error', "CAOS GERADO! O setor de $tipo ficará parado por $dias dias (até {$dataFim->format('d/m/Y')}).");
    }

    // 3. Aplica Quebra de Máquina (Suporta Múltiplas OPs)
    public function quebrarMaquina(Request $request, $turmaId)
    {
        $request->validate([
            'ordem_producao_ids' => 'required|array|min:1',
            'ordem_producao_ids.*' => 'exists:ordens_producao,id',
            'dias_manutencao' => 'required|integer|min:1', 
        ]);

        $turma = Turma::findOrFail($turmaId);
        $dias = (int) $request->dias_manutencao;
        $dataFim = \Carbon\Carbon::parse($turma->data_jogo)->addDays($dias);
        $ids = $request->ordem_producao_ids;

        OrdemProducao::whereIn('id', $ids)->update([
            'em_manutencao' => true,
            'motivo_manutencao' => "Quebra inesperada. Manutenção corretiva em andamento.",
            'previsao_conserto_ate' => $dataFim
        ]);

        $total = count($ids);
        return back()->with('error', "CAOS GERADO: {$total} máquina(s) parada(s) por {$dias} dias!");
    }

    // 5. Salva Mensagem Geral (Plantão)
    public function salvarMensagem(Request $request, $turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        $turma->mensagem_plantao_caos = $request->mensagem;
        $turma->save();

        return back()->with('success', 'Mensagem enviada para todos os alunos da turma.');
    }
    
    // 6. Limpar Bloqueios (Reset)
    public function limparCaos($turmaId)
    {
        $turma = Turma::findOrFail($turmaId);
        $turma->bloqueio_almoxarifado_ate = null;
        $turma->bloqueio_expedicao_ate = null;
        $turma->bloqueio_faturamento_ate = null;
        $turma->mensagem_plantao_caos = null;
        $turma->save();
        
        // Limpa manutenções
        OrdemProducao::whereHas('pedido', fn($q)=>$q->where('turma_id', $turmaId))
                     ->update(['em_manutencao' => false]);

        return back()->with('success', 'Tudo normalizado! O caos acabou.');
    }

    // 4. Aplica Atraso de Fornecedor (Compras - Suporta Múltiplas OCs)
    public function atrasarFornecedor(Request $request, $turmaId)
    {
        $request->validate([
            'ordem_compra_ids' => 'required|array|min:1',
            'ordem_compra_ids.*' => 'exists:ordens_compra,id',
            'dias' => 'required|integer|min:1',
        ]);

        $turma = Turma::findOrFail($turmaId);
        $dias = (int) $request->dias;
        $ocs = OrdemCompra::whereIn('id', $request->ordem_compra_ids)->get();

        foreach ($ocs as $oc) {
            $baseData = $oc->data_entrega_prevista 
                ? \Carbon\Carbon::parse($oc->data_entrega_prevista) 
                : \Carbon\Carbon::parse($turma->data_jogo);

            $oc->data_entrega_prevista = $baseData->addDays($dias);
            $oc->save();
        }

        $total = $ocs->count();
        return back()->with('error', "CAOS GERADO: Fornecedor atrasou {$total} compra(s) em {$dias} dias!");
    }

    public function sabotarCarga(Request $request, $turmaId)
    {
        $request->validate([
            'ordem_compra_ids' => 'required|array|min:1',
            'ordem_compra_ids.*' => 'exists:ordens_compra,id',
            'descricao_inconformidade' => 'required|string',
        ]);

        $ids = $request->ordem_compra_ids;
        OrdemCompra::whereIn('id', $ids)->update([
            'tem_inconformidade' => true,
            'descricao_inconformidade' => $request->descricao_inconformidade,
        ]);

        $total = count($ids);
        return back()->with('error', "INCONFORMIDADE GERADA: {$total} carga(s) programada(s) com defeito técnico!");
    }

    // 8. Sabotar Produção (Refugo Programado - Suporta Múltiplas OPs)
    public function sabotarProducao(Request $request, $turmaId)
    {
        $request->validate([
            'ordem_producao_ids' => 'required|array|min:1',
            'ordem_producao_ids.*' => 'exists:ordens_producao,id',
            'qtd_refugo_forcado' => 'required|integer|min:1',
            'motivo_refugo_forcado' => 'required|string',
        ]);

        $ids = $request->ordem_producao_ids;
        OrdemProducao::whereIn('id', $ids)->update([
            'tem_refugo_forcado' => true,
            'qtd_refugo_forcado' => $request->qtd_refugo_forcado,
            'motivo_refugo_forcado' => $request->motivo_refugo_forcado,
        ]);

        $total = count($ids);
        return back()->with('error', "FALHA INJETADA: {$total} OP(s) programada(s) para ter {$request->qtd_refugo_forcado} un de refugo!");
    }
}