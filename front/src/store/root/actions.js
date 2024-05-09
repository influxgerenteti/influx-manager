import Request from '../../utils/request'
import IndexedDB from '../../utils/indexed-db'

export default {
  listarMenus ({commit}) {
    commit('SET_ESTA_CARREGANDO', true)

    return new Promise((resolve, reject) => {
      Request.getProd('/modulo/listar-menu')
        .then(response => {
          commit('setListaMenus', response.body.corpo.modulos)
          commit('setListaFavoritos', response.body.corpo.favoritos)

          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch(reject)
    })
  },

  criarFavorito ({state}) {
    return new Promise((resolve, reject) => {
      Request.post('/favorito/criar', state.objFavorito)
        .then(() => {
          resolve()
        })
        .catch(error => {
          reject(error)
        })
    })
  },

  removerFavorito ({state}) {
    return new Promise((resolve, reject) => {
      Request.delete(`/favorito/remover/${state.favoritoSelecionadoId}`)
        .then(resolve)
        .catch(reject)
    })
  },

  salvarLogin ({commit}, usuario) {
    return new Promise(resolve => {
      IndexedDB.open('influx-manager').then(() => {
        const franqueadaLogada = usuario.franqueadas.find((fran) => {
          return fran.id === usuario.franqueadaSelecionada
        })
        usuario.logadoFranqueadora = franqueadaLogada && franqueadaLogada.franqueadora
        IndexedDB.save('usuarioLogado', usuario)
        commit('setUsuarioLogado', usuario)
        resolve()
      })
    })
  },

  fazerLogout ({commit}) {
    return new Promise(resolve => {
      commit('setUsuarioLogado', null)
      commit('setMenuCarregado', false)

      IndexedDB.open('influx-manager').then(db => {
        IndexedDB.clear('usuarioLogado')
        resolve()
      })
    })
  },

  validarAcessoUsuario ({ dispatch }) {
    return new Promise(resolve => {
      Request.get('/usuario/validar-acesso/usuario')
        .then(response => {
          dispatch('salvarLogin', response.body.corpo.usuario)
        })
    })
  },

  logAcesso ({state}) {
    return new Promise(resolve => {
      const data = {
        info_evento: JSON.stringify({
          'Módulo': state.rotaAtual.name,
          'URL': state.rotaAtual.fullPath,
          'Parâmetros': state.rotaAtual.params
        }),
        tipo_evento: 'A'
      }

    //   Request.post('/log/criar', data)
    //     .then(resolve)
    //     .catch(console.error)
     })
  },

  getState({state}) {
    return state;
  }
}
