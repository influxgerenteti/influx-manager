<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\AlunoNota;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class AlunoNotaBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\AlunoNotaRepository $repository
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
     * @param \App\Repository\Importacao\EstagioRepository $estagioRepository
     * @param \App\Repository\Importacao\TurmaRepository $turmaRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $alunoRepository, $estagioRepository, $turmaRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $alunoNome   = $xlsxHelper->getValorCelulaIndice('A', $i);
            $turmaNome   = $xlsxHelper->getValorCelulaIndice('B', $i);
            $estagioNome = $xlsxHelper->getValorCelulaIndice('C', $i);
            $alunoORM    = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);
            $turmaORM    = self::retornaTurmaOrNull($turmaRepository, $turmaNome, $franqueada_id);
            $estagioORM  = self::retornaEstagioOrNull($estagioRepository, $estagioNome, $franqueada_id);

            $parametros         = [
                'aluno_nome'     => $alunoNome,
                'turma_nome'     => $turmaNome,
                'estagio_nome'   => $estagioNome,
                'nome_prova'     => $xlsxHelper->getValorCelulaIndice('D', $i),
                'nome_avaliacao' => $xlsxHelper->getValorCelulaIndice('E', $i),
                'nota'           => $xlsxHelper->getValorCelulaIndice('F', $i),
                'conceito'       => $xlsxHelper->getValorCelulaIndice('G', $i),
                'aluno'          => $alunoORM,
                'estagio'        => $estagioORM,
                'turma'          => $turmaORM,
                'franqueada_id'  => $franqueada_id,
            ];
            $importacaoAlunoORM = GeneralORMFactory::criar(AlunoNota::class, true, $parametros);
            $entityManager->persist($importacaoAlunoORM);
        }//end for

        $entityManager->flush();
    }


}
