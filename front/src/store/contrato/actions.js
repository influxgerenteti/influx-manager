import EventBus from '../../utils/event-bus'
import Request from '../../utils/request'
import {stringToISODate, dateToString} from '../../utils/date'
import {round} from '../../utils/number'
import moment from 'moment'

const url = '/contrato'

const converterDados = (item) => {
  const data = Object.assign({}, item)

  data.aluno = data.aluno ? data.aluno.id : null
  data.turma = data.turma ? (data.turma.turmaId ? data.turma.turmaId : data.turma.id) : null
  data.curso = data.curso ? data.curso.id : null
  data.livro = data.livro ? data.livro.id : null
  data.instrutor = data.instrutor ? data.instrutor.id : null
  data.sala_franqueada = data.sala_franqueada ? data.sala_franqueada.id : null
  data.convenio_desconto = data.convenio_desconto ? data.convenio_desconto.id : null
  data.modalidade_turma = data.modalidade_turma ? data.modalidade_turma.tipo : null
  data.semestre = data.semestre ? data.semestre.id : null
  data.responsavel_financeiro_pessoa = data.responsavel_financeiro_pessoa ? data.responsavel_financeiro_pessoa.id : null
  data.responsavel_carteira_funcionario = data.responsavel_carteira_funcionario ? data.responsavel_carteira_funcionario.id : null
  data.responsavel_venda_funcionario = data.responsavel_venda_funcionario ? data.responsavel_venda_funcionario.id : null
  if (data.data_matricula === null) {
    data.data_matricula = stringToISODate(dateToString(new Date()), true)
    data.data_inicio_contrato = data.data_matricula
  } else {
    data.data_matricula = stringToISODate(data.data_matricula)
    data.data_inicio_contrato = data.data_matricula
  }

  if (data.responsavel_financeiro_pessoa === null) {
    data.responsavel_financeiro_pessoa = item.aluno.pessoa
  }

  if (data.data_cancelamento !== null) {
    data.data_cancelamento = item.data_cancelamento
  }


  data.data_termino_contrato = data.data_termino_contrato ? stringToISODate(data.data_termino_contrato, true) : null

  if (data.id) {
    data.titulos_receber = null
  } else {
    data.contas_receber = [converterContaReceber(data.dadosContaReceberOriginais)]
    data.titulos_receber = converterTitulos(data.titulosOriginais, data.contas_receber[0], somarDescontos(data.itensOriginais))
    data.contas_receber[0].itens = converterItens(data)
  }

  data.dadosContaReceberOriginais = null
  data.contratoContaReceber = null
  data.itensOriginais = null
  data.titulosOriginais = null

  data.aplica_desconto_super_amigos = data.aplica_desconto_super_amigos ? data.aplica_desconto_super_amigos : null
  data.aplica_desconto_super_amigos_turbinado = data.aplica_desconto_super_amigos_turbinado ? data.aplica_desconto_super_amigos_turbinado : null
  data.aluno_indicador = data.aluno_indicador ? data.aluno_indicador : null

  if (data.creditos_personal && data.creditos_personal.id === -1) {
    data.creditos_personal = data.creditos_personal_avulso.quantidade
    delete data.creditos_personal_avulso
  } else if (data.creditos_personal) {
    data.creditos_personal = data.creditos_personal.id
  } else {
    data.creditos_personal = null
  }



  data.agendamento_personal = data.agendamento_personal || []
  if(data.agendamento_personal.length > 0){
    data.agendamento_personal = data.agendamento_personal.map( agendamento => {
      agendamento.inicio = moment(agendamento.inicio).subtract(3, 'hour').toISOString()
      agendamento.final = moment(agendamento.final).subtract(3, 'hour').toISOString()
      return agendamento
    })

  }
  return data
  
}

const somarDescontos = (itens) => {
  let totalDescontos = 0
  for (let i = 0; i < itens.length; i++) {
    totalDescontos += itens[i].valor_desconto
  }
  return totalDescontos
}

const converterContaReceber = (data) => {
  const contaReceber = Object.assign({}, data)

  contaReceber.franqueada = contaReceber.franqueada
  contaReceber.aluno = contaReceber.aluno ? contaReceber.aluno.id : null
  contaReceber.sacado_pessoa = contaReceber.sacado_pessoa ? contaReceber.sacado_pessoa.id : null
  contaReceber.vendedor_funcionario = contaReceber.vendedor_funcionario ? contaReceber.vendedor_funcionario.id : null

  return contaReceber
}

const converterItens = (data) => {
  const itens = []

  data.itensOriginais.map((item, index) => {
    const itemTratado = Object.assign({}, item)

    // fixos
    itemTratado.quantidade = itemTratado.quantidade ? itemTratado.quantidade : 1
    itemTratado.numero_sequencia = index + 1

    // conversões
    itemTratado.item = item.item ? item.item.id : null
    itemTratado.plano_conta = item.plano_conta ? item.plano_conta.id : null

    // 4 novos campos
    itemTratado.valor_parcela_sem_desconto = itemTratado.valor_parcela || 0
    itemTratado.valor_item = itemTratado.valor ? itemTratado.valor / itemTratado.quantidade : 0
    itemTratado.valor_desconto_super_amigo = itemTratado.valor_desconto_super_amigo || 0

    itemTratado.valor = itemTratado.valor_total_desconto || itemTratado.valor

    itemTratado.desconto_antecipacao = round(itemTratado.valor_item - itemTratado.valor)

    itemTratado.valor_parcela = itemTratado.valor_parcela_desconto
    itemTratado.forma_pagamento = itemTratado.forma_cobranca
    delete itemTratado.forma_cobranca
    itemTratado.dias_subsequentes = itemTratado.dias_subsequentes && itemTratado.dias_subsequentes.numero_dia ? itemTratado.dias_subsequentes.numero_dia : null

    // remoções
    itemTratado.conta = null
    itemTratado.data_vencimento = stringToISODate(itemTratado.data_vencimento, true)

    itens.push(itemTratado)
  })

  return itens
}

const converterFiltros = (item) => {
  const data = Object.assign({}, item)

  data.aluno = data.aluno ? data.aluno.id : null
  data.data_inicio_contrato_inicio = data.data_inicio_contrato_inicio ? stringToISODate(data.data_inicio_contrato_inicio, true) : null
  data.data_inicio_contrato_fim = data.data_inicio_contrato_fim ? stringToISODate(data.data_inicio_contrato_fim, true) : null
  data.data_termino_contrato_inicio = data.data_termino_contrato_inicio ? stringToISODate(data.data_termino_contrato_inicio, true) : null
  data.data_termino_contrato_fim = data.data_termino_contrato_fim ? stringToISODate(data.data_termino_contrato_fim, true) : null
  data.responsavel_carteira_funcionario = data.responsavel_carteira_funcionario ? data.responsavel_carteira_funcionario.id : null
  data.responsavel_venda_funcionario = data.responsavel_venda_funcionario ? data.responsavel_venda_funcionario.id : null

  return data
}

const converterTitulos = (titulos, contaReceber, totalDescontosItens) => {
  let totalDescontoAntecipacaoTitulos = 0
  let totalTitulosAlteradosSemDesconto = 0
  let titulosConvertidos = Array.from(titulos, titulo => {
    const novoTitulo = Object.assign({}, titulo)

    novoTitulo.franqueada = contaReceber.franqueada
    novoTitulo.sacado_pessoa = contaReceber.sacado_pessoa
    novoTitulo.aluno = contaReceber.aluno

    novoTitulo.conta = novoTitulo.conta ? novoTitulo.conta.id : null
    novoTitulo.forma_cobranca = novoTitulo.forma_cobranca ? novoTitulo.forma_cobranca.id : null
    novoTitulo.forma_recebimento = novoTitulo.forma_cobranca
    novoTitulo.data_vencimento = novoTitulo.data_vencimento ? stringToISODate(novoTitulo.data_vencimento, true) : null
    novoTitulo.data_emissao = stringToISODate(dateToString(new Date()), true)
    novoTitulo.valor_original = novoTitulo.valor
    novoTitulo.valor_item = novoTitulo.valor || 0
    novoTitulo.desconto_antecipacao = novoTitulo.desconto_antecipacao ? round(novoTitulo.desconto_antecipacao) : 0
    novoTitulo.valor_saldo_devedor = novoTitulo.valor + novoTitulo.desconto_antecipacao
    novoTitulo.valor_desconto_super_amigo = novoTitulo.valor_desconto_super_amigo || 0
    novoTitulo.valor_desconto_super_manual = novoTitulo.valor_desconto_super_manual || 0

    if (novoTitulo.transacao_cartao) {
      novoTitulo.transacao_cartao.data_pagamento = novoTitulo.data_vencimento
      novoTitulo.transacao_cartao.operadora_cartao = novoTitulo.transacao_cartao.operadora_cartao ? novoTitulo.transacao_cartao.operadora_cartao.id : null
      novoTitulo.transacao_cartao.parcelamento_operadora_cartao = novoTitulo.transacao_cartao.parcelamento_operadora_cartao ? novoTitulo.transacao_cartao.parcelamento_operadora_cartao.id : null
      novoTitulo.transacao_cartao.valor_liquido = novoTitulo.valor
    } else {
      novoTitulo.transacao_cartao = null
    }

    if (novoTitulo.cheque) {
      novoTitulo.cheque.data_bom_para = novoTitulo.data_vencimento
      novoTitulo.cheque.valor = novoTitulo.valor
    } else {
      novoTitulo.cheque = null
    }

    if (novoTitulo.valorTituloGeradoAutomatico) {
      totalDescontoAntecipacaoTitulos += round(novoTitulo.desconto_antecipacao)
    } else {
      novoTitulo.desconto_antecipacao = 0
      totalTitulosAlteradosSemDesconto += novoTitulo.valor
    }

    return novoTitulo
  })

  // Ajustando os valores de desconto_antecipacao caso os titulos tenham sido alterados após serem gerados automaticamente
  if (round(totalDescontosItens) !== round(totalDescontoAntecipacaoTitulos)) {
    const descontoFaltante = round(totalDescontosItens - totalDescontoAntecipacaoTitulos)
    const percentualDescontoFaltante = descontoFaltante / totalTitulosAlteradosSemDesconto
    titulosConvertidos = titulosConvertidos.map((titulo) => {
      if (!titulo.valorTituloGeradoAutomatico) {
        const descontoAdicional = round(titulo.valor * percentualDescontoFaltante)
        totalDescontoAntecipacaoTitulos += descontoAdicional
        titulo.desconto_antecipacao += descontoAdicional
      }
      return titulo
    })
    // Ajusta para totalizar o valor de desconto na primeira parcela que não tiver o valor original
    if (parseFloat(totalDescontosItens).toFixed(2) !== parseFloat(totalDescontoAntecipacaoTitulos).toFixed(2)) {
      const descontoAdicional = round(contaReceber.desconto_antecipacao - totalDescontoAntecipacaoTitulos)
      for (let i = 0; i < titulosConvertidos.length; i++) {
        if (!titulosConvertidos[i].valorTituloGeradoAutomatico) {
          totalDescontoAntecipacaoTitulos += descontoAdicional
          titulosConvertidos[i].desconto_antecipacao += descontoAdicional
        }
      }
    }
  }

  titulosConvertidos = titulosConvertidos.map((titulo) => {
    titulo.valor_parcela_sem_desconto = titulo.valor + titulo.desconto_antecipacao
    return titulo
  })

  return titulosConvertidos
}

export default {
  listar ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar`, {pagina: state.paginaAtual, order: state.order, direcao: state.direcao, ...converterFiltros(state.filtros)})
        .then(response => {
          commit('SET_LISTA', response.body.corpo.itens)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('INCREMENTAR_PAGINA_ATUAL')
          resolve()
        })
        .catch(reject)
        .finally(() => {
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },

  listarContratos ({state, commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.get(`${url}/listar_contratos`, {pagina: state.paginaAtual, order: state.order, direcao: state.direcao, ...converterFiltros(state.filtros)})
        .then(response => {
          commit('SET_LISTA', response.body.corpo.itens)
          commit('SET_TOTAL_ITENS', response.body.corpo.total)
          commit('INCREMENTAR_PAGINA_ATUAL')
          resolve()
        })
        .catch(reject)
        .finally(() => {
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },

  buscarContratosComDollarAtivosPorTurma ({state, commit}, turmaId) {
    return new Promise((resolve, reject) => {
      Request.get(`${url}/buscar_contratos_ativos_com_dollar_por_turma/${turmaId}`)
        .then(response => {
          resolve(response.body.corpo)
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  buscar ({state, commit}, contratoId) {
    commit('SET_ESTA_CARREGANDO', true)
    return new Promise((resolve, reject) => {
      Request.get(`${url}/${state.itemSelecionadoID || contratoId}`)
        .then(response => {
          const data = response.body.corpo

          data.data_inicio_contrato = data.data_inicio_contrato ? dateToString(new Date(data.data_inicio_contrato)) : ''
          data.data_termino_contrato = data.data_termino_contrato ? dateToString(new Date(data.data_termino_contrato)) : ''
          data.data_matricula = data.data_matricula ? dateToString(new Date(data.data_matricula)) : ''
          data.agendamento_personal = data.agendamentoPersonals || []
          delete data.agendamentoPersonals

          if (this.modalidadePersonal && data.agendamento_personal.length) {
            data.sala_franqueada = data.agendamento_personal[0].sala_franqueada
            data.instrutor = data.agendamento_personal[0].funcionario
            data.curso = null
            data.turma = null
          }

          data.creditos_personal = data.creditos_personal || data.creditosPersonal || {}

          data.contratoContaReceber.map(conta => {
            if (conta.itemsContaReceber) {
              conta.itemsContaReceber.map(item => {
                item.valor = round(item.valor)

                return item
              })
            }

            if (conta.tituloRecebers) {
              conta.titulos_receber = conta.tituloRecebers.map(titulo => {
                titulo.valor_original = round(titulo.valor_original)
                titulo.valor_saldo_devedor = round(titulo.valor_saldo_devedor)
                titulo.data_prorrogacao = titulo.data_prorrogacao ? dateToString(new Date(titulo.data_prorrogacao)) : ''

                return titulo
              })
            }

            return conta
          })

          commit('SET_ITEM_SELECIONADO', Object.assign({}, state.itemSelecionado, data))
          resolve(state.itemSelecionado)
        })
        .catch(reject)
        .finally(() => {
          commit('SET_ESTA_CARREGANDO', false)
        })
    })
  },

  buscarTextoContrato ({state, commit}, {contratoId, modeloContratoId}) {
    const data = {
      modelo_contrato: modeloContratoId
    }
    return new Promise((resolve, reject) => {
      Request.get(`${url}/buscar_texto_contrato/${contratoId}`, data)
        .then(response => {
          const textoContrato = response.body.corpo.texto
          commit('SET_TEXTO_CONTRATO', textoContrato)
          resolve(textoContrato)
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  criar ({state}) {
    return new Promise((resolve, reject) => {
      let dados = converterDados(state.itemSelecionado)
      Request.post(`${url}/criar`, dados)
        .then(response => {
          resolve(response.body.corpo)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Contrato criado com sucesso!'
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

  atualizar ({state}) {
    return new Promise((resolve, reject) => {
      Request.patch(`${url}/atualizar/${state.itemSelecionado.id}`, converterDados(state.itemSelecionado))
        .then(response => {
          resolve(response.body.corpo ? response.body.corpo.objetoORM : null)
          EventBus.$emit('criarAlerta', {
            tipo: 'S',
            mensagem: 'Contrato atualizado com sucesso!'
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

  atualizarTextoContrato ({state}) {
    return new Promise((resolve, reject) => {
      const data = {texto: state.textoContrato}
      Request.patch(`${url}/atualizar_texto/${state.itemSelecionadoID}`, data)
        .then(response => {
          resolve(response)
        })
        .catch(error => {
          reject(error)
          EventBus.$emit('criarAlerta', {
            tipo: error.status > 500 ? 'E' : 'A',
            mensagem: 'Erro ao gerar PDF: ' + error.body.mensagem
          })
        })
    })
  }
}
