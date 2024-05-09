<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class CidadeFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\CidadeRepository
     */
    private $cidadeRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->cidadeRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Cidade::class);
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar($parametros)
    {
        $retornoRepositorio = $this->cidadeRepository->buscarCidades($parametros);
        return $retornoRepositorio;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param int $id Chave primaria do registro
     *
     * @return null|\App\Entity\Principal\Cidade
     */
    public function buscarPorId($id)
    {
        return $this->cidadeRepository->buscaCidadePorId($id);
    }


}
