<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\Estagio;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class EstagioBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\EstagioRepository $repository
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
     * @param \App\Repository\Importacao\CursoRepository $cursoRepository
     * @param \App\Repository\Importacao\ItemRepository $itemRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $cursoRepository, $itemRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $coluna = $xlsxHelper->getCelula("G", $i);
            if (is_null($coluna) === false) {
                $situacao  = self::retornaAtivoInativoDB($coluna->getValue());
                $cursoNome = $xlsxHelper->getValorCelulaIndice('C', $i);
                $itemNome  = $xlsxHelper->getValorCelulaIndice('F', $i);
                $cursoORM  = self::retornaCursoOrNull($cursoRepository, $cursoNome, $franqueada_id);
                $itemORM   = self::retornaItemOrNull($itemRepository, $itemNome, $franqueada_id);

                $parametros           = [
                    'codigo'           => $xlsxHelper->getValorCelulaIndice('A', $i),
                    'descricao'        => $xlsxHelper->getValorCelulaIndice('B', $i),
                    'curso_nome'       => $cursoNome,
                    'idioma'           => $xlsxHelper->getValorCelulaIndice('D', $i),
                    'nome'             => $xlsxHelper->getValorCelulaIndice('E', $i),
                    'item_nome'        => $itemNome,
                    'maximo_alunos'    => $xlsxHelper->getValorCelulaIndice('H', $i),
                    'idade_minima'     => $xlsxHelper->getValorCelulaIndice('I', $i),
                    'idade_maxima'     => $xlsxHelper->getValorCelulaIndice('J', $i),
                    'ponto_equilibrio' => $xlsxHelper->getValorCelulaIndice('K', $i),
                    'numero_horas'     => $xlsxHelper->getValorCelulaIndice('L', $i),
                    'valor_hora_aula'  => $xlsxHelper->getValorCelulaIndice('M', $i),
                    'curso'            => $cursoORM,
                    'item'             => $itemORM,
                    'situacao'         => $situacao,
                    'franqueada_id'    => $franqueada_id,
                ];
                $importacaoEstagioORM = GeneralORMFactory::criar(Estagio::class, true, $parametros);
                $entityManager->persist($importacaoEstagioORM);
            }//end if
        }//end for

        $entityManager->flush();
    }


}
