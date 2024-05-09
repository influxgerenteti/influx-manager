<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class EstadoFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Principal\EstadoRepository
     */
    private $estadoRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->estadoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Estado::class);
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
        $retornoRepositorio = $this->estadoRepository->buscarEstados($parametros);
        return $retornoRepositorio;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param int $id Chave primaria do registro
     *
     * @return null|\App\Entity\Principal\Estado
     */
    public function buscarPorId($id)
    {
        return $this->estadoRepository->buscaEstadoPorId($id);
    }


}
