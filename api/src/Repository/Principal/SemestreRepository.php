<?php

namespace App\Repository\Principal;

use App\Entity\Principal\Semestre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use DateInterval;

/**
 * @method Semestre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Semestre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Semestre[]    findAll()
 * @method Semestre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SemestreRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Semestre::class);
    }

    /**
     * Monta QueryBuilder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("sm");

        return $queryBuilder;
    }

    /**
     * Filtra os Semestres por paginas
     *
     * @param array $parametros
     * @param number $numeroItensPorPagina
     *
     * @return \Knp\Component\Pager\Pagination\SlidingPagination
     */
    public function filtrarSemestrePorPagina($parametros, $numeroItensPorPagina=50)
    {
        $queryBuilder = $this->montaQueryBase();

        if ($parametros[ConstanteParametros::CHAVE_LISTAR_PROXIMOS] !== '0') {
            $queryBuilder->andWhere("sm.data_termino > :hoje");
            $queryBuilder->setParameter("hoje", new \DateTime());
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $parametros[ConstanteParametros::CHAVE_PAGINA], $numeroItensPorPagina);
    }


    /**
     * Busca semestre atual baseado na data do servidor
     *
     * @return mixed|\App\Entity\Principal\Semestre|NULL
     */
    public function buscarSemestreAtual()
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtraSemestreAtual($queryBuilder);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Aplicar filtro para buscar semestre atual
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder query para aplicação do filtro
     *
     * @return void
     */
    public function filtraSemestreAtual(&$queryBuilder)
    {
        $data     = new \DateTime();
        $ano      = (int) $data->format("Y");
        $mes      = (int) $data->format("m");
        $semestre = "";
        if ($mes <= 6) {
            $semestre = "$ano/01";
        } else {
            $semestre = "$ano/02";
        }

        $this->filtrarPorDescricao($queryBuilder, $semestre);
    }

    /**
     * Aplica filtro pela descrição do semestre
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder query para aplicação do filtro
     * @param string $descricao
     *
     * @return void
     */
    public function filtrarPorDescricao (&$queryBuilder, $descricao)
    {
        $queryBuilder->andWhere("sm.descricao LIKE :descricao");
        $queryBuilder->setParameter("descricao", "%" . $descricao . "%");
    }

    /**
     * Aplica filtro pela descrição do semestre
     *
     * @param string $descricao
     *
     * @return mixed|\App\Entity\Principal\Semestre|NULL
     */
    public function buscarSemestrePorDescricao ($descricao)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarPorDescricao($queryBuilder, $descricao);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Buscar o proximo semestre baseado em uma data
     *
     * @param \DateTime $data data de paremetro
     *
     * @return mixed|\App\Entity\Principal\Semestre|NULL
     */
    public function buscarProximoSemestre ($data)
    {
        $queryBuilder = $this->montaQueryBase();
        $this->filtrarPorProximoSemestre($queryBuilder, $data);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * Função pra fazer filtro do proximos semestres
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \DateTime $data data
     *
     * @return void
     */
    private function filtrarPorProximoSemestre (&$queryBuilder, $data=null)
    {
        if (is_null($data) === true) {
            $data = new \DateTime();
        }

        $data->add(new DateInterval("P6M"));
        $ano = ($data->format("Y"));
        $mes = ($data->format("m"));

        $queryBuilder->andWhere('MONTH(sm.data_inicio) <= (:mes)');
        $queryBuilder->andWhere('MONTH(sm.data_termino) >= (:mes)');
        $queryBuilder->setParameter('mes', $mes);

        $queryBuilder->andWhere("YEAR(sm.data_inicio) = (:ano)");
        $queryBuilder->setParameter("ano", $ano);
    }


}
