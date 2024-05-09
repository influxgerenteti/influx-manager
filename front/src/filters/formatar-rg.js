const regexp = /^(\d{2})(\d{3})(\d{3})(\d{2}).*/
const formatarRG = value => value ? value.replace(regexp, '$1.$2.$3-$4') : ''

export default formatarRG
