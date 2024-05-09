<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\BibliotecaObra;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class BibliotecaObraBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\BibliotecaObraRepository $repository
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
     * @param \App\Repository\Importacao\BibliotecaEditoraRepository $bibliotecaEditoraRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $bibliotecaEditoraRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $bibliotecaEditoraNome = $xlsxHelper->getValorCelulaIndice('D', $i);
            $editora = self::retornaBibliotecaEditoraOrNull($bibliotecaEditoraRepository, $bibliotecaEditoraNome, $franqueada_id);

            $parametros = [
                'codigo'                  => $xlsxHelper->getValorCelulaIndice('A', $i),
                'nome'                    => $xlsxHelper->getValorCelulaIndice('B', $i),
                'genero'                  => $xlsxHelper->getValorCelulaIndice('C', $i),
                'biblioteca_editora_nome' => $bibliotecaEditoraNome,
                'idioma'                  => $xlsxHelper->getValorCelulaIndice('E', $i),
                'nivel'                   => $xlsxHelper->getValorCelulaIndice('F', $i),
                'palavra_chave'           => $xlsxHelper->getValorCelulaIndice('G', $i),
                'cdd'                     => $xlsxHelper->getValorCelulaIndice('H', $i),
                'cutter'                  => $xlsxHelper->getValorCelulaIndice('I', $i),
                'franqueada_id'           => $franqueada_id,
                'biblioteca_editora'      => $editora,
            ];
            $importacaoBibliotecaObraORM = GeneralORMFactory::criar(BibliotecaObra::class, true, $parametros);
            $entityManager->persist($importacaoBibliotecaObraORM);
        }//end for

        $entityManager->flush();
    }


}
