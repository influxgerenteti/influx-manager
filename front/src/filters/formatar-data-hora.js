import moment from 'moment'

const formatarDataHora = value => {
  if (!value) {
    return ''
  }

  return moment(value).format('DD/MM/YYYY HH:mm')
}

export default formatarDataHora
