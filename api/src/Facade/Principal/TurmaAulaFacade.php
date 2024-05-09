<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz A Costa
 */
class TurmaAulaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\TurmaRepository
     */
    private $turmaRepository;

    /**
     *
     * @var \App\Repository\Principal\TurmaAulaRepository
     */
    private $turmaAulaRepository;

    /**
     *
     * @var \App\Repository\Principal\LicaoRepository
     */
    private $licaoRepository;

    /**
     *
     * @var \App\Repository\Principal\FuncionarioRepository
     */
    private $funcionarioRepository;

    /**
     *
     * @var \App\Repository\Principal\AlunoRepository
     */
    private $alunoRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->turmaAulaRepository   = self::getEntityManager()->getRepository(\App\Entity\Principal\TurmaAula::class);
        $this->turmaRepository       = self::getEntityManager()->getRepository(\App\Entity\Principal\Turma::class);
        $this->licaoRepository       = self::getEntityManager()->getRepository(\App\Entity\Principal\Licao::class);
        $this->funcionarioRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Funcionario::class);
        $this->alunoRepository       = self::getEntityManager()->getRepository(\App\Entity\Principal\Aluno::class);
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
    public function atualizaCamposTelaDiarioClasse(&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = $this->turmaAulaRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "TurmaAula não encontrado na base de dados.";
        } else {
            if (isset($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true && is_null($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === false) {
                $funcionarioORM = $this->funcionarioRepository->find($parametros[ConstanteParametros::CHAVE_FUNCIONARIO]);
                if (is_null($funcionarioORM) === true) {
                    $mensagemErro = "Funcionario não encontrado na base de dados.";
                    return false;
                }

                $objetoORM->setFuncionario($funcionarioORM);
            }

            $objetoORM->setFinalizada(true);
            $objetoORM->setObservacao($parametros[ConstanteParametros::CHAVE_OBSERVACAO]);
            $dataAula = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", $parametros[ConstanteParametros::CHAVE_DATA_AULA]);
            $objetoORM->setDataAula($dataAula);
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

    public function listar($turmaId, $parametros, &$mensagemErro)
    {
        $objetoORM = $this->turmaRepository->find($turmaId);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Turma não encontrada na base de dados.";
            return [];
        } else {
            $parametros[ConstanteParametros::CHAVE_TURMA] = $turmaId;
            $parametros[ConstanteParametros::CHAVE_LIVRO] = $objetoORM->getLivro()->getId();
            $licoesDoPlanejamentoLicao = $objetoORM->getLivro()->getPlanejamentoLicao()->getLicaos();
            $numeroLicoes = $licoesDoPlanejamentoLicao->count();
            for ($i = 0; $i < $numeroLicoes; $i++) {
                $licaoORM = $licoesDoPlanejamentoLicao->get($i);
                $parametros[ConstanteParametros::CHAVE_LICAO][] = $licaoORM->getId();
            }

            $parametros[ConstanteParametros::CHAVE_SEMESTRE] = $objetoORM->getSemestre()->getId();
            return $this->turmaAulaRepository->listarDados($parametros);
        }
    }

    public function listarPorTurma($turmaId, $parametros, &$mensagemErro)
    {
        $turmaORM = $this->turmaRepository->find($turmaId);
        if (is_null($turmaORM) === true) {
            $mensagemErro = "Turma não encontrada na base de dados.";
            return [];
        } else {
            $turmaAulas = $turmaORM->getTurmaAulas();
            $aulas      = [];
            foreach ($turmaAulas as $aula) {
                if (isset($parametros["modalidade"]) === true && $parametros["modalidade"] !== $aula->getLicao()->getModalidade()) {
                    continue;
                }

                $aulas[] = [
                    "id"        => $aula->getId(),
                    "licao"     => [
                        "id"        => $aula->getLicao()->getId(),
                        "descricao" => $aula->getLicao()->getDescricao(),
                        "numero"    => $aula->getLicao()->getNumero(),
                    ],
                    "data_aula" => $aula->getDataAula(),
                ];
            }

            return $aulas;
        }//end if
    }

    /**
     * Lista historico de aulas
     *
     * @param int $turmaId
     *
     * @return array
     */
    public function listarHistorico($turmaId)
    {
        $retorno = $this->turmaAulaRepository->listarHistorico($turmaId);
        if ($retorno == null){
            $retorno = [];
        }

        // Inserindo as lições aplicadas em cada lição
        foreach ($retorno as $k => $ret) {
            $turmaAulaORM    = $this->turmaAulaRepository->find($ret["id"]);
            $alunoDiariosORM = $turmaAulaORM->getAlunoDiarios();
            $retorno[$k]["licoes_aplicadas"] = [];
            if (count($alunoDiariosORM) > 0) {
                $licoesORM = $alunoDiariosORM[0]->getLicao();
                foreach ($licoesORM as $licao) {
                    $retorno[$k]["licoes_aplicadas"][] = [
                        "id"        => $licao->getId(),
                        "descricao" => $licao->getDescricao(),
                    ];
                }
            }
        }

        return $retorno;
    }

    /**
     * Busca as aulas finalizadas para exibição de homework
     *
     * @param int $turmaId
     *
     * @return array|NULL
     */
    public function buscarHomeworkPorTurma($turmaId)
    {
        return $this->licaoRepository->listarHomeworkPorTurma($turmaId);
    }

    /**
     * Gera as informações para a seleção de registros do relatório.
     *
     * @param array  $parametros
     *
     * @return string
     */
    public function gerarDadosRelatorio($parametros)
    {
        return $this->turmaAulaRepository->prepararDadosRelatorio($parametros);
    }

    /**
     * Busca as aulas finalizadas para exibição de homework
     *
     * @param int $turmaId
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function listarAvaliacoesPorTurma($turmaId, $parametros)
    {
        return $this->alunoRepository->listarAvaliacoesPorTurma($turmaId, $parametros);
    }

 
     /**
     * Gera Relatorio detalhado de Diario de Classe
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function gerarDadosRelatorioDiarioClasse($parametros) {
        
        $DiarioAula = $this->turmaRepository->buscarDadosTurmaTurmaAulaPorDataFuncionario($parametros);
        return $DiarioAula;

    //    return $this->turmaRepository->buscarDadosRelatorioDiarioClasse($parametros);
    }

         /**
     * Gera Relatorio detalhado de Diario de Classe
     *
     * @param array $parametros
     *
     * @return array|NULL
     */
    public function gerarDadosRelatorioDiarioClasseCompleto($parametros) {
        
        $DiarioAula = $this->turmaRepository->buscarDadosTurmaTurmaAulaPorDataFuncionarioRelatorio($parametros);
        return $DiarioAula;

    //    return $this->turmaRepository->buscarDadosRelatorioDiarioClasse($parametros);
    }

}
