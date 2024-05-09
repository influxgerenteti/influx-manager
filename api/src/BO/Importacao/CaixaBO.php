<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\Caixa;

/**
 *
 * @author Luiz A. Costa
 */
class CaixaBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\CaixaRepository $repository
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
     * @param \App\Repository\Importacao\EmpresaRepository $empresaRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $empresaRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $empresaNome     = $xlsxHelper->getValorCelulaIndice('F', $i);
            $empresaORM      = self::retornaEmpresaOrNull($empresaRepository, $empresaNome, $franqueada_id);
            $tipo            = trim($xlsxHelper->getValorCelulaIndice('D', $i));
            $tipoRecebimento = self::retornaTipoRecebimentoCaixa(trim($xlsxHelper->getValorCelulaIndice('G', $i)));
            $dataLancamento  = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($xlsxHelper->getValorCelulaIndice('B', $i));

            $parametros        = [
                "codigo"            => $xlsxHelper->getValorCelulaIndice('A', $i),
                "data_lancamento"   => $dataLancamento->format("d/m/Y"),
                "valor"             => $xlsxHelper->getValorCelulaIndice('C', $i),
                "tipo"              => $tipo,
                "conta"             => $xlsxHelper->getValorCelulaIndice('E', $i),
                "empresa_nome"      => $xlsxHelper->getValorCelulaIndice('F', $i),
                "tipo_recebimento"  => $tipoRecebimento,
                "plano_conta"       => $xlsxHelper->getValorCelulaIndice('H', $i),
                "codigo_fechamento" => $xlsxHelper->getValorCelulaIndice('I', $i),
                "row_number"        => $xlsxHelper->getValorCelulaIndice('J', $i),
                "codigo_professor"  => $xlsxHelper->getValorCelulaIndice('K', $i),
                "usuario"           => $xlsxHelper->getValorCelulaIndice('L', $i),
                "origem"            => $xlsxHelper->getValorCelulaIndice('M', $i),
                "franqueada_id"     => $franqueada_id,
                "empresa"           => $empresaORM,
            ];
            $importacaoItemORM = GeneralORMFactory::criar(Caixa::class, true, $parametros);
            $entityManager->persist($importacaoItemORM);
        }//end for

        $entityManager->flush();
    }


}
