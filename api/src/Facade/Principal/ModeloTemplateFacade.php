<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\ModeloTemplateBO;
use App\Facade\Principal\FranqueadaFacade;
use App\Facade\Principal\ModeloTemplateFranqueadaFacade;

/**
 *
 * @author Luiz A Costa
 */
class ModeloTemplateFacade extends GenericFacade
{


    /**
     *
     * @var \App\Facade\Principal\FranqueadaFacade
     */
    private $franqueadaFacade;

    /**
     *
     * @var \App\Facade\Principal\ModeloTemplateFranqueadaFacade
     */
    private $modeloTemplateFranqueadaFacade;

    /**
     *
     * @var \App\Repository\Principal\ModeloTemplateRepository
     */
    private $modeloTemplateRepository;

    /**
     *
     * @var \App\BO\Principal\ModeloTemplateBO
     */
    private $modeloTemplateBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->modeloTemplateRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\ModeloTemplate::class);
        $this->modeloTemplateBO         = new ModeloTemplateBO(self::getEntityManager());
        $this->franqueadaFacade         = new FranqueadaFacade($managerRegistry);
        $this->modeloTemplateFranqueadaFacade = new ModeloTemplateFranqueadaFacade($managerRegistry);
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
        $parametros[ConstanteParametros::CHAVE_FRANQUEADA];
        $retornoRepositorio = $this->modeloTemplateRepository->filtrarModeloTemplatePorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @return array|\App\Entity\Principal\ModeloTemplate
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->modeloTemplateRepository->buscarRegistroPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "ModeloTemplate não encontrado na base de dados.";
        }

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
        if ($this->modeloTemplateBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\ModeloTemplate::class, true, $parametros);
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
        $objetoORM = $this->modeloTemplateRepository->findOneBy([ConstanteParametros::CHAVE_ID => $id, ConstanteParametros::CHAVE_FRANQUEADA => $parametros[ConstanteParametros::CHAVE_FRANQUEADA]]);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "ModeloTemplate não encontrado na base de dados.";
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
        $objetoORM = $this->modeloTemplateRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "ModeloTemplate não encontrado na base de dados.";
        } else {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Altera o registro do modeloTemplate ou insere/remove um registro do modeloTemplateFranqueada
     *
     * @param string $mensagemErro Mensagem que ira retornar para o front-end
     * @param array $parametros Parametros para a alteração
     *
     * @return boolean|int
     */
    public function alterarSituacao(&$mensagemErro, $parametros)
    {
        if (isset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === false || empty($parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            $mensagemErro = "Erro ao buscar franqueada";
            return false;
        }

        $ehFranqueadora = $this->franqueadaFacade->ehFranqueadora($mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
        if (empty($mensagemErro) === false) {
            return false;
        }

        $retorno = false;

        if ($ehFranqueadora === true) {
            $objetoORM = $this->modeloTemplateRepository->find($parametros[ConstanteParametros::CHAVE_MODELO_TEMPLATE]);
            if (is_null($objetoORM) === true) {
                $mensagemErro = "ModeloTemplate não encontrado na base de dados.";
            } else {
                $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
                $retorno = true;
            }
        } else {
            if ($parametros[ConstanteParametros::CHAVE_SITUACAO] === SituacoesSistema::SITUACAO_ATIVO) {
                $modeloTemplateFranqueadaMetadata = [
                    ConstanteParametros::CHAVE_MODELO_TEMPLATE => $parametros[ConstanteParametros::CHAVE_MODELO_TEMPLATE],
                    ConstanteParametros::CHAVE_FRANQUEADA      => $parametros[ConstanteParametros::CHAVE_FRANQUEADA],
                ];
                $retorno = $this->modeloTemplateFranqueadaFacade->criar($mensagemErro, $modeloTemplateFranqueadaMetadata);
            } else {
                if (isset($parametros[ConstanteParametros::CHAVE_MODELO_TEMPLATE_FRANQUEADA]) === true && is_null($parametros[ConstanteParametros::CHAVE_MODELO_TEMPLATE_FRANQUEADA]) === false) {
                    $retorno = $this->modeloTemplateFranqueadaFacade->remover($parametros[ConstanteParametros::CHAVE_MODELO_TEMPLATE_FRANQUEADA], $mensagemErro);
                } else {
                    $mensagemErro = "ModeloTemplateFranqueada não informado.";
                }
            }
        }//end if

        if (empty($mensagemErro) === true) {
            self::flushSeguro($mensagemErro);
        }

        if (empty($mensagemErro) === false) {
            $retorno = false;
        }

        return $retorno;
    }


}
