<?php

namespace App\Facade\Principal;


use App\Entity\Principal\ClassificacaoAluno;
use App\BO\Principal\ClassificacaoAlunoBO;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class ClassificacaoAlunoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\ClassificacaoAlunoRepository
     */
    private $classificacaoAlunoRepository;

    /**
     *
     * @var \App\BO\Principal\ClassificacaoAlunoBO
     */
    private $classificacaoAlunoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->classificacaoAlunoRepository = self::getEntityManager()->getRepository(ClassificacaoAluno::class);
        $this->classificacaoAlunoBO         = new ClassificacaoAlunoBO(self::getEntityManager());

    }

    /**
     * Atualiza o registro no banco de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $id Id do registro a ser atualizado
     * @param array $parametros
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $classificacaoAlunoORM = null;
        if ($this->classificacaoAlunoBO->verificaClassificacaoAlunoExiste($this->classificacaoAlunoRepository, $id, $mensagemErro, $classificacaoAlunoORM, true) === true) {
            if ($this->classificacaoAlunoBO->verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
                if ($this->classificacaoAlunoBO->verificaNomeExiste($this->classificacaoAlunoRepository, $classificacaoAlunoORM->getFranqueada()->getId(), $parametros[ConstanteParametros::CHAVE_NOME], $id, $mensagemErro) === false) {
                    self::getFctHelper()->setParams($parametros, $classificacaoAlunoORM);
                    self::flushSeguro($mensagemErro);
                    return empty($mensagemErro);
                }
            }
        }

        return false;
    }

    /**
     * Busca um registro atraves da ID
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $id Id do registro a ser buscado no banco de dados
     *
     * @return NULL|\App\Entity\Principal\ClassificacaoAluno
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $classificacaoAlunoORM = $this->classificacaoAlunoRepository->buscarRegistroPorId($id);
        if (empty($classificacaoAlunoORM) === true) {
            $mensagemErro = "Classificação não encontrado na base de dados.";
        }

        return $classificacaoAlunoORM;
    }

    /**
     * Cria um registro na base de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $franqueada_id ID da Franqueada
     * @param array $parametros Parametros para realizar a criacao do registro
     *
     * @return boolean null|\App\Entity\Principal\ClassificacaoAluno
     */
    public function criar(&$mensagemErro, $franqueada_id, $parametros=[])
    {
        if (\App\BO\Principal\FranqueadaBO::franqueadaExisteBanco(self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class), ["id" => $franqueada_id], $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            if ($this->classificacaoAlunoBO->verificaNomeExiste($this->classificacaoAlunoRepository, $franqueada_id, $parametros[ConstanteParametros::CHAVE_NOME], 0, $mensagemErro) === false) {
                $classificacaoAlunoORM = \App\Factory\GeneralORMFactory::criar(ClassificacaoAluno::class, true, $parametros);
                self::criarRegistro($classificacaoAlunoORM, $mensagemErro);
                return $classificacaoAlunoORM;
            }
        }

        return null;
    }

    /**
     * Lista todas as classificacões de alunos do banco de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param array $parametros Array de parametros de paginacao
     *
     * @return array
     */
    public function listar(&$mensagemErro, $parametros)
    {
        $retornoRepositorio = $this->classificacaoAlunoRepository->filtrarClassificacoesAlunoPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_FRANQUEADA], $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Altera o campo "excluido" para true
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $id Id do registro a ser 'removido'
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        $classificacaoAlunoORM = null;
        if ($this->classificacaoAlunoBO->verificaClassificacaoAlunoExiste($this->classificacaoAlunoRepository, $id, $mensagemErro, $classificacaoAlunoORM, true) === true) {
            $classificacaoAlunoORM->setExcluido(true);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }


}
