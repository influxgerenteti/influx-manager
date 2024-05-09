<template>
  <div class="animated fadeIn">
    <div class="filtro-avancado body-sector">
      <div
        class="d-flex justify-content-between filtro-header head-content-sector"
      >
        <div>
          <!-- <div :class="{'filtro-selecionado': filtroSelecionado === 1}" class="btn" aria-controls="filtros-rapidos" aria-expanded="false" @click="filtroRapido = !filtroRapido, filtroAvancado = false, className = filtroRapido ? 'rapido-open' : null, filtroSelecionado = 1, limparFiltros()">Filtro Rápido</div> -->
          <div
            :class="{ 'filtro-selecionado': filtroSelecionado === 2 }"
            class="btn"
            aria-controls="filtros-avancados"
            aria-expanded="true"
            @click="
              (filtroAvancado = !filtroAvancado),
                (filtroRapido = false),
                (className = filtroAvancado ? 'filtro-open' : null),
                (filtroSelecionado = 2)
            "
          >
            Filtros
          </div>
        </div>
      </div>

      <b-collapse id="filtros-avancados" v-model="filtroAvancado">
        <form
          class="p-2"
          @submit.prevent="
            (buscaRapida = false), (buscaAvancada = true), filtrar()
          "
        >
          <div class="form-group row mb-0">
            <b-col sm="6" md="">
              <label
                v-help-hint="'lista-entrega-material_aluno_filtro_avancado'"
                for="aluno_filtro_avancado"
                class="col-form-label"
                >Aluno</label
              >
              <typeahead
                id="aluno_filtro_avancado"
                :item-hit="setAlunoTemporario"
                source-path="/api/aluno/buscar-nome"
                key-name="pessoa.nome_contato"
              />
            </b-col>

            <b-col sm="3" md="">
              <label
                v-help-hint="'lista-entraga-=material_item_filtro_avancado'"
                for="item_filtro_avancado"
                class="col-form-label"
                >Item
              </label>
              <typeahead
                id="item_filtro_avancado"
                :item-hit="setItemTemporario"
                source-path="/api/item/buscar_descricao"
                key-name="descricao"
              />
            </b-col>

            <b-col sm="6" md="">
              <label
                v-help-hint="'lista-entrega-material_turma_filtro_avancado'"
                for="descricao"
                class="col-form-label"
                >Turma</label
              >
              <typeahead
                id="turma_filtro_avancado"
                :item-hit="setTurma"
                source-path="/api/turma/buscar_descricao"
                key-name="descricao"
              />
            </b-col>

            <b-col sm="3" md="">
              <label
                v-help-hint="
                  'lista-entrega-material_modalidade_turma_filtro_avancado'
                "
                for="modalidade_turma_avancado"
                class="col-form-label"
                >Modalidade</label
              >
              <g-select
                id="modalidade_turma_avancado"
                :select="setModalidadeTurma"
                :value="modalidade_turma"
                :options="listaModalidadesTurmaRequisicao"
                label="descricao"
                track-by="id"
              />
            </b-col>
          </div>

          <div class="form-group row mb-0">
            <b-col sm="6" md="">
              <label
                v-help-hint="'lista-entrega-material_data_saida'"
                class="col-form-label"
                >Data Venda</label
              >
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_saida_inicio_temporario'"
                      :value="data_saida_inicio_temporario"
                      :selected="setDataSaidaInicio"
                    />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">à</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_saida_fim_temporario'"
                      :value="data_saida_fim_temporario"
                      :selected="setDataSaidaFim"
                    />
                  </div>
                </div>
              </div>
              <div
                v-if="
                  dateToCompare(data_saida_inicio_temporario) >
                    dateToCompare(data_saida_fim_temporario) &&
                  data_saida_fim_temporario !== ''
                "
                class="floating-message bg-danger"
              >
                Data inicial deve ser menor que a data final!
              </div>
            </b-col>

            <b-col sm="6" md="">
              <label
                v-help-hint="'lista-entrega-material_data_entrega'"
                class="col-form-label"
                >Data Entrega</label
              >
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_entrega_inicio_temporario'"
                      :value="data_entrega_inicio_temporario"
                      :selected="setDataEntregaInicio"
                    />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">à</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_entrega_fim_temporario'"
                      :value="data_entrega_fim_temporario"
                      :selected="setDataEntregaFim"
                    />
                  </div>
                </div>
              </div>
              <div
                v-if="
                  dateToCompare(data_entrega_inicio_temporario) >
                    dateToCompare(data_entrega_fim_temporario) &&
                  data_entrega_fim_temporario !== ''
                "
                class="floating-message bg-danger"
              >
                Data inicial deve ser menor que a data final!
              </div>
            </b-col>

            <b-col sm="6" md="">
              <label
                v-help-hint="'lista-entrega-material_data_inicio_aula'"
                class="col-form-label"
                >Data Inicio Aulas</label
              >
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">De</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_inicio_aula_temporario'"
                      :value="data_inicio_aula_temporario"
                      :selected="setDataInicioAula"
                    />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">à</div>
                    </div>
                    <g-datepicker
                      :element-id="'data_fim_aula_temporario'"
                      :value="data_fim_aula_temporario"
                      :selected="setDataFimAula"
                    />
                  </div>
                </div>
              </div>
              <div
                v-if="
                  dateToCompare(data_inicio_aula_temporario) >
                    dateToCompare(data_fim_aula_temporario) &&
                  data_fim_aula_temporario !== ''
                "
                class="floating-message bg-danger"
              >
                Data inicial deve ser menor que a data final!
              </div>
            </b-col>

            <b-col sm="6" md="">
              <label
                v-help-hint="'lista-entrega-material_valor'"
                class="col-form-label"
                >Valor</label
              >
              <div class="row">
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Min</div>
                    </div>
                    <input
                      v-money="moeda"
                      id="valor_inicial_temporario"
                      v-model="valor_inicial_temporario"
                      type="text"
                      class="form-control"
                      maxlength="9"
                    />
                  </div>
                </div>
                <div class="col">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text">Max</div>
                    </div>
                    <input
                      v-money="moeda"
                      id="valor_final_temporario"
                      v-model="valor_final_temporario"
                      type="text"
                      class="form-control"
                      maxlength="9"
                    />
                  </div>
                </div>
              </div>
            </b-col>
          </div>

          <div class="form-group row mb-0">
            <b-col sm="3" md="3">
              <label
                v-help-hint="'lista-entrega-material_usuario'"
                for="usuario"
                class="col-form-label"
                >Usuário do sistema</label
              >
              <g-select
                id="usuario"
                :select="setUsuario"
                :value="usuario_entregue_temporario"
                :options="listaUsuariosSelect"
                class="multiselect-truncate"
                label="nome"
                track-by="id"
              />
            </b-col>

            <b-col md="">
              <label
                v-help-hint="'lista-entrega-material_situacao_filtro_avancado'"
                for="situacao_filtro_avancado"
                class="col-form-label"
                >Situação</label
              >
              <div class="d-block">
                <b-form-checkbox-group
                  id="situacao_filtro_avancado"
                  v-model="selected"
                  :options="situacao"
                  buttons
                  button-variant="cinza"
                  class="checkbtn-line"
                  name="situacao_filtro_avancado"
                />
              </div>
            </b-col>

            <b-col sm="" md="2" class="d-flex">
              <button
                type="submit"
                class="btn btn-roxo text-uppercase mt-4 ml-auto btn-block"
                @click="(filtroAvancado = false), (className = null)"
              >
                Buscar
              </button>
            </b-col>
          </div>
        </form>
      </b-collapse>
    </div>

    <div class="table-responsive-sm">
      <g-table class="selectAll">
        <thead class="text-dark">
          <tr>
            <th class="coluna-checkbox" data-label="Selecionar todos">
              <b-form-checkbox
                v-if="selected.length === 1"
                id="marcar_todos"
                :disabled="
                  !selected.includes('N') &&
                  (!gerentePermitiu ||
                    !permissoes['ENTREGA_ITEM_PERMISSAO'].possui_permissao)
                "
                @change="adicionarTodosOsNaoEntregues"
              />
            </th>
            <th>Aluno/Pessoa</th>
            <th class="text-truncate size-210">Item</th>
            <th class="text-truncate size-100">Quantidade</th>
            <th class="text-truncate size-125">Valor</th>
            <th class="text-truncate coluna-data">Modalidade</th>
            <th class="text-truncate coluna-data">Data Venda</th>
            <th class="text-truncate coluna-data">Data Entrega</th>
            <th class="text-truncate size-175">Usuario</th>
            <th class="coluna-situacao-icone">Situação</th>
            <th class="coluna-icones"></th>
          </tr>
        </thead>
        <tbody ref="scroll-wrap">
          <perfect-scrollbar>
            <div v-if="estaCarregando" class="form-loading">
              <load-placeholder :loading="estaCarregando" />
            </div>
            <div
              v-if="!listaItems.length && !estaCarregando"
              class="busca-vazia"
            >
              <p>Nenhum resultado encontrado.</p>
            </div>

            <tr
              v-for="itemContaReceber in listaItems"
              :key="itemContaReceber.id"
            >
              <td class="coluna-checkbox" data-label="Selecionar">
                <b-form-checkbox
                  v-if="selected.length === 1"
                  :id="`entregaBox_${itemContaReceber.id}`"
                  :value="itemContaReceber"
                  v-model="listaIdItemContaReceber"
                />
              </td>
              <td data-label="Aluno/Pessoa" class="text-truncate">
                <span>{{
                  itemContaReceber.conta_receber.aluno
                    ? itemContaReceber.conta_receber.aluno.pessoa.nome_contato
                    : itemContaReceber.conta_receber.sacado_pessoa.nome_contato
                }}</span>
              </td>
              <td data-label="Item" class="text-truncate size-210">
                <span>{{ itemContaReceber.item.descricao }}</span>
              </td>
              <td data-label="Quantidade" class="text-truncate size-100">
                <span>{{ itemContaReceber.quantidade }}</span>
              </td>
              <td data-label="Valor" class="text-truncate size-125">
                <span>{{ itemContaReceber.valor | formatarMoeda }}</span>
              </td>
              <td data-label="Modalidade" class="text-truncate coluna-data">
                <span>{{
                  itemContaReceber.conta_receber.contrato
                    ? itemContaReceber.conta_receber.contrato.modalidade_turma
                        .descricao
                    : ""
                }}</span>
              </td>
              <td data-label="Data Venda" class="text-truncate coluna-data">
                <span>{{
                  itemContaReceber.conta_receber.data_emissao | formatarData
                }}</span>
              </td>
              <td data-label="Data Entrega" class="text-truncate coluna-data">
                <span>{{ itemContaReceber.data_entrega | formatarData }}</span>
              </td>
              <td data-label="Usuario" class="text-truncate size-175">
                <span>{{
                  itemContaReceber.usuario_entregue
                    ? itemContaReceber.usuario_entregue.nome
                    : ""
                }}</span>
              </td>

              <td data-label=" " class="coluna-situacao-icone">
                <PillSituation 
                    :situation="getSituacao(itemContaReceber.situacao_entrega)" 
                    :situationClass="getSufixoClass(itemContaReceber.situacao_entrega)" 
                    :textTooltip="getSituacao(itemContaReceber.situacao_entrega)"
                  >
                  </PillSituation>
              </td>

              <td class="d-flex coluna-icones">
                <template v-if="itemContaReceber.situacao_entrega === 'N'">
                  <a
                    v-b-tooltip.hover.left="'Entregar'"
                    :id="`entregar-${itemContaReceber.id}`"
                    data-title="Entregar"
                    href="#"
                    class="icone-link"
                    @click.prevent="verificaItens('E', itemContaReceber)"
                  >
                    <font-awesome-icon icon="truck" />
                  </a>
                </template>

                <template v-else-if="itemContaReceber.situacao_entrega === 'C'">
                  <a
                    v-b-tooltip.hover.left="'Entregar'"
                    v-input-locker="lockInput(itemContaReceber)"
                    :id="`entregar-${itemContaReceber.id}`"
                    data-title="Entregar"
                    href="#"
                    class="icone-link"
                    @click.prevent="verificaItens('E', itemContaReceber)"
                  >
                    <font-awesome-icon icon="truck" />
                  </a>
                </template>

                <template v-if="itemContaReceber.situacao_entrega === 'E'">
                  <a
                    v-b-tooltip.hover.left="'Cancelar'"
                    v-input-locker="lockInput(itemContaReceber)"
                    :id="`cancelar-${itemContaReceber.id}`"
                    data-title="Cancelar"
                    href="#"
                    class="icone-link"
                    @click.prevent="verificaItens('C', itemContaReceber)"
                  >
                    <font-awesome-icon icon="minus-square" />
                  </a>
                </template>
              </td>
            </tr>
          </perfect-scrollbar>
        </tbody>
      </g-table>
    </div>

    <div
      id="total-container"
      class="d-flex justify-content-between align-items-center"
    >
      <div class="d-flex">
        <div class="form-group row mb-0">
          <b-col v-if="!selected.includes('E')" sm="6" md="">
            <button
              :disabled="listaIdItemContaReceber.length === 0"
              class="btn btn-roxo"
              @click="verificaItens('E')"
            >
              Entregar selecionados
            </button>
          </b-col>

          <b-col v-if="!selected.includes('C')" sm="6" md="">
            <button
              v-input-locker="lockButton()"
              :disabled="
                (permissoes['ENTREGA_ITEM_PERMISSAO'] &&
                  permissoes['ENTREGA_ITEM_PERMISSAO'].possui_permissao ===
                    false &&
                  !gerentePermitiu) ||
                listaIdItemContaReceber.length === 0
              "
              class="btn btn-vermelho"
              @click="verificaItens('C')"
            >
              Cancelar selecionados
            </button>
          </b-col>
          <b-col v-if="selected.includes('E')" sm="6" md="">
            <button class="btn btn-verde" @click="gerarRecibo()">
              Imprimir Recibo
            </button>
          </b-col>
        </div>
      </div>

      <div class="info-label">
        <div class="text-right">
          <small>
            {{ totalItens === 0 ? "Nenhum" : totalItens }} ite{{
              totalItens > 1 ? "ns" : "m"
            }}
            encontrado{{ totalItens > 1 ? "s" : "" }}
            <br />
            {{
              listaIdItemContaReceber.length === 0
                ? "Nenhum"
                : listaIdItemContaReceber.length
            }}
            ite{{
              listaIdItemContaReceber.length > 1 ? "ns" : "m"
            }}
            selecionado{{ listaIdItemContaReceber.length > 1 ? "s" : "" }}
          </small>
        </div>
      </div>
    </div>

    <b-modal
      id="entregaMaterialModal"
      ref="entregaMaterialModal"
      size="sm"
      centered
      no-close-on-backdrop
      hide-header
      hide-footer
    >
      <form-entrega-material ref="formEntregaMaterial" />
    </b-modal>
  </div>
</template>

<script>
import { mapState, mapActions, mapMutations } from "vuex";
import {
  beginOfDay,
  endOfDay,
  getDateFromISO,
  dateToCompare,
  stringToISODate,
} from "../../utils/date";
import FormEntregaMaterial from "./FormEntregaMaterial.vue";
import PillSituation from "../../components/PillSituation.vue";
import { currencyToNumber } from "../../utils/number";
import moment from "moment";
import EventBus from "../../utils/event-bus";
// import HostUrl from "../../utils/host-url";
var host = process.env.VUE_APP_HOST;

export default {
  name: "ListaEntregaMaterial",
  components: {
    FormEntregaMaterial,
    PillSituation
  },
  data() {
    return {
      className: "rapido-open",
      data_saida_inicio_temporario: "",
      data_saida_fim_temporario: "",
      data_inicio_aula_temporario: "",
      data_fim_aula_temporario: "",
      data_entrega_inicio_temporario: "",
      data_entrega_fim_temporario: "",
      gerentePermitiu: false, // indica ao input-locker que já foi desbloqueado
      gerenteId: null,
      buscaAvancada: false,
      buscaRapida: false,
      checkAll: false,
      indeterminate: false,
      filtroAvancado: false,
      enviandoDataEntrega: false,
      filtroRapido: true,
      filtroSelecionado: 2,
      valor_inicial: null,
      valor_final: null,
      valor_inicial_temporario: null,
      valor_final_temporario: null,
      usuario_entregue_temporario: null,
      aluno_temporario: null,
      item_temporario: null,
      turma_temporario: null,
      listaId: [],
      listaIdItemContaReceber: [],
      selected: ["N"],
      situacao: [
        { text: "Entregue", value: "E", sufixoClass: "ati" },
        { text: "Não Entregue", value: "N", sufixoClass: "lea" },
        { text: "Cancelado", value: "C", sufixoClass: "pen" },
      ],
      moeda: {
        decimal: ",",
        thousands: ".",
        precision: 2,
        masked: true,
      },
      input_locker_callback: null,

      data_inicial_temporario: "",
      data_final_temporario: "",

      modalidade_turma: null,
    };
  },
  computed: {
    ...mapState("usuarios", ["listaUsuarios"]),
    ...mapState("itemContaReceber", {
      listaItems: "lista",
      estaCarregando: "estaCarregando",
      totalItens: "totalItens",
    }),
    ...mapState("modulos", ["permissoes"]),

    listaUsuariosSelect: {
      get() {
        return [{ nome: "Selecione", id: null }].concat(this.listaUsuarios);
      },
    },

    listaModalidadesTurmaRequisicao: {
      get() {
        return [{ nome: "Selecione", id: null }].concat(
          this.$store.state.modalidadeTurma.lista
        );
      },
    },
  },

  watch: {
    input_locker_callback(value) {
      if (value && value.id) {
        this.gerentePermitiu = true;
        this.gerenteId = value.id;
      }
    },
  },

  mounted() {
    this.selected = ["N"];
    this.$store.commit("modalidadeTurma/SET_PAGINA_ATUAL", 1);
    this.$store.dispatch("modalidadeTurma/listar").then(() => {
      // this.modalidade_turma = this.listaModalidadesTurmaRequisicao.find(modalidade => modalidade.tipo === 'TUR')
      this.SET_PAGINA_ATUAL(1);
      this.filtrar();
      this.listarCamposSelects();
    });
  },
  methods: {
    ...mapActions("itemContaReceber", {
      listarItems: "listar",
      atualizarStatusEntrega: "atualizarStatusEntrega",
      imprimirRecibo: "imprimirRecibo",
    }),
    ...mapMutations("itemContaReceber", [
      "SET_PAGINA_ATUAL",
      "SET_FILTRO_ALUNO_ID",
      "SET_FILTRO_ITEM_ID",
      "SET_FILTRO_USUARIO_ID",
      "SET_FILTRO_VALOR_INICIAL",
      "SET_FILTRO_VALOR_FINAL",
      "SET_FILTRO_DATA_AULA_INICIO",
      "SET_FILTRO_DATA_AULA_FIM",
      "SET_FILTRO_DATA_SAIDA_INICIO",
      "SET_FILTRO_DATA_SAIDA_FIM",
      "SET_FILTRO_DATA_ENTREGA_INICIO",
      "SET_FILTRO_DATA_ENTREGA_FIM",
      "SET_FILTRO_DATA_INICIO",
      "SET_FILTRO_DATA_FIM",
      "SET_FILTRO_ENTREGUE",
      "SET_FILTRO_TURMA",
      "SET_ITEM_SELECIONADO",
      "SET_FILTRO_MODALILADE_TURMA",
    ]),

    getDateFromISO: getDateFromISO,

    dateToCompare: dateToCompare,

    getSituacao (situacao) {
      const objSituacao = this.situacao.find(item => item.value === situacao)
      return objSituacao.text
    },
    getSufixoClass (situacao) {
      const objSituacao = this.situacao.find(item => item.value === situacao)
      return objSituacao.sufixoClass
    },

    lockButton() {
      if (!this.gerentePermitiu) {
        return {
          permissao: this.permissoes["ENTREGA_ITEM_PERMISSAO"],
          callBack: this,
        };
      }
    },

    lockInput(item) {
      if (
        !this.gerentePermitiu &&
        new RegExp(/(e|n|c)/i).test(item.situacao_entrega)
      ) {
        return {
          permissao: this.permissoes["ENTREGA_ITEM_PERMISSAO"],
          callBack: this,
        };
      }
    },

    verificaItens(sSituacao, itemContaReceberObj) {
      if (sSituacao === "E") {
        console.log("aqui");
        this.configuraModal(sSituacao, itemContaReceberObj);
      } else {
        if (sSituacao === "C") {
          EventBus.$emit(
            "chamarModal",
            {
              resolve: (success) => {
                if (
                  this.gerentePermitiu ||
                  this.permissoes["ENTREGA_ITEM_PERMISSAO"].possui_permissao
                ) {
                  if (itemContaReceberObj) {
                    this.salvar(
                      moment().format("DD/MM/YYYY"),
                      sSituacao,
                      this.gerenteId,
                      itemContaReceberObj.id
                    );
                  } else {
                    this.salvar(
                      moment().format("DD/MM/YYYY"),
                      sSituacao,
                      this.gerenteId
                    );
                  }
                }
              },
            },
            `Cancelar entrega selecionada ?`
          );
        } else {
          if (
            this.gerentePermitiu ||
            this.permissoes["ENTREGA_ITEM_PERMISSAO"].possui_permissao
          ) {
            if (itemContaReceberObj) {
              this.salvar(
                moment().format("DD/MM/YYYY"),
                sSituacao,
                this.gerenteId,
                itemContaReceberObj.id
              );
            } else {
              this.salvar(
                moment().format("DD/MM/YYYY"),
                sSituacao,
                this.gerenteId
              );
            }
          }
        }
      }
    },

    configuraModal(sSituacaoNova, itemContaReceberObj) {
      console.log(itemContaReceberObj);
      let callbackFuncoes = {
        salvar: this.salvar,
        cancelar: this.cancelar,
        gerarRecibo: this.gerarRecibo,
      };
      console.log(this.gerarRecibo);
      this.$refs.entregaMaterialModal.show();
      //espera a tela carregar, se der problema implementar um callback
      setTimeout(() => {
        this.$refs.formEntregaMaterial.itemArrayOuObjeto = itemContaReceberObj;
        this.$refs.formEntregaMaterial.alterarSituacaoEntrega = sSituacaoNova;
        this.$refs.formEntregaMaterial.listaCallback = callbackFuncoes;
      }, 500);
    },

    salvar(dataEntregaSelecionada, situacaoEntrega, usuarioId, itemId) {
      let listaId = itemId
        ? [itemId]
        : this.listaIdItemContaReceber.map((icrObj) => icrObj.id);
      console.log(listaId);
      let objParams = {
        data_entrega: stringToISODate(dataEntregaSelecionada, true),
        lista_id: listaId,
        situacao_entrega: situacaoEntrega,
      };
      if (usuarioId) {
        objParams.usuario_autorizacao = usuarioId;
      }
      this.atualizarStatusEntrega(objParams).finally(() => {
        this.cancelar();
      });
    },

    gerarRecibo(itemId) {
      let listaId = itemId
        ? [itemId]
        : this.listaIdItemContaReceber.map((icrObj) => icrObj.id);

      const usuario = this.$store.state.root.usuarioLogado.id;
      const franqueada =
        this.$store.state.root.usuarioLogado.franqueadaSelecionada;
      const auth =
        this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso;

      const listaIds = listaId.map((id) => [id]);
      const listaIdsConcatenado = listaIds
        .flatMap((ids) => ids)
        .join("&lista_id[]=");
      const url = `${host}/api/recibo_entrega_item/imprimir?usuario=${usuario}&franqueada=${franqueada}&lista_id[]=${listaIdsConcatenado}&Authorization=${auth}`;

      open(url, "_blank");
    },

    cancelar() {
      this.listaIdItemContaReceber = [];
      this.$refs.entregaMaterialModal.hide();
      this.filtrar();
    },

    addRemoveLista(valueChecked, itemContaReceberObj) {
      if (valueChecked === true) {
        this.listaIdItemContaReceber.push(itemContaReceberObj);
      } else {
        let indexElement = this.listaIdItemContaReceber(
          (icrObj) => icrObj.id === itemContaReceberObj.id
        );
        this.listaIdItemContaReceber.splice(indexElement);
      }
    },

    adicionarTodosOsNaoEntregues(valueChecked) {
      if (valueChecked === true) {
        this.listaIdItemContaReceber = this.listaItems;
      } else {
        this.listaIdItemContaReceber = [];
      }
    },

    listarCamposSelects() {
      this.$store.commit("usuarios/SET_PAGINA_ATUAL", 1);
      this.$store.dispatch("usuarios/getListaUsuarios");
    },

    setDataInicioAula(value) {
      this.data_inicio_aula_temporario = value;
    },

    setDataFimAula(value) {
      this.data_fim_aula_temporario = value;
    },

    setDataSaidaInicio(value) {
      this.data_saida_inicio_temporario = value;
    },

    setDataSaidaFim(value) {
      this.data_saida_fim_temporario = value;
    },

    setDataEntregaInicio(value) {
      this.data_entrega_inicio_temporario = value;
    },

    setDataEntregaMaterialTemporario(value) {
      this.data_entrega_material_temporario = value;
    },

    setDataEntregaFim(value) {
      this.data_entrega_fim_temporario = value;
    },

    setAlunoTemporario(value) {
      this.aluno_temporario = value;
      if (this.filtroSelecionado === 1) {
        this.filtrar();
      }
    },

    setItemTemporario(value) {
      this.item_temporario = value;
      if (this.filtroSelecionado === 1) {
        this.filtrar();
      }
    },

    setTurma(value) {
      this.turma_temporario = value;
      if (this.filtroSelecionado === 1) {
        this.filtrar();
      }
    },

    setModalidadeTurma(value) {
      this.modalidade_turma = value;
      if (this.filtroSelecionado === 1) {
        this.filtrar();
      }
    },

    setUsuario(value) {
      this.usuario_entregue_temporario = value;
    },

    setUsuarioTemporario(value) {
      this.usuario_entrega_temporario = value;
    },

    setDataInicial(value) {
      this.data_inicial_temporario = value;
    },

    setDataFinal(value) {
      this.data_final_temporario = value;
    },

    setSituacao(value) {
      this.listaIdItemContaReceber = [];
      // this.selected = value

      this.filtrar();
    },

    limparStateAnterior() {
      this.SET_FILTRO_ALUNO_ID(null);
      this.SET_FILTRO_ITEM_ID(null);
      this.SET_FILTRO_USUARIO_ID(null);
      this.SET_FILTRO_DATA_AULA_INICIO(null);
      this.SET_FILTRO_DATA_AULA_FIM(null);
      this.SET_FILTRO_DATA_SAIDA_INICIO(null);
      this.SET_FILTRO_DATA_SAIDA_FIM(null);
      this.SET_FILTRO_DATA_ENTREGA_INICIO(null);
      this.SET_FILTRO_DATA_ENTREGA_FIM(null);
      this.SET_FILTRO_VALOR_INICIAL(null);
      this.SET_FILTRO_VALOR_FINAL(null);
      this.SET_FILTRO_TURMA(null);
      this.SET_FILTRO_ENTREGUE(0);

      this.SET_FILTRO_DATA_INICIO(null);
      this.SET_FILTRO_DATA_FIM(null);
      this.SET_FILTRO_MODALILADE_TURMA(null);
    },

    executaFiltroRapido() {
      this.SET_FILTRO_ALUNO_ID(
        this.aluno_temporario ? this.aluno_temporario.id : null
      );
      this.SET_FILTRO_ITEM_ID(
        this.item_temporario ? this.item_temporario.id : null
      );
      this.SET_FILTRO_USUARIO_ID(
        this.usuario_entregue_temporario
          ? this.usuario_entregue_temporario.id
          : null
      );
      this.SET_FILTRO_ENTREGUE(this.selected);
      this.SET_FILTRO_TURMA(
        this.turma_temporario ? this.turma_temporario.id : null
      );

      const modalidade = this.modalidade_turma
        ? this.modalidade_turma.id
        : null;
      this.SET_FILTRO_MODALILADE_TURMA(modalidade);
    },

    executaFiltroAvancado() {
      this.SET_FILTRO_ALUNO_ID(
        this.aluno_temporario ? this.aluno_temporario.id : null
      );
      this.SET_FILTRO_ITEM_ID(
        this.item_temporario ? this.item_temporario.id : null
      );
      this.SET_FILTRO_USUARIO_ID(
        this.usuario_entregue_temporario
          ? this.usuario_entregue_temporario.id
          : null
      );
      this.SET_FILTRO_ENTREGUE(this.selected);
      this.SET_FILTRO_TURMA(
        this.turma_temporario ? this.turma_temporario.id : null
      );
      const valorInicial = this.valor_inicial_temporario
        ? currencyToNumber(this.valor_inicial_temporario)
        : null;
      const valorFinal = this.valor_final_temporario
        ? currencyToNumber(this.valor_final_temporario)
        : null;
      this.SET_FILTRO_VALOR_INICIAL(valorInicial);
      this.SET_FILTRO_VALOR_FINAL(valorFinal);

      const dataAulaInicio = this.data_inicio_aula_temporario
        ? beginOfDay(this.data_inicio_aula_temporario)
        : null;
      const dataAulaFim = this.data_fim_aula_temporario
        ? endOfDay(this.data_fim_aula_temporario)
        : null;
      const dataSaidaInicio = this.data_saida_inicio_temporario
        ? beginOfDay(this.data_saida_inicio_temporario)
        : null;
      const dataSaidaFim = this.data_saida_fim_temporario
        ? endOfDay(this.data_saida_fim_temporario)
        : null;
      const dataEntregaInicio = this.data_entrega_inicio_temporario
        ? beginOfDay(this.data_entrega_inicio_temporario)
        : null;
      const dataEntregaFim = this.data_entrega_fim_temporario
        ? endOfDay(this.data_entrega_fim_temporario)
        : null;

      this.SET_FILTRO_DATA_AULA_INICIO(dataAulaInicio);
      this.SET_FILTRO_DATA_AULA_FIM(dataAulaFim);
      this.SET_FILTRO_DATA_SAIDA_INICIO(dataSaidaInicio);
      this.SET_FILTRO_DATA_SAIDA_FIM(dataSaidaFim);
      this.SET_FILTRO_DATA_ENTREGA_INICIO(dataEntregaInicio);
      this.SET_FILTRO_DATA_ENTREGA_FIM(dataEntregaFim);

      const modalidade = this.modalidade_turma
        ? this.modalidade_turma.id
        : null;
      this.SET_FILTRO_MODALILADE_TURMA(modalidade);
    },

    filtrar() {
      this.limparStateAnterior();
      if (this.filtroSelecionado === 1) {
        this.executaFiltroRapido();
      } else {
        this.executaFiltroAvancado();
      }
      this.SET_PAGINA_ATUAL(1);
      this.listarItems();
    },

    limparFiltros() {
      this.data_inicio_aula_temporario = "";
      this.data_fim_aula_temporario = "";
      this.data_entrega_inicio_temporario = "";
      this.data_entrega_fim_temporario = "";
      this.data_saida_inicio_temporario = "";
      this.data_saida_fim_temporario = "";
      this.valor_inicial_temporario = null;
      this.valor_final_temporario = null;
      this.usuario_entregue_temporario = null;
    },
  },
};

//   console.log(listaId)
//   this.$router.push("/cadastros/entrega-material/?listaId[]");
</script>
<style scoped>
span.badge {
  font-size: 95%;
}

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
  /* color: #4a69c5; */
  color: #151b1e;
  background-color: #fff;
  /* cursor: default; */
}
.filtro-avancado .input-group-text {
  border: 0;
  background-color: #e5e5e5;
}

.input-group > .datepicker {
  position: relative;
  flex: 1 1 auto;
  width: 1%;
  margin-bottom: 0;
}

.datepicker {
  padding: 0;
}

.table-actions {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  color: #3e515b;
  border-top: 1px solid #ebecf0;
}

@media (min-width: 769px){
  .table-sm .coluna-situacao-icone {
      max-width: 100px;
  }
}
</style>
