const infoEvento = value => {
  if (!value) {
    return ''
  }

  const json = JSON.parse(value)
  const resultado = []

  for (let key in json) {
    const conteudo = typeof json[key] === 'object' ? JSON.stringify(json[key]) : json[key]
    resultado.push(`${key}: ${conteudo}`)
  }

  return resultado.join('\n')
}

export default infoEvento
