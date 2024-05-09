export default {
  setArquivoSelecionado (state, arquivo) {
    state.arquivoSelecionado = arquivo
    state.nomeArquivoSelecionado = arquivo ? arquivo.name : null
  },

  setWorkbookData (state, data) {
    state.workbookData = data
  },

  setReadingFile (state, data) {
    state.readingFile = data
  },

  setSendingFile (state, data) {
    state.sendingFile = data
  },

  setStep (state, step) {
    state.step = step
  },

  setResultSet (state, result) {
    state.resultSet = result
  }
}
