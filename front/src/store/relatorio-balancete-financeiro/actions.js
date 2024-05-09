import EventBus from "../../utils/event-bus";
import Request from "../../utils/request";

/**
 * Avaliar se é necessario, caso não seja, realizar os passos abaixo:
 * 1 - Remover este arquivo(deletar no caso)
 * 2 - Excluir referencia no index.js do modulo
 * 3 - Alterar o Lista.vue/Formulario.vue para remover a referencia desta ação
 **/
const url = "RelatorioBalanceteFinanceiro";

export default {
  listar({ state, commit }) {
    commit("SET_ESTA_CARREGANDO", true);

    let params = state.parametros;
    console.log(params)

    return new Promise((resolve, reject) => {
      Request.get("/relatorios/balancete_financeiro" + (params ? "?" + params : ""), null)
        .then((response) => {
          commit('SET_LISTA',response.body)
          commit('INCREMENTAR_PAGINA_ATUAL')
          commit('SET_ESTA_CARREGANDO', false)
          resolve()
        })
        .catch((error) => {
          commit("SET_ESTA_CARREGANDO", false);
          reject(error);
        });
    });
  },

};
