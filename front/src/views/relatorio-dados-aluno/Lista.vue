<template>
  <div class="animated fadeIn">
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
                    <b-col md="6">
                      <label
                        v-help-hint="
                          'filtroAvancado-ocorrencia_academica_nome_aluno'
                        "
                        for="nome_aluno"
                        class="col-form-label"
                        >Aluno</label
                      >
                      <typeahead
                        id="nome_aluno"
                        :item-hit="setAluno"
                        source-path="/api/aluno/buscar-nome"
                        key-name="pessoa.nome_contato"
                      />
                    </b-col>
                  </div>
                </b-collapse>
              </div>
              <div class="mb-2 d-flex justify-content-end">
                <div class="col-md-auto" v-if="alunoSelecionado != null">
                  <g-print></g-print>
                </div>
                <div class="col-md-auto" v-if="alunoSelecionado != null">
                  <g-excel
                    class="btn btn-cinza btn-block text-uppercase"
                    :data="fieldsExcel"
                    type="xls"
                    name="relatorio-dados-aluno"
                  >
                    <font-awesome-icon icon="file-code" />
                    Exportar para Excel
                  </g-excel>
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
    <div class="d-flex w-100 h-100" v-if="alunoSelecionado == null">
      <div class="w-100" style="font-size: 14px; color: #928585; font-weight: 500;">
       <span class="d-flex justify-content-center">Filtre um aluno para exibir o relatório.</span>
      </div>
    </div>
    <div class="row row-flex row-flex-wrap" v-if="alunoSelecionado != null">
      <div class="col-md-3">
        <div class="upload-area">
          <div class="image-load">
            <svg
              v-if="alunoSelecionado.foto === ''"
              data-v-113b2ed0=""
              aria-hidden="true"
              focusable="false"
              data-prefix="fas"
              data-icon="user"
              role="img"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 448 512"
              class="svg-inline--fa fa-user"
            >
              <path
                data-v-113b2ed0=""
                fill="currentColor"
                d="M224 256c70.7 0 128-57.31 128-128s-57.3-128-128-128C153.3 0 96 57.31 96 128S153.3 256 224 256zM274.7 304H173.3C77.61 304 0 381.6 0 477.3c0 19.14 15.52 34.67 34.66 34.67h378.7C432.5 512 448 496.5 448 477.3C448 381.6 370.4 304 274.7 304z"
                class=""
              ></path>
            </svg>
            <img 
            v-if="alunoSelecionado.foto != ''" 
            :src="alunoSelecionado.foto" 
            alt="Imagem do aluno selecionado"
            >
          </div>
        </div>
      </div>
      <div class="col-md-9">
        <div class="row form group">
          <div class="col-md-12">
            <label class="col-form-label" for="nome_contato">Nome</label>
            <input
              disabled
              id="nome_contato"
              :value="alunoSelecionado.nome_contato"
              class="form-control"
            />
          </div>
          <div class="col-md-5">
            <label class="col-form-label" for="cpf">CPF</label>
            <input
              id="cpf"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.cnpj_cpf"
            />
          </div>
          <div class="col-md-5">
            <label class="col-form-label" for="identidade"
              >Identidade (RG)</label
            >
            <input
              id="Identidade"
              type="text"
              :value="alunoSelecionado.numero_identidade"
              disabled
              class="form-control"
            />
          </div>
          <div class="col-md-2">
            <label class="col-form-label" for="orgao_emissor"
              >Emissor</label
            >
            <input
              id="orgao_emissor"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.orgao_emissor"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="estado_civil"
              >Estado Civil</label
            >
            <input
              id="estado_civil"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.estado_civil"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="sexo">Sexo</label>
            <input
              id="sexo"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.sexo"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="data_nascimento"
              >Data Nascimento</label
            >
            <input
              id="data_nascimento"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.data_nascimento"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="emancipado">Emancipado?</label>
            <input
              id="emancipado"
              type="text"
              disabled
              :value="alunoSelecionado.emancipado"
              class="form-control"
            />
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="col-md-3 mt-3">
          <h4>Contatos:</h4>
        </div>
        <div class="row form-group">
          <div class="col-md-6">
            <label class="col-form-label" for="telefone"
              >Telefone (celular)</label
            >
            <input
              id="telefone"
              type="text"
              disabled
              :value="alunoSelecionado.telefone_preferencial"
              class="form-control"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="email">E-mail</label>
            <input
              id="email"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.email_preferencial"
            />
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="col-md-3 mt-3">
          <h4>Endereço:</h4>
        </div>
        <div class="row form-group">
          <div class="col-md-3">
            <label class="col-form-label" for="cep">CEP</label>
            <input
              id="cep"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.cep_endereco"
            />
          </div>
          <div class="col-md-7">
            <label class="col-form-label" for="endereco">Endereço</label>
            <input
              id="endereco"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.endereco"
            />
          </div>
          <div class="col-md-2">
            <label class="col-form-label" for="numero">Número</label>
            <input
              id="numero"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.numero_endereco"
            />
          </div>
          <div class="col-md-3">
            <label class="col-form-label" for="complemento">Complemento</label>
            <input
              id="complemento"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.complemento_endereco"
            />
          </div>
          <div class="col-md-3">
            <label class="col-form-label" for="bairro">Bairro</label>
            <input
              id="bairro"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.bairro_endereco"
            />
          </div>
          <div class="col-md-3">
            <label class="col-form-label" for="cidade">Cidade</label>
            <input
              id="cidade"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.cidade"
            />
          </div>
          <div class="col-md-3">
            <label class="col-form-label" for="estado">Estado</label>
            <input
              id="estado"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.estado"
            />
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="col-md-3 mt-3">
          <h4>Responsáveis:</h4>
        </div>
        <div class="row form-group">
          <div class="col-md-6">
            <label class="col-form-label" for="responsavel_financeiro"
              >Responsável Financeiro</label
            >
            <input
              id="responsavel_financeiro"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.responsavel_financeiro_nome"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="relacionamento_fiananciero"
              >Relacionameto</label
            >
            <input
              id="relacionamento_fiananciero"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.responsavel_financeiro_relacionamento_aluno ?
              alunoSelecionado.responsavel_financeiro_relacionamento_aluno : ''"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="responsavel_didatico"
              >Responsável Didático</label
            >
            <input
              id="responsavel_didatico"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.responsavel_didatico_nome"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="relacionamento_didatico"
              >Relacionamento Didático</label
            >
            <input
              id="relacionamento_didatico"
              type="text"
              disabled
              class="form-control"
              :value="alunoSelecionado.responsavel_didatico_nome"
            />
          </div>
        </div>
      </div>
      <div v-if="alunoSelecionado.contratos.length > 0" class="col-md-12">
        <div class="col-md-3 mt-3">
          <h4>Contratos:</h4>
        </div>
        <div
          class="row form-group"
          v-for="(contrato, indexContrato) in alunoSelecionado.contratos"
          :key="indexContrato"
        >
          <div class="col-md-2">
            <label class="col-form-label" for="bolsista">Bolsista</label>
            <input
              id="bolsista"
              type="text"
              disabled
              class="form-control"
              :value="contrato.bolsista == false ? 'Não' : 'Sim'"
            />
          </div>
          <div class="col-md-5">
            <label class="col-form-label" for="curso">Curso</label>
            <input
              id="curso"
              type="text"
              disabled
              :value="contrato.curso === undefined ? '' : contrato.curso.descricao"
              class="form-control"
            />
          </div>
          <div class="col-md-5">
            <label class="col-form-label" for="idioma">Idioma</label>
            <input
              id="idioma"
              type="text"
              disabled
              class="form-control"
              :value="contrato.curso === undefined ? '' : contrato.curso.idioma.descricao"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="data_matricula"
              >Data matrícula</label
            >
            <input
              id="data_matricula"
              type="text"
              disabled
              class="form-control"
              :value="contrato.data_matricula"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="data_contrato"
              >Data contrato</label
            >
            <input
              id="data_contrato"
              type="text"
              disabled
              class="form-control"
              :value="contrato.data_contrato"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="data_inicio_contrato"
              >Inicio contrato</label
            >
            <input
              id="data_inicio_contrato"
              type="text"
              disabled
              class="form-control"
              :value="contrato.data_inicio_contrato"
            />
          </div>
          <div class="col-md-6">
            <label class="col-form-label" for="data_termino_contrato"
              >Termino contrato</label
            >
            <input
              id="data_termino_contrato"
              type="text"
              disabled
              class="form-control"
              :value="contrato.data_termino_contrato"
            />
          </div>
          <div class="col-md-12">
            <div class="row form-group">
              <div class="col-md-6">
                <label class="col-form-label" for="livro">Livro</label>
                <input
                  id="livro"
                  type="text"
                  disabled
                  class="form-control"
                  :value="contrato.livro.descricao"
                />
              </div>
              <div class="col-md-3">
                <label class="col-form-label" for="situacao">Situação</label>
                <input
                  id="situacao"
                  type="text"
                  disabled
                  class="form-control"
                  :value="contrato.situacao_livro"
                />
              </div>
              <div class="col-md-3">
                <label class="col-form-label" for="portal_level_reference_code"
                  >Ref code</label
                >
                <input
                  id="portal_level_reference_code"
                  type="text"
                  disabled
                  class="form-control"
                  :value="contrato.livro.portal_level_reference_code"
                />
              </div>
            </div>
            <hr class="my-4" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import { mapState, mapActions, mapMutations } from "vuex";
import formatarData from "../../../src/filters/formatar-data";

export default {
  name: "ListaRelatorioDadosAluno",
  data() {
    return {
      fieldsExcel: [],
      filtroVisivel: true,
      alunoSelecionado: null,
      arrAlunoSelecionado: [],
      alunoContratos: null,
    };
  },

  computed: {
    ...mapState("relatorioDadosAluno", ["filtros", "lista", "estaCarregando"]),
  },

  mounted() {
    this.SET_LISTA([]);
  },

  methods: {
    formatarData,
    ...mapActions("relatorioDadosAluno", ["listar"]),
    ...mapMutations("relatorioDadosAluno", ["SET_LISTA", "SET_PARAMETROS"]),

    setAluno(value) {
      if (value) {
        this.alunoSelecionado = {
          foto: value.foto,
          nome_contato: value.pessoa.nome_contato,
          cnpj_cpf: value.pessoa.cnpj_cpf,
          numero_identidade: value.pessoa.numero_identidade,
          orgao_emissor: value.pessoa.orgao_emissor,
          estado_civil:
            value.pessoa.estado_civil == "S"
              ? "Solteiro(a)"
              : value.pessoa.estado_civil == "C"
              ? "Casado(a)"
              : value.pessoa.estado_civil == "D"
              ? "Divorciado(a)"
              : value.pessoa.estado_civil == "V"
              ? "Solteiro(a)"
              : value.pessoa.estado_civil,
          sexo:
            value.pessoa.sexo == "F"
              ? "Feminino"
              : value.pessoa.sexo == "M"
              ? "Masculino"
              : value.pessoa.sexo,
          data_nascimento: value.pessoa.data_nascimento
            ? moment(value.pessoa.data_nascimento).format("DD/MM/YYYY")
            : "",
          emancipado: value.emancipado === false ? "Não" : "Sim",
          telefone_preferencial: value.pessoa?.telefone_preferencial,
          email_preferencial: value.pessoa?.email_preferencial,
          cep_endereco: value.pessoa?.cep_endereco,
          endereco: value.pessoa?.endereco,
          numero_endereco: value.pessoa?.numero_endereco,
          bairro_endereco: value.pessoa?.bairro_endereco,
          complemento_endereco: value.pessoa?.complemento_endereco,
          cidade: value.pessoa.cidade?.nome ,
          estado: value.pessoa.estado?.sigla,
          responsavel_financeiro_nome:
            value.responsavel_financeiro_pessoa?.nome_contato,
          responsavel_financeiro_relacionamento_aluno:
            value.responsavel_financeiro_relacionamento_aluno?.descricao,
          responsavel_didatico_nome:
            value.responsavel_didatico_pessoa?.nome_contato,
          contratos: this.converterContratos(value.contratos),
        };

        this.fieldsExcel = [
          {campo: 'Nome contato', valor: this.alunoSelecionado.nome_contato},
          {campo: 'CPF', valor: this.alunoSelecionado.cnpj_cpf},
          {campo: 'Identidade', valor: this.alunoSelecionado.numero_identidade},
          {campo: 'Órgão emissor', valor: this.alunoSelecionado.orgao_emissor},
          {campo: 'Estado civil', valor: this.alunoSelecionado.estado_civil},
          {campo: 'Sexo', valor: this.alunoSelecionado.sexo},
          {campo: 'Data Nascimento', valor: this.alunoSelecionado.data_nascimento},
          {campo: 'Emancipado', valor: this.alunoSelecionado.emancipado},
          {campo: 'Telefone', valor: this.alunoSelecionado.telefone_preferencial},
          {campo: 'E-mail', valor: this.alunoSelecionado.email_preferencial},
          {campo: 'CEP', valor: this.alunoSelecionado.cep_endereco},
          {campo: 'Endereco', valor: this.alunoSelecionado.endereco},
          {campo: 'Numero', valor: this.alunoSelecionado.numero_endereco},
          {campo: 'Bairro', valor: this.alunoSelecionado.bairro_endereco},
          {campo: 'Complemento', valor: this.alunoSelecionado.complemento_endereco},
          {campo: 'Cidade', valor: this.alunoSelecionado.cidade},
          {campo: 'Estado', valor: this.alunoSelecionado.estado},
          {campo: 'Responsavel financeiro', valor: this.alunoSelecionado.responsavel_financeiro_nome},
          {campo: 'Relacionamento', valor: this.alunoSelecionado.responsavel_financeiro_relacionamento_aluno},
          {campo: 'Responsável didático', valor: this.alunoSelecionado.responsavel_didatico_nome},
          {campo: 'bolsista', valor: this.alunoSelecionado.contratos[this.alunoSelecionado.contratos.length - 1].bolsista ? 'Sim' : 'Não'},
          {campo: 'Data contrato', valor: this.alunoSelecionado.contratos[this.alunoSelecionado.contratos.length - 1].data_contrato},
          {campo: 'Data matrícula', valor: this.alunoSelecionado.contratos[this.alunoSelecionado.contratos.length - 1].data_matricula},
          {campo: 'Data Início', valor: this.alunoSelecionado.contratos[this.alunoSelecionado.contratos.length - 1].data_inicio_contrato},
          {campo: 'Data término', valor: this.alunoSelecionado.contratos[this.alunoSelecionado.contratos.length - 1].data_termino_contrato},
          {campo: 'Curso', valor: this.alunoSelecionado.contratos[this.alunoSelecionado.contratos.length - 1].curso.descricao},
          {campo: 'Livro', valor: this.alunoSelecionado.contratos[this.alunoSelecionado.contratos.length - 1].livro.descricao},
          ]
        this.arrAlunoSelecionado = [this.alunoSelecionado]
        console.log(this.arrAlunoSelecionado)

        this.filtros.aluno = value;
        return;
        
      }
      this.filtros.aluno = null;
      this.alunoSelecionado = null;
    },

    abrirRelatorio() {
      // this.carregarDados();
      this.listar();
    },
    converterContratos(value) {
      // console.log(value);
      let contratosConvertidos = [];
      value.forEach((el) => {
        let dataTemp = {
          bolsista: el.bolsista,
          curso: el.curso,
          data_matricula: moment(el.data_matricula).format("DD/MM/YYYY"),
          data_contrato: moment(el.data_contrato).format("DD/MM/YYYY"),
          data_inicio_contrato: moment(el.data_inicio_contrato).format(
            "DD/MM/YYYY"
          ),
          data_termino_contrato: moment(el.data_termino_contrato).format(
            "DD/MM/YYYY"
          ),
          situacao_livro: el.livro.situacao == 'A' ? 'ATIVO' : 'INATIVO',
          livro: el.livro,
        };
        contratosConvertidos.push(dataTemp);
      });
      return contratosConvertidos;
    },
  },
};
</script>

<style scoped>
#filtros-rapidos,
#filtros-avancados {
  transition: all 0.1s;
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

/* Image Uploader */
.upload-area {
  width: 100%;
  height: 100%;
  min-height: 190px;
  position: relative;
  text-align: center;
  background-color: #ebecf0;
  cursor: pointer;
  color: #7d7e7f;
  margin: 0;
}
.upload-area > div {
  display: flex;
  flex-direction: column;
  transition: all 0.2s;
  height: 100%;
}
.image-load {
  padding: 1rem;
  height: 100%;
}
.img-container {
  overflow: hidden;
  height: 100%;
  display: flex;
  justify-content: center;
  background-color: #fff;
  position: relative;
}
.image-preview {
  position: relative;
}
.image-preview img {
  /* height: 100%;
  top: 0; */
  position: absolute;
  display: flex;
  align-self: center;
  height: auto;
  max-width: 100%;
}
.image-preview label {
  margin: 0;
}
.image-preview div:last-child {
  transition: all 0.2s;
  opacity: 0;
  position: absolute;
  width: 100%;
  bottom: 0;
  padding: 0.5rem;
}
.image-preview div:last-child label {
  background-color: #ebecf0;
  width: 100%;
}
.image-preview:hover div {
  opacity: 1;
}
.image-load > svg {
  font-size: 5rem;
  margin: auto;
}
.image-load:hover {
  background: rgba(0, 0, 0, 0.2);
  color: #ebecf0;
  border-color: #ebecf0;
}
</style>
