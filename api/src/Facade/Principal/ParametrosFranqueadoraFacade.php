<?php

namespace App\Facade\Principal;


use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class ParametrosFranqueadoraFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ParametrosFranqueadoraRepository
     */
    private $parametrosFranqueadoraRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->parametrosFranqueadoraRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ParametrosFranqueadora::class);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @return null|\App\Entity\Principal\ParametrosFranqueadora
     */
    public function buscarPorId()
    {
        return $this->parametrosFranqueadoraRepository->buscarPrimeiro();
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = $this->parametrosFranqueadoraRepository->find($id);

        self::getFctHelper()->setParams($parametros, $objetoORM);
        self::flushSeguro($mensagemErro);

        return empty($mensagemErro);
    }


}
