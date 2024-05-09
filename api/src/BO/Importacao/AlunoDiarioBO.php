<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\AlunoDiario;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class AlunoDiarioBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\AlunoDiarioRepository $repository
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
     * @param \App\Repository\Importacao\TurmaRepository $turmaRepository
     * @param \App\Repository\Importacao\EstagioRepository $estagioRepository
     * @param \App\Repository\Importacao\FuncionarioRepository $funcionarioRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $alunoRepository, $turmaRepository, $estagioRepository, $funcionarioRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $alunoNome       = $xlsxHelper->getValorCelulaIndice('A', $i);
            $turmaNome       = $xlsxHelper->getValorCelulaIndice('B', $i);
            $estagioNome     = $xlsxHelper->getValorCelulaIndice('C', $i);
            $funcionarioNome = $xlsxHelper->getValorCelulaIndice('D', $i);
            $alunoORM        = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);
            $turmaORM        = self::retornaTurmaOrNull($turmaRepository, $turmaNome, $franqueada_id);
            $estagioORM      = self::retornaEstagioOrNull($estagioRepository, $estagioNome, $franqueada_id);
            $funcionarioORM  = self::retornaFuncionarioOrNull($funcionarioRepository, $funcionarioNome, $franqueada_id);
            $situacao        = self::retornaSituacaoAulaDB($xlsxHelper->getValorCelulaIndice('H', $i));

            $parametros = [
                'aluno_nome'       => $alunoNome,
                'turma_nome'       => $turmaNome,
                'estagio_nome'     => $estagioNome,
                'funcionario_nome' => $funcionarioNome,
                'data'             => $xlsxHelper->getValorCelulaIndice('E', $i),
                'horario_inicial'  => $xlsxHelper->getValorCelulaIndice('F', $i),
                'horario_final'    => $xlsxHelper->getValorCelulaIndice('G', $i),
                'situacao'         => $situacao,
                'aluno'            => $alunoORM,
                'turma'            => $turmaORM,
                'estagio'          => $estagioORM,
                'funcionario'      => $funcionarioORM,
                'franqueada_id'    => $franqueada_id,
            ];
            $importacaoAlunoDiarioORM = GeneralORMFactory::criar(AlunoDiario::class, true, $parametros);
            $entityManager->persist($importacaoAlunoDiarioORM);
        }//end for

        $entityManager->flush();
    }


}
