<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\BO\Principal\TipoVisibilidadeBO;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class TipoVisibilidadeFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\TipoVisibilidadeRepository
     */
    private $tipoVisibilidadeRepository;

    /**
     *
     * @var \App\BO\Principal\TipoVisibilidadeBO
     */
    private $tipoVisibilidadeBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->tipoVisibilidadeRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoVisibilidade::class);
        $this->tipoVisibilidadeBO         = new TipoVisibilidadeBO(self::getEntityManager());
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

    }

    /**
     * Busca todos os registro da franqueadora e da franqueada
     *
     * @return array
     */
    public function buscarTodos()
    {
        return $this->tipoVisibilidadeRepository->buscarTodos();
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|Object
     */
    public function buscarPorId(&$mensagemErro, $id)
    {

    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|Object
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->tipoVisibilidadeBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TipoVisibilidade::class, true, $parametros);
            self::criarRegistro($objetoORM, $mensagemErro);
        }

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
        $objetoORM = $this->tipoVisibilidadeRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "TipoVisibilidade não encontrado";
        } else {
            unset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            self::getFctHelper()->setParams($parametros, $objetoORM);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Remove um registro do banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        return empty($mensagemErro);
    }


}
