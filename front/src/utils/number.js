const toNumber = value => value * 1

const round = value => parseFloat(toNumber(value).toFixed(2))

const paddingTwo = value => value && (`${value}`).length === 1 ? `0${value}` : `${value || '00'}`

const toString = number => `${number}`

const currencyToNumber = value => {
  if (typeof value === 'number') {
    return value
  }

  return toNumber(isNumberAsString(value))
}

const numberToCurrency = (value, digits = 2) =>
  value.toLocaleString('pt-BR', {
    style: 'decimal',
    minimumFractionDigits: digits
  })

const isNumberAsString = value => {
  if (typeof value === 'number') {
    return value
  }

  if (!value) {
    value = ''
  }

  if (value.includes(',')) {
    value = value.replace('.', '').replace(',', '.')
  }

  return value.replace(/[^\d.-]+/g, '')
}

export {
  round,
  toNumber,
  paddingTwo,
  toString,
  currencyToNumber,
  numberToCurrency,
  isNumberAsString
}
