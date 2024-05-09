const formatarDataHHMM = value => {
    if (!value) {
      return ''
    }
  
    // Divide o valor em horas, minutos e segundos
    let [horas, minutos, segundos] = value.split(':')
  
    // Retorna a hora formatada como uma string no formato HH:mm
    return `${horas}:${minutos}`
  }
  
  export default formatarDataHHMM