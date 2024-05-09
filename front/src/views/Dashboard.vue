<template>
  <div id="home-bg" class="animated fadeIn d-flex align-items-center justify-content-center">
    <div class="flex-grow-1 d-flex align-items-end">
      <h2 style="display: flex; justify-content: center; margin: 2em; text-align: center;">
        {{ saudacao }} {{ user.nome }}!
      </h2>
    </div>

    <img src="../assets/img/fluxie.png" alt="Fluxie" height="240px">

    <div class="blue-bg">
      <h2>Seja bem-vindo(a) ao inFlux Manager!</h2>
    </div>
  </div>
</template>

<script>
import moment from 'moment'

// import ConfigJSON from '../../environment.config.json'
// import host from '../utils/host-url'

export default {
  name: 'Dashboard',
  data () {
    return {
      saudacao: ''
    }
  },

  computed: {
    user () {
      return this.$store.state.root.usuarioLogado
    }
  },

  mounted () {

    

    this.$store.commit('root/SET_SHOW_BREADCRUMBS', false)

    const d = moment()
    if (d.hours() >= 12) {
      this.saudacao = 'Boa tarde'
    } else if (d.hours() < 12) {
      this.saudacao = 'Bom dia'
    } else {
      this.saudacao = 'Boa noite'
    }

    // let env = `${ConfigJSON.ENV}`
    if(process.env.NODE_ENV === 'development'){
      this.saudacao = "Atenção você esta acessando o ambiente DEVELOPMENT, seus dados podem ser perdidos."
    }
    
    if(process.env.NODE_ENV  === 'beta'){
      this.saudacao = "Atenção você esta acessando o ambiente BETA,  se quiser acessar o ambiente principal acesse manager.influx.com.br após limpar o seu cache."
    }
    
  },

  destroyed () {
    this.$store.commit('root/SET_SHOW_BREADCRUMBS', true)
  }
}
</script>

<style scoped>
#home-bg {
  margin: -8px;
  background-color: #B9C93B;
  color: #001D7E;
}

img {
  margin: 20px 0 -45px;
  position: relative;
  z-index: 2;
}

.blue-bg {
  position: relative;
  z-index: 1;
  width: 100%;
  text-align: center;
  height: 40vh;
  padding-top: 64px;
  background-color: #001D7E;
  color: #B9C93B;
}
</style>
