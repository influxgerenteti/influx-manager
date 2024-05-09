<template>
    <span 
        :class="['pill', 'pill-'+situationClass]" 
        v-b-tooltip.viewport.left.hover
        :title="textTooltip.toUpperCase()"
    >
        {{ situation.toUpperCase() }}
    </span>
  </template>
  
  <script>
  export default {
    name: 'PillSituation',
    props: {
      situation: {
        type: String,
        required: true
      },
      situationClass: {
        type: String,
        required: true
      },
      textTooltip: {
        type: String,
        required: true
      }
    },
    computed: {

    }
  };
  </script>
  
  <style scoped>
  .pill {
    padding: 3px 4px 1px;
    font-size: 10px;
    text-align: center;
    font-weight: 900;
    display: block;
    width: 90px;
    height: 20px;
    margin-left: 0px !important;
    border-radius: 10px;
    color: #fff;
  }


.pill-ati , .pill-abe, .pill-a{ /* Ativo */ /* Aberta */
  background-color: #00D1B2;
}
.pill-bol { /* Bolsista */
  background-color: #4a69c5;
}
.pill-ren { /* Renovar */
  background-color: #7957d5;
}
.pill-ina,.pill-i { /* Inativo */
  background-color: #FF3860;
}
.pill-int { /* Interessado */
  background-color: #ec8644;
}
.pill-lea { /* Lead */
  background-color: #FFDD57;
  color: #746c49;
}
.pill-for { /* Formado */
  background-color: #85d017;
}
.pill-des { /* Desistente */
  background-color: #a4b7c1;
}
.pill-tra { /* Trancado */
  background-color: #8c1b3f;
}

.pill-mud { /* Mudança */
  background-color: #23cbd2;
}

.pill-v { /* Vigente */
  background-color: #00D1B2;
}
.pill-e, .pill-enc { /* Encerrado */
  background-color: #a4b7c1;
}
.pill-r { /* Rescindido */
  background-color: #FF3860;
}
.pill-c, .pill-cc, .pill-can { /* Cancelado */
  background-color: #4A4A4A;
}
.pill-t { /* Trancado */
  background-color: #8c1b3f;
}

.pill-pen, .pill-p { /* Pendente/Cheque pendente - Vermelho */
  background-color: #FF3860;
}
.pill-ven { /* Vencido - Vermelho */
  background-color: #FF3860;
}
.pill-bai {
  background-color: #4A4A4A;
}
.pill-can, .pill-c, .pill-sub { /* Cancelado/Cheque cancelado/Renegociado */
  background-color: #4a4a4a;
}
.pill-liq, .pill-b { /* Liquidado/Cheque baixado */
  background-color: #00D1B2;
}
.pill-liq-pen { /* Liquidação pendente */
  background-color: #8398ff;
}

.pill-cre, .pill-con { /* Concluido */
  background-color: #00D1B2;
}

.pill-est {
  background-color: #666666;
}

.pill-exc, .pill-d { /* Devolvido/Cheque devolvido */
  background-color: #000000;
}
  </style>
  