<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\SalaBO;

/**
 *
 * @author Luiz A Costa
 */
class SalaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\SalaRepository
     */
    private $salaRepository;

    /**
     *
     * @var \App\BO\Principal\SalaBO
     */
    private $salaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->salaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Sala::class);
        $this->salaBO         = new SalaBO(self::getEntityManager());
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar ($parametros)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA]) === true) && ((int) $parametros[ConstanteParametros::CHAVE_SALA_FRANQUEADA] === 1)) {
            $retornoRepositorio = $this->salaRepository->buscaSalaESalaFranqueada($parametros, $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $parametros[ConstanteParametros::CHAVE_PAGINA]);

            $retorno = [
                ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
                ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
            ];
        } else {
            $retornoRepositorio = $this->salaRepository->filtrarSalaPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);

            $retorno = [
                ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
                ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
            ];
        }

        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|\App\Entity\Principal\Sala
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->salaRepository->buscarSalaPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Sala não encontrada na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Sala
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Sala::class, true, $parametros);
        self::criarRegistro($objetoORM, $mensagemErro);

        return $objetoORM;
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
        $objetoORM = $this->salaRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Sala não encontrada na base de dados.";
        } else {
            $this->salaBO->configuraParametros($parametros, $objetoORM);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
