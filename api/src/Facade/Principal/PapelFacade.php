<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class PapelFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\PapelRepository
     */
    private $papelRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->papelRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Papel::class);
    }

    /**
     * Busca todos os papeis
     *
     * @param array $parametros
     *
     * @return NULL[]|number[]
     */
    public function listar($parametros)
    {
        $retornoRepositorio = $this->papelRepository->buscarTodosPapeis($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Busca um registro atraves da ID
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $id Id do registro a ser buscado no banco de dados
     *
     * @return NULL|\App\Entity\Principal\Papel
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        return $this->papelRepository->buscar($id);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Papel
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        unset($parametros[ConstanteParametros::CHAVE_DADOS_PERMISSAO]);
        $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Papel::class, true, $parametros);
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
        $objetoORM = $this->papelRepository->find($id);
        unset($parametros[ConstanteParametros::CHAVE_DADOS_PERMISSAO]);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Papel não encontrado na base de dados.";
        } else {
            self::getFctHelper()->setParams($parametros, $objetoORM);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
