import Request from "@/utils/request";

export default {

    // Lista os dados para preenchimento do relatório
    listar({state, commit}) {
        commit('SET_ESTA_CARREGANDO', true)
        commit('SET_RESUMO', null)
        commit('SET_LISTA', [])

        let params = ''
        let filtros = state.filtros

        for(const [key, value] of Object.entries(filtros)) {
            if(value){
                params += (params ? '&' : params) + key + '=' + value
            }
        }
        return new Promise((resolve, reject) => {
            Request.get('/relatorios/descontos' + (params ? '?' + params : ''), null).then(
                response => {
                    commit('SET_LISTA', response.body)
                    commit('SET_RESUMO', response.body)
                    commit('SET_ESTA_CARREGANDO', false)
                    resolve()
                }
            ).catch(
                error => {
                    commit('SET_ESTA_CARREGANDO', false)
                    reject(error)
                }
            )
        })
    },

    // Carrega os semestres cadastrados no banco de dados
    listarSemestres({state, commit}) {
        commit('SET_ESTA_CARREGANDO', true)

        return new Promise((resolve, reject) => {
            Request.get('/semestre/listar', null).then(
                response => {
                    const itens = response.body.corpo.itens
                    commit('SET_SEMESTRES', itens)
                    commit('SET_ESTA_CARREGANDO', false)
                    resolve()
                }
            ).catch(
                error => {
                    commit('SET_ESTA_CARREGANDO', false)
                    reject(error)
                }
            )
        })
    },

    // Carrega as formas de pagamento cadastradas no banco de dados
    listarFormaPagamento({state, commit}) {
        commit('SET_ESTA_CARREGANDO', true)
        
        return new Promise((resolve, reject) => {
            Request.get('/forma_pagamento/listar', null).then(
                response => {
                    commit('SET_FORMA_PAGAMENTO', response.body.corpo.itens)
                    commit('SET_ESTA_CARREGANDO', false)
                    resolve()
                }
            ).catch(
                error => {
                    commit('SET_ESTA_CARREGANDO', false)
                    reject(error)
                }
            )
        })
    },

    atualizarFiltros({state, commit}) {
        return state.filtros;
    }
}