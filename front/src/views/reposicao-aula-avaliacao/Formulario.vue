<template>
  <div>
    <b-modal
      id="reposicao-aula-avaliacao"
      ref="reposicao-aula-avaliacao"
      v-model="reposicaoAulaAvaliacao"
      size="xl"
      centered
      no-close-on-backdrop
      hide-header
      hide-footer
      @shown="openModal()"
    >
      <div class="">
        <form
          :class="{ 'was-validated': !isValid }"
          class="needs-validation"
          novalidate
          @submit.prevent="salvar()"
        >
          <div v-if="estaCarregando" class="form-loading">
            <load-placeholder :loading="estaCarregando" />
          </div>

          <div class="form-group row">
            <div class="col-md-6">
              <label
                v-help-hint="'form-reposicao_aula_nome_aluno'"
                for="reposicao_aula_nome_aluno"
                class="col-form-label"
                >{{
                  isEdit || alunoId
                    ? "Nome do aluno"
                    : "Buscar aluno com nome/cpf *"
                }}</label
              >
              <template>
                <template v-if="isEdit || alunoId">
                  <input
                    id="reposicao_aula_nome_aluno"
                    v-model="nomeAluno"
                    type="text"
                    class="form-control"
                    readonly
                    required
                  />
                </template>
                <template v-if="!isEdit && !alunoId">
                  <typeahead
                    id="reposicao_aula_nome_aluno"
                    ref="typeAHeadAluno"
                    v-model="objAluno"
                    :item-hit="setNomeAluno"
                    :key-name="['pessoa.cnpj_cpf', 'pessoa.nome_contato']"
                    source-path="/api/aluno/buscar_nome_com_contrato_todos"
                    selected-key="pessoa.nome_contato"
                    required
                  />
                </template>
              </template>
            </div>

            <div class="col-md-3">
              <b-form-group label="Frequência">
                <b-form-radio-group
                  id="reposicao_presenca"
                  v-model="presenca"
                  :options="opcoesPresenca"
                  :disabled="readOnly"
                  name="radio-options"
                />
              </b-form-group>
            </div>
          </div>

          <div class="">
            <div class="form-group row">
              <div class="col-md-4">
                <label
                  v-help-hint="'form-reposicao_turma'"
                  for="reposicao_turma"
                  class="col-form-label"
                  >Turma *</label
                >
                <g-select
                  id="reposicao_turma"
                  :select="setTurma"
                  :value="turma"
                  :options="listaDeTurma"
                  :disabled="isEdit"
                  :required="true"
                  :invalid="!isValid && !turma"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id"
                />
              </div>
              <div class="col-md-2">
                <label
                  v-help-hint="'form-reposicao_livro'"
                  for="reposicao_livro"
                  class="col-form-label"
                  >Livro</label
                >
                <input
                  id="reposicao_livro"
                  v-model="livro"
                  type="text"
                  class="form-control"
                  readonly
                />
              </div>
              <div class="col-md-6">
                <label
                  v-help-hint="'form-reposicao_tipo'"
                  for="reposicao_tipo"
                  class="col-form-label"
                  >Tipo de Make-up *</label
                >
                <g-select
                  id="reposicao_tipo"
                  :select="setTipoReposicao"
                  :value="tipoReposicao"
                  :options="listaDeItem"
                  :disabled="isEdit || !turma"
                  :invalid="!isValid && !tipoReposicao"
                  :required="true"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id"
                />
              </div>
            </div>

            <div class="form-group row">
              <div class="col-md-3">
                <label
                  v-help-hint="'form-reposicao_licao'"
                  for="reposicao_licao"
                  class="col-form-label"
                  >Lição *</label
                >
                <g-select
                  id="reposicao_licao"
                  :select="setLicao"
                  :value="licao"
                  :options="listaDeLicao"
                  :disabled="!turma || readOnly"
                  :required="true"
                  :invalid="!isValid && !licao"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id"
                />
              </div>

              <div class="col-md-2  pl-3">
                <label v-help-hint="'form-reposicao_data'" for="reposicao_data" class="col-form-label">Data *</label>

                            <div class="datepicker-input">
                  <v-date-picker v-model="data">
                    <template v-slot="{ inputValue, inputEvents }">
                      <input
                        class="form-control"
                        :input-props="{
                          id: 'reposicao_data',
                          class: 'form-control',
                          placeholder: 'Data',
                          required: true,
                          readonly: readOnly,
                          autocomplete: 'off',
                        }"
                        :attributes="attributes"
                        is-required
                        :value="inputValue"
                        v-on="inputEvents"
                      />
                    </template>
                  </v-date-picker>
                </div>
              </div>
              <div class="col-md-2 pl-3">
                <label
                  v-help-hint="'form-reposicao_horario'"
                  for="reposicao_horario"
                  class="col-form-label"
                  >Hora *</label
                >
                <input
                  v-mask="'##:##'"
                  id="reposicao_horario"
                  v-model="horario"
                  :class="$v.horario.$invalid ? 'is-invalid' : null"
                  :readonly="readOnly"
                  required
                  type="text"
                  class="form-control"
                  maxlength="5"
                />
                <div class="invalid-feedback">
                  {{ $v.horario.$invalid ? "Horário inválido" : "" }}
                </div>
              </div>
              <div class="col-md-4">
                <label
                  v-help-hint="'form-reposicao_sala'"
                  for="reposicao_sala"
                  class="col-form-label"
                  >Sala {{ salaObrigatorio ? "*" : "" }}</label
                >
                <g-select
                  id="reposicao_sala"
                  :select="setSala"
                  :value="sala"
                  :options="listaDeSala"
                  :invalid="!isValid && !sala && salaObrigatorio"
                  :disabled="readOnly"
                  class="multiselect-truncate"
                  label="descricao"
                  track-by="id"
                />
              </div>
            </div>

            <div class="form-group row">
              <div class="col-md-6">
                <label
                  v-help-hint="'form-reposicao_usuario'"
                  for="reposicao_usuario"
                  class="col-form-label"
                  >Usuário *</label
                >
                <input
                  id="reposicao_usuario"
                  v-model="usuario"
                  type="text"
                  class="form-control"
                  disabled
                />
              </div>

              <div class="col-md-6">
                <label
                  v-help-hint="'form-reposicao_responsavel'"
                  for="reposicao_responsavel"
                  class="col-form-label"
                  >Responsável pela execução *</label
                >
                <g-select
                  id="reposicao_responsavel"
                  :select="setResponsavel"
                  :value="responsavel"
                  :options="listaDeFuncionario"
                  :required="true"
                  :invalid="!isValid && !responsavel"
                  :disabled="readOnly"
                  class="multiselect-truncate"
                  label="apelido"
                  track-by="id"
                />
              </div>
            </div>
          </div>

          <!-- <div class="sector-secondary mb-3">
            <div v-if="makeUpAvaliacao || retake" class="content-sector-extra p-2 mb-2 mt-2">
              <div class="form-group row">
                Notas
                <div class="col-md-6">
                  <h5 class="title-module mb-2">Notas Atuais</h5>
                  <div class="row">
                    <div class="col-md-2">
                      <label for="reposicao_nota_oral" class="col-form-label">OG</label>
                      <input id="reposicao_nota_oral" v-model="nota_anterior_oral" type="text" class="form-control" readonly>
                    </div>
                    <div v-if="!retake" class="col-md-2">
                      <label for="reposicao_nota_test" class="col-form-label">T</label>
                      <input id="reposicao_nota_test" v-model="nota_anterior_test" type="text" class="form-control" readonly>
                    </div>
                    <div v-if="!retake" class="col-md-2">
                      <label for="reposicao_nota_composition" class="col-form-label">C</label>
                      <input id="reposicao_nota_composition" v-model="nota_anterior_composition" type="text" class="form-control" readonly>
                    </div>
                    <div class="col-md-2">
                      <label for="reposicao_nota_writtten" class="col-form-label">WG</label>
                      <input id="reposicao_nota_writtten" v-model="nota_anterior_escrita" type="text" class="form-control" readonly>
                    </div>
                  </div>
                </div>

                Notas make-up
                <div class="col-md-6">
                  <h5 class="title-module mb-2">Notas make-up</h5>
                  <div class="row">
                    <div class="col-md-2">
                      <label for="reposicao_nota_make_up_oral" class="col-form-label">OG</label>
                      <g-notas-avaliacao :id="`reposicao_nota_make_up_oral`" v-model="nota_proxima_oral" :options="listaConceitoAvaliacao" label="descricao"/>
                    </div>
                    <div v-if="!retake" class="col-md-2">
                      <label for="reposicao_nota_make_up_test" class="col-form-label">T</label>
                      <vue-numeric id="reposicao_nota_make_up_test" :precision="2" :empty-value="null" v-model="nota_proxima_test" :max="2.00" :disabled="readOnly" separator="." class="form-control"/>
                    </div>
                    <div v-if="!retake" class="col-md-2">
                      <label for="reposicao_nota_make_up_composition" class="col-form-label">C</label>
                      <vue-numeric id="reposicao_nota_make_up_composition" :precision="2" :empty-value="null" v-model="nota_proxima_composition" :max="8.00" :disabled="readOnly" separator="." class="form-control"/>
                    </div>
                    <div class="col-md-2">
                      <template>
                        <template v-if="retake">
                          <label for="reposicao_nota_make_up_writtten" class="col-form-label">WG</label>
                          <vue-numeric id="reposicao_nota_make_up_writtten" :precision="2" :empty-value="null" v-model="nota_proxima_escrita" :max="10.00" :disabled="readOnly" separator="." class="form-control"/>
                        </template>
                        <template v-else>
                          <label for="reposicao_nota_make_up_writtten" class="col-form-label">WG</label>
                          <vue-numeric id="reposicao_nota_make_up_writtten" :precision="2" :empty-value="null" v-model="nota_proxima_escrita" :soma="nota_proxima_escrita = nota_proxima_composition + nota_proxima_test" :max="10.00" separator="." class="form-control" disabled/>
                        </template>
                      </template>
                    </div>
                  </div>
                </div>
              </div>
              Grades:
              <ul class="d-flex list-grades">
                <li v-for="nota in listaConceitoAvaliacaoRequisicao" :key="nota.id"> {{ nota.descricao }} = {{ nota.valor }}</li>
              </ul>
            </div>

            <div v-if="isEdit">
              <label for="reposicao_ocorrencia_academicas" class="col-form-label">Histórico da ocorrência acadêmica</label>
              <b-form-textarea
                id="reposicao_ocorrencia_academicas"
                :value="historicoOcorrecias"
                class="full-textarea"
                readonly
                rows="6"
              />
            </div>
          </div> -->

          <div class="row">
            <div class="col-md-6">
              <b-form-checkbox
                v-help-hint="''"
                v-model="isento"
                :disabled="readOnly"
                @change="setIsento"
                >Atividade Isenta</b-form-checkbox
              >
            </div>
          </div>

          <div v-if="!isento" class="form-group row">
            <div class="col-md-4">
              <label
                v-help-hint="'form-reposicao_forma_cobranca'"
                for="reposicao_forma_cobranca"
                class="col-form-label"
                >Forma pagamento {{ isento ? "" : "*" }}</label
              >
              <g-select
                id="reposicao_forma_cobranca"
                :select="setFormaPagamento"
                :value="formaPagamento"
                :options="listaDeCobrancas"
                :disabled="isento || readOnly"
                :invalid="!isValid && !formaPagamento && !isento"
                class="multiselect-truncate"
                label="descricao"
                required
                track-by="id"
              />
            </div>
            <div class="col-md-3">
              <label
                v-help-hint="'form-reposicao_valor'"
                for="reposicao_valor"
                class="col-form-label"
                >Valor</label
              >
              <div class="input-group">
                <div class="input-group-prepend p-0">
                  <span id="pre-icon-saldo" class="input-group-text border-0"
                    >R$</span
                  >
                </div>
                <vue-numeric
                  id="reposicao_valor"
                  :precision="2"
                  :empty-value="0"
                  v-model="valor"
                  :max="9999999.99"
                  :disabled="isento || readOnly"
                  separator="."
                  class="form-control"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <template v-if="!readOnly">
              <div class="col-md-6 form-group pt-2">
                <b-btn
                  :disabled="salvando"
                  type="submit"
                  variant="verde"
                  @click="bSalvarESair = false"
                  >{{ salvando ? "Salvando..." : "Salvar" }}</b-btn
                >
                <b-btn
                  :disabled="salvando"
                  type="submit"
                  variant="verde"
                  @click="bSalvarESair = true"
                  >{{ salvando ? "Salvando..." : "Salvar e sair" }}</b-btn
                >
                <!-- <b-btn :disabled="salvando" type="button" variant="primary" @click="abrirModalConcluir()">Concluir</b-btn> -->
                <b-btn :disabled="salvando" variant="link" @click="voltar()"
                  >Cancelar</b-btn
                >
              </div>
              <div class="col-md-6 form-group pt-2">
                <b-btn
                  v-if="isEdit"
                  :disabled="salvando"
                  class="d-flex ml-auto"
                  variant="outline-danger"
                  @click="modalCancelarReposicao = true"
                  >Cancelar reposição</b-btn
                >
              </div>
            </template>
            <template v-else>
              <div class="col-md-6 form-group pt-2">
                <b-btn :disabled="salvando" variant="link" @click="voltar()"
                  >Fechar</b-btn
                >
              </div>
            </template>
          </div>
        </form>
      </div>
    </b-modal>
    <!-- Modal de concluir -->
    <b-modal
      id="modalConcluir"
      ref="modalConcluir"
      v-model="modalConcluir"
      size="sm"
      title="Concluir reposição"
      hide-footer
      no-close-on-backdrop
    >
      <div class="d-block text-center">
        <p>Reposição foi realmente concluído?</p>
      </div>
      <div class="d-flex justify-content-center">
        <b-btn
          type="button"
          variant="primary"
          @click="(modalConcluir = false), (concluido = true), salvar()"
          >Concluir</b-btn
        >
        <button
          :disabled="salvando"
          type="button"
          class="btn btn-link"
          @click="
            (modalConcluir = false),
              (voltaDoConcluir = true),
              (reposicaoAulaAvaliacao = true)
          "
        >
          Cancelar
        </button>
      </div>
    </b-modal>

    <!-- Modal de cancelamento -->
    <b-modal
      id="modalCancelarReposicao"
      ref="modalCancelarReposicao"
      v-model="modalCancelarReposicao"
      size="sm"
      title="Cancelar reposição"
      hide-footer
      no-close-on-backdrop
    >
      <div class="d-block text-center">
        <p>Deseja cancelar esta reposição ?</p>
        <p>(ação irreversível)</p>
      </div>
      <div class="d-flex justify-content-center">
        <b-btn
          :disabled="salvando"
          variant="vermelho"
          @click="
            (modalCancelarReposicao = false), (cancelamento = true), salvar()
          "
          >Confirmar</b-btn
        >
        <button
          :disabled="salvando"
          type="button"
          class="btn btn-link"
          @click="
            (modalCancelarReposicao = false),
              (voltaDoCancelar = true),
              (reposicaoAulaAvaliacao = true)
          "
        >
          Cancelar
        </button>
      </div>
    </b-modal>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { required, requiredIf } from "vuelidate/lib/validators";
import { validateHour } from "../../utils/validators";
import {
  dateToString,
  converteHorarioParaBanco,
  converteHorarioBancoParaInputText,
} from "../../utils/date";
import DatePicker from "../../components/fields/DatePicker";
import VueCal from "vue-cal";
import "vue-cal/dist/i18n/pt-br.cjs.js";
import "vue-cal/dist/vuecal.css";

export default {
  name: "FormularioReposicaoAulaAvaliacao",
  components: {
    VueCal,
    DatePicker,
  },

  props: {
    alunoId: {
      type: [String, Number],
      required: false,
      default: null,
    },

    readOnly: {
      type: Boolean,
      default: false,
      required: false,
    },
  },
  data() {
    return {
      isValid: true,
      isEdit: false,
      buscando: false,
      bSalvarESair: false,
      salvando: false,
      concluido: false,
      cancelamento: false,
      voltaDoCancelar: false,
      voltaDoConcluir: false,
      modalCancelarReposicao: false,
      modalConcluir: false,
      tipoAvaliacao: null,
      makeUpAvaliacao: false,
      retake: false,
      reposicaoAulaAvaliacao: false,
      presenca: "P",
      opcoesPresenca: [
        { text: "P", value: "P" },
        { text: "F", value: "F" },
      ],
      listaDeTurma: [],
      listaLicao: [],
      nomeAluno: null,
      turma: null,
      livro: null,

      nota_anterior_oral: "",
      nota_anterior_test: null,
      nota_anterior_composition: null,
      nota_anterior_escrita: null,

      nota_proxima_oral: "",
      nota_proxima_test: 0,
      nota_proxima_composition: 0,
      nota_proxima_escrita: 0,

      tipoReposicao: null,
      licao: null,
      data: "",
      sala: null,
      horario: "",
      usuario: null,
      responsavel: null,
      descricao_atividade: null,
      ocorrencia_academicas: null,
      descricao_followup: null,
      item: null,
      formaPagamento: null,
      valor: 0,
      salaObrigatorio: false,
      objAluno: null,

      isento: true,
      isento_valor: 0,
      attributes: [
        {
          highlight: { class: "today-mark" },
          dates: new Date(),
        },
      ],
    };
  },
  computed: {
    ...mapState("aluno", {
      alunoSelecionado: "itemSelecionado",
      alunoSelecionadoId: "itemSelecionadoID",
    }),
    ...mapState("turma", { listaTurmaRequisicao: "lista" }),
    ...mapState("livro", { listaLivroRequisicao: "lista" }),
    ...mapState("cadastroServico", { listaItemRequisicao: "lista" }),
    ...mapState("sala", { listaSalaRequisicao: "lista" }),
    ...mapState("conceitoAvaliacao", {
      listaConceitoAvaliacaoRequisicao: "lista",
    }),
    ...mapState("funcionario", { listaFuncionarioRequisicao: "lista" }),
    ...mapState("formaPagamento", { listaFormaPagamentoRequisicao: "lista" }),
    ...mapState("reposicaoAulaAvaliacao", [
      "itemSelecionado",
      "itemSelecionadoID",
      "estaCarregando",
    ]),
    ...mapState("root", { usuarioLogado: "usuarioLogado" }),

    historicoOcorrecias: {
      get() {
        let stringFinal = "";
        const novaLinha = "\n";
        if (this.itemSelecionado.ocorrencia_academica) {
          if (
            this.itemSelecionado.ocorrencia_academica
              .ocorrenciaAcademicaDetalhes
          ) {
            var arrayDeOcorrencias =
              this.itemSelecionado.ocorrencia_academica
                .ocorrenciaAcademicaDetalhes;
            arrayDeOcorrencias.forEach((ocorrencia) => {
              let novo;
              let data = ocorrencia.data_criacao;
              let horas = new Date(data).getHours();
              let minutos = new Date(data).getMinutes();
              data = dateToString(new Date(data));
              let funcionario = ocorrencia.funcionario.apelido;
              let descricao = ocorrencia.texto;

              data = data + "-" + horas + ":" + minutos;
              novo =
                data + "-" + funcionario + novaLinha + descricao + novaLinha;
              stringFinal += novo;
            });
          }
        }
        return stringFinal;
      },
    },

    listaDeLicao: {
      get() {
        if (this.turma) {
          return [{ id: null, descricao: "Selecione" }, ...this.listaLicao];
        }
        return [{ id: null, descricao: "Selecione" }];
      },
    },

    listaDeLivro: {
      get() {
        return [
          { id: null, descricao: "Selecione" },
          ...this.listaLivroRequisicao,
        ];
      },
    },

    listaDeItem: {
      get() {
        // MC - MAKE UP CLASS
        // MT - MAKE UP TEST
        // MF - MAKE UP FINAL
        // RM - RETAKE MID TERM
        // RF - RETAKE FINAL
        const requisitos = ["MC", "MT"];
        function verificarTiposDosItens(tipo) {
          return requisitos.indexOf(tipo) >= 0;
        }

        return [
          { id: null, descricao: "Selecione", tipo_item: null },
          ...this.listaItemRequisicao.filter((item) => {
            if (verificarTiposDosItens(item.tipo_item.tipo)) {
              return item;
            }
          }),
        ];
      },
    },

    listaDeSala: {
      get() {
        return [
          { id: null, descricao: "Selecione" },
          ...this.listaSalaRequisicao,
        ];
      },
    },

    listaDeFuncionario: {
      get() {
        let listaRequisicao = this.listaFuncionarioRequisicao;
        return [{ id: null, apelido: "Selecione" }].concat(listaRequisicao);
      },
    },

    listaDeCobrancas: {
      get() {
        return [
          { id: null, descricao: "Selecione" },
          ...this.listaFormaPagamentoRequisicao,
        ];
      },
    },

    listaConceitoAvaliacao: {
      get() {
        return [
          { id: null, descricao: " " },
          ...this.listaConceitoAvaliacaoRequisicao,
        ];
      },
    },
  },
  mounted() {},
  validations: {
    nomeAluno: {
      required: requiredIf(function () {
        return !this.objAluno;
      }),
    },
    sala: {
      required: requiredIf(function () {
        return this.salaObrigatorio;
      }),
    },
    formaPagamento: {
      required: requiredIf(function () {
        return !this.isento;
      }),
    },
    licao: { required },
    horario: { validateHour },
    responsavel: { required },
    data: { required },
    turma: { required },
    tipoReposicao: { required },
  },
  methods: {
    ...mapMutations("reposicaoAulaAvaliacao", [
      "SET_ITEM_SELECIONADO_ID",
      "LIMPAR_ITEM_SELECIONADO",
      "SET_ESTA_CARREGANDO",
      "SET_ITEM_SELECIONADO",
    ]),
    ...mapActions("reposicaoAulaAvaliacao", ["buscar", "criar", "atualizar"]),
    ...mapMutations("aluno", {
      setAlunoSelecionadoId: "SET_ITEM_SELECIONADO",
      limparStateAluno: "LIMPAR_ITEM_SELECIONADO",
    }),
    ...mapActions("aluno", { buscarAluno: "buscarAluno" }),

    setIsento(value) {
      if (!value) {
        if (this.itemSelecionado && this.itemSelecionado.valor) {
          this.valor = this.itemSelecionado.valor;
        }

        this.valor = this.isento_valor;
      } else {
        this.formaPagamento = null;
        this.valor = 0;
      }
    },

    setNomeAluno(value) {
      this.objAluno = value;

      if (!this.objAluno) {
        this.limparCampos();
      } else {
        this.getClassList(this.objAluno);
        this.setAlunoSelecionadoId(this.objAluno.id);
        this.buscarAluno()
          .then((response) => {})
          .catch(() => {});
      }
    },

    setTurma(value) {
      this.turma = value.id == null ? null : value;
      this.livro = value.livro ? value.livro.descricao : null;
      this.setLicoes();
    },

    setLicoes() {
      const aulas =
        this.turma && this.turma.turmaAulas ? this.turma.turmaAulas : [];
      const licoes = [];
      aulas.forEach((aula) => {
        if (aula.licao) {
          licoes.push(aula.licao);
        }
      });
      this.listaLicao = licoes;
    },

    abrirModalConcluir() {
      if (this.$v.$invalid) {
        this.isValid = false;
      } else {
        this.modalConcluir = true;
      }
    },

    setCamposNotasAnteriores() {
      if (this.turma) {
        let alunoAvaliacoes = null;
        if (this.isEdit) {
          alunoAvaliacoes = this.itemSelecionado.aluno.alunoAvaliacaos[0]
            ? this.itemSelecionado.aluno.alunoAvaliacaos[0]
            : null;
        } else {
          alunoAvaliacoes = this.alunoSelecionado.item.alunoAvaliacaos.find(
            (av) => {
              return av.turma.id === this.turma.id;
            }
          );
        }

        if (alunoAvaliacoes) {
          let notaAnteriorOral = "";
          let notaAnteriorTest = "";
          let notaAnteriorComposition = "";
          let notaAnteriorEscrita = "";

          if (this.tipoAvaliacao === "MT") {
            notaAnteriorOral = alunoAvaliacoes.nota_mid_term_oral
              ? this.buscarNota(alunoAvaliacoes.nota_mid_term_oral, "descricao")
              : "";
            notaAnteriorTest = alunoAvaliacoes.nota_mid_term_test
              ? alunoAvaliacoes.nota_mid_term_test
              : "";
            notaAnteriorComposition = alunoAvaliacoes.nota_mid_term_composition
              ? alunoAvaliacoes.nota_mid_term_composition
              : "";
            notaAnteriorEscrita = alunoAvaliacoes.nota_mid_term_escrita
              ? alunoAvaliacoes.nota_mid_term_escrita
              : "";
          } else if (this.tipoAvaliacao === "MF") {
            notaAnteriorOral = alunoAvaliacoes.nota_final_oral
              ? this.buscarNota(alunoAvaliacoes.nota_final_oral, "descricao")
              : "";
            notaAnteriorTest = alunoAvaliacoes.nota_final_test
              ? alunoAvaliacoes.nota_final_test
              : "";
            notaAnteriorComposition = alunoAvaliacoes.nota_final_composition
              ? alunoAvaliacoes.nota_final_composition
              : "";
            notaAnteriorEscrita = alunoAvaliacoes.nota_final_escrita
              ? alunoAvaliacoes.nota_final_escrita
              : "";
          } else if (this.tipoAvaliacao === "RM") {
            notaAnteriorOral = alunoAvaliacoes.nota_retake_mid_term_oral
              ? this.buscarNota(
                  alunoAvaliacoes.nota_retake_mid_term_oral,
                  "descricao"
                )
              : "";
            notaAnteriorTest = alunoAvaliacoes.nota_retake_mid_term_test
              ? alunoAvaliacoes.nota_retake_mid_term_test
              : "";
            notaAnteriorComposition =
              alunoAvaliacoes.nota_retake_mid_term_composition
                ? alunoAvaliacoes.nota_retake_mid_term_composition
                : "";
            notaAnteriorEscrita = alunoAvaliacoes.nota_retake_mid_term_escrita
              ? alunoAvaliacoes.nota_retake_mid_term_escrita
              : "";
          } else if (this.tipoAvaliacao === "RF") {
            notaAnteriorOral = alunoAvaliacoes.nota_retake_final_oral
              ? this.buscarNota(
                  alunoAvaliacoes.nota_retake_final_oral,
                  "descricao"
                )
              : "";
            notaAnteriorTest = alunoAvaliacoes.nota_retake_final_test
              ? alunoAvaliacoes.nota_retake_final_test
              : "";
            notaAnteriorComposition =
              alunoAvaliacoes.nota_retake_final_composition
                ? alunoAvaliacoes.nota_retake_final_composition
                : "";
            notaAnteriorEscrita = alunoAvaliacoes.nota_retake_final_escrita
              ? alunoAvaliacoes.nota_retake_final_escrita
              : "";
          }
          this.nota_anterior_oral = notaAnteriorOral;
          this.nota_anterior_test = notaAnteriorTest;
          this.nota_anterior_composition = notaAnteriorComposition;
          this.nota_anterior_escrita = notaAnteriorEscrita;
        }
      }
    },

    setTipoAvaliacao(tipo) {
      // MC - MAKE UP CLASS
      // MT - MAKE UP TEST
      // MF - MAKE UP FINAL
      // RM - RETAKE MID TERM
      // RF - RETAKE FINAL
      this.tipoAvaliacao = null;

      if (tipo === "MT") {
        this.tipoAvaliacao = "MT";
        this.makeUpAvaliacao = true;
      } else if (tipo === "MF") {
        this.tipoAvaliacao = "MF";
        this.makeUpAvaliacao = true;
      } else if (tipo === "RM") {
        this.tipoAvaliacao = "RM";
        this.retake = true;
      } else if (tipo === "RF") {
        this.tipoAvaliacao = "RF";
        this.retake = true;
      }
    },

    setTipoReposicao(value) {
      this.tipoReposicao = value.id === null ? null : value;

      this.isento_valor = value.valor_venda ? value.valor_venda * 1 : 0;
      if (!this.isento) {
        this.valor = this.isento_valor;
      }

      this.nota_proxima_oral = "";
      this.nota_proxima_test = 0;
      this.nota_proxima_composition = 0;
      this.nota_proxima_escrita = 0;
      this.makeUpAvaliacao = false;
      this.retake = false;

      this.salaObrigatorio = false;

      if (value.tipo_item) {
        let tipoItem = value.tipo_item.tipo;
        if (tipoItem) {
          if (tipoItem !== "R") {
            this.setTipoAvaliacao(tipoItem);
          } else {
            this.salaObrigatorio = true;
          }
        }
      }
    },

    setLicao(value) {
      this.licao = value.id === null ? null : value;
    },

    verificarLicaoAplicada(licao) {
      const obj = this.turma.turmaAulas.find(
        (turmaAula) => turmaAula.licao.id === licao.id
      );
      return obj !== undefined && obj.finalizada;
    },

    setData(value) {
      this.data = value;
    },

    setSala(value) {
      this.sala = value.id === null ? null : value;
    },

    setResponsavel(value) {
      this.responsavel = value.id === null ? null : value;
    },

    setFormaPagamento(value) {
      this.formaPagamento = value.id === null ? null : value;
    },

    openModal() {
      if (this.voltaDoCancelar || this.voltaDoConcluir) {
        this.voltaDoCancelar = false;
        this.voltaDoConcluir = false;

        return;
      }

      this.limparFiltrosCamposDinamicos();
      this.listarCamposDinamicos();
      this.limparCampos();

      this.usuario = this.usuarioLogado ? this.usuarioLogado.nome : null;

      if (this.alunoId) {
        this.setAlunoSelecionadoId(this.alunoId);
        this.buscarAluno()
          .then((response) => {
            let aluno = this.alunoSelecionado.item;
            this.nomeAluno = aluno.pessoa ? aluno.pessoa.nome_contato : null;
            this.getClassList(aluno);
          })
          .catch(() => {
            this.buscando = false;
          });
      } else if (this.itemSelecionadoID) {
        this.isEdit = true;
        this.buscar().then(() => {
          this.nomeAluno = this.itemSelecionado.aluno.pessoa.nome_contato;
          this.presenca = this.itemSelecionado.presenca;
          this.turma = this.itemSelecionado.turma;
          this.livro = this.itemSelecionado.livro.descricao;

          this.isento = this.itemSelecionado.isenta;

          this.tipoReposicao = this.itemSelecionado.item;
          this.setTipoReposicao(this.tipoReposicao);

          this.licao = this.itemSelecionado.licao;
          this.data = new Date(this.itemSelecionado.data_hora_inicio);
          this.horario = converteHorarioBancoParaInputText(
            this.itemSelecionado.data_hora_inicio
          );

          if (this.itemSelecionado.sala_franqueada) {
            this.sala = this.listaSalaRequisicao.find(
              (sala) => sala.id === this.itemSelecionado.sala_franqueada.sala.id
            );
            this.setSala(this.sala);
          }

          this.responsavel = this.listaFuncionarioRequisicao.find(
            (funcionario) =>
              funcionario.id === this.itemSelecionado.responsavel_execucao.id
          );
          this.descricao_atividade = this.itemSelecionado.descricao_atividade;
          this.usuario = this.itemSelecionado.usuario_solicitante.nome;

          // NOTAS PARA EDICAO
          if (this.tipoReposicao.tipo_item.tipo === "MT") {
            this.nota_proxima_oral = this.buscarNota(
              this.itemSelecionado.nota_mid_term_oral
            );

            this.nota_proxima_test = this.itemSelecionado.nota_mid_term_test
              ? this.itemSelecionado.nota_mid_term_test * 1
              : 0;
            this.nota_proxima_composition = this.itemSelecionado
              .nota_mid_term_composition
              ? this.itemSelecionado.nota_mid_term_composition * 1
              : 0;
            this.nota_proxima_escrita = this.itemSelecionado
              .nota_mid_term_escrita
              ? this.itemSelecionado.nota_mid_term_escrita * 1
              : 0;
          } else if (this.tipoReposicao.tipo_item.tipo === "MF") {
            this.nota_proxima_oral = this.buscarNota(
              this.itemSelecionado.nota_final_oral
            );

            this.nota_proxima_test = this.itemSelecionado.nota_final_test
              ? this.itemSelecionado.nota_final_test * 1
              : 0;
            this.nota_proxima_composition = this.itemSelecionado
              .nota_final_composition
              ? this.itemSelecionado.nota_final_composition * 1
              : 0;
            this.nota_proxima_escrita = this.itemSelecionado.nota_final_escrita
              ? this.itemSelecionado.nota_final_escrita * 1
              : 0;
          } else if (this.tipoReposicao.tipo_item.tipo === "RM") {
            this.nota_proxima_oral = this.buscarNota(
              this.itemSelecionado.nota_retake_mid_term_oral
            );

            this.nota_proxima_test = this.itemSelecionado
              .nota_retake_mid_term_test
              ? this.itemSelecionado.nota_retake_mid_term_test * 1
              : 0;
            this.nota_proxima_composition = this.itemSelecionado
              .nota_retake_mid_term_composition
              ? this.itemSelecionado.nota_retake_mid_term_composition * 1
              : 0;
            this.nota_proxima_escrita = this.itemSelecionado
              .nota_retake_mid_term_escrita
              ? this.itemSelecionado.nota_retake_mid_term_escrita * 1
              : 0;
          } else if (this.tipoReposicao.tipo_item.tipo === "RF") {
            this.nota_proxima_oral = this.buscarNota(
              this.itemSelecionado.nota_retake_final_oral
            );

            this.nota_proxima_test = this.itemSelecionado.nota_retake_final_test
              ? this.itemSelecionado.nota_retake_final_test * 1
              : 0;
            this.nota_proxima_composition = this.itemSelecionado
              .nota_retake_final_composition
              ? this.itemSelecionado.nota_retake_final_composition * 1
              : 0;
            this.nota_proxima_escrita = this.itemSelecionado
              .nota_retake_final_escrita
              ? this.itemSelecionado.nota_retake_final_escrita * 1
              : 0;
          }

          if (this.itemSelecionado.forma_cobranca) {
            this.formaPagamento = this.itemSelecionado.forma_cobranca;
          }

          if (!this.isento) {
            this.valor = this.itemSelecionado.valor * 1;
          } else {
            this.valor = this.isento_valor;
          }
        });
      }
    },

    buscarNota(objNota, label = undefined) {
      if (!objNota) {
        objNota = { id: null, descricao: " " };
      }
      let obj = this.listaConceitoAvaliacao.find(
        (nota) => nota.id === objNota.id
      );
      if (obj && label) {
        return obj[label];
      }
      if (obj === undefined) {
        obj = null;
      }
      return obj;
    },

    limparFiltrosCamposDinamicos() {
      this.$store.commit("turma/SET_PAGINA_ATUAL", 1);
      this.$store.commit("turma/SET_LISTA", []);
      this.$store.commit("licao/SET_PAGINA_ATUAL", 1);
      this.$store.commit("licao/SET_LISTA", []);
      this.$store.commit("livro/SET_PAGINA_ATUAL", 1);
      this.$store.commit("livro/SET_LISTA", []);
      this.$store.commit("sala/SET_PAGINA_ATUAL", 1);
      this.$store.commit("sala/SET_LISTA", []);
      this.$store.commit("funcionario/SET_PAGINA_ATUAL", 1);
      this.$store.commit("funcionario/SET_LISTA", []);
      this.$store.commit("formaPagamento/SET_PAGINA_ATUAL", 1);
      this.$store.commit("formaPagamento/SET_LISTA", []);
      this.$store.commit("cadastroServico/SET_PAGINA_ATUAL", 1);
      this.$store.commit("cadastroServico/SET_LISTA", []);
      this.$store.commit("conceitoAvaliacao/SET_PAGINA_ATUAL", 1);
      this.$store.commit("conceitoAvaliacao/SET_LISTA", []);
    },

    listarCamposDinamicos() {
      this.$store.commit("funcionario/SET_FILTROS", {
        instrutor_ou_coordenador_pedagogico: true,
        consultor_ou_gestor_comercial: false,
      });
      this.$store.dispatch("turma/listar");
      this.$store.dispatch("livro/listar");
      this.$store.dispatch("sala/listar", {
        sala_franqueada: true,
        apenas_sala_ativa: true,
      });
      this.$store.dispatch("funcionario/listar");
      this.$store.dispatch("formaPagamento/getLista");
      this.$store.dispatch("cadastroServico/listar");
      this.$store.dispatch("conceitoAvaliacao/listar");
    },

    limparState() {
      this.LIMPAR_ITEM_SELECIONADO();
      this.limparStateAluno();
    },

    limparCampos() {
      if (this.objAluno) {
        this.$refs.typeAHeadAluno.resetSelection();
      }

      this.retake = false;
      this.makeUpAvaliacao = false;
      this.responsavel = null;
      this.nomeAluno = null;
      this.presenca = "P";
      this.turma = null;
      this.livro = null;
      this.tipoReposicao = null;
      this.tipoAvaliacao = null;
      this.licao = null;
      this.data = "";
      this.horario = "";
      this.sala = null;
      this.usuario = this.usuarioLogado ? this.usuarioLogado.nome : null;
      this.usuario_solicitante = null;
      this.descricao_atividade = null;
      this.descricao_followup = null;
      this.formaPagamento = null;

      this.nota_anterior_oral = "";
      this.nota_anterior_test = null;
      this.nota_anterior_composition = null;
      this.nota_anterior_escrita = null;

      this.nota_proxima_oral = "";
      this.nota_proxima_test = 0;
      this.nota_proxima_composition = 0;
      this.nota_proxima_escrita = 0;

      this.valor = 0;
      this.isento_valor = 0;
      this.concluido = false;
      this.cancelamento = false;
      this.salaObrigatorio = false;
      this.objAluno = null;
      this.isento = true;
      this.voltaDoCancelar = false;
      this.voltaDoConcluir = false;
    },

    voltar() {
      this.isValid = true;
      this.limparCampos();
      this.isEdit = false;
      this.salvando = false;
      this.LIMPAR_ITEM_SELECIONADO();
      this.limparStateAluno();
      this.reposicaoAulaAvaliacao = false;
      this.$emit("filtrar");
    },

    montarParametrosCriar(viaTelaReposicao = false) {
      let alunoId = this.alunoSelecionado.item
        ? this.alunoSelecionado.item.id
        : null;

      if (viaTelaReposicao) {
        alunoId = this.objAluno ? this.objAluno.id : null;
      }

      let alunoAvaliacao = this.turma.alunoAvaliacaos[0]
        ? this.turma.alunoAvaliacaos[0].id
        : null;
      let turmaId = this.turma ? this.turma.id : null;
      let livroId = this.turma ? this.turma.livro.id : null;
      let licaoId = this.licao ? this.licao.id : null;

      let itemId = this.tipoReposicao ? this.tipoReposicao.id : null;
      let salaFranqueadaId = this.sala ? this.sala.salaFranqueadaId : null;
      let usuarioId = this.usuarioLogado ? this.usuarioLogado.id : null;
      let responsavelExecucaoId = this.responsavel ? this.responsavel.id : null;

      let descricaoAtividade = this.descricao_atividade;
      let data = this.data.toISOString();
      let horaInicio = converteHorarioParaBanco(this.horario);
      let formaCobranca = this.formaPagamento ? this.formaPagamento.id : null;
      let presenca = this.presenca;
      let observacaoOcorrenciaAcademicas = [this.descricao_followup];

      let obj = Object.assign({
        aluno: alunoId,
        aluno_avaliacao: alunoAvaliacao,
        turma: turmaId,
        livro: livroId,
        licao: licaoId,
        item: itemId,
        sala_franqueada: salaFranqueadaId,
        usuario_solicitante: usuarioId,
        responsavel_execucao: responsavelExecucaoId,
        descricao_atividade: descricaoAtividade,
        data: data,
        hora_inicio: horaInicio,
        forma_cobranca: formaCobranca,
        valor: this.valor,
        presenca: presenca,
        observacao_ocorrencia_academicas: observacaoOcorrenciaAcademicas,
        concluido: this.concluido,
        isenta: this.isento,
      });

      this.SET_ITEM_SELECIONADO(obj);
      this.montarParametrosDeProximasNotas();
    },

    montarParamentrosEditar() {
      // Parametros nao editaveis
      let id = this.itemSelecionado ? this.itemSelecionado.id : null;
      let alunoId = this.itemSelecionado.aluno
        ? this.itemSelecionado.aluno.id
        : null;
      let alunoAvaliacao = this.itemSelecionado.aluno.alunoAvaliacaos[0]
        ? this.itemSelecionado.aluno.alunoAvaliacaos[0].id
        : null;
      let turmaId = this.itemSelecionado.turma
        ? this.itemSelecionado.turma.id
        : null;
      let livroId = this.itemSelecionado.livro
        ? this.itemSelecionado.livro.id
        : null;
      let itemId = this.itemSelecionado.item
        ? this.itemSelecionado.item.id
        : null;
      let usuarioId = this.usuarioLogado ? this.usuarioLogado.id : null;

      // Parametros editaveis
      let presenca = this.presenca ? this.presenca : null;
      let licaoId = this.licao ? this.licao.id : null;
      let data = this.data ? this.data.toISOString() : null;
      let horaInicio = converteHorarioParaBanco(this.horario);
      let salaId = this.sala ? this.sala.salaFranqueadaId : null;
      let responsavelExecucaoId = this.responsavel ? this.responsavel.id : null;

      let descricaoAtividade = this.descricao_atividade
        ? this.descricao_atividade
        : null;
      let observacaoOcorrenciaAcademicas = this.descricao_followup
        ? [this.descricao_followup]
        : [];
      let formaPagamentoId = this.formaPagamento
        ? this.formaPagamento.id
        : null;

      let obj = Object.assign({
        id: id,
        presenca: presenca,
        aluno: alunoId,
        aluno_avaliacao: alunoAvaliacao,
        turma: turmaId,
        livro: livroId,
        licao: licaoId,
        item: itemId,
        sala_franqueada: salaId,
        usuario_solicitante: usuarioId,
        responsavel_execucao: responsavelExecucaoId,
        data: data,
        hora_inicio: horaInicio,
        descricao_atividade: descricaoAtividade,
        observacao_ocorrencia_academicas: observacaoOcorrenciaAcademicas,
        forma_cobranca: formaPagamentoId,
        valor: this.valor,
        concluido: this.concluido,
        cancelamento: this.cancelamento,
        isenta: this.isento,
      });
      this.SET_ITEM_SELECIONADO(obj);
      this.montarParametrosDeProximasNotas();
    },

    montarParametrosDeProximasNotas() {
      // MT - MAKE UP MID TERM
      // MF - MAKE UP FINAL
      // RM - RETAKE MID TERM
      // RF - RETAKE FINAL

      let copiaNotaProximoOral =
        this.nota_proxima_oral.id === null ? null : this.nota_proxima_oral.id;
      this.nota_proxima_test =
        this.nota_proxima_test === 0 ? "" : this.nota_proxima_test;
      this.nota_proxima_composition =
        this.nota_proxima_composition === 0
          ? ""
          : this.nota_proxima_composition;
      this.nota_proxima_escrita =
        this.nota_proxima_escrita === 0 ? "" : this.nota_proxima_escrita;

      if (this.tipoAvaliacao === "MT") {
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_MAKE_UP_MID_TERM_ORAL",
          copiaNotaProximoOral
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_MAKE_UP_MID_TERM_TEST",
          this.nota_proxima_test
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_MAKE_UP_MID_TERM_COMPOSITION",
          this.nota_proxima_composition
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_MAKE_UP_MID_TERM_ESCRITA",
          this.nota_proxima_escrita
        );
      } else if (this.tipoAvaliacao === "MF") {
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_MAKE_UP_FINAL_ORAL",
          copiaNotaProximoOral
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_MAKE_UP_FINAL_TEST",
          this.nota_proxima_test
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_MAKE_UP_FINAl_COMPOSITION",
          this.nota_proxima_composition
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_MAKE_UP_FINAL_ESCRITA",
          this.nota_proxima_escrita
        );
      } else if (this.tipoAvaliacao === "RM") {
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_RETAKE_MID_TERM_ORAL",
          copiaNotaProximoOral
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_RETAKE_MID_TERM_TEST",
          this.nota_proxima_test
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_RETAKE_MID_TERM_COMPOSITION",
          this.nota_proxima_composition
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_RETAKE_MID_TERM_ESCRITA",
          this.nota_proxima_escrita
        );
      } else if (this.tipoAvaliacao === "RF") {
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_RETAKE_FINAL_ORAL",
          copiaNotaProximoOral
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_RETAKE_FINAL_TEST",
          this.nota_proxima_test
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_RETAKE_FINAL_COMPOSITION",
          this.nota_proxima_composition
        );
        this.$store.commit(
          "reposicaoAulaAvaliacao/SET_RETAKE_FINAL_ESCRITA",
          this.nota_proxima_escrita
        );
      }
    },

    salvar() {
      if (this.$v.$invalid) {
        this.isValid = false;
        return;
      }

      this.isValid = true;
      this.salvando = true;

      if (this.itemSelecionadoID) {
        this.montarParamentrosEditar();
        this.atualizar()
          .then(() => {
            if (this.bSalvarESair || this.concluido || this.cancelamento) {
              this.LIMPAR_ITEM_SELECIONADO();
              this.limparCampos();
              this.voltar();
            } else {
              this.limparCampos();
              this.openModal();
            }
            this.salvando = false;
          })
          .catch(() => {
            this.limparCampos();
            this.salvando = false;
          });
      } else {
        this.montarParametrosCriar(!!this.objAluno);
        this.criar()
          .then(() => {
            if (this.bSalvarESair || this.concluido) {
              this.voltar();
            } else {
              this.openModal();
            }
            this.salvando = false;
          })
          .catch(() => {
            this.salvando = false;
          });
      }
    },

    /**
     * Função para pegar lista de turma do aluno
     */
    getClassList(objectStudent) {
      let listaTurmaAluno = [];
      let arrayTurmaSelecionaveis = [
        { id: null, descricao: "Selecione", livro: null },
      ];

      if (objectStudent) {
        const aluno = Object.assign({}, objectStudent);

        aluno.contratos.map((contrato) => {
          if (contrato.turma) {
            listaTurmaAluno.push(contrato.turma);
          }
        });
      }
      this.listaDeTurma = arrayTurmaSelecionaveis.concat(listaTurmaAluno);
      this.setTurma(arrayTurmaSelecionaveis[0]);
      if (listaTurmaAluno.length === 1) {
        this.setTurma(listaTurmaAluno[0]);
      }
    },
  },
};
</script>
<style scoped>
.list-grades {
  list-style-type: none;
}

.list-grades li {
  padding: 5px;
  border-right: 1px solid gray;
}

.card, .form-control {
    border: 0;
    background-color: #c2cfd6
    }
    
</style>
