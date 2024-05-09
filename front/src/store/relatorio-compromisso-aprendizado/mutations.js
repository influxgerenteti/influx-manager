export default {
  SET_LISTA(state, lista) {
    state.lista = lista;
  },
  SET_ESTA_CARREGANDO(state, value) {
    state.estaCarregando = value;
  },
  SET_PARAMETROS(state, value) {
    state.parametros = value;
  },
  SET_RESUMO(state, value) {
    let items = [];


    function validaCompromisso(el) {
      if (el.aulas <= 0) {
        return 2;
      }
      if (el.tarefas_no_prazo < 85 || el.Frequencia_minima < 85) {
        return 0;
      }
      let menor_nota_mid_term = 0;
      let maior_nota_mid_term = 0;
      if (
        parseFloat(el.nota_mid_term_test) >
        parseFloat(el.nota_mid_term_composition)
      ) {
        maior_nota_mid_term = parseFloat(el.nota_mid_term_test);
        menor_nota_mid_term = parseFloat(el.nota_mid_term_composition);
      } else {
        menor_nota_mid_term = parseFloat(el.nota_mid_term_test);
        maior_nota_mid_term = parseFloat(el.nota_mid_term_composition);
      }
      if (el.nota_retake_mid_term_escrita) {
        if (parseFloat(el.nota_retake_mid_term_escrita) > menor_nota_mid_term) {
          menor_nota_mid_term = parseFloat(el.nota_retake_mid_term_escrita);
        }
      }
      let menor_nota_final = 0;
      let maior_nota_final = 0;

      if (
        parseFloat(el.nota_final_test) > parseFloat(el.nota_final_composition)
      ) {
        maior_nota_final = parseFloat(el.nota_final_test);
        menor_nota_final = parseFloat(el.nota_final_composition);
      } else {
        menor_nota_final = parseFloat(el.nota_final_test);
        maior_nota_final = parseFloat(el.nota_final_composition);
      }
      if (el.nota_retake_final_escrita) {
        if (parseFloat(el.nota_retake_final_escrita) > menor_nota_final) {
          menor_nota_final = parseFloat(el.nota_retake_final_escrita);
        }
      }
      if (el.nota_final_test && el.nota_mid_term_test) {
        if (
          (menor_nota_mid_term + maior_nota_mid_term) / 2 > 8.5 &&
          (menor_nota_final + maior_nota_final) / 2 > 8.5
        ) {
          return 1;
        }
      } else {
        if ((menor_nota_mid_term + maior_nota_mid_term) / 2 > 8.5) {
          return 1;
        }
      }
      return 0;
    }
    value.forEach((element) => {
      let aluno = element.aluno;
      let contato = element.contato;
      let livro = element.livro;
      let data_inicio_contrato = element.data_inicio_contrato;
      let total_ca =
        parseFloat(element.atividade_ca_entregue) /
        (parseFloat(element.atividade_ca_atrasada) +
          parseFloat(element.atividade_ca_entregue) +
          parseFloat(element.atividade_ca_nao_entregue));
      let total_ce =
        parseFloat(element.atividade_ce_entregue) /
        (parseFloat(element.atividade_ce_atrasada) +
          parseFloat(element.atividade_ce_entregue) +
          parseFloat(element.atividade_ce_nao_entregue));
      let tarefas_no_prazo = (total_ca + total_ce) / 2;
      let nota_final_escrita = element.nota_final_escrita;
      let Frequencia_minima =
        parseInt(element.presencas) +
        parseInt(element.reposicoes) -
        parseInt(element.faltas);
      let classificacao = validaCompromisso(element);
      let aulas = parseInt(element.aulas);

      let item = {
        aluno: aluno,
        aulas: aulas,
        contato: contato,
        livro: livro,
        data_inicio_contrato: data_inicio_contrato,
        nota_final_escrita: nota_final_escrita,
        Frequencia_minima: Frequencia_minima,
        tarefas_no_prazo: tarefas_no_prazo ? tarefas_no_prazo : 0,
        classificacao: classificacao,

      };
      items.push(item);


    });
    let contagemClassificacao = {
      0: 0,
      1: 0,
      2: 0
    };

    items.forEach((item) => {
      contagemClassificacao[item.classificacao]++;
    });

    
    let tabelaContagem = [{
        classificacao: "0",
        valor: contagemClassificacao[0]
      },
      {
        classificacao: "1",
        valor: contagemClassificacao[1]
      },
      {
        classificacao: "2",
        valor: contagemClassificacao[2]
      }
    ];
  
    state.resumo = items;
    state.tabelaContagem = tabelaContagem;



  },
};

