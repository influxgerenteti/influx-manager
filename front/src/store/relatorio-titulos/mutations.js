export default {
  SET_LISTA(state, value) {
    state.lista = value;
  },
  SET_ESTA_CARREGANDO(state, value) {
    state.estaCarregando = value;
  },

  SET_EXCEL_LIST(state, value) {
    if (value.agrupado) {
      let listTemp = [];
      let tituloTemp = {};

      for (const [key, element] of Object.entries(value.data)) {
        tituloTemp = {};
        tituloTemp.nome_contato = element.nome_contato;
        tituloTemp.valor_pago = element.valor_pago;
        tituloTemp.valor_total = element.valor_total;
        element.data.forEach((titulo) => {
          tituloTemp.data_pagamento = titulo.data_pagamento;
          tituloTemp.data_vencimento = titulo.data_vencimento;
          tituloTemp.forma_cobranca = titulo.forma_cobranca;
          tituloTemp.forma_pagamento = titulo.forma_pagamento;
          tituloTemp.parcela = titulo.parcela;
          tituloTemp.situacao_titulo = titulo.situacao_titulo;
          tituloTemp.valor_pago = titulo.valor_pago;
          tituloTemp.valor_parcela_sem_desconto =
            titulo.valor_parcela_sem_desconto;

          listTemp.push(tituloTemp);
       
        });
      }

      state.excelList = listTemp;
    } else {
      state.excelList = value.data;
    }
  },
  SET_RESUMO(state, value) {
   
    let valor = 0;
    let nome = "";
    let listTemp = [];

    if (!value) {
      listTemp.push({
        situacao: "Liquidados",
        valor: 0,
      });
      listTemp.push({
        situacao: "Cancelados",
        valor: 0,
      });
      listTemp.push({
        situacao: "Pendentes",
        valor: 0,
      });
      listTemp.push({
        situacao: "Renegociados",
        valor: 0,
      });
      listTemp.push({
        situacao: "Vencidos",
        valor: 0,
      });
      state.resumo = value
      return
    }

    if (state.filtros.situacao.length === 0) {

      listTemp.push({
        situacao: "Liquidados",
        valor: value.LIQUIDADOS,
      });
      listTemp.push({
        situacao: "Cancelados",
        valor: value.CANCELADOS,
      });
      listTemp.push({
        situacao: "Pendentes",
        valor: value.PENDENTES,
      });
      listTemp.push({
        situacao: "Renegociados",
        valor: value.RENEGOCIADOS,
      });
      listTemp.push({
        situacao: "Vencidos",
        valor: value.VENCIDOS,
      });

      state.resumo = listTemp;
   
      
    } else {
      state.filtros.situacao.forEach((situacao) => {
        if (!value.agrupado) {
          if (situacao === "CAN") {
            nome = "Cancelados";
            valor = value.CANCELADOS;
          }
          if (situacao === "LIQ") {
            nome = "Liquidados";
            valor = value.LIQUIDADOS;
          }
          if (situacao === "PEN") {
            nome = "Pendentes";
            valor = value.PENDENTES;
          }
          if (situacao === "SUB") {
            nome = "Renegociados";
            valor = value.RENEGOCIADOS;
          }
          if (situacao === "VEN") {
            nome = "Vencidos";
            valor = value.VENCIDOS;
          }
          listTemp.push({ situacao: nome, valor: valor });
        }
      });
      state.resumo = listTemp;
  
    }
  },
};
