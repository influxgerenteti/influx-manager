const formatarCPF = (value) => value ? value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4') : ''

const formatarCNPJ = (value) => value ? value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5') : ''

// Calculo e validação do CNPJ e CPF extraidas do backend

const verificaSequenciaInvalidaCpf = (value) => {
  if (value === '00000000000' ||
        value === '11111111111' ||
        value === '22222222222' ||
        value === '33333333333' ||
        value === '44444444444' ||
        value === '55555555555' ||
        value === '66666666666' ||
        value === '77777777777' ||
        value === '88888888888' ||
        value === '99999999999'
  ) {
    return true
  }

  return false
}

const verificaSequenciaInvalidaCnpj = (value) => {
  if (value === '00000000000000' ||
      value === '11111111111111' ||
      value === '22222222222222' ||
      value === '33333333333333' ||
      value === '44444444444444' ||
      value === '55555555555555' ||
      value === '66666666666666' ||
      value === '77777777777777' ||
      value === '88888888888888' ||
      value === '99999999999999'
  ) {
    return true
  }

  return false
}

const digitosVerificadoresInvalidoCpf = (cpf) => {
  let d = 0
  let c = 0
  for (let t = 9; t < 11; t++) {
    for (d = 0, c = 0; c < t; c++) {
      d += cpf[c] * ((t + 1) - c)
    }

    d = ((10 * d) % 11) % 10
    if (cpf[c] !== (d.toString())) {
      return true
    }
  }

  return false
}

const digitosVerificadoresValidosCnpj = (cnpj) => {
  let j = 5
  let k = 6

  let soma1 = 0
  let soma2 = 0
  let digito1
  let digito2

  for (let i = 0; i < 13; i++) {
    if (j === 1) {
      j = 9
    }

    if (k === 1) {
      k = 9
    }

    soma2 += (cnpj[i] * k)

    if (i < 12) {
      soma1 += (cnpj[i] * j)
    }

    k--
    j--
  }

  if (soma1 % 11 < 2) {
    digito1 = 0
  } else {
    digito1 = 11 - soma1 % 11
  }

  if (soma2 % 11 < 2) {
    digito2 = 0
  } else {
    digito2 = 11 - soma2 % 11
  }

  return ((cnpj[12].toString() === digito1.toString()) && (cnpj[13].toString() === digito2.toString()))
}

const isCnpjValido = (cnpj) => {
  let retorno = true
  // Limpa a formatacao do CNPJ(caso venha formatado)
  cnpj = cnpj.replace(/\D/g, '')
  cnpj = cnpj.padStart(14, '0')
  if (cnpj.length !== 14) {
    retorno = false
  } else {
    if (verificaSequenciaInvalidaCnpj(cnpj) === true) {
      retorno = false
    } else {
      if (digitosVerificadoresValidosCnpj(cnpj) === false) {
        retorno = false
      }
    }
  }

  return retorno
}

const isCpfValido = (cpf) => {
  let retorno = true
  // Limpa a formatacao do CPF(caso venha formatado)
  cpf = cpf.replace(/\D/g, '')

  if (cpf.length !== 0) {
    if (verificaSequenciaInvalidaCpf(cpf) === true) {
      retorno = false
    } else {
      if (digitosVerificadoresInvalidoCpf(cpf) === true) {
        retorno = false
      }
    }
  }

  return retorno
}

export {
  formatarCPF,
  formatarCNPJ,
  isCnpjValido,
  isCpfValido
}
