<?php

namespace App\Facade\Principal;


use App\BO\Principal\BancoBO;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class BancoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\BancoRepository
     */
    private $bancoRepository;

    /**
     *
     * @var \App\BO\Principal\BancoBO
     */
    private $bancoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry);
        $this->bancoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Banco::class);
        $this->bancoBO         = new BancoBO($this->bancoRepository);
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
        $retornoRepositorio = $this->bancoRepository->filtrarBancosPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param int $id Chave primaria do registro
     *
     * @return null|\App\Entity\Principal\Banco
     */
    public function buscarPorId($id)
    {
        $mensagem = "";
        $bancoORM = null;
        $this->bancoBO->verificaBancoExiste($id, $mensagem, $bancoORM);
        return $bancoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return null|\App\Entity\Principal\Banco
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->bancoBO->podeSalvar($parametros, null, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Banco::class, true, $parametros);
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
        $bancoORM = null;
        if ($this->bancoBO->verificaBancoExiste($id, $mensagemErro, $bancoORM) === true) {
            if ($this->bancoBO->podeSalvar($parametros, $id, $mensagemErro) === true) {
                self::limparParametrosVazios($parametros);
                self::getFctHelper()->setParams($parametros, $bancoORM);
                self::flushSeguro($mensagemErro);
                return true;
            }
        }

        return false;
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
        $bancoORM = null;
        if ($this->bancoBO->verificaBancoExiste($id, $mensagemErro, $bancoORM) === true) {
            $bancoORM->setSituacao(SituacoesSistema::SITUACAO_REMOVIDO);
            self::flushSeguro($mensagemErro);
            return empty($mensagemErro);
        }

        return false;
    }


}
