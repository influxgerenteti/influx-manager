<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class NivelInstrutorFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\NivelInstrutorRepository
     */
    private $nivelInstrutorRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->nivelInstrutorRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\NivelInstrutor::class);
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
        $retornoRepositorio = $this->nivelInstrutorRepository->filtrarNivelInstrutorPorPagina($parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|\App\Entity\Principal\NivelInstrutor
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->nivelInstrutorRepository->find($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Nivel Instrutor não encontrado na base de dados.";
        }

        return $objetoORM;
    }


}
