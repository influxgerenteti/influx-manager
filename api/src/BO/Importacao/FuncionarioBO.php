<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\Funcionario;

/**
 *
 * @author Luiz A. Costa
 */
class FuncionarioBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\FuncionarioRepository $repository
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
            $professor = self::retornaSimNaoDB($xlsxHelper->getValorCelulaIndice('V', $i));
            $situacao  = self::retornaAtivoInativoDB($xlsxHelper->getValorCelulaIndice('W', $i));
            $atendente = self::retornaSimNaoDB($xlsxHelper->getValorCelulaIndice('AE', $i));

            $parametros = [
                'codigo'                  => $xlsxHelper->getValorCelulaIndice('A', $i),
                'nome_abreviado'          => $xlsxHelper->getValorCelulaIndice('B', $i),
                'nome'                    => $xlsxHelper->getValorCelulaIndice('C', $i),
                'estado_civil'            => $xlsxHelper->getValorCelulaIndice('D', $i),
                'bairro'                  => $xlsxHelper->getValorCelulaIndice('E', $i),
                'cidade'                  => $xlsxHelper->getValorCelulaIndice('F', $i),
                'usuario'                 => $xlsxHelper->getValorCelulaIndice('G', $i),
                'sexo'                    => $xlsxHelper->getValorCelulaIndice('I', $i),
                'data_nascimento'         => $xlsxHelper->getValorCelulaIndice('J', $i),
                'cpf'                     => $xlsxHelper->getValorCelulaIndice('K', $i),
                'rg'                      => $xlsxHelper->getValorCelulaIndice('L', $i),
                'carteira_profissional'   => $xlsxHelper->getValorCelulaIndice('M', $i),
                'endereco'                => $xlsxHelper->getValorCelulaIndice('N', $i),
                'complemento'             => $xlsxHelper->getValorCelulaIndice('O', $i),
                'cep'                     => $xlsxHelper->getValorCelulaIndice('P', $i),
                'data_cadastro'           => $xlsxHelper->getValorCelulaIndice('Q', $i),
                'telefone'                => $xlsxHelper->getValorCelulaIndice('R', $i),
                'celular'                 => $xlsxHelper->getValorCelulaIndice('S', $i),
                'salario'                 => $xlsxHelper->getValorCelulaIndice('T', $i),
                'observacao'              => $xlsxHelper->getValorCelulaIndice('U', $i),
                'professor'               => $professor,
                'valor_hora_aula'         => $xlsxHelper->getValorCelulaIndice('X', $i),
                'valor_hora_aula_externa' => $xlsxHelper->getValorCelulaIndice('Y', $i),
                'inss'                    => $xlsxHelper->getValorCelulaIndice('Z', $i),
                'email'                   => $xlsxHelper->getValorCelulaIndice('AA', $i),
                'conta'                   => $xlsxHelper->getValorCelulaIndice('AB', $i),
                'agencia'                 => $xlsxHelper->getValorCelulaIndice('AC', $i),
                'nome_banco'              => $xlsxHelper->getValorCelulaIndice('AD', $i),
                'atendente'               => $atendente,
                'data_admissao'           => $xlsxHelper->getValorCelulaIndice('AF', $i),
                'situacao'                => $situacao,
                'franqueada_id'           => $franqueada_id,
            ];
            $importacaoFuncionarioORM = GeneralORMFactory::criar(Funcionario::class, true, $parametros);
            $entityManager->persist($importacaoFuncionarioORM);
        }//end for

        $entityManager->flush();
    }


}
