const regexp = /^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/
const formatarCNPJ = value => value ? value.replace(regexp, '$1.$2.$3/$4-$5') : ''

export default formatarCNPJ
