import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
import { stringToISODate } from '../../utils/date'

const url = '/interessado'

function converteDadosParaEnvio (data) {
  const dados = {}
  dados.nome = data.nome ? data.nome : null
  dados.idade = data.idade ? data.idade : null
  dados.sexo = data.sexo ? data.sexo : null
  dados.telefone_contato = data.telefone_contato ? data.telefone_contato : null
  dados.telefone_secundario = data.telefone_secundario ? data.telefone_secundario : null
  dados.email_contato = data.email_contato ? data.email_contato : null
  dados.email_secundario = data.email_secundario ? data.email_secundario : null
  dados.consultor_funcionario = data.consultor_funcionario ? data.consultor_funcionario.id : null

  dados.pessoa_indicou = data.pessoa_indicou ? data.pessoa_indicou : null
  dados.pessoa_indicou_nome = data.pessoa_indicou_nome ? data.pessoa_indicou_nome : null


  dados.tipo_contato = data.tipo_contato ? data.tipo_contato.id : null
  dados.tipo_prospeccao = data.tipo_prospeccao ? data.tipo_prospeccao.id : null
  dados.grau_interesse = data.grau_interesse ? data.grau_interesse : null

  dados.curso = data.curso ? data.curso.id : null
  // dados.idiomas = data.idiomas ? data.idiomas.map(i => i.id) : null
  dados.idiomas = null
  dados.data_validade_promocao =  null
  // dados.data_validade_promocao = data.data_validade_promocao ? stringToISODate(data.data_validade_promocao) : null
  dados.periodo_pretendido = data.periodo_pretendido ? data.periodo_pretendido : null
  dados.data_proximo_contato = data.data_proximo_contato ? stringToISODate(data.data_proximo_contato) : null
  dados.horario_proximo_contato = data.horario_proximo_contato ? data.horario_proximo_contato : null

  dados.consultor_responsavel_funcionario = data.consultor_responsavel_funcionario ? data.consultor_responsavel_funcionario.id : null

  return dados
}

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
   
      Request.get(`${url}/listar`, {pagina: state.paginaAtual, ...state.parametros, order: state.order, direcao: state.direcao})
        .then(response => {
          const lista = response.body.corpo.itens
         
          commit('SET_LISTA', lista)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('INCREMENTAR_PAGINA_ATUAL')
          commit('SET_ESTA_CARREGANDO', false)
          resolve(lista)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${state.itemSelecionadoID}`)
        .then(response => {
          commit('SET_ITEM_SELECIONADO', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(response.body.corpo)
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.post(`${url}/criar`, converteDadosParaEnvio(state.itemSelecionado))
        .then(response => {
          commit('SET_ITEM_SELECIONADO_ID', response.body.corpo.objetoORM)
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Criado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  },

  atualizar ({state, commit}) {
    return new Promise((resolve, reject) => {
      Request.patch(`${url}/atualizar/${state.itemSelecionado.id}`, converteDadosParaEnvio(state.itemSelecionado))
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Atualizado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  },

  atualizarDadosFollowup ({state, commit}) {
    return new Promise((resolve, reject) => {
      let dadosRequisicao = {}

      dadosRequisicao.data_proximo_contato = state.itemSelecionado.data_proximo_contato
      dadosRequisicao.data_primeiro_atendimento = state.itemSelecionado.data_primeiro_atendimento
      dadosRequisicao.data_cadastro = state.itemSelecionado.data_cadastro
      dadosRequisicao.motivo_nao_fechamento = state.itemSelecionado.motivo_nao_fechamento
      dadosRequisicao.horario_proximo_contato = state.itemSelecionado.horario_proximo_contato
      dadosRequisicao.grau_interesse = state.itemSelecionado.grau_interesse
      dadosRequisicao.situacao = state.itemSelecionado.situacao
      dadosRequisicao.follow_ups = state.itemSelecionado.follow_ups
      dadosRequisicao.periodo_pretendido = state.itemSelecionado.periodo_pretendido
      dadosRequisicao.nome = state.itemSelecionado.nome
      dadosRequisicao.telefone_contato = state.itemSelecionado.telefone_contato
      dadosRequisicao.email_contato = state.itemSelecionado.email_contato
      dadosRequisicao.idade = state.itemSelecionado.idade
      dadosRequisicao.consultor_funcionario = state.itemSelecionado.consultor_funcionario
      dadosRequisicao.consultor_responsavel_funcionario = state.itemSelecionado.consultor_responsavel_funcionario
      dadosRequisicao.curso = state.itemSelecionado.curso
      dadosRequisicao.idiomas = state.itemSelecionado.idiomas
      dadosRequisicao.data_validade_promocao = state.itemSelecionado.data_validade_promocao
      dadosRequisicao.workflow_acao = state.itemSelecionado.workflow_acao
      dadosRequisicao.tipo_lead = state.itemSelecionado.tipo_lead
      dadosRequisicao.tipo_contato = state.itemSelecionado.tipo_contato
      dadosRequisicao.pessoa_indicou = state.itemSelecionado.pessoa_indicou ? state.itemSelecionado.pessoa_indicou : null
      dadosRequisicao.pessoa_indicou_nome = state.itemSelecionado.pessoa_indicou_nome ? state.itemSelecionado.pessoa_indicou_nome : null
      dadosRequisicao.tipo_prospeccao = state.itemSelecionado.tipo_prospeccao
      dadosRequisicao.formulario_follow_up = state.itemSelecionado.formulario_follow_up ? state.itemSelecionado.formulario_follow_up : null

      Request.patch(`${url}/followup/${state.itemSelecionadoID}`, dadosRequisicao)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Atualizado com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: 'A',
            mensagem: error.body.mensagem
          })
        })
    })
  },

  listarFunilVendas ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      const filtros = { ...state.filtros }
      filtros.data_agendamento = stringToISODate(filtros.data_agendamento)
      if (filtros.consultor_comercial) {
        if (!filtros.consultor_comercial.usuario || !filtros.consultor_comercial.usuario.id) {
          delete filtros.consultor_comercial
        } else {
          filtros.consultor_comercial = filtros.consultor_comercial.usuario.id
        }
      }

      Request.get(`${url}/listar_funil_vendas`, filtros)
        .then(response => {
          const objeto = response.body.corpo
          commit('SET_FUNIL_VENDAS', objeto)
          commit('SET_ESTA_CARREGANDO', false)
          resolve(objeto)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  checarTelefonesCadastrados ({state, commit}, telefones) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      const data = {
        telefone: telefones
      }
      Request.get(`${url}/checar_telefones_cadastrados`, data)
        .then(response => {
          const objeto = response.body.corpo
          commit('SET_ESTA_CARREGANDO', false)
          resolve(objeto)
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  }
}
