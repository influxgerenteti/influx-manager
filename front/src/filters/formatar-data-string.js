const formatarDataString = value => {
  if (!value) {
    return ''
  }

  return `${value}`.replace(/^(\d{4})-(\d{2})-(\d{2}).*/, '$3/$2/$1')
}

export default formatarDataString
