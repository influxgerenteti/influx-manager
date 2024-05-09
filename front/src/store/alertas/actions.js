export default {

  criarAlerta ({commit}, {tipo, mensagem, tempo, informacaoAdicional}) {
    const configAlerta = {
      s: {variante: 'success', icone: 'check', titulo: 'Tudo certo!'},
      e: {variante: 'danger', icone: 'times', titulo: 'Oops! Algo está errado...'},
      i: {variante: 'info', icone: 'info', titulo: 'Informativo'},
      a: {variante: 'warning', icone: 'exclamation', titulo: 'Atenção!'},
      l: {variante: 'light', icone: 'lightbulb', titulo: 'Notificação do sistema'}
    }

    const alerta = configAlerta[tipo.toLowerCase()] || configAlerta['l']
    alerta.mensagem = mensagem
    alerta.informacaoAdicional = informacaoAdicional // precisa ser um array
    alerta.tempo = tempo || 5

    commit('adicionarAlerta', alerta)
  }

}
