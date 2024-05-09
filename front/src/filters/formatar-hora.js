const formatarHoraDB = value => {
  if (!value) {
    return ''
  }

  let dataCortada = value.split('T')
  let horaCortada = dataCortada[1].split('+')
  let indicesHoraCortada = horaCortada[0].split(':')
  let dataHoraFinal = new Date()
  dataHoraFinal.setHours(indicesHoraCortada[0], indicesHoraCortada[1], 0)

  return String(dataHoraFinal.getHours()).padStart(2, '0') + ':' + String(dataHoraFinal.getMinutes()).padStart(2, '0')
}

export default formatarHoraDB
