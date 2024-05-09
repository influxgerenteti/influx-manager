const tipoEvento = value => {
  const options = {
    L: 'Login',
    A: 'Acesso',
    C: 'Criado',
    R: 'Leitura',
    U: 'Atualização',
    D: 'Exclusão'
  }

  const option = value && value.toUpperCase()
  return options[option] || null
}

export default tipoEvento
