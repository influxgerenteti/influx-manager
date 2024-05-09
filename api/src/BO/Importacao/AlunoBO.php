<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\Aluno;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class AlunoBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\AlunoRepository $repository
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
            $coluna = $xlsxHelper->getCelula("Y", $i);
            if (is_null($coluna) === false) {
                $situacao = self::retornaSituacaoDB($coluna->getValue());

                $parametros         = [
                    'codigo'             => $xlsxHelper->getValorCelulaIndice('A', $i),
                    'aluno'              => $xlsxHelper->getValorCelulaIndice('B', $i),
                    'sexo'               => $xlsxHelper->getValorCelulaIndice('C', $i),
                    'numero_matricula'   => $xlsxHelper->getValorCelulaIndice('D', $i),
                    'data_nascimento'    => $xlsxHelper->getValorCelulaIndice('E', $i),
                    'cidade'             => $xlsxHelper->getValorCelulaIndice('F', $i),
                    'bairro'             => $xlsxHelper->getValorCelulaIndice('G', $i),
                    'cpf'                => $xlsxHelper->getValorCelulaIndice('H', $i),
                    'rg'                 => $xlsxHelper->getValorCelulaIndice('I', $i),
                    'endereco'           => $xlsxHelper->getValorCelulaIndice('J', $i),
                    'cep'                => $xlsxHelper->getValorCelulaIndice('K', $i),
                    'complemento'        => $xlsxHelper->getValorCelulaIndice('L', $i),
                    'telefone'           => $xlsxHelper->getValorCelulaIndice('M', $i),
                    'celular'            => $xlsxHelper->getValorCelulaIndice('N', $i),
                    'ramal'              => $xlsxHelper->getValorCelulaIndice('O', $i),
                    'profissao'          => $xlsxHelper->getValorCelulaIndice('P', $i),
                    'email'              => $xlsxHelper->getValorCelulaIndice('Q', $i),
                    'telefone_comercial' => $xlsxHelper->getValorCelulaIndice('R', $i),
                    'horario_contato'    => $xlsxHelper->getValorCelulaIndice('S', $i),
                    'data_cadastro'      => $xlsxHelper->getValorCelulaIndice('T', $i),
                    'conta'              => $xlsxHelper->getValorCelulaIndice('U', $i),
                    'agencia'            => $xlsxHelper->getValorCelulaIndice('V', $i),
                    'codigo_cli_banco'   => $xlsxHelper->getValorCelulaIndice('W', $i),
                    'data_inclusao'      => $xlsxHelper->getValorCelulaIndice('X', $i),
                    'midia'              => $xlsxHelper->getValorCelulaIndice('Z', $i),
                    'tipo_contato'       => $xlsxHelper->getValorCelulaIndice('AA', $i),
                    'atendente'          => $xlsxHelper->getValorCelulaIndice('AB', $i),
                    'estado_civil'       => $xlsxHelper->getValorCelulaIndice('AC', $i),
                    'situacao'           => $situacao,
                    'franqueada_id'      => $franqueada_id,
                ];
                $importacaoAlunoORM = GeneralORMFactory::criar(Aluno::class, true, $parametros);
                $entityManager->persist($importacaoAlunoORM);
            }//end if
        }//end for

        $entityManager->flush();
    }


}
