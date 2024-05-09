<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\BibliotecaObraAutor;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class BibliotecaObraAutorBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\BibliotecaObraAutorRepository $repository
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
     * @param \App\Repository\Importacao\BibliotecaAutorRepository $bibliotecaAutorRepository
     * @param \App\Repository\Importacao\BibliotecaObraRepository $bibliotecaObraRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $bibliotecaAutorRepository, $bibliotecaObraRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $nomeObra        = $xlsxHelper->getValorCelulaIndice("A", $i);
            $nomeAutor       = $xlsxHelper->getValorCelulaIndice("B", $i);
            $bibliotecaAutor = self::retornaBibliotecaAutorOrNull($bibliotecaAutorRepository, $nomeAutor, $franqueada_id);
            $bibliotecaObra  = self::retornaBibliotecaObraOrNull($bibliotecaObraRepository, $nomeObra, $franqueada_id);

            $parametros = [
                'biblioteca_obra_nome'  => $nomeObra,
                'biblioteca_autor_nome' => $nomeAutor,
                'biblioteca_obra'       => $bibliotecaObra,
                'biblioteca_autor'      => $bibliotecaAutor,
                'franqueada_id'         => $franqueada_id,
            ];
            $importacaoBibliotecaObraAutorORM = GeneralORMFactory::criar(BibliotecaObraAutor::class, true, $parametros);
            $entityManager->persist($importacaoBibliotecaObraAutorORM);
        }//end for

        $entityManager->flush();
    }


}
