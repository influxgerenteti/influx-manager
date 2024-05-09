<?php

namespace App\Facade\Importacao;

use App\Facade\Principal\GenericFacade;
use App\Helper\ConstanteParametros;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\XlsxHelper;

/**
 *
 * @author Luiz A Costa
 */
class GeralImportacaoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Helper\XlsxHelper
     */
    private $xlsxHelper;

    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $baseImportacaoManager;

    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $basePrincipalManager;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct(ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->baseImportacaoManager = $managerRegistry->getManager("base_importacao");
        $this->basePrincipalManager  = $managerRegistry->getManager($connection);
        $this->xlsxHelper            = new XlsxHelper();
    }

    /**
     * Seleciona a importacao conforme passada na $flagImportacao
     *
     * @param array $flagImportacao Array de flagas para realizar a importacao. Exemplo: [0=>'A']
     * @param string $mensagemErro Ponteiro de mensagem para retornar ao front-end
     * @param int $franqueada_id ID da franqueada
     */
    private function selecionaImportacao($flagImportacao, &$mensagemErro, $franqueada_id)
    {
        switch ($flagImportacao) {
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_ALUNO_DIARIO:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("DiarioAlunos") === true) {
                $alunoDiario = new AlunoDiarioFacade(self::getManagerRegistry());
                $alunoDiario->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_ALUNOS:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Alunos") === true) {
                $alunoFacade = new AlunoFacade(self::getManagerRegistry());
                $alunoFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_ALUNOS_MEDIA:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Medias") === true) {
                $alunoMediaFacade = new AlunoMediaFacade(self::getManagerRegistry());
                $alunoMediaFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_ALUNOS_NOTA:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Notas") === true) {
                $alunoNotaFacade = new AlunoNotaFacade(self::getManagerRegistry());
                $alunoNotaFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_ALUNOS_TURMA:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("TurmaAlunos") === true) {
                $alunoTurmaFacade = new AlunoTurmaFacade(self::getManagerRegistry());
                $alunoTurmaFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_ALUNOS_EMAILS_OBS:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Alunos - Email - Obs") === true) {
                $alunoEmailFacade = new AlunoEmailFacade(self::getManagerRegistry());
                $alunoEmailFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_AULA_LIVRE:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("AulasLivres") === true) {
                $aulaLivre = new AulaLivreFacade(self::getManagerRegistry());
                $aulaLivre->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_BIBLIOTECA_AUTOR:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Biblioteca_Autores") === true) {
                $bibliotecaAutorFacade = new BibliotecaAutorFacade(self::getManagerRegistry());
                $bibliotecaAutorFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_BIBLIOTECA_CLASSIFICACAO:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Obras X Classificações") === true) {
                $bibliotecaClassificacaoFacade = new BibliotecaClassificacaoFacade(self::getManagerRegistry());
                $bibliotecaClassificacaoFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_BIBLIOTECA_EDITORA:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Biblioteca_Editoras") === true) {
                $bibliotecaEditoraFacade = new BibliotecaEditoraFacade(self::getManagerRegistry());
                $bibliotecaEditoraFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_BIBLIOTECA_EMPRESTIMO:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Obras X Emprestimos") === true) {
                $bibliotecaEmprestimoFacade = new BibliotecaEmprestimoFacade(self::getManagerRegistry());
                $bibliotecaEmprestimoFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_BIBLIOTECA_EXEMPLARES:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Obras X Exemplares") === true) {
                        $bibliotecaExemplaresFacade = new BibliotecaExemplaresFacade(self::getManagerRegistry());
                        $bibliotecaExemplaresFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_BIBLIOTECA_OBRA:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Obras") === true) {
                $bibliotecaObraFacade = new BibliotecaObraFacade(self::getManagerRegistry());
                $bibliotecaObraFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_BIBLIOTECA_OBRA_AUTOR:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Obras X Autores") === true) {
                $bibliotecaObraAutorFacade = new BibliotecaObraAutorFacade(self::getManagerRegistry());
                $bibliotecaObraAutorFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_BIBLIOTECA_OBRA_RESERVA:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Obras X Reservas") === true) {
                $bibliotecaObraReservaFacade = new BibliotecaObraReservaFacade(self::getManagerRegistry());
                $bibliotecaObraReservaFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_CAIXA:
                {
                    if ($this->xlsxHelper->setAbaAtivaPorNome("Caixa") === true) {
                        $caixaFacade = new CaixaFacade(self::getManagerRegistry());
                        $caixaFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
                    } else {
                        $mensagemErro .= $this->xlsxHelper->getErrorMsg();
                    }
                }
                break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_CONTRATOS:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Contratos") === true) {
                $contratosFacade = new ContratoFacade(self::getManagerRegistry());
                $contratosFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
                }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_CONTAS_PAGAR:
                {
                    if ($this->xlsxHelper->setAbaAtivaPorNome("ContasPagar") === true) {
                        $contasPagarFacade = new ContasPagarFacade(self::getManagerRegistry());
                        $contasPagarFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
                    } else {
                        $mensagemErro .= $this->xlsxHelper->getErrorMsg();
                    }
                }
                break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_CONTAS_RECEBER:
                {
                    if ($this->xlsxHelper->setAbaAtivaPorNome("ContasReceber") === true) {
                        $contasReceberFacade = new ContasReceberFacade(self::getManagerRegistry());
                        $contasReceberFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
                    } else {
                        $mensagemErro .= $this->xlsxHelper->getErrorMsg();
                    }
                }
                break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_CONTRATOS_AULAS_LIVRES:
                {
                    if ($this->xlsxHelper->setAbaAtivaPorNome("ContratosAulasLivres") === true) {
                        $contratosAulasLivresFacade = new ContratoAulaLivreFacade(self::getManagerRegistry());
                        $contratosAulasLivresFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
                    } else {
                        $mensagemErro .= $this->xlsxHelper->getErrorMsg();
                    }
                }
                break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_CURSOS:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Cursos") === true) {
                $cursosFacade = new CursoFacade(self::getManagerRegistry());
                $cursosFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_CHEQUES:
                {
                    if ($this->xlsxHelper->setAbaAtivaPorNome("Cheques") === true) {
                        $chequeFacade = new ChequeFacade(self::getManagerRegistry());
                        $chequeFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
                    } else {
                        $mensagemErro .= $this->xlsxHelper->getErrorMsg();
                    }
                }
                break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_EMPRESA:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Empresas") === true) {
                $empresaFacade = new EmpresaFacade(self::getManagerRegistry());
                $empresaFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_ESTAGIO:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Estagios") === true) {
                $estagioFacade = new EstagioFacade(self::getManagerRegistry());
                $estagioFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_FUNCIONARIO:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Funcionários") === true) {
                $funcionarioFacade = new FuncionarioFacade(self::getManagerRegistry());
                $funcionarioFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_FOLLOWUP:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("FollowUps") === true) {
                $followupFacade = new FollowUpFacade(self::getManagerRegistry());
                $followupFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_ITEM:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Itens") === true) {
                $itemFacade = new ItemFacade(self::getManagerRegistry());
                $itemFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_RESPONSAVEL:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("AlunosResponsaveis") === true) {
                $responsavelFacade = new ResponsavelFacade(self::getManagerRegistry());
                $responsavelFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
                }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_SALAS:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Salas") === true) {
                $salaFacade = new SalaFacade(self::getManagerRegistry());
                $salaFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            case ConstanteParametros::CHAVE_FLAG_IMPORTACAO_TURMAS:
        {
            if ($this->xlsxHelper->setAbaAtivaPorNome("Turmas") === true) {
                $turmaFacade = new TurmaFacade(self::getManagerRegistry());
                $turmaFacade->criar($mensagemErro, $franqueada_id, $this->xlsxHelper);
            } else {
                $mensagemErro .= $this->xlsxHelper->getErrorMsg();
            }
            }
        break;
            default:
            $mensagemErro .= "Não foi encontrado um metodo de importacao para a flag:" . $flagImportacao;
                break;
        }//end switch
    }

    /**
     * Realiza a importacao da planilha
     *
     * @param string $mensagemErro Ponteiro de mensagem para retornar ao front-end
     * @param array $parametros Array de parametros da requisição
     * @param int $franqueada_id ID da franqueada
     * @param string $caminhoArquivo
     *
     * @return boolean
     */
    public function importar(&$mensagemErro, &$parametros, $franqueada_id, $caminhoArquivo)
    {
        if ($this->xlsxHelper->carregarArquivoExcel($caminhoArquivo) === true) {
            sort($parametros[ConstanteParametros::CHAVE_FLAG_IMPORTACAO]);
            for ($i = 0; $i < count($parametros[ConstanteParametros::CHAVE_FLAG_IMPORTACAO]);$i++) {
                $this->selecionaImportacao($parametros[ConstanteParametros::CHAVE_FLAG_IMPORTACAO][$i], $mensagemErro, $franqueada_id);
            }

            return true;
        }

        return false;
    }


}
