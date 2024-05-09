import {numberToCurrency} from '../utils/number'

const formatarNumero = (value, digits = 2) => {
  if (!value) {
    value = 0
  }

  return numberToCurrency(1 * value, digits)
}

export default formatarNumero
