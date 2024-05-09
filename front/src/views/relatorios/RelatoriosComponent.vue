<template id="relatorios-todos">
  <div class="wrapper">
    <template v-for="(item, i) in items">
      <div v-if="item.relatorios.length > 0" class="item" :key="i">
        <div v-b-toggle="'col-' + i" class="categoria-title">
          {{ item.title }}
        <span style="text-aling: end" >
          +
        </span>
        </div>
        
        <template v-for="(rel, j) in item.relatorios">
          <div :key="j">
            <b-collapse :id="'col-' + i" class="mt-2">
              <router-link  :to="`${rel.link}`">
                <div
                  class="relatorio-item"
                
                >
                  <span class="relatorio-span">{{ rel.nome }} </span>
                  <img
                    class="relatorio-icon"
                    src="../../assets/img/icons/icon_novo.png"
                  />
                </div>
              </router-link>
            </b-collapse>
          </div>
        </template>
      </div>
    </template>
  </div>
</template>

<script>

export default {
  name: "RelatoriosComponent",
  components: {},
  data() {
    return {
      accordion: true,
      isLoading: false,
      count: 0,
      //
      items: [
        {
          title: "PEDAGÓGICO",
          relatorios: [
            { nome: "Alunos por Turma", link: "/relatorios/alunos-por-turma" },
            {
              nome: "Atividades Extra",
              link: "/relatorios/atividade-extra",
            },
            { nome: "Aulas Desmarcadas", link: "/relatorios/aula-desmarcada" },
            { nome: "Aulas Ocorridas", link: "/relatorios/aulas-ocorridas" },
            {
              nome: "Compromisso Aprendizado",
              link: "/relatorios/compromisso-aprendizado",
            },
            {
              nome: "Consulta de Conversão",
              link: "/relatorios/consulta-conversao",
            },
            {
              nome: "Consulta Desistência de Interessados",
              link: "/relatorios/consulta-desistencias",
            },
            {
              nome: "Dados de Cadastro",
              link: "/relatorios/dados-cadastro",
            },
            {
              nome: "Dados do Aluno",
              link: "/relatorios/dados-aluno",
            },
            { nome: "Frequência", link: "/relatorios/frequencia" },
            { nome: "Follow-Up", link: "/relatorios/follow-up" },
            {
              nome: "Impressão de Class Record",
              link: "/relatorios/impressao-class-record",
            },
            {
              nome: "Mapa de Sala e Turma",
              link: "/relatorios/mapa-sala-turma",
            },
            // { nome: "Notas Alunos", link: "/relatorios/nota" },
            // { nome: "Nota e Frequência", link: "/relatorios/nota-frequencia" },
            { nome: "Notas Turmas", link: "/relatorios/notas-turmas" },
            {
              nome: "Ocorrências",
              link: "/relatorios/ocorrencias",
            },
            { nome: "Retenção de Alunos", link: "/relatorios/retencao-alunos" },
            { nome: "Saldo de Horas", link: "/relatorios/saldo-horas" },
            { nome: "Situação Alunos", link: "/relatorios/alunos-situacao" },

            { nome: "Turmas Existentes", link: "/relatorios/turmas" },
          ],
        },
        {
          title: "FINANCEIRO",
          relatorios: [
            {
              nome: "Balancete",
              link: "/relatorios/relatorio-balancete-financeiro",
            },
            { nome: "Cheques", link: "/relatorios/relatorio-cheques" },
            {
              nome: "Contas a Pagar",
              link: "/relatorios/relatorio-contas-pagar",
            },
          //  {
          //    nome: "Contas a Receber",
          //    link: "/relatorios/titulos-a-vencer-vencidos",
          //  },
            { nome: "Descontos", link: "/relatorios/descontos" },
            { nome: "Fluxo de Caixa", link: "/relatorios/fluxocaixa" },
            { nome: "Inadimplência", link: "/relatorios/relatorio-inadimplencia" },
            { nome: "Valores por Turma", link: "/relatorios/valores-turma" },
          ],
        },
        {
          title: "RH",
          relatorios: [
            {
              nome: "Aniversariantes",
              link: "/relatorios/aniversariantes",
            },
            {
              nome: "Disponibilidade de Instrutores",
              link: "/relatorios/disponibilidade-de-instrutores",
            },
            { nome: "Funcionário", link: "/relatorios/informacoes-funcionarios" },
            // {
            //   nome: "Informações de Funcionários",
            //   link: "/relatorios/informacoes-funcionarios",
            // },
          ],
        },
        {
          title: "ADM",
          relatorios: [
            // {
            //   nome: "Comissões sobre matrículas",
            //   link: "/relatorios/comissoes-sobre-matriculas",
            // },
            {
              nome: "Controle Material Didático",
              link: "/relatorios/controle-material-didatico",
            },
            {
              nome: "Entrega de Material",
              link: "/cadastros/entrega-material",
            },
            { nome: "Estoque", link: "/relatorios/estoque" },
            {
              nome: "Geração de Mala Direta",
              link: "/relatorios/mala-direta-aluno",
            },
            { nome: "Itens de Estoque", link: "/relatorios/itens-de-estoque" },
            {
              nome: "Negociação de Convênio",
              link: "/relatorios/negociacao-convenios",
            },
            {
              nome: "Pedidos de Material",
              link: "/relatorios/pedido-material-didatico",
            },
            {
              nome: "Saídas de Estoque",
              link: "/relatorios/saidas-estoque",
            },
            {
              nome: "Serviços Solicitados",
              link: "/relatorios/servico-solicitado",
            },
          ],
        },
        {
          title: "COMERCIAL",
          relatorios: [
            { nome: "Contatos", link: "/relatorios/contatos" },
            { nome: "Interessados", link: "/relatorios/interessados" },
            { nome: "Matrículas", link: "/relatorios/matricula" },
            { nome: "Matrículas a Renovar", link: "/relatorios/matricula-a-renovar",
          },
          {
            nome: "Matrículas Perdidas",
            link: "/relatorios/matriculas-perdidas",
          },
          { nome: "Matrículas Vendas", link: "/relatorios/matriculas-venda" },
            { nome: "Prospecção", link: "/relatorios/prospeccao" },
            {
              nome: "Retornos Realizados",
              link: "/relatorios/retorno-consultor",
            },
            {
              nome: "Situação de Contrato",
              link: "/relatorios/contrato-situacao",
            },
            { nome: "Visitas", link: "/relatorios/visitas" },
          ],
        },
      ],
    };
  },
  computed: {},
  mounted() {},
  validations: {},
  methods: {},
};
</script>

<style>
.wrapper {
  width: 100%;
  /* display: grid;
  grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
  gap: 10px;
  grid-auto-flow: dense;
  min-width: 150px; */
}

template div {
  grid-column-end: span 2;
}
.item {
  overflow: auto;
  margin: 10px 10px;
  color: #4661ad;
  border-radius: 2px;
  box-shadow: rgb(0 0 0 / 9%) 0px 0px 2px, rgb(0 0 0 / 9%) 0px 0px 2px;
}

.categoria-title {
  display: flex;
  justify-content: space-between;
  padding: 0 15px;
  height: 46px;
  width: 100%;
  background-color: #3e59a9;
  align-items: center;
  color: #ffffff;
  /* border: 1px solid #3e59a9; */
  font-weight: 600;
  cursor: pointer;
}
.categoria-title span{
  font-weight: 600;
  font-size: 16px;
  color: #ffffff;
  background-color: #3e59a9;
  border: 1px solid #3e59a9;
  padding: 2px 8px;
  border-radius: 40px;

}
.categoria-title span:hover{
  cursor: pointer;
  border: 1px solid #3e59a9;
  background-color: #ffffff;
  color: #3e59a9;
  transition: all 450ms ease-in-out;



}

.relatorio-item {
  padding-left: 15px;
  padding-right: 15px;
  padding-top: 10px;
  display: flex;
  height: 40px;
  width: 100%;
  justify-content: space-between;
  cursor: pointer;
}
.relatorio-item:hover {
  background-color: #ebecf085;
  color: #696969;
  transform: 0.8s;
  font-weight: 400;
}
.relatorio-item img {
  padding: 0px 0px 8px 8px;
}

.relatorio-span {
  height: 30px;
}
.relatorio-icon {
  height: 30px;
  width: 30px;
}
</style>
