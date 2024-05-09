import {paddingTwo} from './number'
import moment from 'moment'

const isISODate = str => {
  if (typeof str !== 'string') {
    return false
  }

  return str.match(/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{3})?(Z|[+-]\d{2}:?\d{2})/) !== null
}

const guaranteeISOString = str => moment(str).toISOString()

const dateToString = date =>
  `${paddingTwo(date.getDate())}/${paddingTwo(date.getMonth() + 1)}/${date.getFullYear()}`


const stringToISODate = (str, midDay = false) => {
  if (!str) {
    return ''
  }

  if (isISODate(str)) {
    return guaranteeISOString(str)
  }

  const split = str.split('/')
  const hours = midDay === true ? '12' : '00'
  return `${split[2]}-${split[1]}-${split[0]}T${hours}:00:00.000Z`
}

const stringToISODateMinMax = (str, max = false) => {
  if (!str) {
    return ''
  }

  if (isISODate(str)) {
    return guaranteeISOString(str)
  }

  const split = str.split('/')
  const time = max === true ? '23:59:59' : '00:00:00'
  return `${split[2]}-${split[1]}-${split[0]}T${time}.000Z`
}

const dateToCompare = str => {
  const regex = /^(\d{2})\/(\d{2})\/(\d{4}).*/
  str = str ? str.replace(regex, '$3-$2-$1') : ''
  return str
}

const dateToStringConvert = dataEntrada => {
  const data = new Date(dataEntrada);

  const ano = data.getFullYear();
  const mes = (data.getMonth() + 1).toString().padStart(2, '0'); // getMonth() retorna um valor entre 0 e 11, então adicione 1 para obter o mês correto
  const dia = data.getDate().toString().padStart(2, '0');

  let dataFormatada = `${dia}/${mes}/${ano}`;
  // console.log(dataFormatada);
  return dataFormatada;
}
const stringToDateConvert = dataEntrada => {  
  const partes = dataEntrada.split('/');

  // Extrai o dia, mês e ano da data
  const dia = parseInt(partes[0], 10);
  const mes = parseInt(partes[1], 10) - 1; // Subtrai 1 porque os meses em JavaScript começam do zero
  const ano = parseInt(partes[2], 10);

  // Cria um novo objeto Date com os valores extraídos
  const data = new Date(ano, mes, dia);

  data.setHours(12, 0, 0, 0);
  return data;
}

const dateToDBStringConvert = dataEntrada => {
  const data = new Date(dataEntrada);

  const ano = data.getFullYear();
  const mes = (data.getMonth() + 1).toString().padStart(2, '0'); // getMonth() retorna um valor entre 0 e 11, então adicione 1 para obter o mês correto
  const dia = data.getDate().toString().padStart(2, '0');

  let dataFormatada = `${ano}-${mes}-${dia} 12:00:00`;
  // console.log(dataFormatada);
  return dataFormatada;
}

// const stringDateFormat = dataEntrada => {  
//   const partes = dataEntrada.split('/');

//   // Extrai o dia, mês e ano da data
//   const dia = parseInt(partes[0], 10);
//   const mes = parseInt(partes[1], 10) - 1; // Subtrai 1 porque os meses em JavaScript começam do zero
//   const ano = parseInt(partes[2], 10);

//   // Cria um novo objeto Date com os valores extraídos
//   const data = new Date(ano, mes, dia);

//   data.setHours(12, 0, 0, 0);
//   return data;
// }


const dayAndMonthToCompare = str => {
  const regex = /^(\d{2})\/(\d{2})\.*/
  str = str ? str.replace(regex, '$2-$1') : ''
  return str
}

const getDateFromISO = date => {
  return date.split('T')[0]
}

const ISOToString = date => {
  return dateToString(new Date(date))
}

const diffInDays = (firstDate, secondDate) =>
  (new Date(firstDate) - new Date(secondDate)) / (1000 * 60 * 60 * 24)

const beginOfDay = date => stringToISODate(date)

const endOfDay = date => {
  const dateStr = stringToISODate(date)
  return dateStr.replace('T00:00:00.000Z', 'T23:59:59.999Z')
}

const turnDatetime = time => {
  time = time.split(':')

  const date = new Date()
  date.setHours(time[0], time[1], 0)

  return date
}

const horaAMaiorHoraB = (strHoraA, strHoraB) => {
  if (strHoraA.length === 2) {
    strHoraA += ':00'
  }
  if (strHoraB.length === 2) {
    strHoraB += ':00'
  }
  if ((strHoraA.length === 5) && (strHoraB.length === 5)) {
    let dataHoraObjA = turnDatetime(strHoraA)
    let dataHoraObjB = turnDatetime(strHoraB)
    return dataHoraObjA > dataHoraObjB
  }
  return false
}

const idade = (dataNascimento) => {
  const date = new Date()
  const anoAtual = date.getFullYear()
  const mesAtual = date.getMonth() + 1
  const diaAtual = date.getDate()

  const nascimento = new Date(dataNascimento)
  const anoN = nascimento.getFullYear()
  const mesN = nascimento.getMonth() + 1
  const diaN = nascimento.getDate()

  let idade = anoAtual - anoN

  if (mesAtual < mesN || (mesAtual === mesN && diaAtual < diaN)) {
    idade--
  }

  return idade < 0 ? 0 : idade
}

const converterDataDoBancoParaString = (horarioString) => {
  return moment(horarioString, 'YYYY-MM-DDTHH:mm:ss.SSS\\Z').format('DD/MM/YYYY')
}

const converteHorarioParaBanco = (horarioString, dataOpcional, retornaDataObjeto) => {
  let data = moment()
  if (dataOpcional) {
    data = moment(dataOpcional)
  }
  data = data.set({hour: 0, minute: 0, second: 10})
  let arrayHoras = []
  if (horarioString.length > 2) {
    arrayHoras = horarioString.split(':')
    data.set({hour: arrayHoras[0], minute: arrayHoras[1]})
  } else {
    data.set({hour: horarioString})
  }

  if (retornaDataObjeto) {
    return data
  }

  return data.format('YYYY-MM-DDTHH:mm:ss.SSS\\Z')
}

const converteHorarioBancoParaInputText = (horarioBancoDados) => {
  return moment(horarioBancoDados).format('HH:mm:ss')
}

const converteFormatoBrasilParaAmericano = (dataFormatoBR) => {
  const dataArray = dataFormatoBR.split('/')
  return `${dataArray[2]}-${dataArray[1]}-${dataArray[0]}`
}

const localeStringToISODate = (dateString) => moment(dateString, 'DD/MM/YYYY').toISOString()

const formatarDataPadraoBancoDados = function(dateString) {
  return dateString.format('YYYY-MM-DD')
}

const formatarDataPadraoBrasileiro = function(dateString) {
  return dateString.format('DD/MM/YYYY')
}

const firstDayOfWeek = function(dateString) {
  return moment(dateString, 'DD/MM/YYYY').startOf('week').add(1, 'day')
}

const lastDayOfWeek = function(dateString) {
  return moment(dateString, 'DD/MM/YYYY').endOf('week')
}

const nextWeek = function(dateString) {
  return moment(dateString, 'DD/MM/YYYY').add(1, 'week')
}

const previousWeek = function(dateString) {
  return moment(dateString, 'DD/MM/YYYY').subtract(1, 'week')
}

const retornarHora = function(dateString) {
  return moment(dateString).format('HH:mm')
}

export {
  isISODate,
  dateToString,
  dateToStringConvert,
  stringToDateConvert,
  dateToDBStringConvert,
  stringToISODate,
  stringToISODateMinMax,
  getDateFromISO,
  dateToCompare,
  dayAndMonthToCompare,
  ISOToString,
  diffInDays,
  beginOfDay,
  endOfDay,
  turnDatetime,
  horaAMaiorHoraB,
  converteHorarioParaBanco,
  converteHorarioBancoParaInputText,
  converteFormatoBrasilParaAmericano,
  idade,
  localeStringToISODate,
  firstDayOfWeek,
  lastDayOfWeek,
  nextWeek,
  previousWeek,
  formatarDataPadraoBrasileiro,
  formatarDataPadraoBancoDados,
  retornarHora,
  converterDataDoBancoParaString
}
