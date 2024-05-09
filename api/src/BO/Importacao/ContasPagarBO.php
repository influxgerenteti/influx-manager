<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\ContasPagar;

/**
 *
 * @author Luiz A. Costa
 */
class ContasPagarBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\ContasPagarRepository $repository
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
     * @param \App\Repository\Importacao\FuncionarioRepository $funcionarioRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $empresaRepository, $funcionarioRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $funcionarioNome = $xlsxHelper->getValorCelulaIndice('A', $i);
            $empresaNome     = $xlsxHelper->getValorCelulaIndice('B', $i);

            $tipo     = self::retornaTipoContasPagarReceber(trim($xlsxHelper->getValorCelulaIndice('D', $i)));
            $situacao = self::retornaSituacaoContasPagarReceber(trim($xlsxHelper->getValorCelulaIndice('K', $i)));

            $funcionarioORM = self::retornaFuncionarioOrNull($funcionarioRepository, $funcionarioNome, $franqueada_id);
            $empresaORM     = self::retornaEmpresaOrNull($empresaRepository, $empresaNome, $franqueada_id);
            $valor          = $xlsxHelper->getValorCelulaIndice('G', $i);
            $valorPago      = $xlsxHelper->getValorCelulaIndice('H', $i);
            $valorDesconto  = $xlsxHelper->getValorCelulaIndice('L', $i);
            $valorJuro      = $xlsxHelper->getValorCelulaIndice('M', $i);
            \App\Helper\FunctionHelper::formataValorDouble($valor);
            \App\Helper\FunctionHelper::formataValorDouble($valorPago);
            \App\Helper\FunctionHelper::formataValorDouble($valorDesconto);
            \App\Helper\FunctionHelper::formataValorDouble($valorJuro);
            $parametros        = [
                "funcionario_nome" => $funcionarioNome,
                "empresa_nome"     => $empresaNome,
                "numero_parcela"   => $xlsxHelper->getValorCelulaIndice('C', $i),
                "tipo_recebimento" => $tipo,
                "descricao"        => $xlsxHelper->getValorCelulaIndice('E', $i),
                "titular"          => $xlsxHelper->getValorCelulaIndice('F', $i),
                "valor"            => $valor,
                "valor_pago"       => $valorPago,
                "data_vencimento"  => $xlsxHelper->getValorCelulaIndice('I', $i),
                "data_pagamento"   => $xlsxHelper->getValorCelulaIndice('J', $i),
                "situacao"         => $situacao,
                "valor_desconto"   => $valorDesconto,
                "valor_juro"       => $valorJuro,
                "numero_cheque"    => $xlsxHelper->getValorCelulaIndice('N', $i),
                "titular_cheque"   => $xlsxHelper->getValorCelulaIndice('O', $i),
                "banco_cheque"     => $xlsxHelper->getValorCelulaIndice('P', $i),
                "agencia_cheque"   => $xlsxHelper->getValorCelulaIndice('Q', $i),
                "conta_cheque"     => $xlsxHelper->getValorCelulaIndice('R', $i),
                "total_plano"      => $xlsxHelper->getValorCelulaIndice('S', $i),
                "observacao"       => $xlsxHelper->getValorCelulaIndice('T', $i),
                "franqueada_id"    => $franqueada_id,
                "funcionario"      => $funcionarioORM,
                "empresa"          => $empresaORM,
            ];
            $importacaoItemORM = GeneralORMFactory::criar(ContasPagar::class, true, $parametros);
            $entityManager->persist($importacaoItemORM);
        }//end for

        $entityManager->flush();
    }


}
