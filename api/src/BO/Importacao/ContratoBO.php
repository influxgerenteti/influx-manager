<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\Contrato;

/**
 *
 * @author Luiz A. Costa
 */
class ContratoBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\ContratoRepository $repository
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param integer $franqueada_id
     */
    public static function excluirRegistrosPorFranqueada($repository, $entityManager, $franqueada_id)
    {
        $registros = $repository->findBy(["franqueada_id" => $franqueada_id]);
        if (empty($registros) === false) {
            $contador = count($registros);
            for ($i = 0; $i < $contador; $i++) {
                $entityManager->remove($registros[$i]);
            }

            $entityManager->flush();
        }
    }

    /**
     * Criar Registros por Franqueada
     *
     * @param \App\Helper\XlsxHelper $xlsxHelper
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param integer $franqueada_id
     * @param \App\Repository\Importacao\AlunoRepository $alunoRepository
     * @param \App\Repository\Importacao\ResponsavelRepository $responsavelRepository
     * @param \App\Repository\Importacao\FuncionarioRepository $funcionarioRepository
     * @param \App\Repository\Importacao\TurmaRepository $turmaRepository
     * @param \App\Repository\Importacao\CursoRepository $cursoRepository
     * @param \App\Repository\Importacao\EstagioRepository $estagioRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $alunoRepository, $responsavelRepository, $funcionarioRepository, $turmaRepository, $cursoRepository, $estagioRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $alunoNome       = $xlsxHelper->getValorCelulaIndice('C', $i);
            $responsavelNome = $xlsxHelper->getValorCelulaIndice('D', $i);
            $funcionarioNome = $xlsxHelper->getValorCelulaIndice('E', $i);
            $turmaNome       = $xlsxHelper->getValorCelulaIndice('N', $i);
            $cursoNome       = $xlsxHelper->getValorCelulaIndice('O', $i);
            $estagioNome     = $xlsxHelper->getValorCelulaIndice('P', $i);
            $tipo            = self::retornaTipoContrato($xlsxHelper->getValorCelulaIndice('B', $i));
            $situacao        = self::retornaSituacaoContrato($xlsxHelper->getValorCelulaIndice('L', $i));

            $alunoORM          = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);
            $responsavelORM    = self::retornaResponsavelOrNull($responsavelRepository, $responsavelNome, $franqueada_id);
            $funcionarioORM    = self::retornaFuncionarioOrNull($funcionarioRepository, $funcionarioNome, $franqueada_id);
            $turmaORM          = self::retornaTurmaOrNull($turmaRepository, $turmaNome, $franqueada_id);
            $cursoORM          = self::retornaCursoOrNull($cursoRepository, $cursoNome, $franqueada_id);
            $estagioORM        = self::retornaEstagioOrNull($estagioRepository, $estagioNome, $franqueada_id);
            $parametros        = [
                "numero_contrato"    => $xlsxHelper->getValorCelulaIndice('A', $i),
                "tipo"               => $tipo,
                "aluno_nome"         => $alunoNome,
                "responsavel_nome"   => $responsavelNome,
                "funcionario_nome"   => $funcionarioNome,
                "turma_nome"         => $turmaNome,
                "curso_nome"         => $cursoNome,
                "estagio_nome"       => $estagioNome,
                "usuario"            => $xlsxHelper->getValorCelulaIndice('G', $i),
                "data_inicio"        => $xlsxHelper->getValorCelulaIndice('H', $i),
                "data_termino"       => $xlsxHelper->getValorCelulaIndice('I', $i),
                "data_contrato"      => $xlsxHelper->getValorCelulaIndice('J', $i),
                "data_matricula"     => $xlsxHelper->getValorCelulaIndice('Q', $i),
                "observacao"         => $xlsxHelper->getValorCelulaIndice('K', $i),
                "situacao"           => $situacao,
                "percentual_empresa" => $xlsxHelper->getValorCelulaIndice('M', $i),
                "franqueada_id"      => $franqueada_id,
                "aluno"              => $alunoORM,
                "responsavel"        => $responsavelORM,
                "funcionario"        => $funcionarioORM,
                "turma"              => $turmaORM,
                "curso"              => $cursoORM,
                "estagio"            => $estagioORM,
            ];
            $importacaoItemORM = GeneralORMFactory::criar(Contrato::class, true, $parametros);
            $entityManager->persist($importacaoItemORM);
        }//end for

        $entityManager->flush();
    }


}
