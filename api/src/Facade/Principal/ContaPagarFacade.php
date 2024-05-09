<?php

namespace App\Facade\Principal;


use App\Helper\ConstanteParametros;
use App\Entity\Principal\ContaPagar;
use App\BO\Principal\FranqueadaBO;
use App\BO\Principal\ContaPagarBO;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class ContaPagarFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ContaPagarRepository
     */
    private $contaPagarRepository;

    /**
     *
     * @var \App\BO\Principal\ContaPagarBO
     */
    private $contaPagarBO;

    /**
     *
     * @var \App\BO\Principal\FranqueadaBO
     */
    private $franqueadaBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->contaPagarRepository = self::getEntityManager()->getRepository(ContaPagar::class);
        $this->contaPagarBO         = new ContaPagarBO(self::getEntityManager());
        $this->franqueadaBO         = new FranqueadaBO(self::getEntityManager());
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     *
     * @return array
     */
    public function listar($parametros, &$mensagemErro)
    {
        if (empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            $mensagemErro = "Não foi passado a franqueada na requisição.";
            return [];
        }

        $retornoRepositorio = $this->contaPagarRepository->filtrarContaPagarPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return null|\App\Entity\Principal\ContaPagar
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = null;
        $this->contaPagarBO->verificaContaPagarExiste($this->contaPagarRepository, $id, $mensagemErro, $objetoORM, 2);
        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return null|\App\Entity\Principal\ContaPagar
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->contaPagarBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(ContaPagar::class, true, $parametros);
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
        $objetoORM = null;
        if ($this->contaPagarBO->verificaContaPagarExiste($this->contaPagarRepository, $id, $mensagemErro, $objetoORM) === true) {
            if ($this->contaPagarBO->podeAtualizar($parametros, $mensagemErro) === true) {
                unset($parametros['fornecedor_pessoa']);
                unset($parametros['conta']);
                unset($parametros['franqueada']);

                self::getFctHelper()->setParams($parametros, $objetoORM);
            }
        }

        return $objetoORM;
    }

    /**
     * Remove todas as contas a pagar
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return boolean
     */
    public function excluir(&$mensagemErro, $id)
    {
        $objetoORM = null;
        if ($this->contaPagarBO->verificaContaPagarExiste($this->contaPagarRepository, $id, $mensagemErro, $objetoORM) === true) {
            if ($this->franqueadaBO->verificaDataMaiorTempoLimiteDeAlteracao($objetoORM->getDataEmissao()) === true) {
                $mensagemErro .= "Não foi possível excluir a nota, pois a data permitida para exclusão ultrapassou a estipulada pela franqueadora.";
            } else {
                self::removerSeguro($objetoORM, $mensagemErro);
            }
        }

        return empty($mensagemErro);
    }


}
