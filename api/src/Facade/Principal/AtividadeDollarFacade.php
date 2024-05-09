<?php

namespace App\Facade\Principal;


use App\BO\Principal\AtividadeDollarBO;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class AtividadeDollarFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AtividadeDollarRepository
     */
    private $atividadeDollarRepository;

    /**
     *
     * @var \App\BO\Principal\AtividadeDollarBO
     */
    private $atividadeDollarBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry);
        $this->atividadeDollarRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AtividadeDollar::class);
        $this->atividadeDollarBO         = new AtividadeDollarBO(self::getEntityManager());
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
        $retornoRepositorio = $this->atividadeDollarRepository->filtrarAtividadesDollarPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return null|\App\Entity\Principal\AtividadeDollar
     */
    public function buscarPorId($id)
    {
        $mensagem           = "";
        $atividadeDollarORM = null;
        $this->atividadeDollarBO->verificaAtividadeDollarExiste($id, $mensagem, $atividadeDollarORM);
        return $atividadeDollarORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return null|\App\Entity\Principal\AtividadeDollar
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->atividadeDollarBO->podeSalvar($parametros, null, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AtividadeDollar::class, true, $parametros);
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
        $atividadeDollarORM = null;
        if ($this->atividadeDollarBO->verificaAtividadeDollarExiste($id, $mensagemErro, $atividadeDollarORM) === true) {
            if ($this->atividadeDollarBO->podeSalvar($parametros, $id, $mensagemErro) === true) {
                self::limparParametrosVazios($parametros);
                self::getFctHelper()->setParams($parametros, $atividadeDollarORM);
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
        $atividadeDollarORM = null;
        if ($this->atividadeDollarBO->verificaAtividadeDollarExiste($id, $mensagemErro, $atividadeDollarORM) === true) {
            $atividadeDollarORM->setSituacao(SituacoesSistema::SITUACAO_REMOVIDO);
            self::flushSeguro($mensagemErro);
            return empty($mensagemErro);
        }

        return false;
    }


}
