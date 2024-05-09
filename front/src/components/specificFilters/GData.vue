<template>
  <div>
    <div class="date-inputs">
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">
            {{ labelDe }}
          </div>
        </div>
        <g-datepicker :value="dataDe" v-on:input="setDataDe" />
      </div>
      <div class="input-group">
        <div class="input-group-prepend">
          <div class="input-group-text">
            {{ labelAte }}
          </div>
        </div>
        <g-datepicker :value="dataAte" v-on:input="setDataAte" />
      </div>
    </div>
    <div v-if="aviso" class="floating-message bg-danger">
      {{ aviso }}
    </div>
  </div>
</template>
  
<script>
import moment from "moment";
import { dateToCompare } from "../../utils/date";

/**
   * COMO UTILIZAR O COMPONENTE
   * 
   * Dentro da tag g-data insira as propriedades que desejar:
      Confira a lista de props abaixo

   * Dentro da tag g-data declaro os seguintes eventos:
      @dataDe="filtros.dataDe = $event"
      @dataAte="filtros.dataAte = $event"
      @dataValida="dataValida = $event"
   */
export default {
  name: "GData",
  data() {
    return {
      hoje: null,
      hojeStr: null,
      dataDe: "",
      dataAte: "",
      aviso: "",
    };
  },
  props: {
    // Altera a descrição da data de
    labelDe: {
      type: String,
      default: "De",
      required: false,
    },
    // Altera a descrição da data até
    labelAte: {
      type: String,
      default: "Ate",
      required: false,
    },
    /** Configura a Data de Inicial
     * Faz com que o periodo seja ignorado
     */
    dataDeInicial: {
      required: false,
      validator: function (value) {
        return moment(value, "DD/MM/YYYY", true).isValid();
      },
    },
    /** Configura a Data até Inicial
     * Faz com que o periodo seja ignorado
     */
    dataAteInicial: {
      required: false,
      validator: function (value) {
        return moment(value, "DD/MM/YYYY", true).isValid();
      },
    },
    /** Configura qual o periodo inicial exemplo: mês anterior, dia atual.
     * Ignorado quando enviado dataDeInicial OU dataAteInicial
     */
    periodo: {
      type: String,
      required: false,
      default: "mes_anterior",
      validator: function (value) {
        let options = ["mes_anterior", "mes_corrente", "dia_atual", "sem_data"];
        return options.includes(value);
      },
    },
    validarMesmoAno: {
      type: Boolean,
      required: false,
      default: false,
    },
  },

  beforeMount() {
    this.initialValues();
  },

  methods: {
    validarDatas() {

      if (
        this.dataDe &&
        this.dataAte &&
        dateToCompare(this.dataDe) > dateToCompare(this.dataAte)
      ) {
        this.aviso = "A data inicial não pode ser posterior a data final.";
        this.$emit("dataValida", false);
        return;
      }
      if (this.validarMesmoAno) {
                if (this.dataDe && this.dataAte ) {
          const anoInicial = new Date(dateToCompare (this.dataDe)+' 03:00:00').getFullYear();
         
          const anoFinal = new Date(dateToCompare (this.dataAte)).getFullYear();
        
          if ( anoInicial  !==  anoFinal) {
            this.aviso =
              "A data inicial e a data final devem ser no mesmo ano!";
            this.$emit("dataValida", false);
            return;
          }
        }
      }

      this.aviso = "";
      this.$emit("dataValida", true);
    },

    initialValues() {
      if (this.dataDeInicial || this.dataAteInicial) {
        if (this.dataDeInicial) {
          this.setDataDe(
            new Date(this.dataDeInicial).toLocaleDateString("pt-BR")
          );
        }
        if (this.dataAteInicial) {
          this.setDataAte(
            new Date(this.dataAteInicial).toLocaleDateString("pt-BR")
          );
        }
      } else {
        this.hoje = new Date();
        switch (this.periodo) {
          case "mes_anterior":
            this.setDataDe(
              new Date(
                new Date(this.hoje).setMonth(this.hoje.getMonth() - 1)
              ).toLocaleDateString("pt-BR")
            );
            this.setDataAte(this.hoje.toLocaleDateString("pt-BR"));
            break;
          case "mes_corrente":
            this.setDataDe(this.hoje.toLocaleDateString("pt-BR"));
            this.setDataAte(
              new Date(
                new Date(this.hoje).setMonth(this.hoje.getMonth() + 1)
              ).toLocaleDateString("pt-BR")
            );
            break;
          case "dia_atual":
            this.setDataDe(this.hoje.toLocaleDateString("pt-BR"));
            this.setDataAte(this.hoje.toLocaleDateString("pt-BR"));
            break;
          case "sem_data":
            this.setDataDe("");
            this.setDataAte("");
            break;
          default:
            this.setDataDe(this.hoje.toLocaleDateString("pt-BR"));
            this.setDataAte(this.hoje.toLocaleDateString("pt-BR"));
            break;
        }
      }
    },

    setDataDe(value) {
      this.dataDe = value;
      this.validarDatas();
      this.$emit("dataDe", value, this.extraParam);
    },

    setDataAte(value) {
      this.dataAte = value;
      this.validarDatas();
      this.$emit("dataAte", value, this.extraParam);
    },

    resetData() {
      this.setDataDe("");
      this.setDataAte("");
    },
  },
};
</script>
<style scoped>
.date-inputs {
  display: flex;
  align-content: center;
  justify-content: center;
  gap: 5%;
}
</style>