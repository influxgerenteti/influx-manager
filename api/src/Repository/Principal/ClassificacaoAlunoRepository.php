<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ClassificacaoAluno;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @method ClassificacaoAluno|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassificacaoAluno|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassificacaoAluno[]    findAll()
 * @method ClassificacaoAluno[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassificacaoAlunoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ClassificacaoAluno::class);
    }

    /**
     * Monta query base de busca
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("clas");
        $queryBuilder->addSelect("fra");
        $queryBuilder->innerJoin("clas.franqueada", "fra");

        return $queryBuilder;
    }

    /**
     * Query para realizar fitlro de franqueada
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    private function filtrarFranqueada(&$queryBuilder)
    {
        $queryBuilder->where('clas.franqueada = :franqueada');
        $queryBuilder->setParameter('franqueada', VariaveisCompartilhadas::$franqueadaID);
    }

    /**
     * Busca todos os registros da tabela classificação aluno atraves do Nome informado
     *
     * @param integer $franqueada ID da Franqueada
     * @param string $nome       Nome da Classificação de Aluno
     * @param integer $id         Id da Classificação de Aluno
     *
     * @return array|NULL
     */
    public function buscarPorNome($franqueada, $nome, $id=0)
    {
        $queryBuilder = $this->createQueryBuilder('ca');
        $queryBuilder->select('ca');
        $queryBuilder->where('ca.franqueada = :franqueada')->andWhere('UPPER(ca.nome) LIKE :nome')->andWhere('ca.id != :id');
        $queryBuilder->setParameters(
            [
                'franqueada' => $franqueada,
                'nome'       => strtoupper($nome),
                'id'         => $id,
            ]
        );
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder);
    }

    /**
     * Filtra a classificação de aluno por pagina e numero de itens por pagina
     *
     * @param array $parametros
     * @param integer $franqueada           ID da Franqueada
     * @param integer $pagina               Numero da pagina
     * @param integer $numeroItensPorPagina Numero de itens a serem trazidos na consulta
     *
     * @return \App\Entity\Principal\ClassificacaoAluno[] Resultados em formato de array
     */
    public function filtrarClassificacoesAlunoPorPagina($parametros, $franqueada, $pagina=1, $numeroItensPorPagina=50)
    {
        $opcoes       = [];
        $queryBuilder = $this->buscarTodosSemExclusao($franqueada);

        if ((isset($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === true)&&(is_null($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA]) === false)) {
            $queryBuilder->orderBy($parametros[ConstanteParametros::CHAVE_ORDENACAO_COLUNA], $parametros[ConstanteParametros::CHAVE_ORDENACAO_SORT]);
            $opcoes[\Knp\Component\Pager\Paginator::SORT_FIELD_PARAMETER_NAME]     = "~";
            $opcoes[\Knp\Component\Pager\Paginator::SORT_DIRECTION_PARAMETER_NAME] = "~";
        }

        return \App\Helper\FunctionHelper::montaPaginatorPaginacao($queryBuilder, $pagina, $numeroItensPorPagina, $opcoes);
    }

    /**
     * Busca todas as classificações de aluno que não estiverem excluídas
     *
     * @param integer $franqueada ID da Franqueada
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function buscarTodosSemExclusao($franqueada)
    {
        $qb = $this->createQueryBuilder('ca')
            ->select('ca.id as id, ca.nome as nome, ca.icone as icone')
            ->where('ca.franqueada = :franqueada')
            ->andWhere('ca.excluido = 0')
            ->orderBy('ca.nome', 'asc')
            ->setParameter('franqueada', $franqueada);
        return $qb;
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
        $queryBuilder->andWhere("clas.id = :id");
        $queryBuilder->setParameter("id", $id);
        return \App\Helper\FunctionHelper::retornaArrayNull($queryBuilder, true);
    }


}
