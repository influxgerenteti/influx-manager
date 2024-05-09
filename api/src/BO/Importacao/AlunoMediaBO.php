<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\AlunoMedia;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class AlunoMediaBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\AlunoMediaRepository $repository
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
            $situacao  = self::retornaSituacaoAprovadoReprovadoDB($xlsxHelper->getValorCelulaIndice('D', $i));
            $alunoNome = $xlsxHelper->getValorCelulaIndice('A', $i);
            $alunoORM  = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);

            $parametros = [
                'aluno_nome'    => $xlsxHelper->getValorCelulaIndice('A', $i),
                'nota'          => $xlsxHelper->getValorCelulaIndice('B', $i),
                'conceito'      => $xlsxHelper->getValorCelulaIndice('C', $i),
                'situacao'      => $situacao,
                'aluno'         => $alunoORM,
                'franqueada_id' => $franqueada_id,
            ];
            $importacaoAlunoMediaORM = GeneralORMFactory::criar(AlunoMedia::class, true, $parametros);
            $entityManager->persist($importacaoAlunoMediaORM);
        }//end for

        $entityManager->flush();
    }


}
