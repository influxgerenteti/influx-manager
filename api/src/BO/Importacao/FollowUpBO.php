<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\FollowUp;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class FollowUpBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\FollowUpRepository $repository
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
     * @param \App\Repository\Importacao\EmpresaRepository $empresaRepository
     * @param \App\Repository\Importacao\FuncionarioRepository $funcionarioRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $alunoRepository, $empresaRepository, $funcionarioRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $situacao        = self::retornaSituacaoDB($xlsxHelper->getValorCelulaIndice('K', $i));
            $alunoNome       = $xlsxHelper->getValorCelulaIndice('F', $i);
            $empresaNome     = $xlsxHelper->getValorCelulaIndice('E', $i);
            $funcionarioNome = $xlsxHelper->getValorCelulaIndice('G', $i);
            $atendenteNome   = $xlsxHelper->getValorCelulaIndice('H', $i);
            $alunoORM        = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);
            $empresaORM      = self::retornaEmpresaOrNull($empresaRepository, $empresaNome, $franqueada_id);
            $funcionarioORM  = self::retornaFuncionarioOrNull($funcionarioRepository, $funcionarioNome, $franqueada_id);
            $atendenteORM    = self::retornaFuncionarioOrNull($funcionarioRepository, $atendenteNome, $franqueada_id);
            $parametros      = [
                'codigo'               => $xlsxHelper->getValorCelulaIndice('A', $i),
                'descricao'            => $xlsxHelper->getValorCelulaIndice('B', $i),
                'grau_interesse'       => $xlsxHelper->getValorCelulaIndice('D', $i),
                'empresa_nome'         => $empresaNome,
                'aluno_nome'           => $alunoNome,
                'funcionario_nome'     => $funcionarioNome,
                'atendente_nome'       => $atendenteNome,
                'data'                 => $xlsxHelper->getValorCelulaIndice('I', $i),
                'assunto'              => $xlsxHelper->getValorCelulaIndice('J', $i),
                'situacao'             => $situacao,
                'data_proximo_contato' => $xlsxHelper->getValorCelulaIndice('L', $i),
                'aluno'                => $alunoORM,
                'empresa'              => $empresaORM,
                'funcionario'          => $funcionarioORM,
                'atendente'            => $atendenteORM,
                'franqueada_id'        => $franqueada_id,
            ];
            $importacaoFollowUpORM = GeneralORMFactory::criar(FollowUp::class, true, $parametros);
            $entityManager->persist($importacaoFollowUpORM);
        }//end for

        $entityManager->flush();
    }


}
