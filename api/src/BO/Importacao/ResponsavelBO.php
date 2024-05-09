<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\Responsavel;

/**
 *
 * @author Luiz A. Costa
 */
class ResponsavelBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\ResponsavelRepository $repository
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
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $alunoRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $alunoNome = $xlsxHelper->getValorCelulaIndice('D', $i);

            $alunoORM          = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);
            $parametros        = [
                'aluno_nome'            => $alunoNome,
                'tipo_responsavel'      => $xlsxHelper->getValorCelulaIndice('E', $i),
                'cidade'                => $xlsxHelper->getValorCelulaIndice('F', $i),
                'bairro'                => $xlsxHelper->getValorCelulaIndice('G', $i),
                'nome_responsavel'      => $xlsxHelper->getValorCelulaIndice('H', $i),
                'endereco'              => $xlsxHelper->getValorCelulaIndice('I', $i),
                'cep'                   => $xlsxHelper->getValorCelulaIndice('J', $i),
                'telefone'              => $xlsxHelper->getValorCelulaIndice('K', $i),
                'email'                 => $xlsxHelper->getValorCelulaIndice('L', $i),
                'telefone_profissional' => $xlsxHelper->getValorCelulaIndice('M', $i),
                'profissao'             => $xlsxHelper->getValorCelulaIndice('N', $i),
                'observacao'            => $xlsxHelper->getValorCelulaIndice('O', $i),
                'data_nascimento'       => $xlsxHelper->getValorCelulaIndice('P', $i),
                'cpf'                   => $xlsxHelper->getValorCelulaIndice('Q', $i),
                'rg'                    => $xlsxHelper->getValorCelulaIndice('R', $i),
                'complemento'           => $xlsxHelper->getValorCelulaIndice('S', $i),
                'aluno'                 => $alunoORM,
                'franqueada_id'         => $franqueada_id,
            ];
            $importacaoItemORM = GeneralORMFactory::criar(Responsavel::class, true, $parametros);
            $entityManager->persist($importacaoItemORM);
        }//end for

        $entityManager->flush();
    }


}
