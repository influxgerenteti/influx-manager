<?php

namespace App\Facade\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Principal\Aluno;
use App\BO\Principal\AlunoBO;
use App\Helper\SituacoesSistema;
use App\Helper\VariaveisCompartilhadas;

/**
 *
 * @author Luiz A Costa
 */
class AlunoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\AlunoRepository
     */
    private $alunoRepository;

    /**
     *
     * @var \App\Repository\Principal\ContratoRepository
     */
    private $contratoRepository;

    /**
     *
     * @var \App\Repository\Principal\UsuarioRepository
     */
    private $usuarioRepository;

    /**
     *
     * @var \App\BO\Principal\AlunoBO
     */
    private $alunoBO;

    /**
     * Grava o historico de alteracao de situacao
     *
     * @param \App\Entity\Principal\Aluno $alunoORM
     * @param string $mensagemErro
     * @param string $situacaoAtual
     */
    private function gravaHistoricoAlteracaoSituacao($alunoORM, &$mensagemErro, $situacaoAtual=null)
    {
        $usuarioORM = $this->usuarioRepository->find(VariaveisCompartilhadas::$usuarioID);
        if (is_null($usuarioORM) === true) {
            $mensagemErro = "Usuário não encontrado!";
            return false;
        }

        $historicoSituacaoAluno = new \App\Entity\Principal\HistoricoSituacaoAluno();
        $historicoSituacaoAluno->setAluno($alunoORM);
        if (is_null($situacaoAtual) === true) {
            $historicoSituacaoAluno->setSituacaoAnterior(null);
            $historicoSituacaoAluno->setSituacaoAtual($alunoORM->getSituacao());
        } else {
            $historicoSituacaoAluno->setSituacaoAnterior($alunoORM->getSituacao());
            $historicoSituacaoAluno->setSituacaoAtual($situacaoAtual);
        }

        $historicoSituacaoAluno->setUsuarioAlteracao($usuarioORM);
        self::persistSeguro($historicoSituacaoAluno, $mensagemErro);
    }

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->alunoRepository    = self::getEntityManager()->getRepository(Aluno::class);
        $this->usuarioRepository  = self::getEntityManager()->getRepository(\App\Entity\Principal\Usuario::class);
        $this->contratoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Contrato::class);
        $this->alunoBO            = new AlunoBO(self::getEntityManager());
    }

    /**
     * Atualiza o registro no banco de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param int $id Id do registro a ser atualizado
     * @param array $parametros
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = null;
        if ($this->alunoBO->verificaAlunoExiste($this->alunoRepository, $id, $mensagemErro, $objetoORM, true) === true) {
            $parametros[ConstanteParametros::CHAVE_PESSOA] = $objetoORM->getPessoa();

            if ($this->alunoBO->podeAtualizar($parametros, $mensagemErro) === true) {
                if ((is_null($parametros[ConstanteParametros::CHAVE_ALUNO_FOTO]) === true)||(empty($parametros[ConstanteParametros::CHAVE_ALUNO_FOTO]) === true)) {
                    $arquivo = $parametros[ConstanteParametros::CHAVE_ALUNO_FOTO_EXISTENTE];
                } else {
                    $arquivo = $parametros[ConstanteParametros::CHAVE_ALUNO_FOTO];
                }

                $listaDeTipoVisibilidade = $objetoORM->getTipoVisibilidade();
                foreach ($listaDeTipoVisibilidade as $key => $midiaORM) {
                    $objetoORM->removeTipoVisibilidade($listaDeTipoVisibilidade[$key]);
                }

                foreach ($parametros[ConstanteParametros::CHAVE_TIPO_VISIBILIDADE] as $midiaORM) {
                    $objetoORM->addTipoVisibilidade($midiaORM);
                }

                if ($this->alunoBO->gravaArquivo($arquivo, $mensagemErro, $objetoORM, "setFoto", "getFoto") === true) {
                    unset($parametros[ConstanteParametros::CHAVE_ALUNO_FOTO_EXISTENTE]);
                    if (is_object($parametros[ConstanteParametros::CHAVE_INTERESSADO]) === true) {
                        $parametros[ConstanteParametros::CHAVE_INTERESSADO]->setAluno($objetoORM);
                    }

                    self::getFctHelper()->setParams($parametros, $objetoORM);
                }

                $this->atualizarSituacao($mensagemErro, $objetoORM);
                self::flushSeguro($mensagemErro);
            }//end if
        }//end if

        return empty($mensagemErro);
    }

    /**
     * Atualiza a situação do aluno de acordo com as regras de negócio
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param \App\Entity\Principal\Aluno $alunoORM
     *
     * @return boolean
     */
    public function atualizarSituacao(&$mensagemErro, &$alunoORM)
    {
        $contratos = $alunoORM->getContratos();
        if (count($contratos) === 0) {
           // $situacao = SituacoesSistema::ALUNO_INTERESSADO;
            $situacao = SituacoesSistema::ALUNO_ATIVO;
        } else {
            $possuiVigente  = false;
            $possuiTrancado = false;
            foreach ($contratos as $contratoORM) {
                $situacaoContrato = $contratoORM->getSituacao();
                if ($situacaoContrato === SituacoesSistema::SITUACAO_CONTRATO_VIGENTE) {
                    $possuiVigente = true;
                    break;
                }

                if ($situacaoContrato === SituacoesSistema::SITUACAO_CONTRATO_TRANCADO) {
                    $possuiTrancado = true;
                }
            }

            if ($possuiVigente === true) {
                $situacao = SituacoesSistema::ALUNO_ATIVO;
            } else if ($possuiTrancado === true) {
                $situacao = SituacoesSistema::ALUNO_TRANCADO;
            } else {
                $situacao = SituacoesSistema::ALUNO_INATIVO;
            }
        }//end if

        if ($situacao !== $alunoORM->getSituacao()) {
            $this->gravaHistoricoAlteracaoSituacao($alunoORM, $mensagemErro, $situacao);
        }

        $alunoORM->setSituacao($situacao);

        return empty($mensagemErro);
    }

    /**
     * Busca um registro atraves da ID
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param int $id Id do registro a ser buscado no banco de dados
     * @param bool $apenasProximaLicao apenas as proximas licoes
     *
     * @return NULL|\App\Entity\Principal\Aluno
     */
    public function buscarPorId(&$mensagemErro, $id, $apenasProximaLicao)
    {
        $objetoORM = $this->alunoRepository->buscarPorId($id, $apenasProximaLicao);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Aluno não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Busca as pessoas que contem o nome parcial
     *
     * @param string $nome
     * @param int $franqueada
     *
     * @return NULL|\App\Entity\Principal\Aluno
     */
    public function buscarPessoaPorNome($nome, $franqueada)
    {
        $objetoORM = $this->alunoRepository->buscarPessoaPorNome($nome, $franqueada);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Aluno não encontrado na base de dados.";
        }

        return $objetoORM;
    }



    /**
     * Busca um registro atraves do nome
     *
     * @param string $query nome a ser buscado
     *
     * @return NULL|\App\Entity\Principal\Aluno
     */
    public function buscarPorNome ($query)
    {
        return $this->alunoRepository->buscaAlunosPorNome($query);
    }

    /**
     * Busca um registro atraves do nome com contrato
     *
     * @param string $query nome a ser buscado
     * @param array $parametros Lista de parametros
     *
     * @return NULL|\App\Entity\Principal\Aluno
     */
    public function buscarAlunoPorNomeComContrato ($query, $parametros)
    {
        return $this->alunoRepository->buscaAlunosPorNomeComContrato($query, $parametros);
    }

        /**
     * Busca um registro atraves do nome com contrato
     *
     * @param string $query nome a ser buscado
     * @param array $parametros Lista de parametros
     *
     * @return NULL|\App\Entity\Principal\Aluno
     */
    public function buscarAlunoPorNomeComContratoTodos ($query, $parametros)
    {
        return $this->alunoRepository->buscaAlunosPorNomeComContratoTodos($query, $parametros);
    }

    /**
     * Busca um registro atraves do nome ou cpf
     *
     * @param string $query nome ou cpf a ser buscado
     *
     * @return NULL|\App\Entity\Principal\Aluno
     */
    public function buscarPorNomeCpf ($query)
    {
        return $this->alunoRepository->buscaAlunosPorNomeCpf($query);
    }

    /**
     * Busca um registro atraves do cpf
     *
     * @param string $query cpf a ser buscado
     *
     * @return NULL|\App\Entity\Principal\Aluno
     */
    public function buscarPorCpf ($query)
    {
        return $this->alunoRepository->buscaAlunosPorCpf($query);
    }

    /**
     * Busca um registro atraves do cpf
     *
     * @param string $query cpf a ser buscado
     *
     * @return NULL|\App\Entity\Principal\Aluno
     */
    public function buscarPorPessoa($pessoaId)
    {
        return $this->alunoRepository->buscaAlunoPorPessoa($pessoaId);
    }

    /**
     * Cria um registro na base de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param array $parametros Parametros para realizar a criacao do registro
     *
     * @return boolean null|\App\Entity\Principal\Aluno
     */
    public function criar(&$mensagemErro, $parametros=[])
    {
        $objetoORM = null;
        if ($this->alunoBO->podeCriar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(Aluno::class, true, $parametros);
            if ($this->alunoBO->gravaArquivo($parametros[ConstanteParametros::CHAVE_ALUNO_FOTO], $mensagemErro, $objetoORM, "setFoto", "getFoto") === true) {
                unset($parametros[ConstanteParametros::CHAVE_ALUNO_FOTO_EXISTENTE]);

                self::persistSeguro($objetoORM, $mensagemErro);
                if (is_object($parametros[ConstanteParametros::CHAVE_INTERESSADO]) === true) {
                    $parametros[ConstanteParametros::CHAVE_INTERESSADO]->setAluno($objetoORM);
                    $parametros[ConstanteParametros::CHAVE_INTERESSADO]->setSituacao(SituacoesSistema::SITUACAO_CONVERTIDO);
                }

                $listaDeTipoVisibilidade = $objetoORM->getTipoVisibilidade();
                foreach ($listaDeTipoVisibilidade as $key => $midiaORM) {
                    $objetoORM->removeTipoVisibilidade($listaDeTipoVisibilidade[$key]);
                }

                foreach ($parametros[ConstanteParametros::CHAVE_TIPO_VISIBILIDADE] as $midiaORM) {
                    $objetoORM->addTipoVisibilidade($midiaORM);
                }

                self::flushSeguro($mensagemErro);

                if (empty($mensagemErro) === true) {
                    $this->atualizarSituacao($mensagemErro, $objetoORM);
                    $this->gravaHistoricoAlteracaoSituacao($objetoORM, $mensagemErro, SituacoesSistema::ALUNO_INTERESSADO);
                }

                self::flushSeguro($mensagemErro);
            } else {
                $objetoORM = null;
            }//end if
        }//end if

        return $objetoORM;
    }

    /**
     * Lista todos os alunos do banco de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param array $parametros Array de parametros de paginacao
     *
     * @return array
     */
    public function listar(&$mensagemErro, $parametros)
    {
        $retornoRepositorio = $this->alunoRepository->filtrarAlunosPorPagina($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

     /**
     * Lista todos os alunos do banco de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param array $parametros Array de parametros de paginacao
     *
     * @return array
     */
    public function listarHeader(&$mensagemErro, $parametros)
    {
        $retornoRepositorio = $this->alunoRepository->filtrarAlunosPorPaginaHeader($parametros, $parametros[ConstanteParametros::CHAVE_PAGINA]);
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
     * @param int $id Id do registro a ser 'removido'
     *
     * @return boolean
     */
    public function remover(&$mensagemErro, $id)
    {
        $objetoORM = null;
        if ($this->alunoBO->verificaAlunoExiste($this->alunoRepository, $id, $mensagemErro, $objetoORM, true) === true) {
            $objetoORM->setExcluido(true);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Realiza a transferencia de contrato/turma
     *
     * @param string $mensagemErro
     * @param int $contratoId
     * @param array $parametros
     *
     * @return boolean
     */
    public function transfereAluno(&$mensagemErro, $contratoId, $parametros)
    {
        $objetoORM = $this->contratoRepository->find($contratoId);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Contrato não encontrado na base de dados.";
        } else {
            if ($this->alunoBO->aplicaTransferencia($objetoORM, $mensagemErro, $parametros) === true) {
                self::flushSeguro($mensagemErro);
            }
        }

        return empty($mensagemErro);
    }

    /**
     * Retorna lista de alunos
     *
     * @param int $franqueadaId
     * @param array $arrayId
     *
     * @return \App\Entity\Principal\Aluno[]
     */
    public function buscarAlunosORM($franqueadaId, $arrayId)
    {
        return $this->alunoRepository->buscarTodosAlunosORMPorFranqueada($franqueadaId, $arrayId);
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
        return $this->alunoRepository->prepararDadosRelatorio($parametros);
    }

    /**
     * Retorna se o aluno possui algum contrato que não seja rascunho
     *
     * @param int $alunoId
     *
     * @return boolean
     */
    public function alunoPossuiContratoValido($alunoId)
    {
        $alunoORM = $this->alunoRepository->find($alunoId);
        if (is_null($alunoORM) === true) {
            return false;
        }

        $contratos = $alunoORM->getContratos();
        foreach ($contratos as $contratoORM) {
            if ($contratoORM->getSituacao() !== SituacoesSistema::SITUACAO_CONTRATO_RASCUNHO) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gera os dados do relatório de titulos
     *
     * @param string $mensagem
     * @param array $parametros
     *
     * @return array
     */
    public function gerarDadosRelatorioSituacaoAlunos(&$mensagem, $parametros)
    {
        $dadosRelatorio = $this->alunoRepository->buscarDadosRelatorioSituacaoAlunos($mensagem, $parametros);

        $retorno   = [
            "alunos" => $dadosRelatorio,
            "totais" => [],
        ];
        $ativos    = 0;
        $inativos  = 0;
        $trancados = 0;
        foreach ($dadosRelatorio as $dado) {


            if ($dado["situacao"] === 'Ativo') {
                $ativos++;
            } else if ($dado["situacao"] === 'Inativo') {
                $inativos++;
            } else if ($dado["situacao"] === 'Trancado') {
                $trancados++;
            }
        }

        $retorno["totais"]["Total"]     = count($dadosRelatorio);
        $retorno["totais"]["Ativos"]    = $ativos;
        $retorno["totais"]["Inativos"]  = $inativos;
        $retorno["totais"]["Trancados"] = $trancados;

        return $retorno;
    }

    public function gerarDadosRelatorioDescontos($parametros = null) {
        return $this->alunoRepository->buscarDadosRelatorioDescontos($parametros);
    }

    public function relatorioMatriculasRenovar($parametros = null) {
        return $this->alunoRepository->buscarDadosRelatorioMatriculaRenovar($parametros);
    }

    public function buscarDadosRelatorioCompromissoAprendizado($parametros) {
        return $this->alunoRepository->buscarDadosRelatorioCompromissoAprendizado($parametros);
    }
}
