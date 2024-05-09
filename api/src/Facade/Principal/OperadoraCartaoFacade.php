<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\OperadoraCartaoBO;

/**
 *
 * @author Luiz A Costa
 */
class OperadoraCartaoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\OperadoraCartaoRepository
     */
    private $operadoraCartaoRepository;

    /**
     *
     * @var \App\BO\Principal\OperadoraCartaoBO
     */
    private $operadoraCartaoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->operadoraCartaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\OperadoraCartao::class);
        $this->operadoraCartaoBO         = new OperadoraCartaoBO(self::getEntityManager());
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
        $retornoRepositorio = $this->operadoraCartaoRepository->filtrarOperadoraCartaoPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return array|Object
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->operadoraCartaoRepository->buscaRegistroPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "OperadoraCartao não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\OperadoraCartao
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->operadoraCartaoBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\OperadoraCartao::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
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
        $objetoORM = $this->operadoraCartaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "OperadoraCartao não encontrado na base de dados.";
        } else {
            if ($this->operadoraCartaoBO->podeAlterar($parametros, $mensagemErro) === true) {
                self::getFctHelper()->setParams($parametros, $objetoORM);
            }
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
