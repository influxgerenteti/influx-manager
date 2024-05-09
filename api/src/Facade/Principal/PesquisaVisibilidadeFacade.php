<?php

namespace App\Facade\Principal;


use App\BO\Principal\PesquisaVisibilidadeBO;
use App\Entity\Principal\PesquisaVisibilidade;
use App\Facade\Principal\GenericFacade;
use App\Helper\ConstanteParametros;
use App\Helper\VariaveisCompartilhadas;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Dayan Freitas
 */
class PesquisaVisibilidadeFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\PesquisaVisibilidadeRepository
     */
    private $pesquisaVisibilidadeRepository;

    /**
     *
     * @var \App\BO\Principal\PesquisaVisibilidadeBO
     */
    private $pesquisaVisibilidadeBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->pesquisaVisibilidadeRepository = self::getEntityManager()->getRepository(PesquisaVisibilidade::class);
        $this->pesquisaVisibilidadeBO         = new PesquisaVisibilidadeBO(self::getEntityManager());
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $mensagemErro messagem de erro
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar(&$mensagemErro, $parametros)
    {
        $retornoRepositorio = $this->pesquisaVisibilidadeRepository->filtrarPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
        $objetoORM = null;
        $this->pesquisaVisibilidadeBO->verificaPesquisaExiste($this->pesquisaVisibilidadeRepository, $id, $mensagemErro, $objetoORM);
        return $objetoORM;
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

        $parametros[ConstanteParametros::CHAVE_FRANQUEADA] = VariaveisCompartilhadas::$franqueadaID;
        if ($this->pesquisaVisibilidadeBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\PesquisaVisibilidade::class, true, $parametros);
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
        $objetoORM = null;
        if ($this->pesquisaVisibilidadeBO->verificaPesquisaExiste($this->pesquisaVisibilidadeRepository, $id, $mensagemErro, $objetoORM, true) === true) {
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
