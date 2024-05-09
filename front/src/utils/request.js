import Vue from 'vue'
import EventBus from './event-bus'
import IndexedDB from './indexed-db'
import router from '../router'
import store from '../store'
import {rotasErros, rotasAbertasAPI} from './router-config'
// import {host} from './host-url'
var host = process.env.VUE_APP_HOST;

let dadosCarregados = false

EventBus.$on('usuarioLogado', user => {
  dadosCarregados = !!user
})

const options = {
  emulateJSON: true
}

function parseURL (url) {
  const initialSlash = (url && url.length > 0 && url[0] !== '/') ? '/' : ''
  // console.log(host)
  return `${host}/api${initialSlash}${url}`
}
function parseURLProd (url) {
  const initialSlash = (url && url.length > 0 && url[0] !== '/') ? '/' : ''
  // console.log(host)
  return `${host}/api${initialSlash}${url}`
}

function parseData (data) {
  let parseToFormData = false
  for (let key in data) {
    if (data[key] && data[key].constructor && data[key].constructor.name === 'File') {
      parseToFormData = true
      break
    }
  }

  options.emulateJSON = parseToFormData === false

  return parseToFormData ? parseFormData(data) : data
}

function parseFormData (data) {
  const formData = new window.FormData()

  for (let key in data) {
    if (data[key] === '' || data[key] === null) {
      delete data[key]
    } else if (data[key] === true) {
      data[key] = 1
    } else if (data[key] === false) {
      data[key] = 0
    }

    if (data[key] instanceof Array) {
      const arr = data[key]
      for (var i = 0; i < arr.length; i++) {
        formData.append(key + '[]', arr[i])
      }
    } else if (data[key] !== undefined) {
      formData.append(key, data[key])
    }
  }

  return formData
}

function canMakeRequest (route) {
  return dadosCarregados || rotasAbertasAPI.find(r => route.endsWith(r))
}

function catchRequestErrors (error, reject, suppressErrors = false, extraOptions = {}) {
  if (suppressErrors === false) {
    console.error(error)
  }

  const rotaErro = rotasErros[error.status]

  if (rotaErro && error.status === 401) {
    if (suppressErrors === false) {
      EventBus.$emit('criarAlerta', {
        tipo: error.status > 500 ? 'E' : 'A',
        mensagem: rotaErro.mensagem
      })
    }

    if (rotaErro.redirecionar && extraOptions.redirecionar !== false) {
      IndexedDB.open('influx-manager').then(db => {
        IndexedDB.clear('usuarioLogado')
      })

      store.commit('login/setMensagemErro', error.body.mensagem)
      store.state.root.menuCarregado = false
      router.replace(rotaErro.redirecionar)
    }
  }

  reject(error)
}

function selectFranqueada (franqueadaSelecionada, usuario) {
  if (franqueadaSelecionada) {
    return franqueadaSelecionada
  }

  if (usuario) {
    return usuario.franqueadaSelecionada 
    // return usuario.franqueadaSelecionada || usuario.franqueada_padrao.id
  }

  return null
}

function before (request) {
  if (request.method === 'POST' && typeof request.body === 'string') {
    request.body = request.body.replace(/true/g, '1').replace(/false/g, '0')
  }
}

function criarRequisicao (request, url, data, options = null, resolve, reject, suppressErrors = false, extraOptions = {}) {
  // console.log('DEBUG')
  // console.log(url)
  Vue.http.headers.common['Versao'] = process.env.VUE_APP_VERSION || null
  Vue.http.headers.Versao = process.env.VUE_APP_VERSION || null

  if (canMakeRequest(url)) {
    const usuario = store.state.root.usuarioLogado
    const franqueadaSelecionada = store.state.root.franqueadaSelecionada
    const franqueada = selectFranqueada(franqueadaSelecionada, usuario)

    if (data) {
      if (!(data instanceof FormData)) {
        for (let index in data) {
          if (data[index] === '' || data[index] === null || data[index] === undefined) {
            delete data[index]
            continue
          }

          if (data[index] === true) {
            data[index] = 1
            continue
          }

          if (data[index] === false) {
            data[index] = 0
            continue
          }
        }
      }
    } else {
      data = {}
    }

    Vue.http.headers.common['Franqueada'] = `${franqueada}` || null
    
    // console.log(request)
    
    // request.headers.add('Versao', process.env.VUE_APP_VERSION || null);
    if (data instanceof FormData) {
      data.append('franqueada', franqueada || null)
    } else {
      data.franqueada = franqueada || null
    }

    const dataToSend = request === 'get' ? {params: data} : data

    if (store.state.root.rotaAtual) {
      const matchedRoute = store.state.root.rotaAtual.matched
      if (matchedRoute && matchedRoute.length) {
        let path = ''
        if (matchedRoute[0].path !== '') {
          path = matchedRoute[0].path
        } else if (matchedRoute[1] && matchedRoute[1].path !== '') {
          path = matchedRoute[1].path
        }

        Vue.http.headers.common['URLModulo'] = path
      }
    }

    // console.log(request)
    
    return Vue.http[request](url, dataToSend, {...options, before})
      .then(resolve)
      .catch((error) => {
        if (error.status === 404) {
          router.replace('/404')
          return
        }
        if (error.status === 403) {
          router.replace('/403')
          return
        }
        catchRequestErrors(error, reject, suppressErrors, extraOptions)
      })
  }

  // setTimeout(() => {
  //   criarRequisicao(request, url, data, options, resolve, reject, suppressErrors, extraOptions)
  // }, 100)
}

export default {
  get (url, data) {
    return new Promise((resolve, reject) => {
      criarRequisicao('get', parseURL(url), data, null, resolve, reject)
    })
  },
  getProd(url, data) {
    return new Promise((resolve, reject) => {
      criarRequisicao('get', parseURLProd(url), data, null, resolve, reject)
    })
  },
  post (url, data, suppressErrors, extraOptions = {}) {
    return new Promise((resolve, reject) => {
      criarRequisicao('post', parseURL(url), parseData(data), options, resolve, reject, suppressErrors, extraOptions)
    })
  },
  patch (url, data) {
    return new Promise((resolve, reject) => {
      criarRequisicao('patch', parseURL(url), parseData(data), options, resolve, reject)
    })
  },
  put (url, data) {
    return new Promise((resolve, reject) => {
      criarRequisicao('put', parseURL(url), parseData(data), options, resolve, reject)
    })
  },
  delete (url, data) {
    return new Promise((resolve, reject) => {
      criarRequisicao('delete', parseURL(url), data, options, resolve, reject)
    })
  }
}
