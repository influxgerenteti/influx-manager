<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Calendario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 * @method Calendario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Calendario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Calendario[]    findAll()
 * @method Calendario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendarioRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Calendario::class);
    }

    /**
     * Monta a query base para Livro
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("cal");
        $queryBuilder->addSelect("fran");
        $queryBuilder->leftJoin("cal.franqueada", "fran");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where("fran.id = :franqueadaId");
        $queryBuilder->orWhere("fran.franqueadora = :franqueadora");
        $queryBuilder->setParameter('franqueadaId', VariaveisCompartilhadas::$franqueadaID);
        $queryBuilder->setParameter('franqueadora', true);
    }

    /**
     * Monta os filtros de busca
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array $parametros
     */
    protected function montaFiltros(&$queryBuilder, $parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_ANO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_ANO]) === false)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('YEAR(cal.data_inicial)', $parametros[ConstanteParametros::CHAVE_ANO]),
                    $queryBuilder->expr()->eq('cal.feriado_anual', 1)
                )
            );
        } else {
            $queryBuilder->andWhere("cal.feriado_anual = 1");
        }
    }

    /**
     * Busca todos os calendarios
     *
     * @param array $parametros Listar parametros
     *
     * @return \App\Entity\Principal\Calendario[]
     */
    public function buscarTodos($parametros)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $this->montaFiltros($queryBuilder, $parametros);

        return $queryBuilder->getQuery()->getResult();


       
        

       

       
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param int $id
     *
     * @return array|NULL
     */
    public function buscarPorId($id)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);
        $queryBuilder->andWhere("cal.id = :id");
        $queryBuilder->setParameter("id", $id);

        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }

    /**
     * Busca o calendário padrão
     *
     * @return array|NULL
     */
    public function buscarCalendarioPadrao()
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarFranqueada($queryBuilder);

        return \App\Helper\FunctionHelper::retornaPrimeiroResultado($queryBuilder, false);
    }

    /**
     * Busca registros de dias não letivos segundo a data passada, franqueada e calendário
     *
     * @param \App\Entity\Principal\Franqueada $franqueada
     * @param \DateTime $data
     *
     * @return array|NULL
     */
    public function buscaDataNaoLetiva ($franqueada, $data)
    {
        $franqueadaId           = $franqueada->getId();
        $dataFormatada          = $data->format('Y-m-d');
        $dataFormatadaInicial   = $data->format('Y-m-d 00:00:00');
        $dataFormatadaFinal     = $data->format('Y-m-d 23:59:59');
        $dataFormatadaAnual = $data->format('m-d');

        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->leftJoin("cal.franqueada", "fran2");

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('fran2.franqueadora', 1),
                $queryBuilder->expr()->eq('fran.id', ':franqueId')
            )
        );

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->andX(
                   
                            $queryBuilder->expr()->lte("cal.data_inicial", ":dataInicio"),
                            $queryBuilder->expr()->gte("cal.data_final", ":dataFim"),
                        ),   
                   
                        $queryBuilder->expr()->like('cal.data_inicial', ':data'),
                    ),

                    $queryBuilder->expr()->eq('cal.feriado_anual', 0)
                ),
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->like('cal.data_inicial', ':dataAnual'),
                    $queryBuilder->expr()->eq('cal.feriado_anual', 1)
                )
            )
        );

        // $queryBuilder->andWhere("cal.data_inicial LIKE :data");
        $queryBuilder->andWhere("cal.dia_letivo = 0");
        $queryBuilder->setParameters(
            [
                'data'         => "$dataFormatada%",
                'dataInicio'   => "$dataFormatadaInicial",
                'dataFim'      => "$dataFormatadaFinal",
                'dataAnual'    => "%-$dataFormatadaAnual%",
                'franqueId'    => $franqueadaId,
            ]
        );
        return \App\Helper\FunctionHelper::retornaPrimeiroResultado($queryBuilder, true);
    }

     /**
      * Busca a data enviada por parametro se for feriado bancário
      *
      * @param \App\Entity\Principal\Franqueada $franqueada
      * @param \DateTime $data
      *
      * @return array|NULL
      */
    public function buscaFeriadoBancarioPorData ($franqueada, $data)
    {
        $franqueadaId  = $franqueada;
        $dataFormatada = $data;

        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->leftJoin("cal.franqueada", "fran2");

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('fran2.franqueadora', 1),
                $queryBuilder->expr()->eq('fran.id', ':franqueId')
            )
        );

        $queryBuilder->andWhere("cal.data_inicial LIKE :data");
        $queryBuilder->andWhere("cal.feriado_bancario = 0");
        $queryBuilder->setParameters(
            [
                'data'      => "$dataFormatada%",
                'franqueId' => $franqueadaId,
            ]
        );
        $queryObjeto = $queryBuilder->getQuery();
        return $queryObjeto->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }


}
