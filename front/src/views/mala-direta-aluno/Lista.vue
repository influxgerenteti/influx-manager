<template>
   <div class="animated fadeIn wrapper-table-scroll">
    <div class="no-print">
      <b-card no-body>
        <b-tabs card>
          <b-tab
            title="Filtros"
            :class="filtroVisivel ? null : 'collapsed'"
            :aria-expanded="filtroVisivel ? 'true' : 'false'"
            aria-controls="collapse-4"
            @click="filtroVisivel = !filtroVisivel"
            active
          >
            <b-card-text>
              <div class="filtro-avancado">
                <b-collapse
                  id="collapse-4"
                  v-model="filtroVisivel"
                  class="mt-2"
                >
                  <div class="form-group row">
                    <div class="col-md-3">
                      <label for="livros" class="col-form-label">Livro</label>
                      <g-select-livro id="livros" v-model="filtros.livro" />
                    </div>
                    <div class="col-md-3">
                      <label class="col-form-label" for="turma">Turma</label>
                      <g-select-turma
                        id="turma"
                        v-model="filtros.turma"
                        class="valid-input"
                      />
                    </div>
                    <div class="col-md-6">
                      <label
                        v-help-hint="'filtro_avancado_mala_direta'"
                        for="data_inicial"
                        class="col-form-label"
                        >Término do Contrato</label
                      >
                      <g-data
                        :validarMesmoAno="true"
                        :periodo="'mes_anterior'"
                        @dataDe="filtros.data_termino_contrato_inicio = $event"
                        @dataAte="filtros.data_termino_contrato_fim = $event"
                      >
                      </g-data>
                    </div>
                  </div>

                <!--  <div class="form-group row">
                    <div class="col-md-auto">
                      <label
                        v-help-hint="'filtro-mala-direta-tipo_responsavel'"
                        for="tipo_responsavel"
                        class="col-form-label d-block"
                        >Pesquisar por</label
                      >
                      <b-form-group>
                        <b-form-radio-group
                          id="tipo_responsavel"
                          v-model="filtros.tipo_responsavel"
                          :options="listaTipo"
                          name="tipo_responsavel"
                          class="checkbtn-line"
                        />
                      </b-form-group>
                    </div>
                  </div> -->

                </b-collapse>
              </div>

              <div class="mb-2 mt-2 d-flex justify-content-end">
                <div class="col-md-auto">
                  <button class="btn btn-cinza btn-block text-uppercase" @click="exportarParaHTML()">
                    <font-awesome-icon icon="print" /> IMPRIMIR</button>
                </div>

                <div class="col-md-auto">
                  <button class="btn btn-cinza btn-block text-uppercase" @click="exportarParaExcel()">
                    <font-awesome-icon icon="file-code" /> Exportar para Excel</button>          
                </div>
                <div class="col-md-auto">
                  <b-btn
                    :disabled="!podeGerarRelatorio()"
                    class="btn btn-cinza btn-block text-uppercase"
                    @click="abrirRelatorio()"
                  >
                    Gerar Mala Direta
                  </b-btn>
                </div>
              </div>
            </b-card-text>
          </b-tab>
        </b-tabs>
      </b-card>
    </div>
    <div v-if="estaCarregando" class="d-flex h-100">
      <load-placeholder :loading="estaCarregando" />
    </div>

    <div class="tabela-wrapper">
      <b-table
        v-if="!estaCarregando"
        class="mala-direta"
        :items="lista"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        small
        hover
        outlined
        striped
        show-empty
        fixed-header
        sort-icon-right
      >
        <template #empty>
          <h6>Nenhum registro a ser exibido.</h6>
        </template>

        <template #table-busy>
          <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Carregando Dados...</strong>
          </div>
        </template>
      </b-table>
    </div>
  </div>
</template>


<script>
import * as XLSX from 'xlsx';
import { mapState, mapActions, mapMutations } from "vuex";
import { beginOfDay, endOfDay, moment } from "../../utils/date";
import open from "../../utils/open";

export default {
  name: "ListaMalaDiretaAluno",
  data() {
    return {
      filtroVisivel: true,
       
      sortBy: "aluno",
      sortDesc: false,
      listaTipo: [
        { text: "Todos", value: 0 },
        { text: "Apenas os que possuem responsável financeiro.", value: 1 },
        { text: "Apenas os que não possuem responsável financeiro.", value: 2 },
      ],
       exportFields: {
        'Aluno': 'aluno',
        'Curso': 'curso',
        'Responsável Financeiro': 'responsavel_financeiro',
        'Livro': 'livro'
      },
    };
  },
  computed: {
    ...mapState("malaDiretaAluno", ["filtros", "lista", "estaCarregando"]),
  },

  mounted() {
    this.SET_LISTA([]);
  },
  methods: {
    ...mapActions("malaDiretaAluno", ["listar"]),
    ...mapMutations("malaDiretaAluno", ["SET_LISTA", "SET_PARAMETROS"]),

    podeGerarRelatorio() {
      return true;
    },

    exportarParaExcel() {
      
      const wb = XLSX.utils.book_new();

      // Criar uma matriz para armazenar os dados formatados
      const dadosFormatados = [];

      // Adicionar o título do relatório e o cabeçalho das colunas
      dadosFormatados.push(['','Relatório de Geração de Mala Direta']);
      dadosFormatados.push(['']);
      dadosFormatados.push(['Aluno', 'Representante', 'Curso', 'Livro', 'Fone', 'Email']);
      dadosFormatados.push(['']);

      // Iterar sobre os dados do objeto e adicionar à matriz
      this.lista.forEach(item => {
        const row = [
          item.aluno,
          item.responsavel_financeiro,
          item.curso,
          item.livro,
          item.telefone_preferencial,
          item.email_preferencial
        ];
        dadosFormatados.push(row);
      });
      
      // Criar a planilha a partir dos dados formatados
      const ws = XLSX.utils.aoa_to_sheet(dadosFormatados);
      
      // Adicionar estilos CSS diretamente à planilha (exemplo)
      ws["B1"].s = { font: { bold: false } }; 
      
      // Adicionar a planilha ao livro
      XLSX.utils.book_append_sheet(wb, ws, 'Planilha 1');

      // Salvar o arquivo Excel
      XLSX.writeFile(wb, 'Relatorio_Geracao_Mala_Direta.xlsx');
    },

    exportarParaHTML() {
    // Crie uma string HTML com os dados do seu relatório
        let htmlContent = `
            <html>
                <head>
                    <title>Título do Relatório</title>
                    <style>
                        /* Adicione estilos CSS conforme necessário */
                        table {
                            border-collapse: collapse;
                            width: 100%;
                        }
                        th, td {
                            border: 1px solid black;
                            padding: 8px;
                            text-align: left;
                        }
                        th {
                            background-color: #f2f2f2;
                        }
                    </style>
                </head>
                <body>
                    <h1>Título do Relatório</h1>
                    <table>
                        <tr>
                            <th>Aluno</th>
                            <th>Responsavel</th>
                            <th>Curso</th>
                            <th>Livro</th>
                            <th>Fone Preferêncial</th>
                            <th>Email Preferêncial</th>
                        </tr>`;

    // Adicione os dados do seu relatório à string HTML
    this.lista.forEach(item => {
        htmlContent += `<tr>`;
        htmlContent += `<td>${item.aluno}</td>`;
        htmlContent += `<td>${item.responsavel_financeiro}</td>`;
        htmlContent += `<td>${item.curso}</td>`;
        htmlContent += `<td>${item.livro}</td>`;
        htmlContent += `<td>${item.telefone_preferencial}</td>`;
        htmlContent += `<td>${item.email_preferencial}</td>`;
        htmlContent += `</tr>`;
    });

        // Feche as tags HTML
        htmlContent += `
                    </table>
                </body>
            </html>`;

        // Abra uma nova janela ou guia do navegador com o conteúdo HTML
        const newWindow = window.open('');
        newWindow.document.write(htmlContent);
        newWindow.document.close();

        // Permita que os usuários imprimam o conteúdo HTML
        newWindow.print();
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listar();
    },

       converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        tipo_responsavel: form.tipo_responsavel ? form.tipo_responsavel : null,
        turma: form.turma ? form.turma : null,
        livro: form.livro ? form.livro : null,
        data_termino_contrato_inicio: form.data_termino_contrato_inicio
          ? beginOfDay(form.data_termino_contrato_inicio)
          : null,
        data_termino_contrato_fim: form.data_termino_contrato_fim
          ? endOfDay(form.data_termino_contrato_fim)
          : null,
      };

      let dadosArray = [];
      for (let key in dados) {
        if (dados[key] !== null) {
          dadosArray.push(`${key}=${dados[key]}`);
        }
      }

      return dadosArray.join("&");
    },
  },
};
</script>

<style scoped>
.table {
  margin-bottom: 0 !important;
}
.tabela-wrapper {
  overflow-y: scroll;
  min-height: auto;
}
.tabela-wrapper::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}
.tabela-wrapper::-webkit-scrollbar-track {
  background: #f1f1f1;
}
.tabela-wrapper::-webkit-scrollbar-thumb {
  background: #888;
}
.fadeIn {
  max-width: 98vw;
  overflow: hidden;
}
#filtros-rapidos,
#filtros-avancados {
  transition: all 0.1s;
}
.mala-direta >>> tr > th,
.mala-direta >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.mala-direta >>> table thead {
  position: sticky;
  top: -1px;
}
.filtro-avancado .form-group {
  margin-bottom: 1rem;
}
.filtro-header {
  color: #4a4a4a;
}
.btn.filtro-selecionado:not(:disabled):not(.disabled) {
  color: #151b1e;
  background-color: #fff;
}
.filtro-avancado .input-group-text {
  border: 0;
  background-color: #e5e5e5;
}
#mala-direta {
  overflow: visible;
}
@media (max-width: 992px) {
  .tabela-wrapper {
    margin-bottom: 8%;
}
}
@media print {
  .tabela-wrapper {
    overflow: hidden;
  }
}
</style>