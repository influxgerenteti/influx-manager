<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ItemTituloReceber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;

/**
 * !Atenção:
 * *classe ainda não está sendo utilizada. A tabela foi criada para manter uma relação entre o item e o titulo
 * *a receber, pois é uma relação N pra N que não estava relacionada anteriormente. Mas para implementar isto,
 * *seria necessário engessar um pouco o processo (atualmente o título está todo aberto ao usuário).
 * *A princípio seria feito nesta nova forma, mas no final decidiu-se não fazer por enquanto.
 *
 * @method  ItemTituloReceber|null find($id, $lockMode = null, $lockVersion = null)
 * @method  ItemTituloReceber|null findOneBy(array $criteria, array $orderBy = null)
 * @method  ItemTituloReceber[]    findAll()
 * @method  ItemTituloReceber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @author  Augusto Fleith Comitti <augusto.comitti@gatilabs.com.br>
 * @license MIT https://gatilabs.com.br
 */
class ItemTituloReceberRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ItemTituloReceber::class);
    }

    /**
     * Monta queryBase de ligacoes
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("itr");
        // $queryBuilder->addSelect("cr");
        // $queryBuilder->addSelect("sacado");
        // $queryBuilder->addSelect("al");
        // $queryBuilder->addSelect("ctt");
        // $queryBuilder->addSelect("mdlt");
        // $queryBuilder->addSelect("tur");
        // $queryBuilder->addSelect("tma");
        // $queryBuilder->addSelect("agp");
        // $queryBuilder->addSelect("p");
        // $queryBuilder->addSelect("it");
        // $queryBuilder->addSelect("tpi");
        // $queryBuilder->addSelect("plc");
        // $queryBuilder->addSelect("ue");
        // $queryBuilder->leftJoin("icr.conta_receber", "cr");
        // $queryBuilder->leftJoin("cr.aluno", "al");
        // $queryBuilder->leftJoin("cr.contrato", "ctt");
        // $queryBuilder->leftJoin("cr.sacado_pessoa", "sacado");
        // $queryBuilder->leftJoin("ctt.turma", "tur");
        // $queryBuilder->leftJoin("ctt.modalidade_turma", "mdlt");
        // $queryBuilder->leftJoin("tur.turmaAulas", "tma");
        // $queryBuilder->leftJoin("ctt.agendamentoPersonals", "agp");
        // $queryBuilder->leftJoin("al.pessoa", "p");
        // $queryBuilder->leftJoin("icr.item", "it");
        // $queryBuilder->leftJoin("it.tipo_item", "tpi");
        // $queryBuilder->leftJoin("icr.plano_conta", "plc");
        // $queryBuilder->leftJoin("icr.usuario_entregue", "ue");
        return $queryBuilder;
    }


    // /**
    // * Adiciona os filtros De/Ate na query
    // *
    // * @param array $parametros
    // * @param \Doctrine\ORM\QueryBuilder $queryBuilder
    // */
    // protected function filtrosDeAte($parametros, &$queryBuilder)
    // {
    // $bFiltrarPersonal = false;
    // if ((isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === false)) {
    // $modalidadeTurmaRepository = $this->_em->getRepository(\App\Entity\Principal\ModalidadeTurma::class);
    // $modalidadeTurmaORM        = $modalidadeTurmaRepository->find($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
    // if ($modalidadeTurmaORM->getTipo() === 'PER') {
    // $bFiltrarPersonal = true;
    // $queryBuilder->andWhere("tur.id IS NULL");
    // }
    // $queryBuilder->andWhere("mdlt.id = :modalideTurma");
    // $queryBuilder->setParameter("modalideTurma", $parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]) === false)) {
    // if personal == false
    // $queryBuilder->andWhere("cr.data_emissao >= :dataSaidaInicio");
    // $queryBuilder->setParameter("dataSaidaInicio", $parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]) === false)) {
    // if personal == false
    // $queryBuilder->andWhere("cr.data_emissao <= :dataSaidaFim");
    // $queryBuilder->setParameter("dataSaidaFim", $parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]) === false)) {
    // $queryBuilder->andWhere("cr.data_emissao >= :dataSaidaInicio");
    // $queryBuilder->setParameter("dataSaidaInicio", $parametros[ConstanteParametros::CHAVE_DATA_SAIDA_INICIO]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]) === false)) {
    // $queryBuilder->andWhere("cr.data_emissao <= :dataSaidaFim");
    // $queryBuilder->setParameter("dataSaidaFim", $parametros[ConstanteParametros::CHAVE_DATA_SAIDA_FIM]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_INICIO]) === false)) {
    // $queryBuilder->andWhere("icr.data_entrega >= :dataEntregaInicio");
    // $queryBuilder->setParameter("dataEntregaInicio", $parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_INICIO]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_FIM]) === false)) {
    // $queryBuilder->andWhere("icr.data_entrega <= :dataEntregaFim");
    // $queryBuilder->setParameter("dataEntregaFim", $parametros[ConstanteParametros::CHAVE_DATA_ENTREGA_FIM]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_INICIAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_VALOR_INICIAL]) === false)) {
    // $queryBuilder->andWhere("icr.valor >= :valorInicio");
    // $queryBuilder->setParameter("valorInicio", $parametros[ConstanteParametros::CHAVE_VALOR_INICIAL]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_VALOR_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_VALOR_FIM]) === false)) {
    // $queryBuilder->andWhere("icr.valor <= :valorFim");
    // $queryBuilder->setParameter("valorFim", $parametros[ConstanteParametros::CHAVE_VALOR_FIM]);
    // }
    // if ($bFiltrarPersonal === true) {
    // if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]) === false)) {
    // $queryBuilder->andWhere("agp.inicio >= :dataAulaInicio");
    // $queryBuilder->setParameter("dataAulaInicio", $parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]) === false)) {
    // $queryBuilder->andWhere("agp.inicio <= :dataAulaFim");
    // $queryBuilder->setParameter("dataAulaFim", $parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]);
    // }
    // } else {
    // if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]) === false)) {
    // $queryBuilder->andWhere("tma.data_aula >= :dataAulaInicio");
    // $queryBuilder->setParameter("dataAulaInicio", $parametros[ConstanteParametros::CHAVE_DATA_AULA_INICIO]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]) === false)) {
    // $queryBuilder->andWhere("tma.data_aula <= :dataAulaFim");
    // $queryBuilder->setParameter("dataAulaFim", $parametros[ConstanteParametros::CHAVE_DATA_AULA_FIM]);
    // }
    // }//end if
    // }
    // /**
    // * Adiciona os filtros na query
    // *
    // * @param array $parametros
    // * @param \Doctrine\ORM\QueryBuilder $queryBuilder
    // */
    // protected function montaFiltros($parametros, &$queryBuilder)
    // {
    // $queryBuilder->where("cr.franqueada = :idFranqueada");
    // $queryBuilder->setParameter("idFranqueada", $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
    // $queryBuilder->andWhere("tpi.tipo = :tipoItem");
    // $queryBuilder->setParameter("tipoItem", 'P');
    // if ((isset($parametros[ConstanteParametros::CHAVE_TURMA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_TURMA]) === false)) {
    // $queryBuilder->andWhere("tur.id = :idTurma");
    // $queryBuilder->setParameter("idTurma", $parametros[ConstanteParametros::CHAVE_TURMA]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
    // $queryBuilder->andWhere("al.id = :idAluno");
    // $queryBuilder->setParameter("idAluno", $parametros[ConstanteParametros::CHAVE_ALUNO]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_ITEM]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ITEM]) === false)) {
    // $queryBuilder->andWhere("it.id = :idItem");
    // $queryBuilder->setParameter("idItem", $parametros[ConstanteParametros::CHAVE_ITEM]);
    // }
    // if ((isset($parametros[ConstanteParametros::CHAVE_USUARIO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_USUARIO]) === false)) {
    // $queryBuilder->andWhere("ue.id = :idUsuarioEntregue");
    // $queryBuilder->setParameter("idUsuarioEntregue", $parametros[ConstanteParametros::CHAVE_USUARIO]);
    // }
    // if (isset($parametros[ConstanteParametros::CHAVE_ITEM_ENTREGUE]) === true) {
    // $queryBuilder->andWhere("icr.situacao_entrega IN(:situacoesEntrega)");
    // $queryBuilder->setParameter("situacoesEntrega", $parametros[ConstanteParametros::CHAVE_ITEM_ENTREGUE]);
    // }
    // $this->filtrosDeAte($parametros, $queryBuilder);
    // }
    // /**
    // * Monta a query de paginacao
    // *
    // * @param array $parametros
    // * @param number $pagina
    // * @param number $numeroItensPorPagina
    // *
    // * @return \Knp\Component\Pager\Pagination\SlidingPagination
    // */
    // public function filtrarItemTituloReceberPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    // {
    // $queryBuilder = $this->montaQueryBase();
    // $this->montaFiltros($parametros, $queryBuilder);
    // return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina);
    // }
}
