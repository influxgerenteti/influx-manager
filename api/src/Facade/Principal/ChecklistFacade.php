<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\SituacoesSistema;
use App\Helper\ConstanteParametros;
use App\BO\Principal\PermissaoBO;

/**
 *
 * @author Luiz A Costa
 */
class ChecklistFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ChecklistRepository
     */
    private $checklistRepository;

    /**
     *
     * @var \App\BO\Principal\PermissaoBO
     */
    private $permissaoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->checklistRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Checklist::class);
        $this->permissaoBO         = new PermissaoBO(self::getEntityManager());
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
        $retornoRepositorio = $this->checklistRepository->filtraChecklistPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Lista todos os registros do banco de dados por usuario
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function buscarPorUsuarioLogado($parametros)
    {
        $papeisUsuario = $this->permissaoBO->retornaPapeisIdUsuario($parametros[ConstanteParametros::CHAVE_USUARIO]);
        return $this->checklistRepository->buscarPorPapeis($papeisUsuario);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->checklistRepository->buscarPorId($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Checklist não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Checklist
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Checklist::class, true, $parametros);
        self::persistSeguro($objetoORM, $parametros);
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
        $objetoORM = $this->checklistRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Checklist não encontrado na base de dados.";
        } else {
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
        $objetoORM = $this->checklistRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Checklist não encontrado na base de dados.";
        } else {
            $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
