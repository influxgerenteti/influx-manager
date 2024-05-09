<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class LicaoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\LicaoRepository
     */
    private $licaoRepository;

    /**
     *
     * @var \App\Repository\Principal\PlanejamentoLicaoRepository
     */
    private $planejamentoLicaoRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->licaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Licao::class);
        $this->planejamentoLicaoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\PlanejamentoLicao::class);
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
        $retornoRepositorio = $this->licaoRepository->filtrarLicaoPorPagina($parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Filtra as licoes por turma
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function listarLicoesPorTurma($parametros)
    {
        return $this->licaoRepository->buscarLicoesPorTurma($parametros);
    }

    /**
     * Busca as licoes por turma e turma_aula
     *
     * @return array|NULL
     */
    public function listarLicoesPorTurmaETurmaAula($parametros)
    {
        return $this->licaoRepository->buscarLicoesPorTurmaETurmaAula($parametros);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array|\App\Entity\Principal\Licao
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->licaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Licao não encontrada na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Buscar licoes por livro
     *
     * @param string $mensagemErro
     * @param int $livroId
     *
     * @return array|NULL
     */
    public function buscarLicoesPorLivro(&$mensagemErro, $livroId)
    {
        return $this->licaoRepository->buscarLicoesPorLivro($livroId);
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     *
     * @return mixed|null|\App\Entity\Principal\Licao
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objectORM         = null;
        $planejamentoLicao = $this->planejamentoLicaoRepository->find($parametros[ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO]);
        if (is_null($planejamentoLicao) === true) {
            $mensagemErro = "Não foi encontrado Planejamento Licao";
        } else {
            $parametros[ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO] = $planejamentoLicao;
            $objectORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Licao::class, true, $parametros);
            self::criarRegistro($objectORM, $mensagemErro);
        }

        return $objectORM;
    }

    /**
     * Cria uma licao com planejamentoLicao
     *
     * @param string $mensagemErro
     * @param \App\Entity\Principal\PlanejamentoLicao $planejamentoLicaoORM
     * @param array $parametros
     *
     * @return NULL|\App\Entity\Principal\Licao
     */
    public function criarComObjetoPlanejamento(&$mensagemErro, $planejamentoLicaoORM, $parametros=[])
    {
        $objectORM = null;
        $parametros[ConstanteParametros::CHAVE_PLANEJAMENTO_LICAO] = $planejamentoLicaoORM;
        if ((isset($parametros[ConstanteParametros::CHAVE_NUMERO]) === false)||((isset($parametros[ConstanteParametros::CHAVE_NUMERO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NUMERO]) === true))) {
            $mensagemErro = "Numero não informado na lição.\n";
        } else if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)||((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true))) {
            $mensagemErro = "É obrigatorio informar a descrição da lição.\n";
        } else {
            $objectORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Licao::class, true, $parametros);
            self::persistSeguro($objectORM, $mensagemErro);
        }

        return $objectORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param \App\Entity\Principal\PlanejamentoLicao $comparaPaiORM Pai do planejamento
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $comparaPaiORM, $parametros=[])
    {
        $objetoORM = $this->licaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Licao não encontrada na base de dados.";
        } else if ($objetoORM->getPlanejamentoLicao()->getId() !== $comparaPaiORM->getId()) {
            $mensagemErro = "Só é possível alterar a lição que está relacionada ao mesmo planejamento.\n";
        } else {
            if ((isset($parametros[ConstanteParametros::CHAVE_NUMERO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_NUMERO]) === false)) {
                $objetoORM->setNumero($parametros[ConstanteParametros::CHAVE_NUMERO]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DESCRICAO]) === false)) {
                $objetoORM->setDescricao($parametros[ConstanteParametros::CHAVE_DESCRICAO]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_OBSERVACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_OBSERVACAO]) === false)) {
                $objetoORM->setObservacao($parametros[ConstanteParametros::CHAVE_OBSERVACAO]);
            }

            if ((isset($parametros[ConstanteParametros::CHAVE_MODALIDADE]) === true) && (empty($parametros[ConstanteParametros::CHAVE_MODALIDADE]) === false)) {
                $objetoORM->setModalidade($parametros[ConstanteParametros::CHAVE_MODALIDADE]);
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
        $objetoORM = $this->licaoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Licao não encontrada na base de dados.";
        } else {
            self::removerRegistro($objetoORM, $mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Adicionado AlunoDiario com licao
     *
     * @param string $mensagemErro
     * @param int $licaoId
     * @param \App\Entity\Principal\AlunoDiario $alunoDiarioORM
     *
     * @return boolean
     */
    public function atualizarLicaoComAlunoDiario(&$mensagemErro, $licaoId, &$alunoDiarioORM)
    {
        $objetoORM = $this->licaoRepository->find($licaoId);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Licao não encontrada na base de dados.";
        } else {
            $alunoDiarioORM->addLicao($objetoORM);
            $objetoORM->addAlunoDiario($alunoDiarioORM);
        }

        return empty($mensagemErro);
    }


    /**
     * Removendo licao do alunoDiario
     *
     * @param string $mensagemErro
     * @param int $licaoId
     * @param \App\Entity\Principal\AlunoDiario $alunoDiarioORM
     *
     * @return boolean
     */
    public function removerLicaoAlunoDiario(&$mensagemErro, $licaoId, &$alunoDiarioORM)
    {
        $objetoORM = $this->licaoRepository->find($licaoId);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Licao não encontrada na base de dados.";
        } else {
            $alunoDiarioORM->removeLicao($objetoORM);
            $objetoORM->removeAlunoDiario($alunoDiarioORM);
        }

        return empty($mensagemErro);
    }


}
