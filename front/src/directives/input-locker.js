import EventBus from '../utils/event-bus'
/** *  Modelo de aplicação da diretiva *
 *
 * <input v-input-locker="{permissao: permissoes['NOME_DA_REGRA_DE_PERMISSAO'], callBack: contexto this da tela (criar uma variável no Data com o nome input_locker_callback)}" >
 * callBack é opicional
 *
 * objeto de teste:
 * permissoes['NOME_DA_REGRA_DE_PERMISSAO'] = { id: 9, descricao: "NOME_DA_REGRA_DE_PERMISSAO", permissao_descricao: "Descricao permissao.", solicita_login_superior: true, possui_permissao: false }
 *
 ** * TABELAS DO BANCO PARA CRIAR REGRA
 *    acao_sistema
 *    acao_sistema_modulo
 *
 * */
const lock = new DOMParser().parseFromString('<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="lock" class="svg-inline--fa fa-lock fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M400 224h-24v-72C376 68.2 307.8 0 224 0S72 68.2 72 152v72H48c-26.5 0-48 21.5-48 48v192c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V272c0-26.5-21.5-48-48-48zm-104 0H152v-72c0-39.7 32.3-72 72-72s72 32.3 72 72v72z"></path></svg>', 'text/xml').documentElement
let acaoSistema = {}

const inputLocker = {
  bind: (el, binding) => {
    build(el, binding)
  },

  unbind: (el, binding) => {
    // builtIds[el.id] = false
    el.done = undefined
    el.lockedPass = undefined
  },

  update: (el, binding) => {
    build(el, binding)
  }
}

// const builtIds = {}

function build (el, binding) {
  const item = binding.value
  // if (!el.done && item && (!item.permissao || !item.permissao.possui_permissao) && !el.classList.contains('locked-el')) {
  if (!el.done && item && item.permissao && !item.permissao.possui_permissao && !el.classList.contains('locked-el')) {
    const box = document.createElement('div')

    if (!el.classList.contains('custom-checkbox')) {
      box.classList.add('unlock-input')
    } else {
      box.classList.add('unlock-checkbox')
    }

    box.setAttribute('data-id', item.permissao.descricao)

    el.lockedPass = true

    setTimeout(() => {
      el.insertAdjacentElement('afterend', box)
      box.insertAdjacentElement('afterbegin', el)

      el.disabled = true

      const lockIcon = lock.cloneNode(true)
      el.classList.add('locked-el')

      if (!el.classList.contains('icone-link')) {
        box.insertAdjacentElement('afterbegin', lockIcon)
      } else {
        box.classList.add('unlock-icone-link')

        const tooltip = el.getAttribute('data-title')

        if (tooltip) {
          box.setAttribute('data-title', tooltip)
          box.parentElement.style.overflow = 'visible'
        }
      }

      box.addEventListener('click', (e) => unlockRequestModal(e, el, item.permissao.descricao, item.callBack))
      box.addEventListener('keydown', (e) => unlockVerify(e, el))
      box.addEventListener('paste', (e) => unlockVerify(e, el))
      el.done = item.permissao.solicita_login_superior !== undefined

      acaoSistema[el.id] = item.permissao.id
      el.disabled = !item.permissao.possui_permissao && item.permissao.solicita_login_superior
    }, 0)
  }
}

function unlockRequestModal (e, el, dataId, callBack) {
  if (el.disabled) {
    EventBus.$emit('unlockRequestModal', { dataId: dataId, event: e, element: el, acao_sistema: acaoSistema[el.id], show: true, callBack: callBack })
  }
}

function unlockVerify (e, el) {
  if (e.target.lockedPass) {
    e.preventDefault()
  }
}

export default inputLocker
