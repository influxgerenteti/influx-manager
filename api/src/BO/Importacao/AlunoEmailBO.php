<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\AlunoEmail;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz Antonio Costa
 */
class AlunoEmailBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\AlunoEmailRepository $repository
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
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $alunoRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $nomeAluno = $xlsxHelper->getValorCelulaIndice('A', $i);
            $aluno     = self::retornaAlunoOrNull($alunoRepository, $nomeAluno, $franqueada_id);

            $parametros = [
                'aluno_nome'    => $nomeAluno,
                'email'         => $xlsxHelper->getValorCelulaIndice('B', $i),
                'observacao'    => $xlsxHelper->getValorCelulaIndice('C', $i),
                'franqueada_id' => $franqueada_id,
                'aluno'         => $aluno,
            ];
            $importacaoAlunoEmailORM = GeneralORMFactory::criar(AlunoEmail::class, true, $parametros);
            $entityManager->persist($importacaoAlunoEmailORM);
        }//end for

        $entityManager->flush();
    }


}
