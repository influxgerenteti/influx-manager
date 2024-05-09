<template>
  <div class="animated fadeIn">
    <form
      :class="{ 'was-validated': !isValid }"
      class="needs-validation"
      novalidate
      @submit.prevent="salvar()"
    >
      <div v-if="isEdit" class="form-loading">
        <load-placeholder :loading="estaCarregando" />
      </div>
      <div class="content-sector sector-primary p-3">
        <div class="form-group row">
          <div class="col-md-6">
            <label for="descricao_ocorrencia" class="col-form-label"
              >Descrição *</label
            >
            <input
              v-model="descricaoOcorrencia"
              type="text"
              class="form-control"
              required
              maxlength="255"
            />
          </div>
          <div class="col-md-3">
            <label for="tipo_ocorrencia" class="col-form-label"
              >Tipo Ocorrência</label
            >
            <g-select
              id="tipo_ocorrencia"
              :value="tipoOcorrenciaSelecionada"
              :select="setTipoOcorrencia"
              :options="getOptions()"
              :invalid="!isValid && !tipoOcorrenciaSelecionada"
              label="descricao"
              track-by="id"
          
            />
            <div
              v-if="!isValid && !tipoOcorrenciaSelecionada"
              class="multiselect-invalid"
            >
              Selecione uma opção!
            </div>
          </div>
          <div class="col-md-3">
            <label for="situacao" class="col-form-label"
              >Situação *</label
            >
            <g-select
              id="situacao"
              :value="situacaoSelecionada"
              :select="setSituacaoSelecionada"
              :options="situacaoOpcoes"
              :invalid="!isValid && !situacaoSelecionada"
              label="text"
              track-by="value"
            />
            <div
              v-if="!isValid && !situacaoSelecionada"
              class="multiselect-invalid"
            >
              Selecione uma opção!
            </div>
            
          </div>
        </div>
      </div>

      <!--<div class="form-group list-group-accent">
        <div class="list-group-item list-group-item-accent-info list-group-item-info border-0">
          <font-awesome-icon icon="info-circle" /> As informações não salvas durante a adição/alteração, serão perdidas!
        </div>
      </div>-->

      <div class="form-group pt-2">
        <b-btn
          :disabled="isEnviando || !isCamposObrigatoriosPreenchidos()"
          type="submit"
          variant="verde"
          >{{ isEnviando ? "Salvando..." : "Salvar" }}</b-btn
        >
        <b-btn variant="link" @click="voltar()">Cancelar</b-btn>
      </div>
    </form>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from "vuex";
import { required } from "vuelidate/lib/validators";
import Request from '../../utils/request'
import tipoOcorrencia from '@/store/tipo-ocorrencia';


export default {
  name: "FormularioCadastroTipoOcorrencia",
  components: {},
  data() {
    return {
      indiceASerEditado: 0,
      isValid: true,
      isEdit: false,
      isEnviando: false,
      descricaoOcorrencia: "",
      tipoOcorrenciaSelecionada: "",
      tipoOcorrenciaInicial: null,
      situacaoSelecionada: { value: 1, text: "Ativo" },
      arrayItensDeletados: [],
      tipoOcorrenciaOpcoes: [],
      situacaoOpcoes: [
        { value: 1, text: "Ativo" },
        { value: 0, text: "Inativo" }
      ]
    };
  },
  computed: {
    ...mapState("cadastroTipoOcorrencia", [
      "itemSelecionado",
      "itemSelecionadoID",
      "estaCarregando",
    ]),
  },
  mounted() {
    
    this.getTipoOcorrencia();
    this.LIMPAR_ITEM_SELECIONADO();
    const id = this.$route.params.id;
    if (id) {
      this.isEdit = true;
      this.SET_ITEM_SELECIONADO_ID(id);
      this.buscar().then((item) => {
        let optionsTemp = this.getOptions().filter( el => el.id == item.tipo_pai);
        this.tipoOcorrenciaSelecionada = optionsTemp[0]
        this.descricaoOcorrencia = this.itemSelecionado.descricao;
      });
    }
  },
  validations: {
    descricaoOcorrencia: { required }
  },
  methods: {
    ...mapMutations("cadastroTipoOcorrencia", [
      "SET_ITEM_SELECIONADO_ID",
      "LIMPAR_ITEM_SELECIONADO",
      "SET_ESTA_CARREGANDO",
    ]),
    ...mapActions("cadastroTipoOcorrencia", ["buscar", "criar", "atualizar"]),

    isCamposObrigatoriosPreenchidos() {
      if (
        this.descricaoOcorrencia.trim().length > 0
        //this.tipoOcorrenciaSelecionada.value !== null
      ) {
        return true;
      }
      return false;
    },

    setTipoOcorrencia(value) {
      this.tipoOcorrenciaSelecionada = value;
    },

    setSituacaoSelecionada(value) {
      this.situacaoSelecionada = value;
    },

    getTipoOcorrencia() {
     Request.get(`/tipo_ocorrencia/listar`)
        .then(data => {
          const lista = data.body.corpo.itens
          var opcoesTemp = lista.filter(function(obj) { return obj.situacao != 0; });
          //console.log(opcoesTemp)
          this.tipoOcorrenciaOpcoes.push(opcoesTemp)
        })
        
    },

    getOptions(){
      if(this.tipoOcorrenciaOpcoes.length > 0){
        return [{id: null, descricao: 'Selecione'}].concat(this.tipoOcorrenciaOpcoes[0])
      } else {
        return []
      }
    },

    voltar() {
      this.LIMPAR_ITEM_SELECIONADO();
      this.$router.push("/cadastros/tipo-ocorrencia");
    },

    finalizaRequisicao() {
      //    console.info('Erro')
      this.isEnviando = false;
    },

    atualizaDadosListagem(){ 
      
    },

    montaParametrosRequisicao() {
      this.itemSelecionado.descricao = this.descricaoOcorrencia;
      this.itemSelecionado.tipo_pai = this.tipoOcorrenciaSelecionada.id;
      this.itemSelecionado.tipo = null
      this.itemSelecionado.situacao = this.situacaoSelecionada.value
    },

    salvar() {
      this.isEnviando = true;
      console.log(this.tipoOcorrenciaSelecionada)

      if (this.$v.$invalid) {
        this.isValid = false;
        this.isEnviando = false;
        return;
      }

      this.montaParametrosRequisicao();
      if (this.itemSelecionadoID) {
        this.atualizar().then(this.voltar).catch(this.finalizaRequisicao);
      } else {
        this.criar().then(this.voltar).catch(this.finalizaRequisicao);
      }
    },
  },
};
</script>
<style scoped>
.table-scroll {
  height: calc(100vh - 300px);
  height: -webkit-calc(100vh - 300px);
  height: -moz-calc(100vh - 300px);
}
.table-scroll tbody {
  height: calc(100% - 1rem);
  height: -webkit-calc(100% - 1rem);
  height: -moz-calc(100% - 1rem);
}
.table-scroll tbody > div {
  height: 100%;
}
</style>
