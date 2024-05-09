<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\BibliotecaClassificacao;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class BibliotecaClassificacaoBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\CursoRepository $repository
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
            $nomeObra       = $xlsxHelper->getValorCelulaIndice('A', $i);
            $bibliotecaObra = self::retornaBibliotecaObraOrNull($bibliotecaObraRepository, $nomeObra, $franqueada_id);

            $parametros = [
                'biblioteca_obra_nome' => $nomeObra,
                'descricao'            => $xlsxHelper->getValorCelulaIndice('B', $i),
                'biblioteca_obra'      => $bibliotecaObra,
                'franqueada_id'        => $franqueada_id,
            ];
            $importacaoBibliotecaClassificacaoORM = GeneralORMFactory::criar(BibliotecaClassificacao::class, true, $parametros);
            $entityManager->persist($importacaoBibliotecaClassificacaoORM);
        }

        $entityManager->flush();
    }


}
