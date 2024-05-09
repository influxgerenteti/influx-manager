<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\BibliotecaAutor;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class BibliotecaAutorBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\BibliotecaAutorRepository $repository
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
            $coluna = $xlsxHelper->getCelula("C", $i);
            if (is_null($coluna) === false) {
                $situacao = self::retornaAtivoInativoDB($coluna->getValue());

                $parametros = [
                    'codigo'        => $xlsxHelper->getValorCelulaIndice('A', $i),
                    'nome'          => $xlsxHelper->getValorCelulaIndice('B', $i),
                    'situacao'      => $situacao,
                    'franqueada_id' => $franqueada_id,
                ];
                $importacaoBibliotecaAutorORM = GeneralORMFactory::criar(BibliotecaAutor::class, true, $parametros);
                $entityManager->persist($importacaoBibliotecaAutorORM);
            }
        }

        $entityManager->flush();
    }


}
