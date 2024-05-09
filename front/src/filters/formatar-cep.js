const regexp = /^(\d{5})(\d{3}).*/
const formatarCep = value => value ? value.replace(regexp, '$1-$2') : ''

export default formatarCep
