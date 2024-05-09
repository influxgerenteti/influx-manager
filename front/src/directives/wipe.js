const svg = new DOMParser().parseFromString('<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="times-circle" class="svg-inline--fa fa-times-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z"></path></svg>', 'text/xml').documentElement

const wipe = {
  bind: (el, binding, vnode) => {
    build(el, binding.value, vnode)

    el.addEventListener('blur', (e) => buttonState(el))
  }
}

function build (el, value, vnode) {
  let btn = document.createElement('div')
  btn.classList.add('btn-wipe')

  btn.addEventListener('click', (e) => {
    vnode.context.$emit('input', '')

    //  TODO: melhorar forma de alterar o valor do input no futuro, acessando diretamente por esta função
    value.func({propName: value.propName, index: value.index})
    el.classList.remove('visible-wipe')
  })

  const icon = svg.cloneNode(true)

  btn.appendChild(icon)
  setTimeout(function () {
    el.after(btn)
    buttonState(el)
  }, 500)
}

function buttonState (el) {
  if (el.value !== '0,00') {
    el.classList.add('visible-wipe')
  } else {
    el.classList.remove('visible-wipe')
  }
}

export default wipe
