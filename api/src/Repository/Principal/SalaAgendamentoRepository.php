<?php

namespace App\Repository\Principal;

use App\Entity\Principal\SalaAgendamentoPersonal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\VariaveisCompartilhadas;
use App\Helper\SituacoesSistema;
use App\Helper\ConstanteParametros;
use \Carbon\Carbon;
use \Carbon\CarbonImmutable;
use DateTime;

/**
 * @method SalaAgendamentoPersonal|null find($id, $lockMode = null, $lockVersion = null)
 * @method SalaAgendamentoPersonal|null findOneBy(array $criteria, array $orderBy = null)
 * @method SalaAgendamentoPersonal[]    findAll()
 * @method SalaAgendamentoPersonal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalaAgendamentoRepository extends ServiceEntityRepository
{

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SalaAgendamentoPersonal::class);
    }
   

    /**
     * Monta query principal
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder('sap');
        $queryBuilder->addSelect('sala_franqueada');
        $queryBuilder->addSelect('sala');
       
        $queryBuilder->join('sap.sala_franqueada', 'sala_franqueada');
        $queryBuilder->join('sala_franqueada.sala', 'sala');
      
        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->andWhere('sap.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Monta filtros
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    private function montarFiltros(&$queryBuilder, $parametros)
    { 
        if (isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true && empty($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === false) {
            $queryBuilder->andWhere('sap.sala_franqueada = :sala_franqueada');
            $queryBuilder->setParameter('sala_franqueada', $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]);
        }
    }

    /**
     * Lista salas agendamentos para personal
     *
     * @param array $parametros
     *
     * @return array
     */
    public function listar($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);

        $this->filtroDeDataSalasDoPersonal($queryBuilder, $parametros);
        $this->montarFiltros($queryBuilder, $parametros);
  
        return \App\Helper\FunctionHelper::retornaResultados($queryBuilder);
    }

     /**
     * Lista salas agendamentos para personal
     *
     * @param array $parametros
     *
     * @return array
     */
    public function buscarPersonalPorId($id, $parametros = [])
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->montarFiltrosID($queryBuilder, $id);

        $resultadoBusca = \App\Helper\FunctionHelper::retornaResultados($queryBuilder);

        if(empty($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]) || empty($parametros[ConstanteParametros::CHAVE_DATA_FINAL])) {
            return $resultadoBusca;
        }

        $dataInicial = new \DateTime($parametros[ConstanteParametros::CHAVE_DATA_INICIAL]);
        $dataInicial = $dataInicial->sub(\DateInterval::createFromDateString('1 day'));
        $dataFinal = new \DateTime($parametros[ConstanteParametros::CHAVE_DATA_FINAL]);

        $resultadoFinal = [];
        foreach($resultadoBusca as $disponibilidade) {
            $dataInicialTemp = clone($dataInicial);
            switch ($disponibilidade['dia_semana']) {
                case 'SEG':
                    $dataInicialTemp = $dataInicialTemp->modify( 'next monday' );
                    break;
                case 'TER':
                    $dataInicialTemp = $dataInicialTemp->modify( 'next Tuesday' );
                    break;
                case 'QUA':
                    $dataInicialTemp = $dataInicialTemp->modify( 'next Wednesday' );
                    break;
                case 'QUI':
                    $dataInicialTemp = $dataInicialTemp->modify( 'next Thursday' );
                    break;
                case 'SEX':
                    $dataInicialTemp = $dataInicialTemp->modify( 'next Friday' );
                    break;
                case 'SAB':
                    $dataInicialTemp = $dataInicialTemp->modify( 'next Saturday' );
                    break;
                case 'DOM':
                    $dataInicialTemp = $dataInicialTemp->modify( 'next Sunday' );                                                                                                                   
                    break;
            }
            $resultadoFinal[] = [
                'dia_semana' => $disponibilidade['dia_semana'],
                'hora_final' => $disponibilidade['hora_final'],
                'hora_inicial' => $disponibilidade['hora_inicial'],
                'data' => $dataInicialTemp->format('d/m/Y')
            ];
        }
        return $resultadoFinal;
    }

    /**
     * Monta filtros id
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    private function montarFiltrosId(&$queryBuilder, $id)
    { 
        $queryBuilder->andWhere('sap.sala_franqueada = :id');
        $queryBuilder->setParameter('id', $id);
    }

    /**
     * Monta filtro de data customizado para personal
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function filtroDeDataSalasDoPersonal ($queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_DATA]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_DATA]) === false)) {
                        $data = explode("T", $parametros[ConstanteParametros::CHAVE_DATA]);
                        
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->lte("sap.data_inicio", ":dataInicio"),
                        $queryBuilder->expr()->gte("sap.data_fim", ":dataFim")
                    )
                )
            );
            $queryBuilder->setParameter('dataInicio', $data[0] . " 00:00:01");
            $queryBuilder->setParameter('dataFim', $data[0] . " 23:59:59");
        }
    }

  
     /**
      * Filtra a salaAgendamentoPersonal por pagina
      *
      * @param array $parametros
      * @param number $pagina
      * @param number $numeroItensPorPagina
      *
      * @return \Knp\Component\Pager\Pagination\SlidingPagination
      */
    public function filtrarSalaAgendamentoPersonalPorPagina($parametros, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->filtroDeDataDoPersonal($queryBuilder, $parametros);
        $this->montarFiltros($queryBuilder, $parametros);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

      
    public function getBySalaFranqueada($salaFranqueadaId) {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.sala_franqueada = :sala_franqueada')
            ->setParameter('sala_franqueada', $salaFranqueadaId);
        return $queryBuilder->getQuery()->getResult();
    }

    public function deletar(SalaAgendamentoPersonal $salaAgendamento) {
        $em = $this->getEntityManager();
        $em->remove($salaAgendamento);
        $em->flush();
    }
}
