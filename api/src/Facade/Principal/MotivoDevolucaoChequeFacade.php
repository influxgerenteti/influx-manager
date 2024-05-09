<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Entity\Principal\MotivoDevolucaoCheque;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class MotivoDevolucaoChequeFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\MotivoDevolucaoChequeRepository
     */
    private $motivoDevolucaoChequeRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->motivoDevolucaoChequeRepository = self::getEntityManager()->getRepository(MotivoDevolucaoCheque::class);
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
        $retornoRepositorio = $this->motivoDevolucaoChequeRepository->filtrarMotivoDevolucaoChequePorPagina($parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return array|\App\Entity\Principal\MotivoDevolucaoCheque
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->motivoDevolucaoChequeRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Motivo Devolução de Cheque não encontrado na base de dados.";
        }

        return $objetoORM;
    }


}
