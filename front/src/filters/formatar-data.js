import moment from 'moment'

const formatarData = value => {
  if (!value) {
    return ''
  }

  return moment(value).format('DD/MM/YYYY')
}

export default formatarData
