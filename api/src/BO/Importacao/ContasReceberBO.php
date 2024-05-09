<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\ContasReceber;

/**
 *
 * @author Luiz A. Costa
 */
class ContasReceberBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\ContasReceberRepository $repository
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
     * @param \App\Repository\Importacao\AlunoRepository $alunoRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $empresaRepository, $alunoRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $alunoNome   = $xlsxHelper->getValorCelulaIndice('A', $i);
            $empresaNome = $xlsxHelper->getValorCelulaIndice('B', $i);

            $tipo     = self::retornaTipoContasPagarReceber(trim($xlsxHelper->getValorCelulaIndice('D', $i)));
            $situacao = self::retornaSituacaoContasPagarReceber(trim($xlsxHelper->getValorCelulaIndice('K', $i)));

            $empresaORM    = self::retornaEmpresaOrNull($empresaRepository, $empresaNome, $franqueada_id);
            $alunoORM      = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);
            $valor         = $xlsxHelper->getValorCelulaIndice('G', $i);
            $valorPago     = $xlsxHelper->getValorCelulaIndice('H', $i);
            $valorDesconto = $xlsxHelper->getValorCelulaIndice('M', $i);
            $valorJuro     = $xlsxHelper->getValorCelulaIndice('N', $i);
            \App\Helper\FunctionHelper::formataValorDouble($valor);
            \App\Helper\FunctionHelper::formataValorDouble($valorPago);
            \App\Helper\FunctionHelper::formataValorDouble($valorDesconto);
            \App\Helper\FunctionHelper::formataValorDouble($valorJuro);
            $parametros        = [
                "aluno_nome"       => $alunoNome,
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
                "numero_boleto"    => $xlsxHelper->getValorCelulaIndice('L', $i),
                "valor_desconto"   => $valorDesconto,
                "valor_juro"       => $valorJuro,
                "numero_recibo"    => $xlsxHelper->getValorCelulaIndice('O', $i),
                "numero_carne"     => $xlsxHelper->getValorCelulaIndice('P', $i),
                "ocorrencia"       => $xlsxHelper->getValorCelulaIndice('Q', $i),
                "numero_cheque"    => $xlsxHelper->getValorCelulaIndice('R', $i),
                "titular_cheque"   => $xlsxHelper->getValorCelulaIndice('S', $i),
                "banco_cheque"     => $xlsxHelper->getValorCelulaIndice('T', $i),
                "agencia_cheque"   => $xlsxHelper->getValorCelulaIndice('U', $i),
                "conta_cheque"     => $xlsxHelper->getValorCelulaIndice('V', $i),
                "total_plano"      => $xlsxHelper->getValorCelulaIndice('W', $i),
                "observacao"       => $xlsxHelper->getValorCelulaIndice('X', $i),
                "aluno"            => $alunoORM,
                "empresa"          => $empresaORM,
                "franqueada_id"    => $franqueada_id,
            ];
            $importacaoItemORM = GeneralORMFactory::criar(ContasReceber::class, true, $parametros);
            $entityManager->persist($importacaoItemORM);
        }//end for

        $entityManager->flush();
    }


}
