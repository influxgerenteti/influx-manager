import moment from 'moment'
const diasSemana = {
  1: {nome: 'Segunda', abreviacao: 'Seg'},
  2: {nome: 'Terça', abreviacao: 'Ter'},
  3: {nome: 'Quarta', abreviacao: 'Qua'},
  4: {nome: 'Quinta', abreviacao: 'Qui'},
  5: {nome: 'Sexta', abreviacao: 'Sex'},
  6: {nome: 'Sábado', abreviacao: 'Sab'},
  7: {nome: 'Domingo', abreviacao: 'Dom'}
}
/**
 * Retorna o dia da semana
 * @param {Date} value valor para ser transformado
 * @param {Boolean} howHora se o retorno for com hora
 * @param {String} label label do retorno nome(Segunda) ou abreviação(Seg)
 * @param {String} format Formatação do moment
 */
const formatarDataDiaSemana = (value, howHora = false, label = 'nome', format = null) => {
  let weekDay = format !== null ? moment(value, format).isoWeekday() : moment(value).isoWeekday()

  if (howHora) {
    return diasSemana[weekDay][label] + ' ' + moment(value).format('HH:mm')
  }

  return diasSemana[weekDay][label]
}

export default formatarDataDiaSemana
