const regexp = /^(\d{2})(\d{4,})(\d{4}).*/
const formatarTelefone = value => value ? value.replace(regexp, '($1) $2-$3') : ''

export default formatarTelefone
