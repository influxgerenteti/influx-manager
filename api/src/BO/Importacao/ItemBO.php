<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\Item;

/**
 *
 * @author Luiz A. Costa
 */
class ItemBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\ItemRepository $repository
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
            $coluna = $xlsxHelper->getCelula("L", $i);
            if (is_null($coluna) === false) {
                $situacao = self::retornaAtivoInativoDB($coluna->getValue());

                $parametros        = [
                    'codigo'         => $xlsxHelper->getValorCelulaIndice('A', $i),
                    'descricao'      => $xlsxHelper->getValorCelulaIndice('B', $i),
                    'finalidade'     => $xlsxHelper->getValorCelulaIndice('C', $i),
                    'descricao'      => $xlsxHelper->getValorCelulaIndice('D', $i),
                    'codigo_barra'   => $xlsxHelper->getValorCelulaIndice('E', $i),
                    'observacao'     => $xlsxHelper->getValorCelulaIndice('F', $i),
                    'kit'            => $xlsxHelper->getValorCelulaIndice('G', $i),
                    'valor_custo'    => $xlsxHelper->getValorCelulaIndice('H', $i),
                    'venda'          => $xlsxHelper->getValorCelulaIndice('I', $i),
                    'estoque_minimo' => $xlsxHelper->getValorCelulaIndice('J', $i),
                    'estoque_maximo' => $xlsxHelper->getValorCelulaIndice('K', $i),
                    'saldo'          => $xlsxHelper->getValorCelulaIndice('M', $i),
                    'situacao'       => $situacao,
                    'franqueada_id'  => $franqueada_id,
                ];
                $importacaoItemORM = GeneralORMFactory::criar(Item::class, true, $parametros);
                $entityManager->persist($importacaoItemORM);
            }//end if
        }//end for

        $entityManager->flush();
    }


}
