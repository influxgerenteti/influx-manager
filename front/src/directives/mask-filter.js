const maskFilter = (el, binding) => {
  // console.log(el)
  // console.log(binding)
  // el.addEventListener('keypress', (e) => unidade(e))

  el.addEventListener('keyup', (e) => unidade(e))
  el.addEventListener('blur', (e) => unidade(e))
}

function unidade (event) {
  const regex = new RegExp(/(\d{1,9})(,\d{1,6})?/g)

  const e = event.target.value
  let input = e.match(regex)

  if (input !== null && input.length > 0) {
    input = input[0]
    if (event.key === ',' && !input.includes(',')) {
      input += ','
    }
  } else {
    input = e.replace(/[\D\d]/g, '')
  }
  event.target.value = input
}

export default maskFilter
