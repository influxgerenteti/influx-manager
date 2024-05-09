<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\SistemaAvaliacaoBO;

/**
 *
 * @author Luiz A Costa
 */
class SistemaAvaliacaoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\SistemaAvaliacaoRepository
     */
    private $sistemaAvaliacaoRepository;

    /**
     *
     * @var \App\BO\Principal\SistemaAvaliacaoBO
     */
    private $sistemaAvaliacaoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->sistemaAvaliacaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\SistemaAvaliacao::class);
        $this->sistemaAvaliacaoBO         = new SistemaAvaliacaoBO(self::getEntityManager());
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
        $retornoRepositorio = $this->sistemaAvaliacaoRepository->filtrarSistemaAvaliacaoPorPagina($parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return array|\App\Entity\Principal\SistemaAvaliacao
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->sistemaAvaliacaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Sistema Avaliacao não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\SistemaAvaliacao
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->sistemaAvaliacaoBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\SistemaAvaliacao::class, true, $parametros);
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
        $objetoORM = $this->sistemaAvaliacaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Sistema Avaliacao não encontrado na base de dados.";
        } else {
            $this->sistemaAvaliacaoBO->configuraParametros($parametros, $objetoORM);
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
        $objetoORM = $this->sistemaAvaliacaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Sistema Avaliacao não encontrado na base de dados.";
        } else {
            $objetoORM->setExcluido(true);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
