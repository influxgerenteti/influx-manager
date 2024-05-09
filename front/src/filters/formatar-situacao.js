const situacoes = {
  A: 'Ativo',
  I: 'Inativo',
  R: 'Removido'
}

const formatarSituacao = value => {
  const option = value && value.toUpperCase && situacoes[value.toUpperCase()]
  return option || '--'
}

export default formatarSituacao
