<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\Empresa;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class EmpresaBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\EmpresaRepository $repository
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
            $situacao     = self::retornaAtivoInativoDB($xlsxHelper->getValorCelulaIndice('N', $i));
            $cliente      = self::retornaSimNaoDB($xlsxHelper->getValorCelulaIndice('W', $i));
            $fornecedor   = self::retornaSimNaoDB($xlsxHelper->getValorCelulaIndice('X', $i));
            $alunoEscola  = self::retornaSimNaoDB($xlsxHelper->getValorCelulaIndice('Y', $i));
            $alunoEmpresa = self::retornaSimNaoDB($xlsxHelper->getValorCelulaIndice('Z', $i));
            $parametros   = [
                'codigo'               => $xlsxHelper->getValorCelulaIndice('A', $i),
                'nome'                 => $xlsxHelper->getValorCelulaIndice('B', $i),
                'razao_social'         => $xlsxHelper->getValorCelulaIndice('C', $i),
                'endereco'             => $xlsxHelper->getValorCelulaIndice('D', $i),
                'bairro'               => $xlsxHelper->getValorCelulaIndice('E', $i),
                'cidade'               => $xlsxHelper->getValorCelulaIndice('F', $i),
                'cep'                  => $xlsxHelper->getValorCelulaIndice('G', $i),
                'cnpj'                 => $xlsxHelper->getValorCelulaIndice('H', $i),
                'cpf'                  => $xlsxHelper->getValorCelulaIndice('I', $i),
                'complemento'          => $xlsxHelper->getValorCelulaIndice('J', $i),
                'inscricao'            => $xlsxHelper->getValorCelulaIndice('K', $i),
                'email'                => $xlsxHelper->getValorCelulaIndice('L', $i),
                'data_cadastro'        => $xlsxHelper->getValorCelulaIndice('M', $i),
                'situacao'             => $situacao,
                'telefone'             => $xlsxHelper->getValorCelulaIndice('O', $i),
                'ramal'                => $xlsxHelper->getValorCelulaIndice('P', $i),
                'fax'                  => $xlsxHelper->getValorCelulaIndice('Q', $i),
                'celular'              => $xlsxHelper->getValorCelulaIndice('R', $i),
                'rg'                   => $xlsxHelper->getValorCelulaIndice('S', $i),
                'agencia'              => $xlsxHelper->getValorCelulaIndice('T', $i),
                'conta'                => $xlsxHelper->getValorCelulaIndice('U', $i),
                'codigo_cliente_banco' => $xlsxHelper->getValorCelulaIndice('V', $i),
                'cliente'              => $cliente,
                'fornecedor'           => $fornecedor,
                'aluno_escola'         => $alunoEscola,
                'aluno_empresa'        => $alunoEmpresa,
                'observacao'           => $xlsxHelper->getValorCelulaIndice('AA', $i),
                'franqueada_id'        => $franqueada_id,
            ];
            $importacaoEmpresaORM = GeneralORMFactory::criar(Empresa::class, true, $parametros);
            $entityManager->persist($importacaoEmpresaORM);
        }//end for

        $entityManager->flush();
    }


}
