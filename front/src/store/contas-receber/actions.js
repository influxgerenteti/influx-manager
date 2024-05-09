import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
import {stringToISODate, dateToString} from '../../utils/date'

const url = '/conta_receber'

function converterObjetoParaEnvio (item) {
  const data = Object.assign({}, item)
  data.usuario = null
  data.sacado_pessoa = data.sacado_pessoa ? data.sacado_pessoa.id : null
  data.forma_cobranca = data.forma_cobranca ? data.forma_cobranca.id : null
  data.conta = data.conta ? data.conta.id : null
  data.data_vencimento = stringToISODate(data.data_vencimento, true)

  return data
}

function converte (lista, paginaAtual) {
  const convertido = []
  const dataAgora = stringToISODate(dateToString(new Date()), true)

  console.log(lista)

  lista.map(titulo => {
   

      // Controle da situação
      titulo.situacao = titulo.situacao.toUpperCase()
      const vencimentoTitulo = stringToISODate(dateToString(new Date(titulo.data_prorrogacao)), true)
      const estaVencido = vencimentoTitulo < dataAgora

      titulo.podeSerRecebido = /PEN|VEN|LIQ-PEN/.exec(titulo.situacao) !== null

    //   itemInserir.titulo_receber = titulo
     convertido.push(titulo)
    // })
  })

  if (paginaAtual) {
    return {lista: convertido, paginaAtual: paginaAtual}
  }
  return convertido
}

function converteFiltros (filtros) {
  const result = Object.assign({}, filtros)
  result.sacado_pessoa = result.sacado_pessoa ? result.sacado_pessoa.id : null
  result.turma = result.turma ? result.turma.id : null
  result.forma_cobranca = result.forma_cobranca ? result.forma_cobranca.id : null
  result.item = result.item ? result.item.id : null
  result.mes = result.mes ? result.mes.value : null
  result.data_inicial_vencimento = result.data_inicial_vencimento ? stringToISODate(result.data_inicial_vencimento, true) : null
  result.data_final_vencimento = result.data_final_vencimento ? stringToISODate(result.data_final_vencimento, true) : null
  result.valor_inicial = result.valor_inicial > 0 ? result.valor_inicial : null
  result.valor_final = result.valor_final > 0 ? result.valor_final : null
 
  return result
}

export default {
  consultar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    const paginaAtual = state.paginaAtual
    return new Promise((resolve, reject) => {
      Request.get(`${url}/consulta`, { pagina: paginaAtual, ...converteFiltros(state.filtros), order: state.order, direcao: state.direcao })
        .then(response => {

          commit('SET_LISTA',response.body.corpo.itens)
          commit('SET_TOTAL_RECEBIDO', response.body.corpo.total_recebido)        
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('SET_TOTAL_FATURADO', response.body.corpo.total_faturado)        
          commit('SET_TOTAL_RECEBIDO_NAO_CONCILIADO', response.body.corpo.total_recebido_nao_conciliado)        
          commit('SET_TOTAL_RECEBER', response.body.corpo.total_pendente)        
          commit('SET_TOTAL_VENCIDO', response.body.corpo.total_vencido)        
          commit('SET_TOTAL_CANCELADO', response.body.corpo.total_cancelado)        

          if (state.totalItens > state.lista.length && state.paginaAtual === paginaAtual) {
            commit('INCREMENTAR_PAGINA_ATUAL')
          }

          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },
  
  gerarRelatorio ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    const paginaAtual = state.paginaAtual
    console.log('Gerando')
    return new Promise((resolve, reject) => {
      Request.get(`${url}/consulta`, { pagina: paginaAtual, ...converteFiltros(state.filtros), order: state.order, direcao: state.direcao })
        .then(response => {

      //    commit('SET_RELATORIO',response.body)     

              // Obtém o HTML da sua variável
          const htmlString = response.body;

          // Abre uma nova janela
          const novaJanela = window.open('', '_blank');

          // Escreve o HTML na nova janela
          novaJanela.document.open();
          novaJanela.document.write(htmlString);
          novaJanela.document.close();

          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },
  listar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    const paginaAtual = state.paginaAtual
    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, { pagina: paginaAtual, ...converteFiltros(state.filtros), order: state.order, direcao: state.direcao })
        .then(response => {
          const itens = response.body.corpo.itens

          commit('SET_LISTA', converte(itens, paginaAtual))
          commit('SET_TOTAL_RECEBIDO', response.body.corpo.total_recebido)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)

          if (state.totalItens > state.lista.length && state.paginaAtual === paginaAtual) {
            commit('INCREMENTAR_PAGINA_ATUAL')
          }

          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          commit('SET_ESTA_CARREGANDO', false)
          reject(error)
        })
    })
  },

  buscar ({ state, commit }) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${state.itemSelecionadoID}`)
        .then(response => {
          const object = Object.assign({}, response.body.corpo)

          object.possui_titulos_pagos = false
          const parcelas = {}
          object.titulo_receber.map((titulo, index) => {
            if (index === 0) {
              object.data_vencimento = dateToString(new Date(titulo.data_vencimento))
              object.forma_cobranca = titulo.forma_cobranca
              object.conta = titulo.conta
            }

            titulo.data_vencimento = dateToString(new Date(titulo.data_vencimento))
            titulo.valor_documento = titulo.valor_documento * 1
            titulo.verDetalhes = false
            parcelas[titulo.numero_parcela_documento] = Object.assign({}, titulo)

            if (titulo.movimento_conta && titulo.movimento_conta.length) {
              object.possui_titulos_pagos = true
            }
          })
          object.parcelas = parcelas

          object.plano_conta = object.plano_contas_conta_pagar || []
          if (!object.plano_contas_conta_pagar) {
            object.plano_contas_conta_pagar = []
          }

          object.plano_conta = object.plano_contas_conta_pagar.map(item => {
            item.valor *= 1
            return item
          })

          if (!object.plano_conta || !object.plano_conta.length) {
            object.plano_conta.push({ plano_conta: null, valor: undefined, complemento: null, numero_sequencia: 1 })
          }

          object.numero_parcelas = (object.numero_parcelas || 1) * 1
          object.valor_total = (object.valor_total || 0) * 1
          object.valor_parcela = (object.valor_parcela || 0) * 1

          commit('SET_ITEM_SELECIONADO', object)
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criar ({ state }) {
    return new Promise((resolve, reject) => {
      Request.post(`${url}/criar`, converterObjetoParaEnvio(state.itemSelecionado))
        .then(response => {
          resolve(response)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Conta a pagar criada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: `Erro ao criar a conta a pagar: ${error.body.mensagem}`
          })
        })
    })
  },

  atualizar ({ state }) {
    return new Promise((resolve, reject) => {
      Request.patch(`${url}/atualizar/${state.itemSelecionado.id}`, converterObjetoParaEnvio(state.itemSelecionado))
        .then(response => {
          resolve(response)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Conta a pagar atualizada com sucesso!'
          })
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao atualizar a conta a pagar.'
          })
        })
    })
  },

  remover ({ state }) {
    return new Promise((resolve, reject) => {
      Request.delete(`${url}/remover/${state.itemSelecionadoID}`)
        .then(response => {
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Exclusão realizada com sucesso!'
          })
          resolve(response)
        })
        .catch(error => {
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: error.mensagem
          })
          reject(error)
        })
    })
  },

  criarVendaAvulsa (store, contaReceber) {
    return new Promise((resolve, reject) => {
      const data = {
        sacado_pessoa: contaReceber.cliente.id,
        vendedor_funcionario: contaReceber.vendedor_funcionario.id,
        valor_total: contaReceber.venda_avulsa.subtotal_pagar,
        itens: contaReceber.itens_conta_receber,
        titulos_receber: contaReceber.titulos_receber
      }

      data.titulos_receber = data.titulos_receber.map(titulo => {
        const item = {...titulo}
        item.forma_cobranca = item.forma_cobranca.id
        item.forma_recebimento = item.forma_cobranca // inicialmente, são iguais
        item.data_vencimento = stringToISODate(item.data_vencimento, true)
        item.data_prorrogacao = item.data_vencimento
        return item
      })

      Request.post(`${url}/criar`, data)
        .then(response => {
          console.log(response)
          resolve(response)
        })
        .catch(error => {
          reject(error)
        })
    })
  }
}
