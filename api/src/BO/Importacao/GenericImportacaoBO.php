<?php
namespace App\BO\Importacao;

/**
 *
 * @author Luiz Antonio Costa
 */
class GenericImportacaoBO
{


    /**
     * Retorna a situacao da celula para o formato de DB
     *
     * @param string $situacao
     *
     * @return string
     */
    protected static function retornaSituacaoDB($situacao)
    {
        switch (trim(strtolower($situacao))) {
            case 'ativo':
            $retorno = 'A';
                break;
            case 'inativo':
            $retorno = 'I';
                break;
            case 'interessado':
            $retorno = 'IN';
                break;
            case 'trancado':
            $retorno = 'T';
                break;
            case 'cf - ativo':
            $retorno = 'CF';
                break;
            default:
            $retorno = 'IN';
        }

        return $retorno;
    }

    /**
     * Retorna a situacao da celula para o formato de DB
     *
     * @param string $situacao
     *
     * @return string
     */
    protected static function retornaSituacaoAulaDB($situacao)
    {
        switch (trim(strtolower($situacao))) {
            case 'cancelada':
            $retorno = 'C';
                break;
            case 'falta':
            $retorno = 'F';
                break;
            case 'não realizada':
            $retorno = 'N';
                break;
            case 'presença':
            $retorno = 'P';
                break;
            default:
            $retorno = '';
        }

        return $retorno;
    }

    /**
     * Retorna a situacao de aprovado ou nao do DB
     *
     * @param string $situacao
     *
     * @return string
     */
    protected static function retornaSituacaoAprovadoReprovadoDB($situacao)
    {
        switch (trim(strtolower($situacao))) {
            case 'aprovado':
            $retorno = 'A';
                break;
            case 'reprovado':
            $retorno = 'R';
                break;
            default:
            $retorno = '';
        }

        return $retorno;
    }

    /**
     * Retorna a situacao da celula para o formato de DB
     *
     * @param string $situacao
     *
     * @return string
     */
    protected static function retornaAtivoInativoDB($situacao)
    {
        switch (trim(strtolower($situacao))) {
            case 'ativo':
            $retorno = "A";
                break;
            case 'inativo':
            $retorno = "I";
                break;
            default:
            $retorno = null;
        }

        return $retorno;
    }

    /**
     * Retorna a situacao da celula para o formato de DB
     *
     * @param string $situacao
     *
     * @return string
     */
    protected static function retornaSimNaoDB($situacao)
    {
        switch (trim(strtolower($situacao))) {
            case 'sim':
            case '1':
            $retorno = "S";
                break;
            case 'não':
            case '0':
            $retorno = "N";
                break;
            default:
            $retorno = null;
        }

        return $retorno;
    }

    /**
     * Retorna a situacao da celula para o formato de DB
     *
     * @param string $situacao
     *
     * @return string
     */
    protected static function retornaTipoAula($situacao)
    {
        switch (trim($situacao)) {
            case 'Aula Livre':
            $retorno = "A";
                break;
            case 'Reposição':
            $retorno = "R";
                break;
            default:
            $retorno = null;
        }

        return $retorno;
    }

    /**
     * Retorna situacao do contrato
     *
     * @param string $situacao
     *
     * @return NULL
     */
    protected static function retornaSituacaoContrato($situacao)
    {
        switch ($situacao) {
            case 'Cancelado':
            $retorno = 'C';
                break;
            case 'Encerrado':
            $retorno = 'E';
                break;
            case 'Rescindido':
            $retorno = 'R';
                break;
            case 'Vigente':
            $retorno = 'V';
                break;
            case '':
            $retorno = 'S';
                break;
            default:
            $retorno = null;
        }

        return $retorno;
    }

    /**
     * Retorna o Tipo de Contrato
     *
     * @param string $tipo
     *
     * @return NULL
     */
    protected static function retornaTipoContrato($tipo)
    {
        switch ($tipo) {
            case 'Matrícula':
            $retorno = 'M';
                break;
            case 'Rematrícula':
            $retorno = 'R';
                break;
            case 'Transferência':
            $retorno = 'T';
                break;
            case 'Transf. Unidade (Entrada)':
            $retorno = 'T';
                break;
            case 'Sem Status':
            $retorno = 'S';
                break;
            default:
            $retorno = null;
        }

        return $retorno;
    }

    /**
     * Retorna o tipo de Conta a pagar/receber
     *
     * @param string $tipo
     *
     * @return NULL
     */
    protected static function retornaTipoContasPagarReceber($tipo)
    {
        switch ($tipo) {
            case 'Cartão de Crédito':
            $retorno = 'CC';
                break;
            case 'Cartão de Débito':
            $retorno = 'CD';
                break;
            case 'Cheque':
            $retorno = 'C';
                break;
            case 'Cheque Pré-Datado':
            $retorno = 'CP';
                break;
            case 'Cobrança Bancária':
            $retorno = 'CB';
                break;
            case 'Crédito Recorrente':
            $retorno = 'CR';
                break;
            case 'Débito Automático':
            $retorno = 'DA';
                break;
            case 'Dinheiro':
            $retorno = 'D';
                break;
            default:
            $retorno = null;
        }//end switch

        return $retorno;
    }

    /**
     * Retorna a situacao de contas a pagar/receber
     *
     * @param string $tipo
     *
     * @return NULL
     */
    protected static function retornaSituacaoContasPagarReceber($tipo)
    {
        switch ($tipo) {
            case 'Quitada':
            $retorno = 'Q';
                break;
            case 'Pendente':
            $retorno = 'P';
                break;
            case 'Cancelada':
            $retorno = 'C';
                break;
            default:
            $retorno = null;
        }

        return $retorno;
    }

    /**
     * Retorna o Tipo de caixa
     *
     * @param string $tipo
     *
     * @return NULL
     */
    protected static function retornaTipoCaixa($tipo)
    {
        switch ($tipo) {
            case 'Entrada':
            $retorno = 'E';
                break;
            case 'Saída':
            $retorno = 'S';
                break;
            default:
            $retorno = null;
        }

        return $retorno;
    }

    /**
     * Retorna a Situacao Bancaria
     *
     * @param string $situacao
     *
     * @return NULL
     */
    protected static function retornaSituacaoBancaria($situacao)
    {
        switch ($situacao) {
            case 'Devolvido':
            $retorno = 'D';
                break;
            case 'Pendente':
            $retorno = 'P';
                break;
            case 'Quitado':
            $retorno = 'Q';
                break;
            default:
            $retorno = null;
        }

        return $retorno;
    }

    /**
     * Retorna o Tipo Bancario
     *
     * @param string $tipo
     *
     * @return NULL
     */
    protected static function retornaTipoBancario($tipo)
    {
        switch ($tipo) {
            case 'Receber':
            $retorno = 'R';
                break;
            case 'Pagar':
            $retorno = 'P';
                break;
            default:
            $retorno = null;
        }

        return $retorno;
    }

    /**
     * Retorna o Tipo de Recebimento caixa
     *
     * @param string $tipo
     *
     * @return NULL
     */
    protected static function retornaTipoRecebimentoCaixa($tipo)
    {
        switch ($tipo) {
            case 'Cartão de Crédito':
            $retorno = 'CC';
                break;
            case 'Cartão de Débito':
            $retorno = 'CD';
                break;
            case 'Cheque':
            $retorno = 'C';
                break;
            case 'Cheque Pré-Datado':
            $retorno = 'CP';
                break;
            case 'Cobrança Bancária':
            $retorno = 'CB';
                break;
            case 'Crédito Recorrente':
            $retorno = 'CR';
                break;
            case 'Débito Automático':
            $retorno = 'DA';
                break;
            case 'Dinheiro':
            $retorno = 'D';
                break;
            default:
            $retorno = null;
        }//end switch

        return $retorno;
    }

    /**
     * Retorna o AlunoObj atraves do campo "aluno"(nome do aluno) ou Nulo
     *
     * @param \App\Repository\Importacao\AlunoRepository $alunoRepository
     * @param string $alunoNome
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\Aluno
     */
    protected static function retornaAlunoOrNull(\App\Repository\Importacao\AlunoRepository $alunoRepository, $alunoNome, $franqueada_id)
    {
        $alunoORM = $alunoRepository->findBy(["aluno" => $alunoNome, "franqueada_id" => $franqueada_id]);
        if (count($alunoORM) > 0) {
            $alunoORM = $alunoORM[0];
        } else {
            $alunoORM = null;
        }

        return $alunoORM;
    }

    /**
     * Retorna a Empresa atraves do campo "nome"(nome da empresa) ou Nulo
     *
     * @param \App\Repository\Importacao\EmpresaRepository $empresaRepository
     * @param string $empresaNome
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\Empresa
     */
    protected static function retornaEmpresaOrNull(\App\Repository\Importacao\EmpresaRepository $empresaRepository, $empresaNome, $franqueada_id)
    {
        $empresaORM = $empresaRepository->findBy(["nome" => $empresaNome, "franqueada_id" => $franqueada_id]);
        if (count($empresaORM) > 0) {
            $empresaORM = $empresaORM[0];
        } else {
            $empresaORM = null;
        }

        return $empresaORM;
    }

    /**
     * Retorna o Responsavel atraves do campo "nome"(nome do responsavel) ou Nulo
     *
     * @param \App\Repository\Importacao\ResponsavelRepository $responsavelRepository
     * @param string $responsavelNome
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\Responsavel
     */
    protected static function retornaResponsavelOrNull(\App\Repository\Importacao\ResponsavelRepository $responsavelRepository, $responsavelNome, $franqueada_id)
    {
        $responsavelORM = $responsavelRepository->findBy(["nome_responsavel" => $responsavelNome, "franqueada_id" => $franqueada_id]);
        if (count($responsavelORM) > 0) {
            $responsavelORM = $responsavelORM[0];
        } else {
            $responsavelORM = null;
        }

        return $responsavelORM;
    }

    /**
     * Retorna o Curso atraves do campo "nome"(nome do curso) ou Nulo
     *
     * @param \App\Repository\Importacao\CursoRepository $cursoRepository
     * @param string $cursoNome
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\Curso
     */
    protected static function retornaCursoOrNull(\App\Repository\Importacao\CursoRepository $cursoRepository, $cursoNome, $franqueada_id)
    {
        $cursoORM = $cursoRepository->findBy(["nome" => $cursoNome, "franqueada_id" => $franqueada_id]);
        if (count($cursoORM) > 0) {
            $cursoORM = $cursoORM[0];
        } else {
            $cursoORM = null;
        }

        return $cursoORM;
    }

    /**
     * Retorna o Item atraves do campo "descricao"(nome do item) ou Nulo
     *
     * @param \App\Repository\Importacao\ItemRepository $itemRepository
     * @param string $itemNome
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\Item
     */
    protected static function retornaItemOrNull(\App\Repository\Importacao\ItemRepository $itemRepository, $itemNome, $franqueada_id)
    {
        $itemORM = $itemRepository->findBy(["descricao" => $itemNome, "franqueada_id" => $franqueada_id]);
        if (count($itemORM) > 0) {
            $itemORM = $itemORM[0];
        } else {
            $itemORM = null;
        }

        return $itemORM;
    }

    /**
     * Retorna o Estagio atraves do campo "descricao"(nome do estagio) ou Nulo
     *
     * @param \App\Repository\Importacao\EstagioRepository $estagioRepository
     * @param string $estagioNome
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\Estagio
     */
    protected static function retornaEstagioOrNull(\App\Repository\Importacao\EstagioRepository $estagioRepository, $estagioNome, $franqueada_id)
    {
        $estagioORM = $estagioRepository->findBy(["descricao" => $estagioNome, "franqueada_id" => $franqueada_id]);
        if (count($estagioORM) > 0) {
            $estagioORM = $estagioORM[0];
        } else {
            $estagioORM = null;
        }

        return $estagioORM;
    }

    /**
     * Retorna o Funcionario atraves do campo "nome_abreviado"(nome do funcionario abreviado), caso nao encontre, ele ira procurar no campo "nome" ou Nulo
     *
     * @param \App\Repository\Importacao\FuncionarioRepository $funcionarioRepository
     * @param string $funcionarioNome
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\Funcionario
     */
    protected static function retornaFuncionarioOrNull(\App\Repository\Importacao\FuncionarioRepository $funcionarioRepository, $funcionarioNome, $franqueada_id)
    {
        $funcionarioORM = $funcionarioRepository->findBy(["nome_abreviado" => $funcionarioNome, "franqueada_id" => $franqueada_id]);
        if (count($funcionarioORM) > 0) {
            $funcionarioORM = $funcionarioORM[0];
        } else {
            $funcionarioORM = $funcionarioRepository->findBy(["nome" => $funcionarioNome, "franqueada_id" => $franqueada_id]);
            if (count($funcionarioORM) > 0) {
                $funcionarioORM = $funcionarioORM[0];
            } else {
                $funcionarioORM = null;
            }
        }

        return $funcionarioORM;
    }

    /**
     * Retorna o BiliotecaAutor atraves do campo "nome"(nome do autor) ou Nulo
     *
     * @param \App\Repository\Importacao\BibliotecaAutorRepository $bibliotecaAutorRepository
     * @param string $nomeAutor
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\BibliotecaAutor
     */
    protected static function retornaBibliotecaAutorOrNull(\App\Repository\Importacao\BibliotecaAutorRepository $bibliotecaAutorRepository, $nomeAutor, $franqueada_id)
    {
        $bibliotecaAutorORM = $bibliotecaAutorRepository->findBy(["nome" => $nomeAutor, "franqueada_id" => $franqueada_id]);
        if (count($bibliotecaAutorORM) > 0) {
            $bibliotecaAutorORM = $bibliotecaAutorORM[0];
        } else {
            $bibliotecaAutorORM = null;
        }

        return $bibliotecaAutorORM;
    }

    /**
     * Retorna o Contrato atraves do campo "numero_contato"(numero do contrato) ou nulo
     *
     * @param \App\Repository\Importacao\ContratoRepository $contratoRepository
     * @param string $numeroContrato
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\Contrato
     */
    protected static function retornaContratoOrNull(\App\Repository\Importacao\ContratoRepository $contratoRepository, $numeroContrato, $franqueada_id)
    {
        $contratoORM = $contratoRepository->findBy(["numero_contrato" => $numeroContrato, "franqueada_id" => $franqueada_id]);
        if (count($contratoORM) > 0) {
            $contratoORM = $contratoORM[0];
        } else {
            $contratoORM = null;
        }

        return $contratoORM;
    }

    /**
     * Retorna a BibliotecaObra atraves do campo "nome"(nome da obra) ou Nulo
     *
     * @param \App\Repository\Importacao\BibliotecaObraRepository $bibliotecaObraRepository
     * @param string $bibliotecaObraNome
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\BibliotecaObra
     */
    protected static function retornaBibliotecaObraOrNull(\App\Repository\Importacao\BibliotecaObraRepository $bibliotecaObraRepository, $bibliotecaObraNome, $franqueada_id)
    {
        $bibliotecaObraORM = $bibliotecaObraRepository->findBy(["nome" => $bibliotecaObraNome, "franqueada_id" => $franqueada_id]);
        if (count($bibliotecaObraORM) > 0) {
            $bibliotecaObraORM = $bibliotecaObraORM[0];
        } else {
            $bibliotecaObraORM = null;
        }

        return $bibliotecaObraORM;
    }

    /**
     * Retorna a Turma atraves do campo "nome"(nome da turma) ou Nulo
     *
     * @param \App\Repository\Importacao\TurmaRepository $turmaRepository
     * @param string $turmaNome
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\Turma
     */
    protected static function retornaTurmaOrNull(\App\Repository\Importacao\TurmaRepository $turmaRepository, $turmaNome, $franqueada_id)
    {
        $turmaORM = $turmaRepository->findBy(["nome" => $turmaNome, "franqueada_id" => $franqueada_id]);
        if (count($turmaORM) > 0) {
            $turmaORM = $turmaORM[0];
        } else {
            $turmaORM = null;
        }

        return $turmaORM;
    }

    /**
     * Retorna a BibliotecaEditora atraves do campo "nome"(nome da editora) ou Nulo
     *
     * @param \App\Repository\Importacao\BibliotecaEditoraRepository $bibliotecaEditoraRepository
     * @param string $bibliotecaEditoraNome
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\BibliotecaEditora
     */
    protected static function retornaBibliotecaEditoraOrNull(\App\Repository\Importacao\BibliotecaEditoraRepository $bibliotecaEditoraRepository, $bibliotecaEditoraNome, $franqueada_id)
    {
        $editoraORM = $bibliotecaEditoraRepository->findBy(["nome" => $bibliotecaEditoraNome, "franqueada_id" => $franqueada_id]);
        if (count($editoraORM) > 0) {
            $editoraORM = $editoraORM[0];
        } else {
            $editoraORM = null;
        }

        return $editoraORM;
    }

    /**
     * Retorna a Sala atraves do campo "descricao"(nome da sala) ou Nulo
     *
     * @param \App\Repository\Importacao\SalaRepository $salaRepository
     * @param string $salaNome
     * @param int $franqueada_id
     *
     * @return NULL|\App\Entity\Importacao\Sala
     */
    protected static function retornaSalaOrNull(\App\Repository\Importacao\SalaRepository $salaRepository, $salaNome, $franqueada_id)
    {
        $salaORM = $salaRepository->findBy(["descricao" => $salaNome, "franqueada_id" => $franqueada_id]);
        if (count($salaORM) > 0) {
            $salaORM = $salaORM[0];
        } else {
            $salaORM = null;
        }

        return $salaORM;
    }


}
