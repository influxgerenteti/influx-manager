const formatarMoeda = (value, isDecimal, iUS) => {
  if (!value) {
    value = 0
  }

  value = assegurarNumero(value).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'})
  if (isDecimal) {
    value = value.replace(/^[\D]{2,2}/g, '').trim().replace('$', '-')
  }

  if (iUS) {
    return value.replace('R$', 'iU$')
  }

  return value
}

const assegurarNumero = value => {
  if (typeof value === 'string') {
    if (value.includes(',')) {
      value = value.replace(/\./g, '').replace(/,/g, '.')
    }
  }

  return Number(value)
}

export default formatarMoeda
