import Vue from "vue";
import Router from "vue-router";
import { rotasAbertas, rotasExclusivoFranqueada } from "../utils/router-config";

// Containers
import Full from "../containers/Full";

// Views
import Login from "../views/auth/Login";
import RecuperarSenha from "../views/auth/RecuperarSenha";
import RedefinirSenha from "../views/auth/RedefinirSenha";
import CriarSenha from "../views/auth/CriarSenha";

import Dashboard from "../views/Dashboard";
import Page404 from "../views/Page404";
import Page403 from "../views/Page403";

// Logs
import ListaLog from "../views/logs/Lista";
import InfoLog from "../views/logs/Info";

// Usuarios
import ListaUsuario from "../views/usuarios/Lista";
import FormularioUsuario from "../views/usuarios/Formulario";
import InfoUsuario from "../views/usuarios/Info";

/* Planejamento de lições */
import ListaPlanejamentoLicao from "../views/planejamento-licao/Lista";
import FormularioPlanejamentoLicao from "../views/planejamento-licao/Formulario";
import InfoPlanejamentoLicao from "../views/planejamento-licao/Info";

/* Módulo */
import ListaModulo from "../views/modulos/Lista";
import FormularioModulo from "../views/modulos/Formulario";
import InfoModulo from "../views/modulos/Info";

/* Forma de pagamento */
import ListaFormaPagamento from "../views/forma-pagamento/Lista";
import InfoFormaPagamento from "../views/forma-pagamento/Info";
import FormularioFormaPagamento from "../views/forma-pagamento/Formulario";

/* Franqueadas */
import ListaFranqueada from "../views/franqueadas/Lista";
import FormularioFranqueada from "../views/franqueadas/Formulario";
import InfoFranqueada from "../views/franqueadas/Info";

/* Pessoas */
import ListaPessoa from "../views/pessoas/Lista";
import FormularioPessoa from "../views/pessoas/Formulario";

/* Alunos */
import ListaAluno from "../views/aluno/Lista";
import FormularioAluno from "../views/aluno/Formulario";

/* Horários */
import ListaHorario from "../views/horario/Lista";
import FormularioHorario from "../views/horario/Formulario";

/* Cursos */
import ListaCurso from "../views/curso/Lista";
import FormularioCurso from "../views/curso/Formulario";

/* Calendário */
import ListaCalendario from "../views/calendario/Lista";
import FormularioCalendario from "../views/calendario/Formulario";

/* Motivos matrícula perdida */
import ListaMotivosMatriculaPerdida from "../views/motivos-matricula-perdida/Lista";
import FormularioMotivosMatriculaPerdida from "../views/motivos-matricula-perdida/Formulario";

/* Tipo movimento estoque */
import ListaTipoMovimentoEstoque from "../views/tipo-movimento-estoque/Lista";
import FormularioTipoMovimentoEstoque from "../views/tipo-movimento-estoque/Formulario";

/* Parâmetros de franqueadora */
import FormularioParametrosFranqueadora from "../views/parametros-franqueadora/Formulario";

/* Classificação aluno */
import ListaClassificacaoAluno from "../views/classificacao-alunos/Lista";
import FormularioClassificacaoAluno from "../views/classificacao-alunos/Formulario";

/* Banco */
import ListaBanco from "../views/banco/Lista";
import FormularioBanco from "../views/banco/Formulario";
import InfoBanco from "../views/banco/Info";

/* Conta */
import ListaConta from "../views/conta/Lista";
import FormularioConta from "../views/conta/Formulario";
import InfoConta from "../views/conta/Info";

/* Item */
import ListaItem from "../views/item/Lista";
import FormularioItem from "../views/item/Formulario";
import InfoItem from "../views/item/Info";

/* Cheques pagar e receber */
import ListaChequesPagarReceber from "../views/cheques-pagar-receber/Lista";
import ChequesPagarReceberFormulario from "../views/cheques-pagar-receber/Formulario";

/* Tipo de movimento conta */
import ListaTipoMovimentoConta from "../views/tipo-movimento-conta/Lista";
import FormularioTipoMovimentoConta from "../views/tipo-movimento-conta/Formulario";

/* Tipo de nota */
import ListaPlanoConta from "../views/plano-conta/Lista";
import FormularioPlanoConta from "../views/plano-conta/Formulario";
import InfoPlanoConta from "../views/plano-conta/Info";

/* Contas a pagar */
import FormularioContasPagar from "../views/conta-pagar/Formulario";
import ListaContasPagar from "../views/conta-pagar/Lista";
import InfoContasPagar from "../views/conta-pagar/Info";

/* Condições de pagamento */
import ListaCondicaoPagamento from "../views/condicao-pagamento/Lista";
import FormularioCondicaoPagamento from "../views/condicao-pagamento/Formulario";
import InfoCondicaoPagamento from "../views/condicao-pagamento/Info";

/* Contas a receber */
import ListaContasReceber from "../views/contas-receber/Lista";

/* Turma */
import ListaTurma from "../views/turma/Lista";
import FormularioTurma from "../views/turma/Formulario";
import VisualizaTurma from "../views/turma/Info";
/* Diario classe */
import DiarioClasse from "../views/turma/DiarioFormulario";

/* Livro */
import ListaLivro from "../views/livro/Lista";
import FormularioLivro from "../views/livro/Formulario";
import VisualizaLivro from "../views/livro/Info";

/* Funcionário */
import ListaFuncionario from "../views/funcionario/Lista";
import FormularioFuncionario from "../views/funcionario/Formulario";

/* Funcionário */
import ListaInformacoesFuncionario from "../views/relatorio-informacoes-funcionario/Lista.vue";

/* Salas da franqueada */
import ListaSalaFranqueada from "../views/sala-franqueada/Lista";

/* Relatorio Saída de estoque */
import ListaRelatorioSaidasDeEstoque from "../views/relatorio-saidas-de-estoque/Lista.vue";

/* Valor Hora */
import ListaValorHora from "../views/valor-hora/Lista";

/* Contrato */
import ListaContrato from "../views/contrato/Lista";
import FormularioContrato from "../views/contrato/Formulario";

/* Cartão */
import ListaCartao from "../views/cartao/Lista";

/*Cadastro Tipo de ocorrência */
import ListaTipoOcorrencia from "../views/cadastro-tipo-ocorrencia/Lista.vue";
import FormularioCadastroTipoOcorrencia from "../views/cadastro-tipo-ocorrencia/Formulario.vue";

/* Lista entrega material */
import ListaEntregaMaterial from "../views/entrega-material/Lista";


/* Movimentação de conta */
import ListaMovimentacaoConta from "../views/movimentacao-conta/Lista";

/* Importador */
import Importador from "../views/importador/Importador";

/* Formulario follow Up */
import FormularioFormularioFollowUp from "../views/formulario-follow-up/Formulario";
import ListaFormularioFollowUp from "../views/formulario-follow-up/Lista";

/* Comentario para geração de rotas automatizadas NÃO APAGAR! */

/* Segmento empresa convenio */
import ListaSegmentoEmpresaConvenio from "../views/segmento-empresa-convenio/Lista";
import FormularioSegmentoEmpresaConvenio from "../views/segmento-empresa-convenio/Formulario";

/* Interessados */
import ListaInteressados from "../views/interessados/Lista";
import FormularioInteressados from "../views/interessados/Formulario";
import FollowupInteressados from "../views/interessados/Followup";

/* Convenio */
import ListaConvenio from "../views/convenio/Lista";
import ListaConvenioNacional from "../views/convenio/ListaConvenioNacional";
import FormularioConvenio from "../views/convenio/Formulario";
import FormularioFollowupConvenio from "../views/convenio/FormularioFollowupConvenio";
import FormularioIniciarConvenio from "../views/convenio/FormularioIniciarConvenio";

/* Motivo nao fechamento convenio */
import ListaMotivoNaoFechamentoConvenio from "../views/motivos-convenio-perdido/Lista";
import FormularioMotivoNaoFechamentoConvenio from "../views/motivos-convenio-perdido/Formulario";

/* Operadora cartao */
import ListaOperadoraCartao from "../views/operadora-cartao/Lista";
import FormularioOperadoraCartao from "../views/operadora-cartao/Formulario";

/* Relatorio cheques */
import ListaRelatorioCheques from "../views/relatorio-cheques/Lista";
// import FormularioRelatorioCheques from '../views/relatorio-cheques/Formulario'

/* Relatorio contas a pagar */
import ListaRelatorioContasPagar from "../views/relatorio-contas-pagar/Lista";

/* Relatorio de Serviço Solicitado */
import ListaRelatorioServicoSolicitado from "../views/relatorio-servico-solicitado/Lista";

/* Relatorio de descontos */
import FormularioRelatorioDescontos from "../views/relatorio-descontos/Lista";

/* Relatorio Fluxo de Caixa */
import FormularioFluxoCaixa from "../views/relatorio-fluxo-caixa/Lista";

/* Modelo template */
import ListaModeloTemplate from "../views/modelo-template/Lista";
import FormularioModeloTemplate from "../views/modelo-template/Formulario";

/* Funil vendas */
import ListaFunilVendas from "../views/funil-vendas/Lista";

/* Prospeccao */
import ListaProspeccao from "../views/prospeccao/Lista";
import FormularioProspeccao from "../views/prospeccao/Formulario";

/* Ocorrencia academica */
import ListaOcorrenciaAcademica from "../views/ocorrencia-academica/Lista";
import FormularioOcorrenciaAcademica from "../views/ocorrencia-academica/Formulario";

/* Mala direta aluno */
import ListaMalaDiretaAluno from "../views/mala-direta-aluno/Lista";

/* Ativiade extra */
import ListaAtiviadeExtra from "../views/atividade-extra/Lista";

/* Nivelamento */
import ListaNivelamento from "../views/nivelamento/Lista";

/* Relatorio funcionario */
import ListaRelatorioFuncionario from "../views/relatorio-funcionario/Lista";

/* Relatorio Titulos vencidos a vencer */
import ListaRelatorioTitulosAVencerVencidos from "../views/relatorio-titulos/Lista";

/* Relatorio Situação alunos */
import ListaRelatorioAlunosSituacao from "../views/relatorio-alunos/Lista";

/* Relatorio Matriculas/Rematrículas - Situação contratos */
import ListaRelatorioContratosSituacao from '../views/relatorio-contrato/Lista'


/* Relatórios de matrícula, rematrículas e rescisões */
import ListaRelatorioMatricula from "../views/relatorio-matriculas/Lista";

/* Relatórios de matrículas a renovar */
import ListaRelatorioMatriculaRenovar from "../views/relatorio-matricula-renovar/Lista";

/* Relatório de Interessados */
import ListaRelatorioInteressados from "../views/relatorio-interessados/Lista";

/* Relatório Impressão Class Record */
import ListaRelatorioClassRecord from "../views/relatorio-impressao-class-record/Lista";

/*Relatorio Aniversariante */
import ListaRelatorioAniversariante from "../views/relatorio-aniversariante/Lista";

/* Relatório de Estoque */
import ListaRelatorioEstoque from "../views/relatorio-estoque/Lista";

/* Relatório de Alunos por Turma */
import ListaRelatorioAlunosPorTurma from "../views/relatorio-alunos-por-turma/Lista.vue";

/* Relatório Atividade Extra */
import ListaRelatorioAtividadeExtra from "../views/relatorio-atividade-extra/Lista";

/*Relatorio Consulta Desistencia */
import ListaRelatorioConsultaDesistencia from "../views/relatorio-consulta-desistencia/Lista";

/* Relatório matrícula perdida */
import ListaRelatorioMatriculaPerdida from "../views/relatorio-matricula-perdida/Lista";

/* Relatório Aula Desmarcada */
import ListaRelatorioAulaDesmarcada from "../views/relatorio-aula-desmarcada/Lista";

/* Relatório de Prospecção */
import ListaRelatorioProspeccao from "../views/relatorio-prospeccao/Lista";

/* Relatorio Notas */
import ListaRelatorioNotas from "../views/relatorio-nota/Lista";

/* Relatorio Notas por turma */
import ListaRelatorioNotaTurma from "../views/relatorio-notas-turmas/Lista";

/* Relatorio Retencao de Alunos */
import ListaRelatorioRetencaoAlunos from "../views/relatorio-retencao-alunos/Lista";

/* Relatorio Frequencias */
import ListaRelatorioFrequencias from "../views/relatorio-frequencia/Lista";

/* Relatorio balancete financeiro */
import ListaRelatorioBalanceteFinanceiro from "../views/relatorio-balancete-financeiro/Lista";

/* Relatorio aulas ocorridas */
import ListaRelatorioAulasOcorridas from "../views/relatorio-aulas-ocorridas/Lista";

/* Relatorio saldo de horas Vip/Personal */
import ListaRelatorioSaldoHorasVipPersonal from "../views/relatorio-saldo-horas/Lista";

/*Relatorio mapa sala turma */
import ListaRelatorioMapaSalaTurma from "../views/relatorio-mapa-sala-turma/Lista";

/* Relatorio retorno consultor */
import ListaRelatorioRetornoConsultor from "../views/relatorio-retorno-consultor/Lista";

/* Relatorio ocorrências*/
import ListaRelatorioOcorrencia from "../views/relatorio-ocorrencia/Lista";

/* Relatorio Itens de estoque */
import ListaRelatorioItensDeEstoque from "../views/relatorio-itens-de-estoque/Lista";

/* Relatorio visitas */
import ListaRelatorioVisitas from "../views/relatorio-visitas/Lista";

/* Relatorio Contatos */
import ListaRelatorioContatos from "../views/relatorio-contatos/Lista";

/* Relatório de consulta de conversão */
import ListaRelatorioConsultaConversao from "../views/relatorio-consulta-conversao/Lista";

/* Servico */
// import ListaServico from '../views/servico/Lista'
// import FormularioServico from '../views/servico/Formulario'

/* Reposicao aula avaliacao */
import ListaReposicaoAulaAvaliacao from "../views/reposicao-aula-avaliacao/Lista";
import FormularioReposicaoAulaAvaliacao from "../views/reposicao-aula-avaliacao/Formulario";

/* Bonus class */
import ListaBonusClass from "../views/bonus-class/Lista";
import FormularioBonusClass from "../views/bonus-class/Formulario";

/* Relatorio inadimplencia */
import ListaRelatorioInadimplencia from "../views/relatorio-inadimplencia/Lista";

/*Relatorio valores por turma*/
import ListaRelatorioValoresTurma from "../views/relatorio-valores-turma/Lista";

/*Relatório Follow Up */
import ListaRelatorioFollowUp from "../views/relatorio-follow-up/Lista";

/* Relatorio Turma Existente */
import ListaRelatorioTurmaExistente from "../views/relatorio-turma-existente/Lista";

/*Relatorio Disponibilidade de Instrutor */
import ListaRelatorioDisponibilidadeInstrutor from "../views/relatorio-disponibilidade-instrutor/Lista";

/*Relatório de dados do aluno */
import ListaRelatorioDadosAluno from "../views/relatorio-dados-aluno/Lista";


/* Relatorio de Alunos no compromisso de aprendizado */
import ListaRelatorioCompromissoAprendizado from "../views/relatorio-compromisso-aprendizado/Lista";

/* Relatorio Pedido Material Didatico */
import ListaPedidoMaterialDidatico from "../views/relatorio-pedido-material-didatico/Lista";

/*Relatorio Negociacao Convenio */
import ListaRelatorioNegociacaoConvenio from "../views/relatorio-negociacao-convenio/Lista";

/*Relatorio Dados de Cadastro */
import ListaRelatorioDadosCadastro from "../views/relatorio-dados-cadastro/Lista";
import ListarDetalhesDadosCadastro from "../views/relatorio-dados-cadastro/ListarDetalhesDadosCadastro";

/* Relatorio Matricula Venda */
import ListaRelatorioMatriculaVenda from "../views/relatorio-matricula-venda/Lista";

/* Follow-up */
import ListaFollowUp from "../views/follow-up/Lista";
import FormularioFollowUp from "../views/follow-up/Formulario";

/* Agenda compromisso */
import ListaAgendaCompromisso from "../views/agenda-compromisso/Lista";
import FormularioAgendaCompromisso from "../views/agenda-compromisso/Formulario";

/* Personal */
import ListaDiarioPersonal from "../views/personal/Lista";
import FormularioDiarioPersonal from "../views/personal/Formulario";

/* Personal Agenda*/

import AgendaPersonal from "../views/pedagogico-personal-agenda/Agenda";

/* Reagendamento Personal */
import ListaReagendamentoPersonal from "../views/reagendamento-personal/Lista";
// import FormularioReagendamentoPersonal from '../views/reagendamento-personal/Formulario'

/* Tipo visibilidade */
import ListaTipoVisibilidade from "../views/tipo-visibilidade/Lista";
import FormularioTipoVisibilidade from "../views/tipo-visibilidade/Formulario";

/* Cadastro servico */
import ListaCadastroServico from "../views/cadastro-servico/Lista";
import FormularioCadastroServico from "../views/cadastro-servico/Formulario";

/* Meta franqueada */
import ListaMetaFranqueada from "../views/meta-franqueada/Lista";

/* Indicadores */
import ListaIndicadores from "../views/indicadores/Lista";

/*Controle Material Didátivo */
import ListaControleMaterialDidatico from "../views/relatorio-controle-material-didatico/Lista";

/* ImportacaoBoleto */
import ListaImportacaoBoleto from "../views/importacao-boleto/Lista";
import ListaImportacaoBoletoRetorno from "../views/importacao-boleto/ListaRetorno";

/* Midia */
import ListaMidia from "../views/midia/Lista";
import FormularioMidia from "../views/midia/Formulario";

// componente generico
/* Papel */
import ListaPapel from "../views/papel/Lista";
import FormularioPapel from "../views/papel/Formulario";

import FormularioParametrosFinanceiros from "../views/parametros-financeiros/Formulario";

import ListView from "../views/ListView";
import FormView from "../views/FormView";

import CustomComponents from "../views/custom";

import store from "../store";

import RelatoriosComponent from "../views/relatorios/RelatoriosComponent.vue";
import AgendamentoPersonal from "../views/agendamento-personal/Lista.vue";

Vue.use(Router);

// Handle the navigation guard
function routeChanged(to, from, next) {
  store.commit("root/setRotaAtual", to);
  store.commit("modulos/SET_PERMISSAO", []);

  if(rotasExclusivoFranqueada.includes(to.path)) {
    store.dispatch("root/getState").then(data => {
      if("usuarioLogado" in data) {
        if(data.usuarioLogado.franqueadaSelecionada != 1) {
          router.replace("/dashboard ")
        }
      }
    })
  }

  if (!rotasAbertas.includes(to.path)) {
    store.dispatch("root/logAcesso");
    store.dispatch("helpHint/listar");

    store
      .dispatch("modulos/buscarPermissaoPorModulo")
      .then((modulo) => {
        store.commit("root/SET_PERMISSAO_MODULO_ID", modulo.id);

        // Excessão para atualização de franqueada
        // const moduloFranqueada = to.path.match(/franqueada\/atualizar/) !== null && !store.state.modulos.permissoes['EDITAR']
        // if (!store.state.modulos.permissoes['ACESSAR'] && moduloFranqueada === true) {
        //   return router.replace('/dashboard')
        // }

        // if (to.path.match(/adicionar/) !== null && !store.state.modulos.permissoes['CRIAR']) {
        //   return router.replace('/403')
        // }

        // if (to.path.match(/atualizar/) !== null && !store.state.modulos.permissoes['EDITAR']) {
        //   return router.replace('/403')
        // }

        // if (to.path.match(/remover/) !== null && !store.state.modulos.permissoes['EXCLUIR']) {
        //   return router.replace('/403')
        // }
      })
      .catch(console.error);
  }

  return next();
}

const router = new Router({
  mode: "history",
  linkActiveClass: "open active",
  scrollBehavior: () => ({ y: 0 }),
  routes: [
    {
      path: "/m/Renegociacao",
      component: Full,
      meta: { label: "inFlux Manager", disabled: true },
      children: [
        {
          path: "adicionar",
          meta: { label: "Adicionar" },
          component: CustomComponents.RenegociacaoFormView,
          props: () => ({ entity: "Renegociacao" }),
        },
      ],
    },

    {
      path: "/m/:entity",
      component: Full,
      meta: { label: "inFlux Manager" },
      children: [
        {
          path: "",
          component: ListView,
          meta: { label: "Listar" },
        },
        {
          path: "adicionar",
          component: FormView,
          meta: { label: "Adicionar" },
        },
        {
          path: "atualizar/:id",
          component: FormView,
          meta: { label: "Atualizar" },
        },
      ],
    },

    {
      path: "/",
      redirect: "/login",
      component: Full,
      name: "Home",
      meta: { label: "Home" },
      beforeEnter: routeChanged,
      children: [
        {
          path: "/dashboard",
          name: "Dashboard",
          meta: { label: "Dashboard" },
          component: Dashboard,
          beforeEnter: routeChanged,
        },
      ],
    },

    {
      path: "/configuracoes/modulo",
      name: "Módulos",
      component: Full,
      redirect: "/configuracoes/modulo",
      children: [
        {
          path: "",
          component: ListaModulo,
          beforeEnter: routeChanged,
          name: "lista-modulo",
          meta: { label: "Lista" },
        },
        {
          path: "adicionar",
          name: "adicionar-modulo",
          meta: { label: "Adicionar" },
          component: FormularioModulo,
          beforeEnter: routeChanged,
        },
        {
          path: "atualizar/:id",
          name: "atualizar-modulo",
          meta: { label: "Atualizar" },
          component: FormularioModulo,
          beforeEnter: routeChanged,
        },
        {
          path: "info/:id",
          name: "info-modulo",
          meta: { label: "Informações" },
          component: InfoModulo,
          beforeEnter: routeChanged,
        },
      ],
    },
    {
      path: "/configuracoes/usuario",
      name: "Usuários",
      component: Full,
      redirect: "/configuracoes/usuario",
      children: [
        {
          path: "",
          component: ListaUsuario,
          beforeEnter: routeChanged,
          name: "lista-usuario",
          meta: { label: "Lista" },
        },
        {
          path: "atualizar/:id",
          name: "atualizar-usuario",
          meta: { label: "Atualizar" },
          component: FormularioUsuario,
          beforeEnter: routeChanged,
        },
        {
          path: "info/:id",
          name: "info-usuario",
          meta: { label: "Informações" },
          component: InfoUsuario,
          beforeEnter: routeChanged,
        },
        {
          path: "adicionar",
          name: "adicionar-usuario",
          meta: { label: "Adicionar" },
          component: FormularioUsuario,
          beforeEnter: routeChanged,
        },
      ],
    },
    {
      path: "/configuracoes/papel",
      name: "Permissões",
      component: Full,
      redirect: "/configuracoes/papel",
      children: [
        {
          path: "",
          component: ListaPapel,
          beforeEnter: routeChanged,
          name: "lista-papel",
          meta: { label: "Lista" },
        },
        {
          path: "atualizar/:id",
          name: "atualizar-papel",
          meta: { label: "Atualizar" },
          component: FormularioPapel,
          beforeEnter: routeChanged,
        },
        {
          path: "adicionar",
          name: "adicionar-papel",
          meta: { label: "Adicionar" },
          component: FormularioPapel,
          beforeEnter: routeChanged,
        },
      ],
    },
    {
      path: "/configuracoes/planejamento-licao",
      name: "Programação de lições",
      component: Full,
      redirect: "/configuracoes/planejamento-licao",
      children: [
        {
          path: "",
          component: ListaPlanejamentoLicao,
          beforeEnter: routeChanged,
          name: "lista-planejamento-licao",
          meta: { label: "Lista" },
        },
        {
          path: "atualizar/:id",
          name: "atualizar-planejamento-licao",
          meta: { label: "Atualizar" },
          component: FormularioPlanejamentoLicao,
          beforeEnter: routeChanged,
        },
        {
          path: "info/:id",
          name: "informacoes-planejamento-licao",
          meta: { label: "Informações" },
          component: InfoPlanejamentoLicao,
          beforeEnter: routeChanged,
        },
        {
          path: "adicionar",
          name: "adicionar-planejamento-licao",
          meta: { label: "Adicionar" },
          component: FormularioPlanejamentoLicao,
          beforeEnter: routeChanged,
        },
      ],
    },
    {
      path: "/configuracoes/forma-pagamento",
      name: "Forma de pagamento",
      component: Full,
      redirect: "/configuracoes/forma-pagamento",
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaFormaPagamento,
          name: "lista-forma-pagamento",
          meta: { label: "Lista" },
        },
        {
          path: "adicionar",
          name: "adicionar-forma-pagamento",
          meta: { label: "Adicionar" },
          component: FormularioFormaPagamento,
          beforeEnter: routeChanged,
        },
        {
          path: "info/:id",
          name: "info-forma-pagamento",
          meta: { label: "Informações" },
          component: InfoFormaPagamento,
          beforeEnter: routeChanged,
        },
        {
          path: "atualizar/:id",
          name: "atualizar-forma-pagamento",
          meta: { label: "Atualizar" },
          component: FormularioFormaPagamento,
          beforeEnter: routeChanged,
        },
      ],
    },
    {
      path: "/franqueadora/franqueada",
      name: "Franquias",
      component: Full,
      redirect: "/configuracoes/franqueada",
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaFranqueada,
          name: "lista-franqueada",
          meta: { label: "Lista" },
        },
        {
          path: "adicionar",
          name: "adicionar-franqueada",
          meta: { label: "Adicionar" },
          component: FormularioFranqueada,
          beforeEnter: routeChanged,
        },
        {
          path: "atualizar/:id",
          name: "atualizar-franqueada",
          meta: { label: "Atualizar" },
          component: FormularioFranqueada,
          beforeEnter: routeChanged,
        },
        {
          path: "info/:id",
          name: "info-franqueada",
          meta: { label: "Informações" },
          component: InfoFranqueada,
          beforeEnter: routeChanged,
        },
      ],
    },

    {
      path: "/cadastros/entrega-material",
      name: "Entrega de itens",
      component: Full,
      redirect: "/cadastros/entrega-material",
      children: [
        {
          path: "",
          component: ListaEntregaMaterial,
          beforeEnter: routeChanged,
          name: "lista-entrega-material",
          meta: { label: "Lista" },
        },
      
      ],
    },

    {
      path: "/cadastros/pessoa",
      meta: { label: "Responsáveis e Fornecedores" },
      component: Full,
      redirect: "/cadastros/pessoa",
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaPessoa,
          name: "lista-pessoa",
          meta: { label: "Lista" },
        },
        {
          path: "adicionar",
          name: "adicionar-pessoa",
          meta: { label: "Adicionar" },
          component: FormularioPessoa,
          beforeEnter: routeChanged,
        },
        {
          path: "atualizar/:id",
          name: "atualizar-pessoa",
          meta: { label: "Atualizar" },
          component: FormularioPessoa,
          beforeEnter: routeChanged,
        },
      ],
    },
    {
      path: "/cadastros/formulario-follow-up",
      meta: { label: "Formulario follow-up" },
      component: Full,
      redirect: "/cadastros/formulario-follow-up",
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaFormularioFollowUp,
          name: "lista-formulario-follow-up",
          meta: { label: "Lista" },
        },
        {
          path: "adicionar",
          name: "adicionar-formulario-follow-up",
          meta: { label: "Adicionar" },
          component: FormularioFormularioFollowUp,
          beforeEnter: routeChanged,
        },
        {
          path: "atualizar/:id",
          name: "atualizar-formulario-follow-up",
          meta: { label: "Atualizar" },
          component: FormularioFormularioFollowUp,
          beforeEnter: routeChanged,
        },
      ],
    },

    {
      path: "/cadastros/funcionario",
      meta: { label: "Colaboradores" },
      component: Full,
      redirect: "/cadastros/funcionario",
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaFuncionario,
          name: "lista-funcionario",
          meta: { label: "Lista" },
        },
        {
          path: "adicionar",
          name: "adicionar-funcionario",
          meta: { label: "Adicionar" },
          component: FormularioFuncionario,
          beforeEnter: routeChanged,
        },
        {
          path: "atualizar/:id",
          name: "atualizar-funcionario",
          meta: { label: "Atualizar" },
          component: FormularioFuncionario,
          beforeEnter: routeChanged,
        },
      ],
    },

    {
      path: "/configuracoes/classificacao-aluno",
      name: "Classificação de aluno",
      component: Full,
      redirect: "/configuracoes/classificacao-aluno",
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaClassificacaoAluno,
          name: "lista-classificacao-aluno",
          meta: { label: "Lista" },
        },
        {
          path: "adicionar",
          name: "adicionar-classificacao-aluno",
          meta: { label: "Adicionar" },
          component: FormularioClassificacaoAluno,
          beforeEnter: routeChanged,
        },
        {
          path: "atualizar/:id",
          name: "atualizar-classificacao-aluno",
          meta: { label: "Atualizar" },
          component: FormularioClassificacaoAluno,
          beforeEnter: routeChanged,
        },
      ],
    },
    {
      path: "/configuracoes/tipo-movimento-estoque",
      name: "Tipo de movimento em estoque",
      component: Full,
      redirect: "/configuracoes/tipo-movimento-estoque",
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaTipoMovimentoEstoque,
          name: "lista-tipo-movimento-estoque",
          meta: { label: "Lista" },
        },
        {
          path: "adicionar",
          name: "adicionar-tipo-movimento-estoque",
          meta: { label: "Adicionar" },
          component: FormularioTipoMovimentoEstoque,
          beforeEnter: routeChanged,
        },
        {
          path: "atualizar/:id",
          name: "atualizar-tipo-movimento-estoque",
          meta: { label: "Atualizar" },
          component: FormularioTipoMovimentoEstoque,
          beforeEnter: routeChanged,
        },
      ],
    },

    // {
    //   path: "/financeiro/parametros-financeiros",
    //   meta: { label: "Parâmetros financeiros" },
    //   component: Full,
    //   beforeEnter: routeChanged,
    //   children: [
    //     {
    //       path: "",
    //       name: "parametros-financeiros",
    //       meta: { label: "Atualizar" },
    //       component: FormularioParametrosFinanceiros,
    //       beforeEnter: routeChanged,
    //     },
    //   ],
    // },

    {
      path: "/franqueadora",
      name: "Franqueadora",
      meta: { label: "Franqueadora" },
      redirect: "/franqueadora/parametros-franqueadora",
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "parametros-franqueadora",
          name: "parametros-franqueadora",
          meta: { label: "Atualizar" },
          component: FormularioParametrosFranqueadora,
          beforeEnter: routeChanged,
        },

        {
          path: "meta-franqueada",
          component: ListaMetaFranqueada,
          name: "meta-franqueada-franqueadora",
          meta: { label: "Definição de metas" },
        },
      ],
    },

    {
      path: "/configuracoes/banco",
      name: "Banco",
      component: Full,
      redirect: "/configuracoes/banco",
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaBanco,
          name: "lista-banco",
          meta: { label: "Lista" },
        },
        {
          path: "adicionar",
          name: "adicionar-banco",
          meta: { label: "Adicionar" },
          component: FormularioBanco,
          beforeEnter: routeChanged,
        },
        {
          path: "atualizar/:id",
          name: "atualizar-banco",
          meta: { label: "Atualizar" },
          component: FormularioBanco,
          beforeEnter: routeChanged,
        },
        {
          path: "info/:id",
          name: "info-banco",
          meta: { label: "Informações" },
          component: InfoBanco,
          beforeEnter: routeChanged,
        },
      ],
    },
    {
      path: "/configuracoes/conta",
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaConta,
          name: "lista-conta",
          meta: { label: "Lista" },
        },
        {
          path: "adicionar",
          name: "adicionar-conta",
          meta: { label: "Adicionar" },
          component: FormularioConta,
          beforeEnter: routeChanged,
        },
        {
          path: "atualizar/:id",
          name: "atualizar-conta",
          meta: { label: "Atualizar" },
          component: FormularioConta,
          beforeEnter: routeChanged,
        },
        {
          path: "info/:id",
          name: "info-conta",
          meta: { label: "Informações" },
          component: InfoConta,
          beforeEnter: routeChanged,
        },
      ],
    },

    {
      path: "/configuracoes/item",
      name: "Item",
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaItem,
          name: "lista-item",
          meta: { label: "Lista" },
        },
        {
          path: "adicionar",
          name: "adicionar-item",
          meta: { label: "Adicionar" },
          component: FormularioItem,
          beforeEnter: routeChanged,
        },
        {
          path: "atualizar/:id",
          name: "atualizar-item",
          meta: { label: "Atualizar" },
          component: FormularioItem,
          beforeEnter: routeChanged,
        },
        {
          path: "info/:id",
          name: "info-item",
          meta: { label: "Informações" },
          component: InfoItem,
          beforeEnter: routeChanged,
        },
      ],
    },
    {
      path: "/configuracoes/log",
      name: "Logs",
      component: Full,
      redirect: "configuracoes/log",
      children: [
        {
          path: "",
          component: ListaLog,
          beforeEnter: routeChanged,
          name: "lista-log",
          meta: { label: "Lista" },
        },
        {
          path: "info/:id",
          component: InfoLog,
          beforeEnter: routeChanged,
          name: "info-log",
          meta: { label: "Informações" },
        },
      ],
    },

    {
      path: "/academico/aluno",
      meta: { label: "Alunos" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaAluno,
          meta: { label: "Lista" },
          name: "lista-aluno",
        },
        {
          path: "atualizar/:id",
          component: FormularioAluno,
          meta: { label: "Atualizar" },
          name: "atualizar-aluno",
        },
        {
          path: "adicionar",
          component: FormularioAluno,
          meta: { label: "Adicionar" },
          name: "adicionar-aluno",
        },
      ],
    },
    {
      path: "/configuracoes/horario",
      meta: { label: "Horário das turmas" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaHorario,
          meta: { label: "Lista" },
          name: "lista-horario",
        },
        {
          path: "atualizar/:id",
          component: FormularioHorario,
          meta: { label: "Atualizar" },
          name: "atualizar-horario",
        },
        {
          path: "adicionar",
          component: FormularioHorario,
          meta: { label: "Adicionar" },
          name: "adicionar-horario",
        },
      ],
    },
    {
      path: "/cadastros/curso",
      meta: { label: "Cursos" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaCurso,
          meta: { label: "Lista" },
          name: "curso-lista",
        },
        {
          path: "atualizar/:id",
          component: FormularioCurso,
          meta: { label: "Atualizar" },
          name: "curso-atualizar",
        },
        {
          path: "adicionar",
          component: FormularioCurso,
          meta: { label: "Adicionar" },
          name: "curso-adicionar",
        },
      ],
    },
    {
      path: "/configuracoes/calendario",
      meta: { label: "Calendários" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaCalendario,
          meta: { label: "Lista" },
          name: "calendario-lista",
        },
        {
          path: "atualizar/:id",
          component: FormularioCalendario,
          meta: { label: "Atualizar" },
          name: "calendario-atualizar",
        },
        {
          path: "adicionar",
          component: FormularioCalendario,
          meta: { label: "Adicionar" },
          name: "calendario-adicionar",
        },
      ],
    },
    {
      path: "/configuracoes/motivos-matricula-perdida",
      meta: { label: "Motivos de matrícula perdida" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaMotivosMatriculaPerdida,
          meta: { label: "Lista" },
          name: "motivos-matricula-perdida-lista",
        },
        {
          path: "atualizar/:id",
          component: FormularioMotivosMatriculaPerdida,
          meta: { label: "Atualizar" },
          name: "motivos-matricula-perdida-atualizar",
        },
        {
          path: "adicionar",
          component: FormularioMotivosMatriculaPerdida,
          meta: { label: "Adicionar" },
          name: "motivos-matricula-perdida-adicionar",
        },
      ],
    },
    {
      path: "/configuracoes/tipo-movimento-conta",
      meta: { label: "Tipo de movimento em conta" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaTipoMovimentoConta,
          meta: { label: "Lista" },
          name: "lista-tipo-movimento-conta",
        },
        {
          path: "atualizar/:id",
          component: FormularioTipoMovimentoConta,
          meta: { label: "Atualizar" },
          name: "atualizar-tipo-movimento-conta",
        },
        {
          path: "adicionar",
          component: FormularioTipoMovimentoConta,
          meta: { label: "Adicionar" },
          name: "adicionar-tipo-movimento-conta",
        },
      ],
    },

    {
      path: "/configuracoes/plano-conta",
      meta: { label: "Planos de conta" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaPlanoConta,
          meta: { label: "Lista" },
          name: "lista-plano-conta",
        },
        {
          path: "info/:id",
          component: InfoPlanoConta,
          meta: { label: "Informações" },
          name: "info-plano-conta",
        },
        {
          path: "atualizar/:id",
          component: FormularioPlanoConta,
          meta: { label: "Atualizar" },
          name: "atualizar-plano-conta",
        },
        {
          path: "adicionar",
          component: FormularioPlanoConta,
          meta: { label: "Adicionar" },
          name: "adicionar-plano-conta",
        },
      ],
    },

    {
      path: "/configuracoes/condicao-pagamento",
      meta: { label: "Condições de pagamento" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaCondicaoPagamento,
          meta: { label: "Lista" },
          name: "lista-condicao-pagamento",
        },
        {
          path: "info/:id",
          component: InfoCondicaoPagamento,
          meta: { label: "Informações" },
          name: "info-condicao-pagamento",
        },
        {
          path: "atualizar/:id",
          component: FormularioCondicaoPagamento,
          meta: { label: "Atualizar" },
          name: "atualizar-condicao-pagamento",
        },
        {
          path: "adicionar",
          component: FormularioCondicaoPagamento,
          meta: { label: "Adicionar" },
          name: "adicionar-condicao-pagamento",
        },
      ],
    },

    {
      path: "/financeiro/cartao",
      meta: { label: "Controle de cartões" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaCartao,
          name: "lista-cartao",
          meta: { label: "Lista" },
        },
      ],
    },

    {
      path: "/financeiro/cheques-pagar-receber",
      meta: { label: "Controle de cheques" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaChequesPagarReceber,
          name: "lista-cheques-pagar-receber",
          meta: { label: "Lista" },
        },
        {
          path: "atualizar/:id",
          component: ChequesPagarReceberFormulario,
          meta: { label: "Atualizar" },
          name: "atualizar-cheques-pagar-receber",
        },
      ],
    },

    {
      path: "/financeiro/contas-pagar",
      meta: { label: "Contas a pagar" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaContasPagar,
          meta: { label: "Lista" },
          name: "lista-contas-pagar",
        },
        {
          path: "info/:id",
          component: InfoContasPagar,
          meta: { label: "Informações" },
          name: "info-contas-pagar",
        },
        {
          path: "atualizar/:id",
          component: FormularioContasPagar,
          meta: { label: "Atualizar" },
          name: "atualizar-contas-pagar",
        },
        {
          path: "lancar",
          component: FormularioContasPagar,
          meta: { label: "Lançar" },
          name: "adicionar-contas-pagar",
        },
      ],
    },

    {
      path: "/financeiro/contas-receber",
      meta: { label: "Contas a receber" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaContasReceber,
          meta: { label: "Lista" },
          name: "lista-contas-receber",
        },
      ],
    },

    {
      path: "/financeiro/extrato",
      name: "Extrato",
      component: Full,
      redirect: "/financeiro/extrato",
      children: [
        {
          path: "",
          component: ListaMovimentacaoConta,
          beforeEnter: routeChanged,
          name: "lista-movimentacao-conta",
          meta: { label: "Lista" },
        },
      ],
    },

    {
      path: "/academico/turma",
      meta: { label: "Turma" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaTurma,
          meta: { label: "Lista" },
          name: "turma-lista",
        },
        {
          path: "visualizar/:id",
          component: VisualizaTurma,
          meta: { label: "Informações" },
          name: "turma-ver-turma",
        },
        {
          path: "atualizar/:id",
          component: FormularioTurma,
          meta: { label: "Atualizar" },
          name: "turma-atualizar",
        },
        {
          path: "reabertura/:id",
          component: FormularioTurma,
          meta: { label: "Reabertura" },
          name: "turma-reabertura",
        },
        {
          path: "adicionar",
          component: FormularioTurma,
          meta: { label: "Adicionar" },
          name: "turma-adicionar",
        },
        {
          path: "diario-classe/:id",
          component: DiarioClasse,
          meta: { label: "Diário de classe" },
          name: "diario-classe",
        },
      ],
    },
    {
      path: "/configuracoes/livro",
      meta: { label: "Livro" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaLivro,
          meta: { label: "Lista" },
          name: "livro-lista",
        },
        {
          path: "visualizar/:id",
          component: VisualizaLivro,
          meta: { label: "Informações" },
          name: "livro-ver",
        },
        {
          path: "atualizar/:id",
          component: FormularioLivro,
          meta: { label: "Atualizar" },
          name: "livro-atualizar",
        },
        {
          path: "adicionar",
          component: FormularioLivro,
          meta: { label: "Adicionar" },
          name: "livro-adicionar",
        },
      ],
    },

    {
      path: "/configuracoes/sala-franqueada",
      meta: { label: "Salas" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaSalaFranqueada,
          meta: { label: "Lista" },
          name: "lista-sala-franqueada",
        },
      ],
    },

    {
      path: "/configuracoes/valor-hora",
      meta: { label: "Valores por hora" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaValorHora,
          meta: { label: "Lista" },
          name: "lista-valor-hora",
        },
      ],
    },

    {
      path: "/cadastros/contrato",
      meta: { label: "Contratos" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaContrato,
          meta: { label: "Lista" },
          name: "lista-contrato",
        },

        {
          path: "atualizar/:id",
          component: FormularioContrato,
          meta: { label: "Atualizar" },
          name: "atualizar-contrato",
        },

        {
          path: "adicionar",
          component: FormularioContrato,
          meta: { label: "Adicionar" },
          name: "adicionar-contrato",
        },
      ],
    },
    {
      path: "/cadastros/tipo-ocorrencia",
      meta: { label: "Tipo Ocorrência" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaTipoOcorrencia,
          meta: { label: "Lista" },
          name: "lista-tipo-ocorrencia",
        },

        {
          path: "atualizar/:id",
          component: FormularioCadastroTipoOcorrencia,
          meta: { label: "Atualizar" },
          name: "atualizar-tipo-ocorrencia",
        },

        {
          path: "adicionar",
          component: FormularioCadastroTipoOcorrencia,
          meta: { label: "Adicionar" },
          name: "adicionar-tipo-ocorrencia",
        },
      ],
    },

    {
      path: "/configuracoes/importador",
      meta: { label: "Importador" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: Importador,
          name: "importador",
          meta: { label: "Importar dados" },
        },
      ],
    },
    {
      path: "/redefinir-senha",
      name: "Redefinir senha",
      beforeEnter: routeChanged,
      component: RedefinirSenha,
    },

    {
      path: "/criar-senha",
      name: "Criar senha",
      beforeEnter: routeChanged,
      component: CriarSenha,
    },

    {
      path: "/recuperar-senha",
      name: "Recuperar senha",
      beforeEnter: routeChanged,
      component: RecuperarSenha,
    },

    {
      path: "/login",
      name: "Login",
      beforeEnter: routeChanged,
      component: Login,
    },

    {
      path: "*",
      name: "Page404",
      beforeEnter: routeChanged,
      component: Page404,
    },

    {
      path: "/403",
      name: "Page403",
      beforeEnter: routeChanged,
      component: Page403,
    },

    {
      path: "/cadastros/segmento-empresa-convenio",
      meta: { label: "Segmento empresa convênio" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaSegmentoEmpresaConvenio,
          meta: { label: "Lista" },
          name: "segmentoempresaconvenio-lista",
        },
        {
          path: "atualizar/:id",
          component: FormularioSegmentoEmpresaConvenio,
          meta: { label: "Atualizar" },
          name: "segmentoempresaconvenio-atualizar",
        },
        {
          path: "adicionar",
          component: FormularioSegmentoEmpresaConvenio,
          meta: { label: "Adicionar" },
          name: "segmentoempresaconvenio-adicionar",
        },
      ],
    },
    {
      path: "/cadastros/interessados",
      meta: { label: "Interessados" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaInteressados,
          meta: { label: "Lista" },
          name: "interessados-lista",
        },
        {
          path: "atualizar/:id",
          component: FormularioInteressados,
          meta: { label: "Atualizar" },
          name: "interessados-atualizar",
        },
        {
          path: "followup/:id",
          component: FollowupInteressados,
          meta: { label: "Followup" },
          name: "interessados-followup",
        },
        {
          path: "adicionar",
          component: FormularioInteressados,
          meta: { label: "Adicionar" },
          name: "interessados-adicionar",
        },
      ],
    },

    {
      path: "/cadastros/lista-convenio-nacional",
      meta: { label: "Lista de convênios" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaConvenioNacional,
          meta: { label: "Lista" },
          name: "convenio-lista-nacional",
        },
      ],
    },

    {
      path: "/cadastros/convenio",
      meta: { label: "Negociação de parcerias" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaConvenio,
          meta: { label: "Lista" },
          name: "convenio-lista",
        },
        {
          path: "atualizar/:id",
          component: FormularioConvenio,
          meta: { label: "Atualizar" },
          name: "convenio-atualizar",
        },
        {
          path: "adicionar",
          component: FormularioConvenio,
          meta: { label: "Adicionar" },
          name: "convenio-adicionar",
        },
        {
          path: "followup/:id",
          component: FormularioFollowupConvenio,
          meta: { label: "Followup" },
          name: "convenio-followup",
        },
        {
          path: "iniciar/:id",
          component: FormularioIniciarConvenio,
          meta: { label: "Iniciar convênio" },
          name: "convenio-iniciar",
        },
      ],
    },

    {
      path: "/configuracoes/motivos-convenio-perdido",
      meta: { label: "Motivos de convênio perdido" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaMotivoNaoFechamentoConvenio,
          meta: { label: "Lista" },
          name: "motivos-convenio-perdido-lista",
        },
        {
          path: "atualizar/:id",
          component: FormularioMotivoNaoFechamentoConvenio,
          meta: { label: "Atualizar" },
          name: "motivos-convenio-perdido-atualizar",
        },
        {
          path: "adicionar",
          component: FormularioMotivoNaoFechamentoConvenio,
          meta: { label: "Adicionar" },
          name: "motivos-convenio-perdido-adicionar",
        },
      ],
    },

    {
      path: "/cadastros/operadora-cartao",
      meta: { label: "Operadoras de cartão" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaOperadoraCartao,
          meta: { label: "Lista" },
          name: "operadoracartao-lista",
        },
        {
          path: "atualizar/:id",
          component: FormularioOperadoraCartao,
          meta: { label: "Atualizar" },
          name: "operadoracartao-atualizar",
        },
        {
          path: "adicionar",
          component: FormularioOperadoraCartao,
          meta: { label: "Adicionar" },
          name: "operadoracartao-adicionar",
        },
      ],
    },

    {
      path: "/configuracoes/modelo-template",
      meta: { label: "Modelo de documentos" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaModeloTemplate,
          meta: { label: "Lista" },
          name: "modelotemplate-lista",
        },
        {
          path: "atualizar/:id",
          component: FormularioModeloTemplate,
          meta: { label: "Atualizar" },
          name: "modelotemplate-atualizar",
        },
        {
          path: "visualizar/:id",
          component: FormularioModeloTemplate,
          meta: { label: "Visualizar" },
          name: "modelotemplate-visualizar",
        },
        {
          path: "adicionar",
          component: FormularioModeloTemplate,
          meta: { label: "Adicionar" },
          name: "modelotemplate-adicionar",
        },
      ],
    },

    {
      path: "/relatorios",
      meta: { label: "Relatórios" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: RelatoriosComponent,
          meta: { label: "Lista" },
          name: "relatorios-listagem",
        },
      ],
    },
    {
      path: "/relatorios/follow-up",
      meta: { label: "Relatório de Follow Up" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
             {
          path: "",
          component: ListaRelatorioFollowUp,
          meta: { label: "Lista" },
          name: "relatorio-follow-up",
        },
      ],
    },
    {
      path: "/relatorios/relatorio-cheques",
      meta: { label: "Relatório de cheques" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioCheques,
          meta: { label: "Lista" },
          name: "relatorio-cheques-lista",
        },
      ],
    },
    {
      path: "/relatorios/relatorio-contas-pagar",
      meta: { label: "Relatório de contas a pagar" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioContasPagar,
          meta: { label: "Lista" },
          name: "relatorio-contas-pagar-lista",
        },
      ],
    },
    {
      path: "/relatorios/descontos",
      meta: { label: "Relatório de Descontos" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: FormularioRelatorioDescontos,
          meta: { label: "Lista" },
          name: "relatorio-descontos",
        },
      ],
    },
    {
      path: "/relatorios/informacoes-funcionarios",
      meta: { label: "Relatório Informações de Funcionários" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaInformacoesFuncionario,
          meta: { label: "Lista" },
          name: "relatorio-informacoes-funcionarios",
        },
      ],
    },
    {
      path: "/relatorios/fluxocaixa",
      meta: { label: "Relatório Fluxo de Caixa" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: FormularioFluxoCaixa,
          meta: { label: "Lista" },
          name: "relatorio-fluxo-caixa",
        },
      ],
    },
    {
      path: "/configuracoes/prospeccao",
      meta: { label: "Prospeccao" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaProspeccao,
          meta: { label: "Lista" },
          name: "prospeccao-lista",
        },
        {
          path: "alterar/:id",
          component: FormularioProspeccao,
          meta: { label: "Atualizar" },
          name: "prospeccao-alterar",
        },
        {
          path: "adicionar",
          component: FormularioProspeccao,
          meta: { label: "Adicionar" },
          name: "prospeccao-adicionar",
        },
      ],
    },
    {
      path: "/ocorrencia-academica",
      meta: { label: "Ocorrências" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaOcorrenciaAcademica,
          meta: { label: "Lista" },
          name: "ocorrenciaacademica-lista",
        },
        {
          path: "alterar/:id",
          component: FormularioOcorrenciaAcademica,
          meta: { label: "Atualizar" },
          name: "ocorrenciaacademica-alterar",
        },
      ],
    },
    {
      path: "/comercial/funil-vendas",
      meta: { label: "Funil de vendas" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaFunilVendas,
          meta: { label: "Lista" },
          name: "funilvendas-lista",
        },
      ],
    },
    {
      path: "/relatorios/mala-direta-aluno",
      meta: { label: "Gerar mala direta" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaMalaDiretaAluno,
          meta: { label: "Lista" },
          name: "maladiretaaluno-lista",
        },
      ],
    },
  
    {
      path: "/academico/atividade-extra",
      meta: { label: "Atividade extra e retake" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaAtiviadeExtra,
          meta: { label: "Lista" },
          name: "ativiadeextra-lista",
        },
      ],
    },
    {
      path: "/academico/nivelamento",
      meta: { label: "Nivelamento" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaNivelamento,
          meta: { label: "Lista" },
          name: "nivelamento-lista",
        },
      ],
    },
    {
      path: "/relatorios/relatorio-balancete-financeiro",
      meta: { label: "Relatório balancete financeiro" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioBalanceteFinanceiro,
          meta: { label: "Lista" },
          name: "relatoriobalancetefinanceiro-lista",
        },
      ],
    },
    /*
    {
      path: '/academico/servico',
      meta: { label: 'Serviço' },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: '',
          component: ListaServico,
          meta: { label: 'Lista' },
          name: 'servico-lista'
        },
        {
          path: 'alterar/:id',
          component: FormularioServico,
          meta: { label: 'Atualizar' },
          name: 'servico-alterar'
        },
        {
          path: 'adicionar',
          component: FormularioServico,
          meta: { label: 'Adicionar' },
          name: 'servico-adicionar'
        }
      ]
    },
    */

    {
      path: "/academico/reposicao-aula-avaliacao",
      meta: { label: "Make-up class & test" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaReposicaoAulaAvaliacao,
          meta: { label: "Lista" },
          name: "reposicaoaulaavaliacao-lista",
        },
        {
          path: "alterar/:id",
          component: FormularioReposicaoAulaAvaliacao,
          meta: { label: "Atualizar" },
          name: "reposicaoaulaavaliacao-alterar",
        },
        {
          path: "adicionar",
          component: FormularioReposicaoAulaAvaliacao,
          meta: { label: "Adicionar" },
          name: "reposicaoaulaavaliacao-adicionar",
        },
      ],
    },

    {
      path: "/academico/personal",
      meta: { label: "Diário de aulas personal" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaDiarioPersonal,
          meta: { label: "Lista" },
          name: "personal-lista",
        },
        {
          path: "diario-personal/:id",
          component: FormularioDiarioPersonal,
          meta: { label: "Diário personal" },
          name: "diario-personal",
        },
        {
          path: "agenda-personal",
          component: AgendaPersonal,
          meta: { label: "Agenda personal" },
          name: "AgendaPersonal",
        },
      ],
    },
    {
      path: "/academico/reagendamento-personal",
      meta: { label: "Reagendamento Personal" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaReagendamentoPersonal,
          meta: { label: "Lista" },
          name: "reagendamento-personal-lista",
        },
        /* {
          path: 'reagendamento-personal/:id',
          component: FormularioReagendamentoPersonal,
          meta: { label: 'Reagendamento Personal' },
          name: 'reagendamento-personal'
        } */
      ],
    },

    {
      path: "/relatorios/relatorio-funcionario",
      meta: { label: "Relatório funcionário" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioFuncionario,
          meta: { label: "Lista" },
          name: "relatoriofuncionario-lista",
        },
      ],
    },

   
    {
      path: "/relatorios/alunos-situacao",
      meta: { label: "Situação de alunos" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioAlunosSituacao,
          meta: { label: "Lista" },
          name: "relatorioaluno-lista",
        },
      ],
    },

    {
      path: "/relatorios/contrato-situacao",
      meta: { label: "Situação de contrato" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioContratosSituacao,
          meta: { label: "Lista" },
          name: "relatoriocontrato-lista",
        },
      ],
    },
    {
      path: "/relatorios/matricula-a-renovar",
      meta: { label: "Matricula a renovar" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioMatriculaRenovar,
          meta: { label: "Lista" },
          name: "relatoriomatricularenovar-lista",
        },
      ],
    },
    {
      path: "/relatorios/alunos-por-turma",
      meta: { label: "Relatório Alunos Por Turma" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioAlunosPorTurma,
          meta: { label: "Lista" },
          name: "relatorioAlunosPorTurma",
        },
      ],
    },

    {
      path: "/relatorios/matricula",
      meta: { label: "Matricula" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioMatricula,
          meta: { label: "Lista" },
          name: "relatorio-matriculas",
        },
      ],
    },

   

    {
      path: '/relatorios/nota',
      meta: { label: 'Notas' },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioNotas,
          meta: { label: "Lista" },
          name: "relatorionota-lista",
        },
      ],
    },

    {
      path: "/relatorios/notas-turmas",
      meta: { label: "Notas Turmas" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioNotaTurma,
          meta: { label: "Lista" },
          name: "relatorionota-turma-lista",
        },
      ],
    },

    {
      path: "/relatorios/retencao-alunos",
      meta: { label: "Relatório de Retenção de Alunos" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioRetencaoAlunos,
          meta: { label: "Lista" },
          name: "relatorio-retencao-alunos",
        },
      ],
    },

    {
      path: "/relatorios/frequencia",
      meta: { label: "Frequencias" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioFrequencias,
          meta: { label: "Lista" },
          name: "relatoriofrequencia-lista",
        },
      ],
    },

    {
      path: "/relatorios/impressao-class-record",
      meta: { label: "Impressão de class record" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioClassRecord,
          meta: { label: "Lista" },
          name: "impressao-class-record",
        },
        {
          path: "visualizar/:id",
          component: VisualizaTurma,
          meta: { label: "Informações" },
          name: "visualizar-class-record",
        },
      ],
    },
    {
      path: "/relatorios/aulas-ocorridas",
      meta: { label: "Relatório de Aulas Ocorridas" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioAulasOcorridas,
          meta: { label: "Lista" },
          name: "relatorio-aulas-ocorridas",
        },
      ],
    },

    {
      path: "/relatorios/retorno-consultor",
      meta: { label: "Relatório de Retornos Realizados por Consultor" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioRetornoConsultor,
          meta: { label: "Lista" },
          name: "relatorio-retorno-consultor",
        },
      ],
    },

    {
      path: "/relatorios/itens-de-estoque",
      meta: { label: "Relatório de Itens de Estoque" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioItensDeEstoque,
          meta: { label: "Lista" },
          name: "relatorio-itens-de-estoque",
        },
      ],
    },
    {
      path: "/relatorios/estoque",
      meta: { label: "Relatório de Estoque" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioEstoque,
          meta: { label: "Lista" },
          name: "relatorio-estoque",
        },
      ],
    },
    {
      path: "/relatorios/visitas",
      meta: { label: "Relatório de Visitas" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioVisitas,
          meta: { label: "Lista" },
          name: "relatorio-visitas",
        },
      ],
    },

    {
      path: "/relatorios/controle-material-didatico",
      meta: { label: "Controle de Material Didático" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaControleMaterialDidatico,
          meta: { label: "Lista" },
          name: "controle-material-didatico",
        },
      ],
    },

    {
      path: "/relatorios/contatos",
      meta: { label: "Relatório de Contatos" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioContatos,
          meta: { label: "Lista" },
          name: "relatorio-contatos",
        },
      ],
    },
    {
      path: "/academico/bonus-class",
      meta: { label: "Bônus class" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaBonusClass,
          meta: { label: "Lista" },
          name: "bonusclass-lista",
        },
        {
          path: "alterar/:id",
          component: FormularioBonusClass,
          meta: { label: "Atualizar" },
          name: "bonusclass-alterar",
        },
        {
          path: "adicionar",
          component: FormularioBonusClass,
          meta: { label: "Adicionar" },
          name: "bonusclass-adicionar",
        },
      ],
    },

    {
      path: "/relatorios/relatorio-inadimplencia",
      meta: { label: "Relatório de inadimplência" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioInadimplencia,
          meta: { label: "Lista" },
          name: "relatorioinadimplencia-lista",
        },
      ],
    },
    {
      path: "/relatorios/servico-solicitado",
      meta: { label: "Relatório de Serviços Solicitados" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioServicoSolicitado,
          meta: { label: "Lista" },
          name: "relatorio-servico-solicitado",
        },
      ],
    },

    {
      path: "/relatorios/pedido-material-didatico",
      meta: { label: "Relatório Pedido Material Didático" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",

          component: ListaPedidoMaterialDidatico,
          meta: { label: "Lista" },
          name: "relatoriopedidomaterialdidatico-lista",
        },
      ],
    },

    {
      path: "/relatorios/valores-turma",
      meta: { label: "Relatório de valores por turma" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioValoresTurma,
          meta: { label: "Lista" },
          name: "relatoriovalores-turma-lista",
        },
      ],
    },

    {
      path: "/comercial/follow-up",
      meta: { label: "Follow-up" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaFollowUp,
          meta: { label: "Lista" },
          name: "followup-lista",
        },
        {
          path: "alterar/:id",
          component: FormularioFollowUp,
          meta: { label: "Atualizar" },
          name: "followup-alterar",
        },
        {
          path: "adicionar",
          component: FormularioFollowUp,
          meta: { label: "Adicionar" },
          name: "followup-adicionar",
        },
      ],
    },

    {
      path: "/configuracoes/tipo-visibilidade",
      meta: { label: "TipoVisibilidade" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaTipoVisibilidade,
          meta: { label: "Lista" },
          name: "tipovisibilidade-lista",
        },
        {
          path: "alterar/:id",
          component: FormularioTipoVisibilidade,
          meta: { label: "Atualizar" },
          name: "tipovisibilidade-alterar",
        },
        {
          path: "adicionar",
          component: FormularioTipoVisibilidade,
          meta: { label: "Adicionar" },
          name: "tipovisibilidade-adicionar",
        },
      ],
    },
    {
      path: "/academico/agenda-compromisso",
      meta: { label: "Agenda" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaAgendaCompromisso,
          meta: { label: "Lista" },
          name: "agendacompromisso-lista",
        },
        {
          path: "alterar/:id",
          component: FormularioAgendaCompromisso,
          meta: { label: "Atualizar" },
          name: "agendacompromisso-alterar",
        },
        {
          path: "adicionar",
          component: FormularioAgendaCompromisso,
          meta: { label: "Adicionar" },
          name: "agendacompromisso-adicionar",
        },
      ],
    },

    {
      path: "/franqueadora/cadastro-servico",
      meta: { label: "Serviço" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaCadastroServico,
          meta: { label: "Lista" },
          name: "cadastroservico-lista",
        },
        {
          path: "atualizar/:id",
          component: FormularioCadastroServico,
          meta: { label: "Atualizar" },
          name: "cadastroservico-alterar",
        },
        {
          path: "adicionar",
          component: FormularioCadastroServico,
          meta: { label: "Adicionar" },
          name: "cadastroservico-adicionar",
        },
      ],
    },
    {
      path: "/configuracoes/meta-franqueada",
      meta: { label: "Definição de Metas" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaMetaFranqueada,
          name: "meta-franqueada",
          meta: { label: "Lista" },
        },
      ],
    },

    {
      path: "/franqueadora/indicadores",
      meta: { label: "Indicadores" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaIndicadores,
          meta: { label: "Lista" },
        },
      ],
    },

    {
      path: "/comercial/indicadores",
      meta: { label: "Indicadores" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaIndicadores,
          meta: { label: "Lista" },
        },
      ],
    },

    {
      path: "/financeiro/importacao-boleto",
      meta: { label: "Boletos" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaImportacaoBoleto,
          meta: { label: "Lista" },
          name: "importacaoboleto-lista",
        },
        {
          path: "retorno",
          component: ListaImportacaoBoletoRetorno,
          meta: { label: "Lista retorno de boletos" },
          name: "importacaoboletoretorno-lista",
        },
      ],
    },

    {
      path: "/configuracoes/midia",
      meta: { label: "Midia" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaMidia,
          meta: { label: "Lista" },
          name: "midia-lista",
        },
        {
          path: "atualizar/:id",
          component: FormularioMidia,
          meta: { label: "Atualizar" },
          name: "midia-alterar",
        },
        {
          path: "adicionar",
          component: FormularioMidia,
          meta: { label: "Adicionar" },
          name: "midia-adicionar",
        },
      ],
    }, // ComponenteGenerico
    {
      path: "/relatorios/interessados",
      meta: { label: "Relatório de Interessados" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioInteressados,
          meta: { label: "Lista" },
          name: "relatorio-interessados",
        },
      ],
    },
    {
      path: "/relatorios/aniversariantes",
      meta: { label: "Relatório de Aniversariantes" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioAniversariante,
          meta: { label: "Lista" },
          name: "relatorio-aniversariante",
        },
      ],
    },
    {
      path: "/relatorios/saldo-horas",
      meta: { label: "Relatório saldo de Horas VIP/Personal" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioSaldoHorasVipPersonal,
          meta: { label: "Lista" },
          name: "relatorio-saldo-horas-vip-personal",
        },
      ],
    },
    {
      path: "/relatorios/ocorrencias",
      meta: { label: "Relatório de ocorrências" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioOcorrencia,
          meta: { label: "Lista" },
          name: "relatorio-ocorrencia",
        },
      ],
    },
    {
      path: "/relatorios/dados-aluno",
      meta: { label: "Relatório de dados do aluno" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioDadosAluno,
          meta: { label: "Lista" },
          name: "relatorio-dados-aluno",
        },
      ],
    },
    {
      path: "/relatorios/aula-desmarcada",
      meta: { label: "Relatório de Aula Desmarcada" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioAulaDesmarcada,
          meta: { label: "Lista" },
          name: "relatorio-aula-desmarcada",
        },
      ],
    },
    {
      path: "/relatorios/negociacao-convenios",
      meta: { label: "Relatório de negociação de Convênio" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioNegociacaoConvenio,
          meta: { label: "Lista" },
          name: "relatorio-negociacao-convenio",
        },
      ],
    },
    {
      path: "/relatorios/mapa-sala-turma",
      meta: { label: "Relatório de Mapas de Sala e Turmas" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioMapaSalaTurma,
          meta: { label: "Lista" },
          name: "relatorio-mapa-sala-turma",
        },
      ],
    },
    {
      path: "/relatorios/prospeccao",
      meta: { label: "Relatório de Prospecção" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioProspeccao,
          meta: { label: "Lista" },
          name: "relatorio-prospeccao",
        },
      ],
    },
    {
      path: "/relatorios/saidas-estoque",
      meta: { label: "Relatório Saídas de estoque" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioSaidasDeEstoque,
          meta: { label: "Lista" },
          name: "relatorio-saida-estoque",
        },
      ],
    },
    {
      path: "/relatorios/turmas",
      meta: { label: "Relatório de turmas existentes" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioTurmaExistente,
          meta: { label: "Lista" },
          name: "relatorio-turma-existente",
        },
      ],
    },

    {
      path: "/relatorios/atividade-extra",
      meta: { label: "Relatório de Atividades Extra" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioAtividadeExtra,
          meta: { label: "Lista" },
          name: "relatorio-atividade-extra",
        },
      ],
    },
    {
      path: "/relatorios/dados-cadastro",
      meta: { label: "Relatório de Dados de Cadastro" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioDadosCadastro,
          meta: { label: "Lista" },
          name: "relatorio-dados-cadastro",
        },
        {
          path: "detalhes/:id",
          props: true,
          component: ListarDetalhesDadosCadastro,
          meta: { label: "Detalhes Dados de Cadastro" },
          name: "detalhes",
        },
      ],
    },
    {
      path: "/relatorios/disponibilidade-de-instrutores",
      meta: { label: "Relatório de Disponibilidade de Instrutores" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioDisponibilidadeInstrutor,
          meta: { label: "Lista" },
          name: "relatorio-disponibilidade-instrutor",
        },
      ],
    },
    {
      path: "/relatorios/compromisso-aprendizado",
      meta: { label: "Relatório de alunos no compromisso de aprendizado" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioCompromissoAprendizado,
          meta: { label: "Lista" },
          name: "relatorio-compromisso-aprendizado",
        },
      ],
    },
    {
      path: "/relatorios/consulta-conversao",
      meta: { label: "Relatório de Consulta de Conversão" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioConsultaConversao,
          meta: { label: "Lista" },
          name: "relatorio-consulta-conversao",
        },
      ],
    },
    {
      path: "/relatorios/matriculas-perdidas",
      meta: { label: "Relatório de matrículas perdidas" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioMatriculaPerdida,
          meta: { label: "Lista" },
          name: "relatorio-matricula-perdida",
        },
      ],
    },
    {
      path: "/relatorios/matriculas-venda",
      meta: { label: "Relatório de matrículas venda" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioMatriculaVenda,
          meta: { label: "Lista" },
          name: "relatoriomatriculas-venda",
        },
      ],
    },
    {
      path: "/relatorios/consulta-desistencias",
      meta: { label: "Relatório de Consulta de Desistência de Interessados" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: ListaRelatorioConsultaDesistencia,
          meta: { label: "Lista" },
          name: "relatorio-consulta-desistencia",
        },
      ],
    },
    {
      path: "/comercial/agenda-personal",
      meta: { label: "Agenda Personal" },
      component: Full,
      beforeEnter: routeChanged,
      children: [
        {
          path: "",
          component: AgendamentoPersonal,
          meta: { label: "agenda-personal" },
          name: "agendamento-personal",
        },
      ],
    },
  ],
});

export default router;
