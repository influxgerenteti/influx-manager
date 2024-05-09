const regexp = /^(\d{3})(\d{3})(\d{3})(\d{2}).*/
const formatarCPF = value => value ? value.replace(regexp, '$1.$2.$3-$4') : ''

export default formatarCPF
