import Request from '../../utils/request'
import EventBus from '../../utils/event-bus'

export default {
  getListaFranqueada ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get('/franqueada/listar', {pagina: state.paginaAtual, order: state.order, direcao: state.direcao})
        .then(response => {
          commit('SET_LISTA', response.body.corpo.itens)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('INCREMENTAR_PAGINA_ATUAL')
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  getFranqueada ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/franqueada/${state.franqueadaSelecionadaId}`)
        .then(response => {
          const franqueada = response.body.corpo
          franqueada.percentual_juro_dia = parseFloat(franqueada.percentual_juro_dia)
          commit('setFranqueada', franqueada)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  buscarParametros ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`/franqueada/parametros/${state.franqueadaSelecionadaId}`)
        .then(response => {
          commit('setFranqueada', response.body.corpo)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  criarFranqueada ({state}) {
    return new Promise((resolve, reject) => {
      const body = Object.assign({}, state.objFranqueada)
      body.estado = body.estado ? body.estado.id : null
      body.cidade = body.cidade ? body.cidade.id : null
      body.cpf = body.cpf ? body.cpf.replace(/\D+/g, '') : null
      body.sabado_dia_util = 0

      Request.post('/franqueada/criar', body)
        .then(response => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Franqueada criada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Erro ao criar franqueada.'
          })
        })
    })
  },

  atualizarFranqueada ({state}) {
    return new Promise((resolve, reject) => {
      const body = {
        nome: state.objFranqueada.nome,
        cnpj: state.objFranqueada.cnpj,
        dias_em_abertos_movimentos: state.objFranqueada.dias_em_abertos_movimentos,
        dias_para_negativacao: state.objFranqueada.dias_para_negativacao,
        dias_lembrete_cobranca: state.objFranqueada.dias_lembrete_cobranca,
        // sabado_dia_util: state.objFranqueada.sabado_dia_util,
        sabado_dia_util: 0,
        desconto_super_amigos_ativo: state.objFranqueada.desconto_super_amigos_ativo,
        desconto_super_amigos_turbinado_ativo: state.objFranqueada.desconto_super_amigos_turbinado_ativo,
        razao_social: state.objFranqueada.razao_social,
        endereco: state.objFranqueada.endereco,
        numero_endereco: state.objFranqueada.numero_endereco,
        bairro_endereco: state.objFranqueada.bairro_endereco,
        complemento_endereco: state.objFranqueada.complemento_endereco,
        cep_endereco: state.objFranqueada.cep_endereco,
        estado: state.objFranqueada.estado ? state.objFranqueada.estado.id : null,
        cidade: state.objFranqueada.cidade ? state.objFranqueada.cidade.id : null,
        inscricao_estadual: state.objFranqueada.inscricao_estadual,
        telefone: state.objFranqueada.telefone,
        telefone_secundario: state.objFranqueada.telefone_secundario,
        percentual_juro_dia: state.objFranqueada.percentual_juro_dia,
        percentual_multa: state.objFranqueada.percentual_multa,
        limite_dias_alteracao_documento: state.objFranqueada.limite_dias_alteracao_documento,
        tipo_movimento_conta_receber: state.objFranqueada.tipo_movimento_conta_receber,
        tipo_movimento_conta_pagar: state.objFranqueada.tipo_movimento_conta_pagar,
        email: state.objFranqueada.email,
        email_direcao: state.objFranqueada.email_direcao,
        email_comercial: state.objFranqueada.email_comercial,
        situacao: state.objFranqueada.situacao,
        percentual_desconto_a_vista: state.objFranqueada.percentual_desconto_a_vista
      }

      Request.patch(`/franqueada/atualizar/${state.objFranqueada.id}`, body)
        .then(() => {
          resolve()
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Franquia atualizada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.body.mensagem || 'Erro ao atualizar a franqueada.'
          })
        })
    })
  }
}
