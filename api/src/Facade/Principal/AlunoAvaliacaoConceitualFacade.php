<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\BO\Principal\AlunoAvaliacaoConceitualBO;

/**
 *
 * @author Luiz A Costa
 */
class AlunoAvaliacaoConceitualFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AlunoAvaliacaoConceitualRepository
     */
    private $alunoAvaliacaoConceitualRepository;

    /**
     *
     * @var \App\BO\Principal\AlunoAvaliacaoConceitualBO
     */
    private $alunoAvaliacaoConceitualBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->alunoAvaliacaoConceitualRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\AlunoAvaliacaoConceitual::class);
        $this->alunoAvaliacaoConceitualBO         = new AlunoAvaliacaoConceitualBO(self::getEntityManager());
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

    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param \App\Entity\Principal\AlunoAvaliacao $objetoORM Retorno do objeto
     *
     * @return bool
     */
    public function criar(&$mensagemErro, $parametros=[], &$objetoORM=null)
    {
        $objetoORM = null;
        if ($this->alunoAvaliacaoConceitualBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\AlunoAvaliacaoConceitual::class, true, $parametros);
            self::persistSeguro($objetoORM, $mensagemErro);
        }

        return (is_null($objetoORM) === false) && (empty($mensagemErro) === true);
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
        $objetoORM = $this->alunoAvaliacaoConceitualRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "AlunoAvaliacaoConceitual não encontrado na base de dados.";
        } else {
            unset($parametros[ConstanteParametros::CHAVE_ALUNO]);
            unset($parametros[ConstanteParametros::CHAVE_FRANQUEADA]);
            if ((bool) $parametros[ConstanteParametros::CHAVE_PERSONAL] === false) {
                unset($parametros[ConstanteParametros::CHAVE_TURMA]);
            } else {
                unset($parametros[ConstanteParametros::CHAVE_CONTRATO]);
            }

            unset($parametros[ConstanteParametros::CHAVE_LIVRO]);
            if ($this->alunoAvaliacaoConceitualBO->podeAtualizar($parametros, $mensagemErro) === true) {
                self::getFctHelper()->setParams($parametros, $objetoORM);
            }
        }

        return empty($mensagemErro);
    }

    /**
     * Lancamento/Atualizacao de Notas Conceituais para Aluno Avaliacao
     *
     * @param string $mensagemErro
     * @param array $parametros
     *
     * @return boolean
     */
    public function lancarAtualizarNotas(&$mensagemErro, $parametros)
    {
        $bPossuiAlunoAvaliacaoConceitualId = isset($parametros[ConstanteParametros::CHAVE_ID]);
        if ($bPossuiAlunoAvaliacaoConceitualId === true) {
            $alunoavaliacaoConceitualId = $parametros[ConstanteParametros::CHAVE_ID];
            unset($parametros[ConstanteParametros::CHAVE_ID]);
            $bSuccesso = $this->atualizar($mensagemErro, $alunoavaliacaoConceitualId, $parametros);
        } else {
            $bSuccesso = $this->criar($mensagemErro, $parametros);
        }

        return $bSuccesso;
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
