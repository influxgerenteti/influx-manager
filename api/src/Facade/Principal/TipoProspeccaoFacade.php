<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\TipoProspeccaoBO;

/**
 *
 * @author Luiz A Costa
 */
class TipoProspeccaoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\TipoProspeccaoRepository
     */
    private $tipoProspeccaoRepository;

    /**
     *
     * @var \App\BO\Principal\TipoProspeccaoBO
     */
    private $tipoProspeccaoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->tipoProspeccaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\TipoProspeccao::class);
        $this->tipoProspeccaoBO         = new TipoProspeccaoBO(self::getEntityManager());
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
        $retornoRepositorio = $this->tipoProspeccaoRepository->filtrarLivroPorPagina($parametros[ConstanteParametros::CHAVE_PAGINA]);
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
        $objetoORM = $this->tipoProspeccaoRepository->buscarPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "TipoProspeccao não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\TipoProspeccao
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\TipoProspeccao::class);
        $this->tipoProspeccaoBO->configuraParametros($parametros, $mensagemErro, $objetoORM);
        if (empty($mensagemErro) === false) {
            $objetoORM = null;
        } else {
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
        $objetoORM = $this->tipoProspeccaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "TipoProspeccao não encontrado na base de dados.";
        } else {
            $this->tipoProspeccaoBO->configuraParametros($parametros, $mensagemErro, $objetoORM);
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
        $objetoORM = $this->tipoProspeccaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "TipoProspeccao não encontrado na base de dados.";
        } else {
            if ($objetoORM->getTipoPaiTipoProspeccao() === null) {
                $resultados = $this->tipoProspeccaoRepository->findBy(["tipo_pai_tipo_prospeccao" => $objetoORM->getId()]);
                if (count($resultados) > 0) {
                    foreach ($resultados as $tipoPaiFilhos) {
                        $tipoPaiFilhos->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
                    }
                }
            }

            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
