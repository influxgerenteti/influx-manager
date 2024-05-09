<?php
namespace App\BO\Importacao;

use App\Entity\Importacao\AulaLivre;
use App\Factory\GeneralORMFactory;

/**
 *
 * @author Luiz A. Costa
 */
class AulaLivreBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\AulaLivreRepository $repository
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
     * @param \App\Repository\Importacao\EstagioRepository $estagioRepository
     * @param \App\Repository\Importacao\FuncionarioRepository $funcionarioRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $alunoRepository, $estagioRepository, $funcionarioRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $alunoNome       = $xlsxHelper->getValorCelulaIndice('A', $i);
            $estagioNome     = $xlsxHelper->getValorCelulaIndice('C', $i);
            $funcionarioNome = $xlsxHelper->getValorCelulaIndice('D', $i);
            $alunoORM        = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);
            $estagioORM      = self::retornaEstagioOrNull($estagioRepository, $estagioNome, $franqueada_id);
            $funcionarioORM  = self::retornaFuncionarioOrNull($funcionarioRepository, $funcionarioNome, $franqueada_id);
            $situacao        = self::retornaSituacaoAulaDB($xlsxHelper->getValorCelulaIndice('H', $i));
            $pago            = self::retornaSimNaoDB($xlsxHelper->getValorCelulaIndice('J', $i));
            $tipo_aula       = self::retornaTipoAula($xlsxHelper->getValorCelulaIndice('K', $i));
            $parametros      = [
                'aluno_nome'       => $alunoNome,
                'descricao'        => $xlsxHelper->getValorCelulaIndice('B', $i),
                'estagio_nome'     => $estagioNome,
                'funcionario_nome' => $funcionarioNome,
                'data'             => $xlsxHelper->getValorCelulaIndice('E', $i),
                'horario_inicial'  => $xlsxHelper->getValorCelulaIndice('F', $i),
                'horario_final'    => $xlsxHelper->getValorCelulaIndice('G', $i),
                'situacao'         => $situacao,
                'licao'            => $xlsxHelper->getValorCelulaIndice('I', $i),
                'pago'             => $pago,
                'tipo_aula'        => $tipo_aula,
                'aluno'            => $alunoORM,
                'estagio'          => $estagioORM,
                'funcionario'      => $funcionarioORM,
                'franqueada_id'    => $franqueada_id,
            ];
            $importacaoAulaLivreORM = GeneralORMFactory::criar(AulaLivre::class, true, $parametros);
            $entityManager->persist($importacaoAulaLivreORM);
        }//end for

        $entityManager->flush();
    }


}
