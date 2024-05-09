<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\BibliotecaEditora;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class BibliotecaEditoraBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\BibliotecaEditoraRepository $repository
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
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $parametros = [
                'codigo'        => $xlsxHelper->getValorCelulaIndice('A', $i),
                'nome'          => $xlsxHelper->getValorCelulaIndice('B', $i),
                'endereco'      => $xlsxHelper->getValorCelulaIndice('C', $i),
                'cidade'        => $xlsxHelper->getValorCelulaIndice('D', $i),
                'bairro'        => $xlsxHelper->getValorCelulaIndice('E', $i),
                'cep'           => $xlsxHelper->getValorCelulaIndice('F', $i),
                'telefone'      => $xlsxHelper->getValorCelulaIndice('G', $i),
                'fax'           => $xlsxHelper->getValorCelulaIndice('H', $i),
                'email'         => $xlsxHelper->getValorCelulaIndice('I', $i),
                'observacao'    => $xlsxHelper->getValorCelulaIndice('J', $i),
                'franqueada_id' => $franqueada_id,
            ];
            $importacaoBibliotecaEditoraORM = GeneralORMFactory::criar(BibliotecaEditora::class, true, $parametros);
            $entityManager->persist($importacaoBibliotecaEditoraORM);
        }//end for

        $entityManager->flush();
    }


}
