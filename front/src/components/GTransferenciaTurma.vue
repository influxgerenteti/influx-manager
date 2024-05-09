<template>
  <!-- Modal transferência de turma -->
  <b-modal
    id="transferenciaTurma"
    ref="transferenciaTurma"
    v-model="modalTransferenciaTurma"
    :aluno-id="alunoId"
    title="Transferência entre turmas"
    size="xl"
    centered
    no-close-on-backdrop
    hide-header
    hide-footer
    @shown="openModal"
    @hide="limparModalTransferenciaTurma()"
  >
    <div class="d-flex justify-content-between">
      <h5 class="title-module">Transferência entre turmas</h5>
    </div>

    <div class="animated fadeIn">
      <form
        :class="{ 'was-validated': !isValid }"
        class="needs-validation"
        novalidate
        @submit.prevent="salvarTransferenciaTurma()"
      >
        <div class="form-loading">
          <load-placeholder :loading="!listaTurmaOrigem.length" />
        </div>

        <div class="content-sector-extra p-2 mb-3 box-scroll">
          <!-- <h5 class="title-module mb-2">{{ alunoTransferencia ? alunoTransferencia.pessoa.nome_contato : '' }}</h5> -->
          <b-row>
            <b-col style="font-size: 14px">{{
              alunoTransferencia ? alunoTransferencia.pessoa.nome_contato : ""
            }}</b-col>
          </b-row>

          <div class="form-group row">
            <div class="col-md-6">
              <label for="turma_origem" class="col-form-label"
                >Turma origem *</label
              >
              <g-select
                id="turma_origem"
                :value="turmaOrigem"
                :select="setTurmaOrigem"
                :options="listaTurmaOrigem"
                :class="
                  !isValid && $v.turmaOrigem.$invalid
                    ? 'invalid-input'
                    : 'valid-input'
                "
                class="multiselect-truncate valid-input"
                label="descricao"
                track-by="id"
              />

              <div
                v-if="!isValid && $v.turmaOrigem.$invalid"
                class="multiselect-invalid"
              >
                Selecione uma opção!
              </div>
            </div>
          </div>

          <template v-if="listaAlunoTransferencia.length">
            <b-row class="header-card-list mb-0">
              <b-col md="1">
                <label class="col-form-label">Matrícula</label>
              </b-col>
              <b-col md="2">
                <label class="col-form-label">Data de matrícula</label>
              </b-col>
              <b-col md="2">
                <label class="col-form-label">Instrutor</label>
              </b-col>
              <b-col md="1">
                <label class="col-form-label">Idade</label>
              </b-col>
              <b-col md="2">
                <label class="col-form-label">Sala</label>
              </b-col>
              <b-col md="2">
                <label class="col-form-label">Livro</label>
              </b-col>
              <b-col md="2">
                <label class="col-form-label">Lição</label>
              </b-col>
            </b-row>

            <div class="row data-scroll">
              <perfect-scrollbar class="scroller col-12">
                <b-row
                  v-for="(item, index) in listaAlunoTransferencia"
                  :key="index"
                  class="body-card-list"
                >
                  <b-col md="1" data-header="Matrícula" class="truncate">{{
                    item.matricula
                  }}</b-col>
                  <!-- <b-col md="4" data-header="Aluno">{{ item.nome }}</b-col> -->
                  <b-col
                    md="2"
                    data-header="Data de matrícula"
                    class="truncate"
                    >{{ item.data_matricula | formatarData }}</b-col
                  >
                  <b-col md="2" data-header="Instrutor" class="truncate">{{
                    item.instrutor
                  }}</b-col>
                  <b-col md="1" data-header="Idade" class="truncate">{{
                    item.idade
                  }}</b-col>
                  <b-col md="2" data-header="Sala" class="truncate">{{
                    item.sala
                  }}</b-col>
                  <b-col md="2" data-header="Livro" class="truncate">{{
                    item.livro
                  }}</b-col>
                  <b-col md="2" data-header="Lição" class="truncate">{{
                    item.licao
                  }}</b-col>
                </b-row>
              </perfect-scrollbar>
            </div>
          </template>
          <template v-else>
            <div class="form-group list-group-accent">
              <div
                class="list-group-item list-group-item-accent-info list-group-item-info border-0"
              >
                <font-awesome-icon icon="info-circle" /> Selecione a turma de
                origem do aluno para iniciar a transferência de turma.
              </div>
            </div>
          </template>
        </div>

        <form class="p-2" @submit.prevent="filtrar">
          <div class="form-group row mb-0">
            <div class="col-md-5">
              <label
                v-help-hint="'filtro-turma_descricao'"
                for="descricao"
                class="col-form-label"
                >Descrição</label
              >
              <input
                id="descricao"
                v-model="filtros.descricao"
                type="text"
                class="form-control"
                maxlength="255"
              />
            </div>

            <div class="col-md-4">
              <label
                v-help-hint="'filtro-turma_curso'"
                for="curso"
                class="col-form-label"
                >Curso</label
              >
              <g-select
                id="curso"
                :value="filtros.curso"
                :select="setCurso"
                :options="listaCursos"
                class="multiselect-truncate"
                label="descricao"
                track-by="id"
              />
            </div>

            <div class="col-md-3">
              <label
                v-help-hint="'filtro-turma_livro'"
                for="livro"
                class="col-form-label"
                >Livro</label
              >
              <g-select
                id="livro"
                :value="filtros.livro"
                :select="setLivro"
                :options="listaLivros"
                class="multiselect-truncate"
                label="descricao"
                track-by="id"
              />
            </div>

            <div class="col-md-5" style="z-index: 0">
              <label
                v-help-hint="'filtro-turma_dia_da_semana'"
                for="turma_dia_da_semana"
                class="col-form-label"
                >Dia da semana</label
              >
              <div>
                <b-form-checkbox-group
                  id="turma_dia_da_semana"
                  v-model="filtros.dia_semana"
                  :options="listaDiasSemana"
                  buttons
                  button-variant="cinza"
                  name="situacao_avancado"
                  class="checkbtn-line"
                />
              </div>
            </div>

            <div class="col-md-4" style="z-index: 0">
              <label
                v-help-hint="'filtro-turma_situacao'"
                for="situacao_avancado"
                class="col-form-label"
                >Situação</label
              >
              <div>
                <b-form-checkbox-group
                  id="situacao_avancado"
                  v-model="filtros.situacao"
                  :options="listaSituacao"
                  buttons
                  button-variant="cinza"
                  name="situacao_avancado"
                  class="checkbtn-line"
                />
              </div>
            </div>

            <div class="col-md-3" style="z-index: 0">
              <button
                type="submit"
                class="btn btn-cinza btn-block text-uppercase mt-3"
              >
                Buscar
              </button>
            </div>
          </div>
        </form>

        <div class="table-responsive-sm">
          <g-table
            :class="{ 'table-check-valid': !isValid && !$v.selected.$invalid }"
          >
            <thead>
              <tr>
                <th class="coluna-checkbox">#</th>
                <th>Turmas</th>
                <th>Instrutor</th>
                <th class="size-85">Sala</th>
                <th class="size-85">Alunos</th>
                <th class="size-85">Livro</th>
                <th class="size-150">Lição</th>
              </tr>
            </thead>
            <tbody>
              <perfect-scrollbar>
                <tr
                  v-for="(item, index) in listaTurmaTransferencia"
                  :key="index"
                >
                  <td data-label="Selecionar" class="coluna-checkbox">
                    <b-form-checkbox
                      v-input-locker="verificaPermissao(item)"
                      v-model="selected"
                      :value="item"
                      class="m-0"
                      required
                      @change="setTurmaDestino($event, item.presenca, index)"
                    />
                  </td>
                  <td data-label="Turmas">{{ item.turmaDescricao }}</td>
                  <td data-label="Instrutor">
                    {{ item.apelidoFuncionario || "---" }}
                  </td>
                  <td data-label="Sala" class="size-85">
                    {{ item.descricaoSala || "---" }}
                  </td>
                  <td data-label="Alunos" class="size-85">
                    {{ item.qtdAlunosDisplay }}
                  </td>
                  <td data-label="Livro" class="size-85">
                    {{ item.nomeLivro }}
                  </td>
                  <td data-label="Lição" class="size-150">
                    {{ item.nomeLicao }}
                  </td>
                </tr>
                <div
                  v-if="isCarregandoTurmasTransferencia"
                  class="d-flex h-100"
                >
                  <load-placeholder
                    :loading="isCarregandoTurmasTransferencia"
                  />
                </div>
                <div
                  v-if="
                    !listaTurmaTransferencia.length &&
                    !isCarregandoTurmasTransferencia
                  "
                  class="busca-vazia"
                >
                  <p>Nenhum resultado encontrado.</p>
                </div>
              </perfect-scrollbar>
            </tbody>
          </g-table>
        </div>
        <div
          v-if="!isValid && $v.selected.$invalid"
          class="form-group list-group-accent"
        >
          <div
            class="list-group-item list-group-item-accent-danger list-group-item-danger border-0"
          >
            Selecione a turma destino do aluno para prosseguir com a
            transferência de turma.
          </div>
        </div>

        <div class="form-group pt-2 mb-0">
          <b-btn :disabled="isTransferindo" type="submit" variant="verde">{{
            isTransferindo ? "Transferindo..." : "Transferir"
          }}</b-btn>
          <b-btn variant="link" @click="voltar">Cancelar</b-btn>
        </div>
      </form>
    </div>
  </b-modal>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import { idade } from "../utils/date";
import { required } from "vuelidate/lib/validators";
import { setTimeout } from "timers";
import EventBus from "../utils/event-bus";

function isRequired(value, vm) {
  return vm.turmaOrigem.id !== null;
}

export default {
  name: "GTransferenciaTurma",
  props: {
    alunoId: {
      type: Number,
      required: false,
      default: null,
    },
  },

  data() {
    return {
      modalTransferenciaTurma: false,
      isValid: true,
      isCarregandoTurmasTransferencia: false,
      isTransferindo: false,
      turmaOrigem: { id: null, descricao: "Selecione" },
      listaTurmaOrigem: [],
      alunoTransferencia: null,
      listaAlunoTransferencia: [],
      listaTurmaTransferencia: [],
      selected: [],
      turma_destino: [],
      isReset: true,
      listaSituacao: [
        { text: "Aberta", value: "ABE" },
        { text: "Em formação", value: "FOR" },
        { text: "Encerrada", value: "ENC" },
      ],
      listaDiasSemana: [
        { text: "Seg", value: "SEG" },
        { text: "Ter", value: "TER" },
        { text: "Qua", value: "QUA" },
        { text: "Qui", value: "QUI" },
        { text: "Sex", value: "SEX" },
        { text: "Sáb", value: "SAB" },
      ],
      filtros: {
        descricao: "",
        curso: {},
        livro: {},
        situacao: ["ABE", "FOR"],
        dia_semana: [],
      },
    };
  },

  computed: {
    ...mapState("turma", { objTurma: "itemSelecionado" }),
    ...mapState("modulos", ["permissoes"]),
    ...mapState("livro", { listaTodosLivros: "lista" }),

   

    listaCursos: {
      get() {
        const lista = this.$store.state.curso.lista || [];
        const registrosFiltrados = lista.filter(
          (item) => item.situacao === "A"
        );
          return [{ id: null, descricao: "Selecione" }].concat(
          registrosFiltrados
        );
      },
    },

    listaLivros: {
      get() {
        let lista = this.listaTodosLivros || [];
        if (this.filtros.curso && this.filtros.curso.id) {
          lista = this.listaTodosLivros.filter((livro) => {
            return (
              livro.curso.filter((curso) => {
                return this.filtros.curso.id === curso.id;
              }).length > 0
            );
          });
        }

        return [
          {
            id: null,
            descricao: lista.length > 0 ? "Selecione" : "Selecione o curso",
          },
        ].concat(lista);
      },
    },
  },

  watch: {
    listaLivros(livros) {
      const livroSelecionado = livros.find((livro) => {
        return livro.id === this.filtros.livro.id;
      });
      if (!livroSelecionado || livroSelecionado.length === 0) {
        this.filtros.livro = livros[0];
      }
    },
  },

  validations: {
    turmaOrigem: { isRequired },
    selected: { required },
  },

  mounted() {
    this.$store.commit("curso/SET_PAGINA_ATUAL", 1);
    this.$store.commit("livro/SET_PAGINA_ATUAL", 1);
    this.$store.dispatch("curso/listar");
    this.$store.dispatch("livro/listar");
    EventBus.$on("modal:reabrir-modal", () => {
      this.modalTransferenciaTurma = true;
      this.isReset = false;
    });
  },

  methods: {
    // Actions
    ...mapActions("cep", { buscarCep: "buscar" }),
    ...mapActions("estado", { listaEstadosRequisicao: "listar" }),
    ...mapActions("cidade", { listaCidadesRequisicao: "listar" }),
    // Mutations
    ...mapMutations("cep", ["SET_CEP_SELECIONADO", "SET_CEP_NUMERO"]),
    ...mapMutations("estado", ["SET_ESTADO_SELECIONADO"]),
    ...mapMutations("cidade", ["SET_ESTADO_FILTRO_ID"]),
    // Methods do componente
    idade: idade,

    voltar() {
      this.limparModalTransferenciaTurma();
      this.modalTransferenciaTurma = false;
    },

    limparModalTransferenciaTurma() {
      this.filtros.descricao = "";
      this.isReset = true;
      this.isValid = true;
      this.isTransferindo = false;
      this.turmaOrigem = { id: null, descricao: "Selecione" };
      this.selected = [];
      this.listaTurmaOrigem = [];
      this.alunoTransferencia = null;
      this.listaAlunoTransferencia = [];
      this.listaTurmaTransferencia = [];
      this.$store.commit("turma/LIMPAR_ITEM_SELECIONADO");
    },

    openModal(item) {
      if (this.isReset) {
        this.limparModalTransferenciaTurma();
        this.$store.commit("aluno/SET_ITEM_SELECIONADO", this.alunoId);
        this.$store.dispatch("aluno/buscarComPessoa", 1).then((response) => {
          this.alunoTransferencia = response;
          const contratosVigentes = this.alunoTransferencia.contratos.filter(
            (contrato) => {
              return contrato.situacao === "V";
            }
          );

          this.listaTurmaOrigem = contratosVigentes.map(
            (contrato) => contrato.turma
          );
        });
      }
    },

    setTurmaOrigem(value) {
      this.turmaOrigem = value;

      this.listaAlunoTransferencia = [];

      const contrato = this.alunoTransferencia.contratos.find(
        (contrato) => contrato.turma === value
      );
      this.alunoTransferencia.contrato = contrato;

      const aluno = {};
      aluno.matricula = `${this.alunoTransferencia.id}/${contrato.sequencia_contrato}`;
      aluno.nome = this.alunoTransferencia.pessoa.nome_contato;
      aluno.data_matricula = contrato.data_matricula;
      aluno.instrutor = value.funcionario ? value.funcionario.apelido : "---";
      aluno.idade = idade(this.alunoTransferencia.pessoa.data_nascimento);
      aluno.sala = value.sala_franqueada
        ? value.sala_franqueada.sala.descricao
        : "---";
      aluno.livro = value.livro.descricao;
      aluno.licao = value.turmaAulas[0].licao.descricao;

      this.listaAlunoTransferencia.push(aluno);

      this.objTurma.livro = value.livro;
      this.objTurma.id = value.id;
    },

    filtrar() {
      this.listaTurmaTransferencia = [];
      this.isCarregandoTurmasTransferencia = true;
      const filtros = {
        livro: this.filtros.livro.id,
        curso: this.filtros.curso.id,
        descricao: this.filtros.descricao,
        dia_semana: this.filtros.dia_semana,
        situacao: this.filtros.situacao,
      };

      this.$store
        .dispatch("turma/buscarTurmas", filtros)
        .then((response) => {
          this.listaTurmaTransferencia = response.map((turma) => {
            turma.qtdAlunosDisplay =
              turma.quantidadeAlunosVigentes + "/" + turma.qtdMaxAluno;
            return turma;
          });
          this.isCarregandoTurmasTransferencia = false;
        })
        .catch((e) => {
          console.log(e);
          this.isCarregandoTurmasTransferencia = false;
        });
    },

    setTurmaDestino(value, oldVal) {
      if (this.selected.length > 1) {
        setTimeout(() => this.selected.splice(this.selected[0], 1), 20);
      }
    },

    setCurso(value) {
      this.filtros.curso = value;
    },

    setLivro(value) {
      this.filtros.livro = value;
    },

    salvarTransferenciaTurma() {
      this.isValid = true;
      this.isTransferindo = true;

      if (this.$v.$invalid) {
        this.isValid = false;
        this.isTransferindo = false;
        return;
      }

      const item = this.selected[0];

      const quantidade = item.quantidadeAlunosVigentes * 1;
      const maximo = item.qtdMaxAluno * 1;

      const data = {
        ignora_validacao_qtd_max_alunos: quantidade >= maximo,
        turma_destino: item.turmaId,
        aluno: this.alunoTransferencia.id,
        contrato: this.alunoTransferencia.contrato.id,
      };

      this.$store
        .dispatch("aluno/transfereAluno", data)
        .then(this.voltar)
        .catch((e) => {
          console.error(e);
          this.isTransferindo = false;
        });
    },

    verificaPermissao(item) {
      const quantidade = item.quantidadeAlunosVigentes * 1;
      const maximo = item.qtdMaxAluno * 1;

      if (quantidade >= maximo) {
        return { permissao: this.permissoes["TRANSFERENCIA_TURMA"] };
      }
    },
  },
};
</script>
<style scoped>
.body-card-list:not(:last-child) {
  border-bottom: transparent;
}

.modal .modal-dialog .table-responsive-sm {
  height: 300px;
}

div.ps {
  height: 100% !important;
}

.table-scroll tbody {
  max-height: 270px !important;
}

table.table-sm {
  position: unset;
  width: calc(100% - 32px) !important;
  width: -webkit-calc(100% - 32px) !important;
  width: -moz-calc(100% - 32px) !important;
  padding-left: 0.5rem !important;
}
</style>
