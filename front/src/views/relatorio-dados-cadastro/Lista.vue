<template>
  <div class="animated fadeIn table-responsive wrapper-table-scroll">
    <div class="filtros-avancado body-sector">
      <div
        class="d-flex justify-content-between filtro-header head-content-sector"
      >
        <div>
       
          <div
            id="filtros"
            v-b-toggle.filtros-rapidos
            :class="{ 'filtro-selecionado': filtroSelecionado === 1 }"
            class="btn"
            aria-controls="filtros-rapidos"
            aria-expanded="false"
            @click="acaoBotaoFiltroRapido()"
          >
            Filtro Rápido
          </div>
          <div
            id="filtros"
            v-b-toggle.filtros-avancados
            :class="{ 'filtro-selecionado': filtroSelecionado === 2 }"
            class="btn"
            aria-controls="filtros-avancados"
            aria-expanded="true"
            @click="acaoBotaoFiltroAvancado()"
          >
            Avançado
          </div>

      
        </div>
      </div>
      <b-collapse id="filtros-rapidos" accordion="filtros" visible>
        <!-- <b-collapse id="filtros-rapidos" v-model="filtroRapido"> -->
        <form class="p-2" @submit.prevent="buscaRapida = true">
          <div class="form-group row mb-0" v-if="lista && !estaCarregando">
            <div class="col-auto">
              <label
                v-help-hint="'filtro-turma_situacao_rapido'"
                for="situacao_rapido"
                class="col-form-label"
                >Situação</label
              >
              <div>
                <b-form-checkbox-group
                  id="situacao_rapido"
                  v-model="filtros.situacao"
                  :options="listaSituacao"
                  buttons
                  button-variant="cinza"
                  name="situacao_rapido"
                  class="checkbtn-line"
                />
              </div>
            </div>
            <div class="col-md-6">
              <label for="periodo" class="col-form-label">Período</label>
              <g-data
                ref="refLimparData"
                :periodo="'sem_data'"
                @dataDe="filtros.data_cadastro_inicial = $event"
                @dataAte="filtros.data_cadastro_final = $event"
              ></g-data>
            </div>

            <div class="col-md-3">
              <label
                v-help-hint="'filtroAvancado-aluno_classificacao'"
                for="classificacao"
                class="col-form-label"
                >Classificação Alu1no</label
              >
              <g-select
                id="classificacao"
                :options="listaClassificacaoAluno"
                v-model="filtros.classificacao_aluno"
                label="nome"
                :selection="setClassificacao"
              />
            </div>
            <div class="col-md-3">
              <label for="bairro" class="col-form-label">Bairro</label>
              <input
                id="bairro"
                v-model="filtros.bairro"
                type="text"
                class="form-control"
              />
            </div>
            <b-col class="col-md-3">
              <label for="licao" class="col-form-label"> Consultor </label>
              <g-select-consultor
                ref="refConsultorRapido"
                id="consultor"
                v-model="filtros.consultor"
                :multiTag="true"
              >
              </g-select-consultor>
            </b-col>
            <b-col class="col-md-3">
              <label for="licao" class="col-form-label"> Atendente </label>
              <g-select-atendente
                ref="refAtendenteRapido"
                id="atendente"
                v-model="filtros.atendente"
                :multiTag="true"
              >
              </g-select-atendente>
            </b-col>

            <div class="col-md-3">
              <label for="idade_minima" class="col-form-label"
                >Idade mínima</label
              >
              <input
                v-mask="'###'"
                id="idade_minima"
                v-model="filtros.idade_minima"
                type="text"
                class="form-control"
              />
            </div>

            <div class="col-md-3">
              <label for="idade_maxima" class="col-form-label"
                >Idade máxima</label
              >
              <input
                v-mask="'###'"
                id="idade_maxima"
                v-model="filtros.idade_maxima"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-md-3">
              <label for="licao" class="col-form-label"> Curso </label>
              <g-select-curso
                ref="refCursoRapido"
                id="curso"
                v-model="filtros.curso"
                :multiTag="true"
              >
              </g-select-curso>
            </div>
          </div>
        </form>
      </b-collapse>
      <b-collapse id="filtros-avancados" accordion="filtros">
        <!-- <b-collapse id="filtros-avancados" v-model="filtroAvancado"> -->
        <div class="p-2" @submit.prevent="buscaRapida = false">
          <div class="form-group row mb-0">
            <div class="col-md-3">
              <label
                v-help-hint="'filtroAvancado-aluno_classificacao'"
                for="busca_classificacao"
                class="col-form-label"
                >Classificação Aluno</label
              >
              <g-select
                id="busca_classificacao"
                :options="listaClassificacaoAluno"
                v-model="filtros.classificacao_aluno"
                label="nome"
                track-by="id"
              />
            </div>

            <b-col class="col-md-3">
              <label for="licao" class="col-form-label"> Atendente </label>
              <g-select-atendente
                ref="refAtendente"
                id="consultor"
                v-model="filtros.atendente"
                :multiTag="true"
              >
              </g-select-atendente>
            </b-col>
            <div class="col-md-3">
              <label for="telefone_preferencial" class="col-form-label">Telefone Preferencial</label>
              <input
                id="telefone_preferencial"
                v-model="filtros.telefone_preferencial"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-md-3">
              <label for="endereco" class="col-form-label">Endereço</label>
              <input
                id="endereco"
                v-model="filtros.endereco"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-md-3">
              <label for="complemento" class="col-form-label"
                >Complemento</label
              >
              <input
                id="complemento"
                v-model="filtros.complemento"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-md-3">
              <label for="bairro" class="col-form-label">Bairro</label>
              <input
                id="bairro"
                v-model="filtros.bairro"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-md-3">
              <label for="cep" class="col-form-label">CEP</label>
              <input
                v-mask="'#####-###'"
                id="cep"
                v-model="filtros.cep"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-md-3">
              <label for="cidade" class="col-form-label">Cidade</label>
              <input
                id="cidade"
                v-model="filtros.cidade"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-md-3">
              <label
                v-help-hint="'form-endereco_estado'"
                for="estado"
                class="col-form-label"
                >Estado</label
              >
              <g-select
                id="estado"
                v-model="filtros.estado_uf"
                :options="listaEstadosSelect"
                :select="setEstado"
                label="nome"
                track-by="id"
              />
            </div>
            <b-col class="col-md-3">
              <label for="licao" class="col-form-label"> Consultor </label>
              <g-select-consultor
                ref="refConsultor"
                id="consultor"
                v-model="filtros.consultor"
                :multiTag="true"
              >
              </g-select-consultor>
            </b-col>

            <b-col class="col-md-3">
              <label for="data_inicial" class="col-form-label"
                >Data de Nascimento</label
              >

              <g-datepicker
                :element-id="'data_nascimento'"
                :value="filtros.data_nascimento"
                :selected="setDataNascimento"
              />
            </b-col>

            <div class="col-md-3">
              <label
                v-help-hint="'form-aluno_input_escolaridade'"
                for="input_escolaridade"
                class="col-form-label"
                >Escolaridade</label
              >
              <g-select
                id="input_escolaridade"
                v-model="filtros.escolaridade"
                :options="listaEscolaridades"
                :select="setEscolaridade"
                label="descricao"
                track-by="id"
              />
            </div>
            <div class="col-md-3">
              <label for="telefone_contato" class="col-form-label"
                >Telefone Contato</label
              >
              <input
                id="telefone_contato"
                v-model="filtros.telefone_contato"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-md-3">
              <label for="telefone_profissional" class="col-form-label"
                >Telefone Profissional</label
              >
              <input
                id="telefone_profissional"
                v-model="filtros.telefone_profissional"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-md-3">
              <label for="matricula" class="col-form-label"
                >Número da Matrícula</label
              >
              <input
                v-mask="'##########'"
                id="numero_matricula"
                v-model="filtros.codigo_matricula"
                type="text"
                class="form-control"
              />
            </div>
            <div class="col-md-3">
              <label for="fone_residencial" class="col-form-label"
                >Observação</label
              >
              <input
                id="observacao"
                v-model="filtros.observacao"
                type="text"
                class="form-control"
              />
            </div>
            <b-col md="3">
              <label
                v-help-hint="'filtroAvancado-aluno_responsavel_financeiro'"
                for="busca_responsavel_financeiro"
                class="col-form-label"
                >Responsável Financeiro</label
              >
              <typeahead
                ref="filtroFinanceiro"
                id="busca_responsavel_financeiro"
                :item-hit="setResponsavelFinanceiro"
                source-path="/api/pessoa/buscar_nome_contato"
                key-name="pessoa.nome_contato"
                v-model="filtros.responsavel_financeiro_pessoa"
              />
            </b-col>

            <b-col md="3">
              <label
                v-help-hint="'filtroAvancado-aluno_responsavel_didatico'"
                for="busca_responsavel_didatico"
                class="col-form-label"
                >Responsável Didático</label
              >
              <typeahead
                ref="filtroDidatico"
                id="busca_responsavel_didatico"
                :item-hit="setResponsavelDidatico"
                source-path="/api/pessoa/buscar_nome_contato"
                key-name="pessoa.nome_contato"
                v-model="filtros.responsavel_didatico_pessoa"
              />
            </b-col>
            <b-col md="3">
              <label
                v-help-hint="'filtroAvancado-aluno_sexo'"
                for="busca_sexo"
                class="col-form-label"
                >Sexo</label
              >
              <g-select
                id="busca_sexo"
                v-model="filtros.pessoa_sexo"
                :options="listaSexo"
                selection="setSexo"
                label="descricao"
                track-by="value"
          
              />
            </b-col>
            <div class="col-auto">
              <label
                v-help-hint="'filtro-turma_situacao_rapido'"
                for="situacao_rapido"
                class="col-form-label"
                >Situação</label
              >
              <div>
                <b-form-checkbox-group
                  id="situacao_rapido"
                  v-model="filtros.situacao"
                  :options="listaSituacao"
                  buttons
                  button-variant="cinza"
                  name="situacao_rapido"
                  class="checkbtn-line"
                />
              </div>
            </div>
            <div class="col-md-3">
              <label for="turma" class="col-form-label">Turma</label>
              <g-select-turma
                ref="limparTurma"
                id="turmaDescricao"
                v-model="filtros.turma"
              ></g-select-turma>
            </div>
            <b-col md="3">
              <label
                v-help-hint="'filtroAvancado-aluno_busca_cpf'"
                for="busca_cpf"
                class="col-form-label"
                >Cpf</label
              >
              <typeahead
                ref="filtroCpf"
                id="busca_cpf"
                :item-hit="setCpf"
                v-model="filtros.cpf"
                source-path="/api/pessoa/buscar"
              />
            </b-col>
            <div class="col-md-3">
              <label
                v-help-hint="'form-aluno_numero_identidade'"
                for="numero_identidade"
                class="col-form-label"
                >Identidade (RG)</label
              >
              <input
                id="numero_identidade"
                v-model="filtros.numero_identidade"
                type="text"
                class="form-control"
                maxlength="15"
              />
            </div>
          </div>
        </div>
      </b-collapse>
      <b-card-text>
        <div class="mb-2 mt-2 d-flex justify-content-end">
          <!-- <div class="col-md-auto" v-if="lista.length">
            <g-excel
              class="btn btn-cinza btn-block text-uppercase"
              :data="lista"
              :fields="exportFields"
              type="xls"
              name="relatorio-matriculas"
            >
              <font-awesome-icon icon="file-code" />
              Exportar para Excel
            </g-excel>
          </div> -->
          <div class="col-md-auto">
            <b-btn
              :disabled="!podeGerarRelatorio()"
              class="btn btn-cinza btn-block text-uppercase"
              @click="abrirRelatorio()"
            >
              Gerar relatório
            </b-btn>
          </div>
        </div>
      </b-card-text>
    </div>
    <div v-if="estaCarregando" class="d-flex h-100">
      <load-placeholder :loading="estaCarregando" />
    </div>
    <div v-if="!lista.length && !estaCarregando" class="busca-vazia">
      <p>Nenhum resultado encontrado.</p>
    </div>
<div class="tabela-wrapper">
    <div class="tabela-dados-cadastro" v-if="lista.length && !estaCarregando">
      <b-table
        small
        hover
        outlined
        bordered
        striped
        fixed-header
        sort-icon-right
        id="tabela-dados-cadastro"
   
        v-if="lista && !estaCarregando"
        :fields="fields"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :items="lista"
      >
        <template #cell(detalhes)="data">
          <div @click="setCadastro(data.item.id)">
            <router-link
              :to="{
                name: 'detalhes',
                params: { id: data.item.id },
                props: { item: data.item },
              }"
              target="_blank" 
            >
              <font-awesome-icon icon="eye" /> Detalhes

            </router-link>
          </div>
        </template>

        <template #cell(cep_endereco)="data">
          <span>{{ data.value | formatarCep }}</span>
        </template>
        <template #cell(telefone_contato)="data">
          <span>{{ data.value | formatarTelefone }}</span>
        </template>
           
      </b-table>
    </div>
    </div>
  </div>
</template>

<script>
import number from "@/directives/number";
import formatarTelefone from "@/filters/formatar-telefone";
import { mapState, mapActions, mapMutations } from "vuex";
import { dateToCompare } from "../../utils/date";

export default {
  name: "ListaRelatorioDadosCadastro",

  data() {
    return {
  
      selected: "",
      descricao: "",
      nome_contato: "",
      id: 0,
      idCadastro: 0,
      sortDesc: false,
      sortBy: "nome_contato",
      filtroSelecionado: null,
      data_aniversario_de: null,
      filtroVisivel: true,
      data_nascimento: null,
      exportFields: {},
      filtrosRapidos: true,
      filtroSelecionado: 1,
      filtroAvancado: false,
      listaSituacao: [
        { text: "Ativo", value: "ATI" },
        { text: "Inativo", value: "INA" },
        { text: "Trancado", value: "TRA" },
      ],
      listaSexo: [
        { value: null, descricao: "Selecione" },
        { value: "N", descricao: "Não Informar" },
        { value: "M", descricao: "Masculino" },
        { value: "F", descricao: "Feminino" },
        { value: "O", descricao: "Outro" },
      ],
      situacaoFiltro: [
        { value: null, descricao: "Selecione" },
        { value: "ATI", descricao: "Ativo" },
        { value: "INA", descricao: "Inativo" },
        { value: "TRA", descricao: "Trancado" },
      ],
      fields: [
        {
          key: "id",
          label: "id",
          sortable: true,
        },

        {
          key: "nome_contato",
          label: "Nome",
          sortable: true,
        },

        { key: "email_contato", label: "E-mail", sortable: true },

        { key: "telefone_contato", label: "Celular", sortable: true },
        { key: "endereco", label: "Endereço", sortable: true },
        { key: "numero_endereco", label: "n°", sortable: true },
        { key: "bairro_endereco", label: "Bairro", sortable: true },
        { key: "cep_endereco", label: "CEP", sortable: true },
        { key: "cidade.nome", label: "Cidade", sortable: true },
        { key: "estado.sigla", label: "UF", sortable: true },
        {
          key: "detalhes",
          label: "Detalhes",
          sortable: false,
          class: "no-print",
        },
      ],
    };
  },

  computed: {
    ...mapState("classificacaoAlunos", {
      listaClassificacaoAlunoRequisicao: "listaClassificacaoAluno",
    }),
    ...mapState("aluno", ["lista"]),
    ...mapState("estado", { listaEstados: "lista" }),
    ...mapState("relatorioDadosCadastro", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
    ...mapState("escolaridade", { listaEscolaridades: "lista" }),

    listaEstadosSelect: {
      get() {
        return [{ id: null, nome: "Selecione" }, ...this.listaEstados];
      },
    },

    listaEscolaridades: {
      get() {
        const lista = this.$store.state.escolaridade.lista || [];
        return [{ id: null, descricao: "Selecione" }].concat(lista);
      },
    },

    listaClassificacaoAluno: {
      get() {
        return [
          { id: null, nome: "Selecione" },
          ...this.listaClassificacaoAlunoRequisicao,
        ];
      },
    },
  },

  mounted() {
    this.SET_LISTA([]);
    this.classificacaoAluno();
    this.listarEstados();
    this.listarEscolaridades();
  },

  methods: {
    dateToCompare: dateToCompare,
    ...mapActions("estado", { listarEstados: "listar" }),
    ...mapActions("relatorioDadosCadastro", ["listar"]),
    ...mapMutations("relatorioDadosCadastro", [
      "SET_LISTA",
      "SET_PARAMETROS",
      "SET_ESTA_CARREGANDO",
      "SET_LIMPAR_ITEM_SELECIONADO"
    ]),
    ...mapActions("classificacaoAlunos", {
      classificacaoAluno: "getListaClassificacaoAluno",
    }),
    ...mapActions("escolaridade", { listarEscolaridades: "listar" }),
    ...mapMutations("aluno", [
      "SET_FILTRO_RESPONSAVEL_FINANCEIRO_PESSOA",
      "SET_FILTRO_RESPONSAVEL_DIDATICO_PESSOA",
    ]),

    setMes(value) {
      this.filtros.mes = value;
      this.filtrar();
    },

    setAno(value) {
      this.filtros.ano = value;
      this.filtrar();
    },

    acaoBotaoFiltroRapido() {
      this.limparFiltros();
    },

    acaoBotaoFiltroAvancado() {
      this.limparFiltros();
    },

    limparFiltros() {
      this.filtros.bairro = null;
      this.filtros.situacao = [];
      this.filtros.classificacao_aluno = "";
      this.$refs.refConsultorRapido.reset();
      this.$refs.refCursoRapido.reset();
      this.$refs.refAtendenteRapido.reset();
      this.filtros.idade_minima = "";
      this.filtros.idade_maxima = "";
      this.$refs.refLimparData.resetData();
      this.filtros.telefone = null;
      this.filtros.endereco = null;
      this.filtros.complemento = null;
      this.filtros.cep = null;
      this.filtros.cidade = null;
      this.filtros.estado_uf = null;
      this.filtros.data_nascimento = null;
      this.filtros.escolaridade = "";
      this.filtros.telefone_profissional = null;
      this.filtros.telefone_contato = null;
      this.filtros.codigo_matricula = null;
      this.filtros.observacao = null;
      this.filtros.responsavel_financeiro_pessoa = null;
      this.filtros.responsavel_didatico_pessoa = null;
      this.filtros.pessoa_sexo = null;
      this.filtros.situacao = [];
      this.$refs.limparTurma.removeItem();
      this.$refs.refConsultor.reset();
      this.$refs.refAtendente.reset();
      this.$refs.filtroCpf.resetSelection();
      this.$refs.filtroFinanceiro.resetSelection();
      this.$refs.filtroDidatico.resetSelection();
      this.filtros.numero_identidade = null;
    },

    setCadastro(value) {
      this.idAtual = value;
    },

    setDataNascimento(value) {
      this.filtros.data_nascimento = value;
    },
    setSexo(value) {
      this.filtros.sexo = value;
      console.log(value)
    },

    setEscolaridade(value) {
      this.filtros.escolaridade = value;
    },

    setEstado(value) {
      this.filtros.id = value;
    },

    setClassificacao(value) {
      console.log("entrou aqui", this.selection);
      this.filtros.classificacao_aluno = value.selection
    },

    setResponsavelFinanceiro(value) {
      this.filtros.responsavel_financeiro_pessoa = value;
    },

    setResponsavelDidatico(value) {
      this.filtros.responsavel_didatico_pessoa = value;
    },

    setCpf(value) {
      this.filtros.cpf = value;
    },

    podeGerarRelatorio() {
      // a função deve retornar um boolean indicando se existe

      return true;
    },

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listar();
      this.limparFiltros();
    },

    converterDadosParaLink() {
      const form = { ...this.filtros };

      const dados = {
        situacao: form.situacao ? form.situacao : null,
        data_cadastro_inicial: form.data_cadastro_inicial
          ? form.data_cadastro_inicial
          : null,
        data_cadastro_final: form.data_cadastro_final
          ? form.data_cadastro_final
          : null,
        classificacao_aluno: form.classificacao_aluno
          ? form.classificacao_aluno.id
          : null,
        bairro: form.bairro ? form.bairro : null,
        consultor: form.consultor ? form.consultor : null,
        atendente: form.atendente ? form.atendente : null,
        idade_minima: form.idade_minima ? form.idade_minima : null,
        idade_maxima: form.idade_maxima ? form.idade_maxima : null,
        curso: form.curso ? form.curso : null,

        telefone_preferencial: form.telefone_preferencial || null,
        endereco: form.endereco ? form.endereco : null,
        complemento: form.complemento ? form.complemento : null,
        bairro: form.bairro ? form.bairro : null,
        cep: form.cep ? form.cep : null,
        cidade: form.cidade ? form.cidade : null,
        estado_uf: form.estado_uf ? form.estado_uf.id : null,
        data_aniversario_de: form.data_aniversario_de || null,
        escolaridade: form.escolaridade ? form.escolaridade.id : null,
        telefone_contato: form.telefone_contato || null,
        telefone_profissional: form.telefone_profissional || null,
        codigo_matricula: form.codigo_matricula ? form.codigo_matricula : null,
        observacao: form.observacao ? form.observacao : null,
        responsavel_financeiro_pessoa: form.responsavel_financeiro_pessoa
          ? form.responsavel_financeiro_pessoa.id
          : null,
        responsavel_didatico_pessoa: form.responsavel_didatico_pessoa
          ? form.responsavel_didatico_pessoa.id
          : null,
        pessoa_sexo: form.pessoa_sexo ? form.pessoa_sexo.value : null,
        turma: form.turma ? form.turma : null,
        cpf: form.cpf ? form.cpf.cnpj_cpf : null,
        numero_identidade: form.numero_identidade
          ? form.numero_identidade
          : null,
      };

      let dadosArray = [];
      for (let key in dados) {
        if (dados[key] !== null) {
          if (dados[key] instanceof Array) {
            dados[key].forEach((element) => {
              dadosArray.push(`${key}[]=${element}`);
            });
          } else {
            dadosArray.push(`${key}=${dados[key]}`);
          }
        }
      }

      let retorno = dadosArray.length > 0 ? "&" : "";
      retorno += dadosArray.join("&");
      return retorno;
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
.tabela-dados-cadastro >>> tr > th,
.tabela-dados-cadastro >>> tr > td {
  vertical-align: middle;
  text-align: center;
  display: table-cell;
  white-space: nowrap; 
}
.tabela-dados-cadastro >>> table thead {
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
#tabela-dados-cadastro {
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

</style>
