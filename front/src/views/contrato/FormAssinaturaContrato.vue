<template>
  <div class="info-card">
    <h2>Informações do Contrato</h2>
    <div class="content">
      <p><strong>Situação:</strong> <span v-if=" item.data_aceite"> {{"Aceito"}} </span> <span v-if=" !item.data_aceite"> {{"PENDENTE"}} </span></p>
      <p><strong>Data de Geração:</strong> {{ item.data_contrato | formatDate}}</p>
      <p><strong>Email do Aluno:</strong> {{ item.aluno.pessoa.email_preferencial
 }}</p>
      <div class="link-section">
        <strong>Link do contrato:</strong>
         
         <span class="text-link">{{linkContrato(item)}}</span>
        <a class="icone-link"  @click.prevent="copyLink(item)">
                  <font-awesome-icon icon="copy" />
                </a>
      </div>
      <!-- <p><strong>Data e Hora do Último Envio:</strong> {{ dataUltimoEnvio }}</p> -->
    </div>
    <div class="actions">
      <button class="cancel-button" @click="close()">Cancelar</button>
      <button v-if=" !item.data_aceite" class="resend-button" @click="reenviarLink">Reenviar Link</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      item:{},
      dataGeracao: '2023-09-07',
      emailAluno: 'aluno@email.com',
      linkAceite: 'https://example.com/aceite/alsdfhaas99a34ho8ha4ojh4vlshp8ah4onao;hbsaf48a;ohf8ohg4h4gh4;o84h;gag8h4;o8hw;ogsh8;o48hsogha',
      dataUltimoEnvio: '2023-09-07 10:00',
      onClose: null
    };
  },
  methods: {

    copyLink(item) {
      navigator.clipboard.writeText(this.linkContrato(item));
    },
    linkContrato(item) {
      let url = "https://influx-manager-assets.s3.sa-east-1.amazonaws.com/contratos/";
      if(item.chave_aceite){
        return url + item.chave_aceite + ".pdf";
      }
      else{
        return url + item.id + ".pdf";
      }
    },
    close() {
      this.onClose()
      // Lógica para a ação de cancelar (se necessário)
    },
    reenviarLink() {
      const franqueada = this.$store.state.root.usuarioLogado.franqueadaSelecionada
      const rota = this.$route.matched[0].path
      const auth = this.$store.state.root.usuarioLogado.usuario_acesso.token_acesso
      const contratoID = this.item.id
      const url = `/api/contrato/enviar/${contratoID}?Authorization=${auth}&URLModulo=${rota}&franqueada=${franqueada}`
      // open(url, '_blank')
      var host = process.env.VUE_APP_HOST;
      window.open(`${host}${url}`, '_blank')

    }
  }
};
</script>

<style scoped>
.info-card {
  background: white;
  padding: 20px;
  border-radius: 8px;
  width: 100%;
  max-width: 500px;
  /* box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22); */
}

.content p, .link-section {
  margin: 10px 0;
}

.actions {
  display: flex;
  justify-content: space-between;  
  margin-top: 20px;
}

button {
  background-color: #6200ea;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #3700b3;
}

.cancel-button {
  background-color: #9e9e9e;
}

.cancel-button:hover {
  background-color: #707070;
}

.link-section {
  display: flex;
  align-items: center;
}

.link-section > button {
  margin-left: 10px;
}

.icone-link{
  margin-left: 10px;
}
.text-link{
  color: black;
  overflow-wrap: anywhere;
}


</style>
