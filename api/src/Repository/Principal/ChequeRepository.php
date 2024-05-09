<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Cheque;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\FunctionHelper;

/**
 * @method Cheque|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cheque|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cheque[]    findAll()
 * @method Cheque[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChequeRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cheque::class);
    }

    /**
     * Monta query base de busca
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("ch");
        $queryBuilder->addSelect("fra");
        $queryBuilder->addSelect("atu");
        $queryBuilder->addSelect("mdc");
        $queryBuilder->addSelect("p");
        $queryBuilder->addSelect("tr");
        $queryBuilder->addSelect("tp");
        $queryBuilder->addSelect("al");
        $queryBuilder->addSelect("alpes");
        $queryBuilder->innerJoin("ch.franqueada", "fra");
        $queryBuilder->innerJoin("ch.atendente_usuario", "atu");
        $queryBuilder->leftJoin("ch.motivo_devolucao_cheque", "mdc");
        $queryBuilder->leftJoin("ch.pessoa", "p");
        $queryBuilder->leftJoin("ch.titulo_receber", "tr");
        $queryBuilder->leftJoin("tr.aluno", "al");
        $queryBuilder->leftJoin("al.pessoa", "alpes");
        $queryBuilder->leftJoin("ch.titulo_pagar", "tp");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('fra.id = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta os filtros relacionados a data
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaDatasParaFiltro(&$queryBuilder, $parametros)
    {
        if (isset($parametros[ConstanteParametros::CHAVE_MES_ENTRADA]) === true) {
            $mesInteiro = (int) $parametros[ConstanteParametros::CHAVE_MES_ENTRADA];
            if ($mesInteiro !== 0) {
                $dataMesAnoAtualPrimeiroDia = null;
                $dataMesAnoAtualUltimoDia   = null;
                FunctionHelper::montaPrimeiroUltimoDiaMesAnoAtual($mesInteiro, $dataMesAnoAtualPrimeiroDia, $dataMesAnoAtualUltimoDia);
                $queryBuilder->andWhere("ch.data_entrada >= :dataEntradaInicial");
                $queryBuilder->andWhere("ch.data_entrada <= :dataEntradaFinal");
                $queryBuilder->setParameter("dataEntradaInicial", $dataMesAnoAtualPrimeiroDia);
                $queryBuilder->setParameter("dataEntradaFinal", $dataMesAnoAtualUltimoDia);
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_MES_BOM_PARA]) === true) {
            $mesInteiro = (int) $parametros[ConstanteParametros::CHAVE_MES_BOM_PARA];
            if ($mesInteiro !== 0) {
                $dataMesAnoAtualPrimeiroDiaBomPara = null;
                $dataMesAnoAtualUltimoDiaBomPara   = null;
                FunctionHelper::montaPrimeiroUltimoDiaMesAnoAtual($mesInteiro, $dataMesAnoAtualPrimeiroDiaBomPara, $dataMesAnoAtualUltimoDiaBomPara);
                $queryBuilder->andWhere("ch.data_bom_para >= :dataBomParaInicial");
                $queryBuilder->andWhere("ch.data_bom_para <= :dataBomParaFinal");
                $queryBuilder->setParameter("dataBomParaInicial", $dataMesAnoAtualPrimeiroDiaBomPara);
                $queryBuilder->setParameter("dataBomParaFinal", $dataMesAnoAtualUltimoDiaBomPara);
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ENTRADA_INICIAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_ENTRADA_INICIAL]) === false)) {
            $dataObj = null;
            FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_ENTRADA_INICIAL], $dataObj);
            $queryBuilder->andWhere("ch.data_entrada >= :dataEntradaInicial");
            $queryBuilder->setParameter("dataEntradaInicial", $dataObj);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ENTRADA_FINAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_ENTRADA_FINAL]) === false)) {
            $dataObj = null;
            FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_ENTRADA_FINAL], $dataObj);
            $queryBuilder->andWhere("ch.data_entrada <= :dataEntradaFinal");
            $queryBuilder->setParameter("dataEntradaFinal", $dataObj);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA_INICIAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA_INICIAL]) === false)) {
            $dataObj = null;
            FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA_INICIAL], $dataObj);
            $queryBuilder->andWhere("ch.data_bom_para >= :dataBomParaInicial");
            $queryBuilder->setParameter("dataBomParaInicial", $dataObj);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA_FINAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA_FINAL]) === false)) {
            $dataObj = null;
            FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA_FINAL], $dataObj);
            $queryBuilder->andWhere("ch.data_bom_para <= :dataBomParaFinal");
            $queryBuilder->setParameter("dataBomParaFinal", $dataObj);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_DEVOLVIDO_INICIAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_DEVOLVIDO_INICIAL]) === false)) {
            $dataObj = null;
            FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_DEVOLVIDO_INICIAL], $dataObj);
            $queryBuilder->andWhere("ch.data_devolucao >= :dataDevolvidoInicial");
            $queryBuilder->setParameter("dataDevolvidoInicial", $dataObj);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_DEVOLVIDO_FINAL]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA_DEVOLVIDO_FINAL]) === false)) {
            $dataObj = null;
            FunctionHelper::formataCampoDateTimeJS($parametros[ConstanteParametros::CHAVE_DATA_DEVOLVIDO_FINAL], $dataObj);
            $queryBuilder->andWhere("ch.data_devolucao <= :dataDevolvidoFinal");
            $queryBuilder->setParameter("dataDevolvidoFinal", $dataObj);
        }
    }

    /**
     * Monta os filtros passado pela requisicao
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        $queryBuilder->andWhere("ch.excluido = :excluido");
        $queryBuilder->setParameter("excluido", false);

        if (isset($parametros[ConstanteParametros::CHAVE_TIPO]) === true) {
            $tipoInteiro = (int) $parametros[ConstanteParametros::CHAVE_TIPO];
            if ($tipoInteiro === 1) {
                $queryBuilder->andWhere("ch.tipo = :tipoCheque");
                $queryBuilder->setParameter("tipoCheque", "P");
            } else if ($tipoInteiro === 2) {
                $queryBuilder->andWhere("ch.tipo = :tipoCheque");
                $queryBuilder->setParameter("tipoCheque", "R");
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_NUMERO_CHEQUE]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NUMERO_CHEQUE]) === false)) {
            $queryBuilder->andWhere("ch.numero LIKE :numeroCheque");
            $queryBuilder->setParameter("numeroCheque", "%" . $parametros[ConstanteParametros::CHAVE_NUMERO_CHEQUE] . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ALUNO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_ALUNO]) === false)) {
            $queryBuilder->andWhere("UPPER(alpes.nome_contato) LIKE :nomeAluno");
            $queryBuilder->setParameter("nomeAluno", "%" . strtoupper($parametros[ConstanteParametros::CHAVE_ALUNO]) . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CONTA]) === true) && (is_numeric($parametros[ConstanteParametros::CHAVE_CONTA]) === true)) {
            $queryBuilder->andWhere("ch.conta LIKE :numeroConta");
            $queryBuilder->setParameter("numeroConta", "%" . $parametros[ConstanteParametros::CHAVE_CONTA] . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_AGENCIA]) === true) && (is_numeric($parametros[ConstanteParametros::CHAVE_AGENCIA]) === true)) {
            $queryBuilder->andWhere("ch.agencia LIKE :numeroAgencia");
            $queryBuilder->setParameter("numeroAgencia", "%" . $parametros[ConstanteParametros::CHAVE_AGENCIA] . "%");
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_BANCO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_BANCO]) === false)) {
            $queryBuilder->andWhere("ch.banco LIKE :bancoSelecionado");
            $queryBuilder->setParameter("bancoSelecionado", "%" . $parametros[ConstanteParametros::CHAVE_BANCO] . "%");
        }

        if (isset($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_INICIAL]) === true) {
            $numero = $parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_INICIAL];
            if ($numero > 0) {
                $queryBuilder->andWhere("ch.valor >= :valorInicial");
                $queryBuilder->setParameter("valorInicial", $numero);
            }
        }

        if (isset($parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_FINAL]) === true) {
            $numero = $parametros[ConstanteParametros::CHAVE_FILTRO_VALOR_FINAL];
            if ($numero > 0) {
                $queryBuilder->andWhere("ch.valor <= :valorFinal");
                $queryBuilder->setParameter("valorFinal", $numero);
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $queryBuilder->andWhere("ch.situacao IN (:situacaoCheque)");
            $queryBuilder->setParameter("situacaoCheque", $parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        $this->montaDatasParaFiltro($queryBuilder, $parametros);
    }

    /**
     * Realiza o filtro de cheques por paginas
     *
     * @param array $parametros
     * @param number $pagina
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarChequePorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Filtra o registro por ID
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarRegistroPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("ch.id = :id");
        $queryBuilder->setParameter("id", $id);
        return FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Monta a query para ser executada no relatório
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return string
     */
    public function prepararDadosRelatorio ($parametros)
    {
        $queryBuilder = $this->createQueryBuilder("ch");
        $queryBuilder->select([
            'ch.numero as numero_cheque',
            'pessoa.nome_contato',
            'ch.situacao',
            'ch.tipo',
            'conta_receber.descricao as conta',
            'motivo_devolucao_cheque.descricao as motivo_devolucao',
            "date_format(ch.data_bom_para, '%d/%m/%Y-%H:%i') as data_bom_para",
            "date_format(ch.data_entrada, '%d/%m/%Y-%H:%i') as data_entrada",
            "date_format(ch.data_baixa, '%d/%m/%Y-%H:%i') as data_baixa",
            "date_format(ch.data_devolucao, '%d/%m/%Y-%H:%i') as data_devolucao"
        ]);
        $queryBuilder->join('ch.franqueada', 'fra');
        $queryBuilder->join('ch.pessoa', 'pessoa');
        $queryBuilder->leftJoin('ch.titulo_receber', 'titulo_receber');
        $queryBuilder->leftJoin('titulo_receber.conta', 'conta_receber');
        $queryBuilder->leftJoin('ch.titulo_pagar', 'titulo_pagar');
        $queryBuilder->leftJoin('titulo_pagar.conta', 'conta_pagar');
        $queryBuilder->leftJoin('ch.motivo_devolucao_cheque', 'motivo_devolucao_cheque');
        $queryBuilder->leftJoin(\App\Entity\Principal\MovimentoConta::class, 'movimento_conta', 'WITH', 'movimento_conta.titulo_receber = titulo_receber OR movimento_conta.titulo_pagar = titulo_pagar');

        $queryBuilder->andWhere('ch.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);

        if(isset($parametros[ConstanteParametros::CHAVE_SITUACAO])) {
            $queryBuilder->andWhere("ch.situacao in (:situacao)");
            $queryBuilder->setParameter("situacao", $parametros[ConstanteParametros::CHAVE_SITUACAO ]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_TIPO])) {
            $queryBuilder->andWhere('ch.tipo in (:tipo)');
            $queryBuilder->setParameter('tipo', $parametros[ConstanteParametros::CHAVE_TIPO]);
        }

        if (is_null($parametros[ConstanteParametros::CHAVE_CONTA]) === false) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('conta_pagar', $parametros[ConstanteParametros::CHAVE_CONTA]),
                    $queryBuilder->expr()->eq('conta_receber', $parametros[ConstanteParametros::CHAVE_CONTA])
                )
            );
        }

        if (isset($parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE])) {
            $queryBuilder->andWhere('motivo_devolucao_cheque = :motivo_devolucao');
            $queryBuilder->setParameter('motivo_devolucao', $parametros[ConstanteParametros::CHAVE_MOTIVO_DEVOLUCAO_CHEQUE]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA_INICIAL])) {
            $queryBuilder->andWhere('ch.data_bom_para >= :data_bom_para_inicial');
            $queryBuilder->setParameter('data_bom_para_inicial', $parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA_INICIAL]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA_FINAL])) {
            $queryBuilder->andWhere('ch.data_bom_para <= :data_bom_para_final');
            $queryBuilder->setParameter('data_bom_para_final', $parametros[ConstanteParametros::CHAVE_DATA_BOM_PARA_FINAL]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_ENTRADA_INICIAL])) {
            $queryBuilder->andWhere('ch.data_entrada >= :data_entrada_inicial');
            $queryBuilder->setParameter('data_entrada_inicial', $parametros[ConstanteParametros::CHAVE_DATA_ENTRADA_INICIAL]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_ENTRADA_FINAL])) {
            $queryBuilder->andWhere('ch.data_entrada <= :data_entrada_final');
            $queryBuilder->setParameter('data_entrada_final', $parametros[ConstanteParametros::CHAVE_DATA_ENTRADA_FINAL]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_BAIXA_INICIAL])) {
            $queryBuilder->andWhere('ch.data_baixa >= :data_baixa_inicial');
            $queryBuilder->setParameter('data_baixa_inicial', $parametros[ConstanteParametros::CHAVE_DATA_BAIXA_INICIAL]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_BAIXA_FINAL])) {
            $queryBuilder->andWhere('ch.data_baixa <= :data_baixa_final');
            $queryBuilder->setParameter('data_baixa_final', $parametros[ConstanteParametros::CHAVE_DATA_BAIXA_FINAL]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_DEVOLUCAO_INICIAL])) {
            $queryBuilder->andWhere('ch.data_devolucao >= :data_devolucao_inicial');
            $queryBuilder->setParameter('data_devolucao_inicial', $parametros[ConstanteParametros::CHAVE_DATA_DEVOLUCAO_INICIAL]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_DATA_DEVOLUCAO_FINAL])) {
            $queryBuilder->andWhere('ch.data_devolucao <= :data_devolucao_final');
            $queryBuilder->setParameter('data_devolucao_final', $parametros[ConstanteParametros::CHAVE_DATA_DEVOLUCAO_FINAL]);
        }

        return $queryBuilder->getQuery()->getResult();
    }


}
