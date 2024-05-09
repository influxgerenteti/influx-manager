<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Augusto
 */
class MensagensAjudaFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Principal\MensagensAjudaRepository $mensagensAjudaRepository
     */
    private $mensagensAjudaRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->mensagensAjudaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\MensagensAjuda::class);
    }


    /**
     * Retorna todas as mensagens de ajuda
     *
     * @param string $url_modulo
     *
     * @return \App\Entity\Principal\MensagensAjuda
     */
    public function listar($url_modulo)
    {
        return $this->mensagensAjudaRepository->buscarTodos($url_modulo);
    }


}
