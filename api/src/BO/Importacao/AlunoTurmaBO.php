<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\AlunoTurma;

/**
 *
 * @author Luiz A. Costa
 */
class AlunoTurmaBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\AlunoTurmaRepository $repository
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
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $alunoRepository, $turmaRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $turmaNome = $xlsxHelper->getValorCelulaIndice('B', $i);
            $alunoNome = $xlsxHelper->getValorCelulaIndice('D', $i);
            $alunoORM  = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);
            $turmaORM  = self::retornaTurmaOrNull($turmaRepository, $turmaNome, $franqueada_id);

            $parametros = [
                'codigo_turma'   => $xlsxHelper->getValorCelulaIndice('A', $i),
                'turma_nome'     => $turmaNome,
                'codigo_aluno'   => $xlsxHelper->getValorCelulaIndice('C', $i),
                'aluno_nome'     => $alunoNome,
                'data_matricula' => $xlsxHelper->getValorCelulaIndice('E', $i),
                'aluno'          => $alunoORM,
                'turma'          => $turmaORM,
                'franqueada_id'  => $franqueada_id,
            ];
            $importacaoAlunoTurmaORM = GeneralORMFactory::criar(AlunoTurma::class, true, $parametros);
            $entityManager->persist($importacaoAlunoTurmaORM);
        }//end for

        $entityManager->flush();
    }


}
