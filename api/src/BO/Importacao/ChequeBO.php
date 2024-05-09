<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\Cheque;

/**
 *
 * @author Luiz A. Costa
 */
class ChequeBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\ChequeRepository $repository
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
     * @param \App\Repository\Importacao\AlunoRepository $alunoRepository
     * @param \App\Repository\Importacao\FuncionarioRepository $funcionarioRepository
     * @param \App\Repository\Importacao\EmpresaRepository $empresaRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $alunoRepository, $funcionarioRepository, $empresaRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $alunoNome       = $xlsxHelper->getValorCelulaIndice('R', $i);
            $empresaNome     = $xlsxHelper->getValorCelulaIndice('S', $i);
            $funcionarioNome = $xlsxHelper->getValorCelulaIndice('T', $i);
            $situacao        = self::retornaSituacaoBancaria(trim($xlsxHelper->getValorCelulaIndice('M', $i)));
            $tipo            = self::retornaTipoBancario(trim($xlsxHelper->getValorCelulaIndice('P', $i)));

            $alunoORM       = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);
            $empresaORM     = self::retornaEmpresaOrNull($empresaRepository, $empresaNome, $franqueada_id);
            $funcionarioORM = self::retornaFuncionarioOrNull($funcionarioRepository, $funcionarioNome, $franqueada_id);

            $parametros        = [
                "codigo"           => $xlsxHelper->getValorCelulaIndice('A', $i),
                "conta"            => $xlsxHelper->getValorCelulaIndice('B', $i),
                "titular"          => $xlsxHelper->getValorCelulaIndice('C', $i),
                "numero"           => $xlsxHelper->getValorCelulaIndice('D', $i),
                "codigo_banco"     => $xlsxHelper->getValorCelulaIndice('E', $i),
                "agencia"          => $xlsxHelper->getValorCelulaIndice('F', $i),
                "data_compensacao" => $xlsxHelper->getValorCelulaIndice('H', $i),
                "data_entrada"     => $xlsxHelper->getValorCelulaIndice('I', $i),
                "data_baixa"       => $xlsxHelper->getValorCelulaIndice('J', $i),
                "data_devolucao"   => $xlsxHelper->getValorCelulaIndice('K', $i),
                "valor"            => $xlsxHelper->getValorCelulaIndice('L', $i),
                "situacao"         => $situacao,
                "complemento"      => $xlsxHelper->getValorCelulaIndice('N', $i),
                "observacao"       => $xlsxHelper->getValorCelulaIndice('O', $i),
                "tipo"             => $tipo,
                "motivo_devolucao" => $xlsxHelper->getValorCelulaIndice('Q', $i),
                "aluno_nome"       => $alunoNome,
                "empresa_nome"     => $empresaNome,
                "funcionario_nome" => $funcionarioNome,
                "aluno"            => $alunoORM,
                "empresa"          => $empresaORM,
                "funcionario"      => $funcionarioORM,
                "franqueada_id"    => $franqueada_id,
            ];
            $importacaoItemORM = GeneralORMFactory::criar(Cheque::class, true, $parametros);
            $entityManager->persist($importacaoItemORM);
        }//end for

        $entityManager->flush();
    }


}
