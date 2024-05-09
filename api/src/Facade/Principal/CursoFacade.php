<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\CursoBO;

/**
 *
 * @author Luiz A Costa
 */
class CursoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\CursoRepository
     */
    private $cursoRepository;

    /**
     *
     * @var \App\Repository\Principal\itemRepository
     */
    private $itemRepository;

    /**
     *
     * @var \App\Repository\Principal\ModalidadeTurmaRepository
     */
    private $modalidadeTurma;


    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->cursoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Curso::class);
        $this->itemRepository  = self::getEntityManager()->getRepository(\App\Entity\Principal\Item::class);
        $this->modalidadeTurma  = self::getEntityManager()->getRepository(\App\Entity\Principal\ModalidadeTurma::class);

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
        return $this->cursoRepository->filtrarCurso($parametros);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|\App\Entity\Principal\Curso
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->cursoRepository->buscarPorId($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Curso não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Curso
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if (CursoBO::podeAtualizar($parametros, $mensagemErro, self::getEntityManager()->getRepository(\App\Entity\Principal\Idioma::class), $this->itemRepository, $this->modalidadeTurma ) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Curso::class, true, $parametros);
            $objetoORM->setIntensidadeRegular(false);
            $objetoORM->setIntensidadeSemiIntensivo(false);
            $objetoORM->setIntensidadeIntensivo(false);
            switch ($parametros[ConstanteParametros::CHAVE_INTENSIDADE]) {
                case SituacoesSistema::CURSO_INTENSIDADE_REGULAR:
                $objetoORM->setIntensidadeRegular(true);
                    break;
                case SituacoesSistema::CURSO_INTENSIDADE_SEMI_INTENSIVO:
                $objetoORM->setIntensidadeSemiIntensivo(true);
                    break;
                case SituacoesSistema::CURSO_INTENSIDADE_INTENSIVO:
                $objetoORM->setIntensidadeIntensivo(true);
                    break;
            }

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
        $objetoORM = $this->cursoRepository->find($id);

        if (is_null($objetoORM) === true) {
            $mensagemErro = "Não foi possivel remover o curso informado.";
        } else {
            if (CursoBO::podeAtualizar($parametros, $mensagemErro, self::getEntityManager()->getRepository(\App\Entity\Principal\Idioma::class), $this->itemRepository, $this->modalidadeTurma ) === true) {
                if (empty($parametros[ConstanteParametros::CHAVE_IDIOMA]) === false) {
                    $objetoORM->setIdioma($parametros[ConstanteParametros::CHAVE_IDIOMA]);
                }
            }
            $objetoORM->setIntensidadeRegular(false);
            $objetoORM->setIntensidadeSemiIntensivo(false);
            $objetoORM->setIntensidadeIntensivo(false);
            switch ($parametros[ConstanteParametros::CHAVE_INTENSIDADE]) {
                case SituacoesSistema::CURSO_INTENSIDADE_REGULAR:
                $objetoORM->setIntensidadeRegular(true);
                    break;
                case SituacoesSistema::CURSO_INTENSIDADE_SEMI_INTENSIVO:
                $objetoORM->setIntensidadeSemiIntensivo(true);
                    break;
                case SituacoesSistema::CURSO_INTENSIDADE_INTENSIVO:
                $objetoORM->setIntensidadeIntensivo(true);
                    break;
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
                $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
                $objetoORM->setDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_SIGLA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SIGLA]) === false)) {
                $objetoORM->setSigla($parametros[ConstanteParametros::CHAVE_SIGLA]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_IDADE_MINIMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_IDADE_MINIMA]) === false)) {
                $objetoORM->setIdadeMinima($parametros[ConstanteParametros::CHAVE_IDADE_MINIMA]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_IDADE_MAXIMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_IDADE_MAXIMA]) === false)) {
                $objetoORM->setIdadeMaxima($parametros[ConstanteParametros::CHAVE_IDADE_MAXIMA]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_SERVICO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SERVICO]) === false)) {
                $objetoORM->setServico($parametros[ConstanteParametros::CHAVE_SERVICO]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === true) && (empty($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]) === false)) {
                $objetoORM->setModalidadeTurma($parametros[ConstanteParametros::CHAVE_MODALIDADE_TURMA]);
            }

            self::flushSeguro($mensagemErro);
        }//end if

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
        $objetoORM = $this->cursoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Não foi possivel remover o curso informado.";
        }

        $objetoORM->setSituacao(SituacoesSistema::SITUACAO_INATIVO);
        self::flushSeguro($mensagemErro);
        return empty($mensagemErro);
    }


}
