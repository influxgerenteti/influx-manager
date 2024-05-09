export default {
 
  SET_LISTA(state, lista) {
        let listaTemp = [];
        lista.forEach((element) => {
          let aluno = { ...element };
          if (aluno.alunos.contratos.length == 1) {
            aluno.alunos.contratos = aluno.alunos.contratos[0];
          } else if (aluno.alunos.contratos.lenght > 1) {
            let contratos = {};
            aluno.alunos.contratos.forEach((el) => {
              if (Object.keys(contratos).length == 0) {
                el = contratos;
              } else {
                if (el.sequencia_contrato > contratos.sequencia_contrato) {
                  el = contratos;
                }
              }
            });
         
            delete aluno.alunos.contratos;
        
            aluno.alunos["contrato"] = contratos;
          }
          listaTemp.push(aluno);
        });
        state.lista = listaTemp;
     
        let listaInteressadoTemp = [];
        lista.forEach((element) => {
          let aluno = { ...element };
          if (aluno.alunos.interessados.length == 1) {
            aluno.alunos.interessados = aluno.alunos.interessados[0];
          } else if (aluno.alunos.interessados.lenght > 1) {
            let interessados = {};
            aluno.alunos.interessados.forEach((el) => {
              if (Object.keys(interessados).length == 0) {
                el = interessados;
              } 
            });
         
            delete aluno.alunos.interessados;
        
            aluno.alunos["interessados"] = interessados;
          }
          listaInteressadoTemp.push(aluno);
        });
        state.lista = listaInteressadoTemp;
     
    
  },
  SET_ESTA_CARREGANDO(state, value) {
    state.estaCarregando = value;
  },
  SET_PARAMETROS(state, value) {
    state.parametros = value;
  },
};

