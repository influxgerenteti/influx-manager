<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\BibliotecaExemplares;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class BibliotecaExemplaresBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\BibliotecaExemplaresRepository $repository
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
     * @param \App\Repository\Importacao\BibliotecaObraRepository $bibliotecaObraRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $bibliotecaObraRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $coluna = $xlsxHelper->getCelula("E", $i);
            if (is_null($coluna) === false) {
                $situacao           = self::retornaAtivoInativoDB($coluna->getValue());
                $bibliotecaObraNome = $xlsxHelper->getValorCelulaIndice('A', $i);
                $bibliotecaObra     = self::retornaBibliotecaObraOrNull($bibliotecaObraRepository, $bibliotecaObraNome, $franqueada_id);

                $parametros = [
                    'biblioteca_obra_nome' => $bibliotecaObraNome,
                    'numero_exemplares'    => $xlsxHelper->getValorCelulaIndice('B', $i),
                    'ano'                  => $xlsxHelper->getValorCelulaIndice('C', $i),
                    'data_aquisicao'       => $xlsxHelper->getValorCelulaIndice('D', $i),
                    'descricao'            => $xlsxHelper->getValorCelulaIndice('F', $i),
                    'edicao'               => $xlsxHelper->getValorCelulaIndice('G', $i),
                    'volume'               => $xlsxHelper->getValorCelulaIndice('H', $i),
                    'codigo_barra'         => $xlsxHelper->getValorCelulaIndice('I', $i),
                    'tombo'                => $xlsxHelper->getValorCelulaIndice('J', $i),
                    'localizacao'          => $xlsxHelper->getValorCelulaIndice('K', $i),
                    'observacao'           => $xlsxHelper->getValorCelulaIndice('L', $i),
                    'isbn'                 => $xlsxHelper->getValorCelulaIndice('M', $i),
                    'issn'                 => $xlsxHelper->getValorCelulaIndice('N', $i),
                    'biblioteca_obra'      => $bibliotecaObra,
                    'situacao'             => $situacao,
                    'franqueada_id'        => $franqueada_id,
                ];
                $importacaoBibliotecaExemplaresORM = GeneralORMFactory::criar(BibliotecaExemplares::class, true, $parametros);
                $entityManager->persist($importacaoBibliotecaExemplaresORM);
            }//end if
        }//end for

        $entityManager->flush();
    }


}
