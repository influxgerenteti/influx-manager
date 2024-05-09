<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\BibliotecaEmprestimo;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class BibliotecaEmprestimoBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\BibliotecaEmprestimoRepository $repository
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
            $alunoNome = $xlsxHelper->getValorCelulaIndice('B', $i);
            $aluno     = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);

            $parametros = [
                'codigo'            => $xlsxHelper->getValorCelulaIndice('A', $i),
                'aluno_nome'        => $alunoNome,
                'numero_exemplares' => $xlsxHelper->getValorCelulaIndice('E', $i),
                'data_emprestimo'   => $xlsxHelper->getValorCelulaIndice('F', $i),
                'data_prevista'     => $xlsxHelper->getValorCelulaIndice('G', $i),
                'data_devolucao'    => $xlsxHelper->getValorCelulaIndice('H', $i),
                'renovacao'         => $xlsxHelper->getValorCelulaIndice('I', $i),
                'aluno'             => $aluno,
                'franqueada_id'     => $franqueada_id,
            ];
            $importacaoBibliotecaEmprestimoORM = GeneralORMFactory::criar(BibliotecaEmprestimo::class, true, $parametros);
            $entityManager->persist($importacaoBibliotecaEmprestimoORM);
        }//end for

        $entityManager->flush();
    }


}
