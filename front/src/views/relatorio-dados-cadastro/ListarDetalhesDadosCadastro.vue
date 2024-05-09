<template>
  <div class="animated fadeIn">
    <div class="tabela-dados-cadastro" v-if="lista.length && !estaCarregando">
      <h5 class="title-module mb-2">Dados de Cadastro</h5>
      <hr>
      <div class="form-group row">
        <div class="col-md-6">
          <label class="col-form-label" for="nome_contato">Nome</label>
          <input
            id="nome_contato"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].nome_contato"
          />
        </div>
        <div class="col-md-3">
          <label class="col-form-label" for="cnpj_cpf">CPF/CNPJ</label>
          <input
            id="cnpj_cpf"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].cnpj_cpf | formatarCPF"
          />
        </div>

        <div class="col-md-3">
          <label class="col-form-label" for="numero_identidade"
            >Identidade (RG)</label
          >
          <input
            id="numero_identidade"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].numero_identidade"
          />
        </div>
        <div class="col-md-2">
          <label class="col-form-label" for="estado_civil">Estado Civil</label>
          <input
            id="estado_civil"
            type="text"
            disabled
            class="form-control"
            :value="
              lista[0].estado_civil === 'S'
                ? 'Solteiro'
                : lista[0].estado_civil === 'C'
                ? 'Casado'
                : lista[0].estado_civil === 'D'
                ? 'Divorciado'
                : 'Não Informado'
            "
          />
        </div>

        <div class="col-md-2">
          <label class="col-form-label" for="sexo">Sexo</label>
          <input
            id="sexo"
            type="text"
            disabled
            class="form-control"
            :value="
              lista[0].sexo === 'F'
                ? 'Feminino'
                : lista[0].sexo === 'M'
                ? 'Masculino'
                : lista[0].sexo === 'O'
                ? 'Outro'
                :'Não Informado'
            "
          />
          </div>

        <div class="col-md-2">
          <label class="col-form-label" for="data_nascimento.date"
            >Data de Nascimento</label
          >
          <input
            id="data_nascimento.date"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].data_nascimento.date | formatarData"
          />
        </div>
      </div>
     
      <h5 class="title-module mb-2">Contatos</h5>
      <hr>
      <div class="form-group row">
        <div class="col-md-4">
          <label class="col-form-label" for="email_contato">E-mail</label>
          <input
            id="email_contato"
            type="mail"
            disabled
            class="form-control"
            :value="lista[0].email_preferencial"
          />
        </div>
        <div class="col-md-2">
          <label class="col-form-label" for="telefone_contato">Telefone</label>
          <input
            id="telefone_contato"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].telefone_contato | formatarTelefone"
          />
        </div>

        <div class="col-md-2">
          <label class="col-form-label" for="telefone_profissional"
            >Telefone Profissional</label
          >
          <input
            id="telefone_profissional"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].telefone_profissional | formatarTelefone"
          />
        </div>

        <div class="col-md-4">
          <label class="col-form-label" for="alunos.escolaridade.descricao"
            >Escolaridade</label
          >
          <input
            id="alunos.escolaridade.descricao"
            type="text"
            disabled
            class="form-control"
            :value="
              lista[0].alunos.escolaridade != null
                ? lista[0].alunos.escolaridade.descricao
                : ''
            "
          />
        </div>

        <div class="col-md-4">
          <label class="col-form-label" for="razao_social"
            >Empresa - Aluno</label
          >
          <input
            id="razao_social"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].razao_social"
          />
        </div>

        <div class="col-md-3">
          <label class="col-form-label" for="profissao">Profissão</label>
          <input
            id="profissao"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].profissao"
          />
        </div>
      </div>
      <h5 class="title-module mb-3">Endereço</h5>
      <hr>
      <div class="form-group row"> 
       <div class="col-md-3">
          <label class="col-form-label" for="endereco">Endereço</label>
          <input
            id="endereco"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].endereco "
          />
        </div>
        <div class="col-md-2">
          <label class="col-form-label" for="numero_endereco">Número</label>
          <input
            id="numero_endereco"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].numero_endereco"
          />
        </div>

        <div class="col-md-3">
          <label class="col-form-label" for="bairro">Bairro</label>
          <input
            id="bairro_endereco"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].bairro_endereco"
          />
        </div>
        <div class="col-md-4">
          <label class="col-form-label" for="complemento">Complemento</label>
          <input
            id="complemento"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].complemento_endereco"
          />
        </div>

        <div class="col-md-3">
          <label class="col-form-label" for="cidade">Cidade</label>
          <input
            id="cidade"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].cidade != null ? lista[0].cidade.nome : null"
          />
        </div>

        <div class="col-md-2">
          <label class="col-form-label" for="estado">UF</label>
          <input
            id="estado"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].estado != null ? lista[0].estado.sigla : null"
          />
        </div>

        <div class="col-md-3">
          <label class="col-form-label" for="cep">CEP</label>
          <input
            id="cep_endereco"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].cep_endereco | formatarCep"
          />
        </div>
      </div>

      <h5 class="title-module mb-2">Responsáveis</h5>
<hr>
      <div class="form-group row">
        <div class="col-md-3">
          <label class="col-form-label" for="responsavel_didatico"
            >Responsável Didático</label
          >
          <input
            id="responsavel_didatico"
            type="text"
            disabled
            class="form-control"
            :value="
              lista[0].alunos.responsavel_didatico_pessoa != null
                ? lista[0].alunos.responsavel_didatico_pessoa.nome_contato
                : ''
            "
          />
        </div>
        <div class="col-md-3">
          <label class="col-form-label" for="responsavel_financeiro"
            >Responsável Financeiro</label
          >
          <input
            id="responsavel_financeiro"
            type="text"
            disabled
            class="form-control"
            :value="
              lista[0].alunos.responsavel_financeiro_pessoa != null
                ? lista[0].alunos.responsavel_financeiro_pessoa.nome_contato
                : ''
            "
          />
        </div>
      </div>
      <h5 class="title-module mb-2">Dados do Contrato</h5>
      <hr>

      <div class="form-group row">
       <div class="col-md-3">
          <label class="col-form-label" for="atendente">Atendente</label>
          <input
            id="atendente"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].alunos.interessados && lista[0].alunos.interessados.consultor_funcionario != null ? lista[0].alunos.interessados.consultor_funcionario.pessoa.nome_contato : null"
          />
        </div>
        <div class="col-md-3">
          <label class="col-form-label" for="alunos.interessados.apelido"
            >Consultor</label
          >
          <input 
            id="alunos.interessados.consultor_funcionario.apelido"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].alunos.interessados && lista[0].alunos.interessados.consultor_funcionario!= null ? lista[0].alunos.interessados.consultor_funcionario.apelido : null"
          />
        </div>

        <div class="col-md-3">
          <label class="col-form-label" for="alunos.contratos">Matrícula</label>
          <input
            id="alunos-contratos"
            type="text"
            disabled
            class="form-control"
            :value="
              lista[0].alunos.contratos.id !== undefined
                ? `${lista[0].alunos.contratos.id}/${lista[0].alunos.contratos.sequencia_contrato}`
                : ''
            "
          />
        </div>

        <div class="col-md-3">
          <label class="col-form-label" for="situacao">Situação Contrato</label>
          <input
            id="situacao"
            type="text"
            disabled
            class="form-control"
            :value="
              lista[0].alunos.contratos != ''
                ? lista[0].alunos.contratos.situacao === 'V'
                  ? 'Vigente'
                  : lista[0].alunos.contratos.situacao === 'E'
                  ? 'Encerrado'
                  : lista[0].alunos.contratos.situacao === 'R'
                  ? 'Rescindido'
                  : lista[0].alunos.contratos.situacao === 'C'
                  ? 'Cancelado'
                  : 'Trancado'
                : ''
            "
          />
        </div>

        <div class="col-md-3">
          <label class="col-form-label" for="turma">Turma</label>
          <input
            id="turma"
            type="text"
            disabled
            class="form-control"
            :value="
              lista[0].alunos.contratos.turma && lista[0].alunos.contratos.turma.descricao != '' ?
                lista[0].alunos.contratos.turma.descricao  : ''
            "   
     
          />
        </div>

        <div class="col-md-3">
          <label class="col-form-label" for="telefone_preferencial"
            >Data Negativação</label
          >
          <input
            id="data_consulta_credito"
            type="text"
            disabled
            class="form-control"
            :value="lista[0].data_consulta_credito | formatarData"
          />
        </div>

        
        <div class="col-md-12">
          <label class="col-form-label" for="observacao">Observação</label>
          <b-form-textarea
            id="formulario_atividade_descricao "
            :value="lista[0].observacao"
            disabled
            class="full-textarea"
            rows="20"
          />
        </div>
      </div>
    </div>

    <b-card-text>
      <div class="mb-2 mt-2 d-flex justify-content-end">
        <div class="col-md-auto no-print" v-if="lista.length">
        <g-excel
              class="btn btn-cinza btn-block text-uppercase"
              :data="lista"
              :fields="exportFields"
              type="xls"
              name="relatorio-dados-cadastro"
            >
              <font-awesome-icon icon="file-code" />
              Exportar para Excel
            </g-excel>
            </div>
        <div class="col-md-auto" v-if="lista.length">
          <g-print></g-print>
        </div>
      
        
      </div>
    </b-card-text>
  </div>
</template>

<script>
import GPrint from "@/components/GPrint.vue";
import moment from "moment";
import { mapState, mapActions, mapMutations } from "vuex";
export default {
  components: { GPrint },
  name: "ListarDetalhesDadosCadastro",


  data() {
    return {
      cadastroSelecionado: null,
    
      exportFields:{
        'Nome' : 'nome_contato',
        'CPF/CNPJ':'cnpj_cpf',
        'Número RG':'numero_identidade',
        'Estado Civil':'estado_civil',
        'Sexo': {
          field:'sexo',
          callback: (value) => value == 'F' ? 'Feminino' : value == 'M' ? 'Masculino' : value == 'O' ? 'Outro' :'Não Informado' 
        },
        'Data Nascimento': {
          field: "data_nascimento.date",
          callback: (value) => moment(value).format("DD/MM/YYYY"),
        },
        'E-mail':'email_preferencial',
        'Telefone':'telefone_contato',
        'Telefone Comercial':'telefone_profissional',
        'Escolaridade':'alunos.escolaridade.descricao',
        'Empresa Aluno':'razao_social',
        'Profissão':'profissao',
        'Endereço':'endereco',
        'Nº': 'numero_endereco',
        'Bairro':'bairro_endereco',
        'Complemento':'complemento_endereco',
        'Cidade':'cidade.nome',
        'UF':'estado.sigla',
        'CEP':'cep_endereco',
        'Responsável Didático':'alunos.responsavel_didatico_pessoa.nome_contato',
        'Responsável Financeiro':'alunos.responsavel_financeiro_pessoa.nome_contato',
        'Atendente':'alunos.interessados.consultor_funcionario.pessoa.nome_contato',
        'Consultor':'alunos.interessados.consultor_funcionario.apelido',
          "Matrícula": {
          field: null,
          callback: (value, key, item) => {
            return `${value.id}/${value.alunos.contratos.sequencia_contrato}`;
          },
        },

        'Situação Contrato': {
          field:'alunos.contratos.situacao',
          callback: (value) => value == 'V' ? 'Vigente' : value == 'E' ? 'Encerrado' : value == 'R' ? 'Rescindido' : value == 'C' ? 'Cancelado' : 'Trancado' 
        },
        'Turma':'alunos.contratos.turma.descricao',
        'Data Negativação':'data_consulta_credito',
        'Observação':'observacao'

       },
  }},

  computed: {
    ...mapState("relatorioDadosCadastro", [
      "filtros",
      "lista",
      "estaCarregando",
    ]),
  },

  methods: {
    ...mapActions("relatorioDadosCadastro", ["listar"]),
    ...mapMutations("relatorioDadosCadastro", [
      "SET_LISTA",
      "SET_PARAMETROS",
      "SET_ESTA_CARREGANDO",
    ]),

    abrirRelatorio() {
      let parametros = this.converterDadosParaLink();
      this.SET_PARAMETROS(parametros);
      this.listar();
  
    },

    converterDadosParaLink() {
      const dados = {
        pessoa_id: this.$route.params.id,
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

  mounted() {
    this.abrirRelatorio();
  },
};
</script>

<style scoped>

.form-control:disabled{
  background-color: #ececec

}
.tabela-dados-cadastro {
  max-height: 85vh;
  overflow: scroll;
  overflow-x: auto;
  padding-right: 15px;
  margin-left: 15px;
}
.title-module {
  padding-top: 35px;
}

@media print {
  .tabela-dados-cadastro{
  overflow: hidden;}
}
</style>
