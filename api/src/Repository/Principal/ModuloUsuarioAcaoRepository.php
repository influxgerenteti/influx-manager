<?php

namespace App\Repository\Principal;

use App\Entity\Principal\ModuloUsuarioAcao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ModuloUsuarioAcao|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuloUsuarioAcao|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuloUsuarioAcao[]    findAll()
 * @method ModuloUsuarioAcao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuloUsuarioAcaoRepository extends ServiceEntityRepository
{


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ModuloUsuarioAcao::class);
    }

    /**
     * Monta Query base
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function montaQueryBase()
    {
        $queryBuilder = $this->createQueryBuilder("mua");
        $queryBuilder->addSelect("mdl");
        $queryBuilder->addSelect("urlsis");
        $queryBuilder->addSelect("usu");
        $queryBuilder->addSelect("act");
        $queryBuilder->leftJoin("mua.modulo", "mdl");
        $queryBuilder->leftJoin("mdl.urlSistemas", "urlsis");
        $queryBuilder->leftJoin("mua.usuario", "usu");
        $queryBuilder->leftJoin("mua.acao_sistema", "act");
        return $queryBuilder;
    }

    /**
     * Busca ModuloUsuarioAcao
     *
     * @param string $rota
     * @param int $moduloId
     * @param int $acaoId
     *
     * @return NULL|\App\Entity\Principal\ModuloPapelAcao
     */
    public function buscaPorRotaModuloAcao($rota, $moduloId, $acaoId)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("urlsis.url_sistema = :rotaString");
        $queryBuilder->andWhere("mdl.id = :moduloId");
        $queryBuilder->andWhere("act.id = :acaoId");
        $queryBuilder->setParameters(
            [
                "rotaString" => $rota,
                "moduloId"   => $moduloId,
                "acaoId"     => $acaoId,
            ]
        );

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Busca ModuloUsuarioAcao
     *
     * @param string $rota
     * @param int $moduloId
     * @param int $acaoId
     *
     * @return NULL|\App\Entity\Principal\ModuloPapelAcao[]
     */
    public function buscaPermissaoPorRotaModuloAcao($usuarioId, $rota, $moduloId, $acaoId)
    {
        $rotaSql = "";

        // controlle para acesso via postman sem passar a rota
        if($rota != null){
            $rotaSql = "and mo.url ='{$rota}' ";
        } else {
            $rotaSql =  "and mo.id = {$moduloId}";
        }

        $sql = "
            select usu.id 
            from modulo_usuario_acao as acao 
            inner join usuario as usu on usu.id = acao.usuario_id
            inner join modulo as mo on mo.id = acao.modulo_id
            inner join acao_sistema as ac_sis on ac_sis.id = acao.acao_sistema_id

            where 
            usu.id = {$usuarioId} 
            and ac_sis.id = {$acaoId}
            {$rotaSql}
            and usu.situacao = 'A'
            and usu.excluido = 0";

        // if($moduloId == null){
        //     $sql = "
        //     select usu.id 
        //     from modulo_usuario_acao as acao 
        //     inner join usuario as usu on usu.id = acao.usuario_id
        //     inner join modulo as mo on mo.id = acao.modulo_id
        //     inner join acao_sistema as ac_sis on ac_sis.id = acao.acao_sistema_id

        //     where 
        //     usu.id = {$usuarioId} 
        //     and mo.url ='{$rota}'
        //     and usu.situacao = 'A'
        //     and usu.excluido = 0";
        // }
        $connection =  $this->_em->getConnection();

        $data = $connection->fetchAll($sql);
        
        return  $data;


        // $queryBuilder = $this->montaQueryBase();
        // $queryBuilder->where("urlsis.url_sistema = :rotaString");
        // $queryBuilder->andWhere("mdl.id = :moduloId");
        // $queryBuilder->andWhere("act.id = :acaoId");
        // $queryBuilder->andWhere("usu.id = :usuarioId");        
        // $queryBuilder->setParameters(
        //     [
        //         "rotaString" => $rota,
        //         "usuarioId" => $usuarioId,
        //         "moduloId"   => $moduloId,
        //         "acaoId"     => $acaoId,
        //     ]
        // );
        // $sql = $queryBuilder->getQuery()->getSql();
        // return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Busca as permissoes módulo e ação
     *
     * @param int $moduloId
     * @param int $acaoId
     *
     * @return NULL|\App\Entity\Principal\ModuloUsuarioAcao
     */
    public function buscaPorModuloAcao($moduloId, $acaoId)
    {
        $queryBuilder = $this->montaQueryBase();
        $queryBuilder->where("mdl.id = :moduloId");
        $queryBuilder->andWhere("act.id = :acaoId");
        $queryBuilder->setParameters(
            [
                "moduloId" => $moduloId,
                "acaoId"   => $acaoId,
            ]
        );

        return $queryBuilder->getQuery()->getResult();
    }


    /**
     * Busca as permissoes módulo e ação
     *
     * @param int $moduloId
     * @param int $acaoId
     *
     * @return NULL|\App\Entity\Principal\ModuloUsuarioAcao[]
     */
    public function buscaPermissaoPorModuloAcao( $usuarioId,$moduloId, $acaoId)
    {
        // $queryBuilder = $this->montaQueryBase();
        // $queryBuilder->where("mdl.id = :moduloId");
        // $queryBuilder->andWhere("act.id = :acaoId");
        // $queryBuilder->andWhere("usu.id = :usuarioId");        
        // $queryBuilder->setParameters(
        //     [                
        //         "usuarioId" => $moduloId,
        //         "moduloId" => $moduloId,
        //         "acaoId"   => $acaoId,
        //     ]
        // );

        // return $queryBuilder->getQuery()->getResult();

        $sql = "
        select usu.id 
        from modulo_usuario_acao as acao 
        inner join usuario as usu on usu.id = acao.usuario_id
        inner join modulo as mo on mo.id = acao.modulo_id
        inner join acao_sistema as ac_sis on ac_sis.id = acao.acao_sistema_id

        where 
        usu.id = {$usuarioId} 
        and mo.id = {$moduloId}
        and ac_sis.id = {$acaoId}
        and usu.situacao = 'A'
        and usu.excluido = 0";

        // if($moduloId == null){
        //     $sql = "
        // select usu.id 
        // from modulo_usuario_acao as acao 
        // inner join usuario as usu on usu.id = acao.usuario_id
        // inner join modulo as mo on mo.id = acao.modulo_id
        // inner join acao_sistema as ac_sis on ac_sis.id = acao.acao_sistema_id

        // where 
        // usu.id = {$usuarioId} 
        // and usu.situacao = 'A'
        // and usu.excluido = 0";

        // }

        
    $connection =  $this->_em->getConnection();

    $data = $connection->fetchAll($sql);
    return  $data;
    }


    public function executePermissionInsertList($sql){
        $connection =  $this->_em->getConnection();
        $result = $connection->executeQuery($sql);
        return $result;
    }

}
