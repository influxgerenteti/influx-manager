import Request from "@/utils/request";

export default {
  // Lista os dados para preenchimento do relatório
  listar({ state, commit }) {
    commit("SET_ESTA_CARREGANDO", true);

    let params = state.parametros;
    console.log(params);
    return new Promise((resolve, reject) => {
      Request.get("relatorios/contatos" + (params ? "?" + params : ""), null)
        .then((response) => {
          commit("SET_LISTA", response.body);
          commit("SET_ESTA_CARREGANDO", false);
          commit("SET_RESUMO", response.body);
          commit("SET_GERAL", response.body);
          commit("SET_CONTATO", response.body);
          resolve();
        })
        .catch((error) => {
          commit("SET_ESTA_CARREGANDO", false);
          reject(error);
        });
    });
  },
};
