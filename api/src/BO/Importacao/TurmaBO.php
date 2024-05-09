<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\Turma;

/**
 *
 * @author Luiz A. Costa
 */
class TurmaBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\TurmaRepository $repository
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
     * @param \App\Repository\Importacao\SalaRepository $salaRepository
     * @param \App\Repository\Importacao\CursoRepository $cursoRepository
     * @param \App\Repository\Importacao\EstagioRepository $estagioRepository
     * @param \App\Repository\Importacao\FuncionarioRepository $funcionarioRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $salaRepository, $cursoRepository, $estagioRepository, $funcionarioRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $salaNome        = $xlsxHelper->getValorCelulaIndice('E', $i);
            $cursoNome       = $xlsxHelper->getValorCelulaIndice('J', $i);
            $estagioNome     = $xlsxHelper->getValorCelulaIndice('H', $i);
            $funcionarioNome = $xlsxHelper->getValorCelulaIndice('I', $i);
            $salaORM         = self::retornaSalaOrNull($salaRepository, $salaNome, $franqueada_id);
            $cursoORM        = self::retornaCursoOrNull($cursoRepository, $cursoNome, $franqueada_id);
            $estagioORM      = self::retornaEstagioOrNull($estagioRepository, $estagioNome, $franqueada_id);
            $funcionarioORM  = self::retornaFuncionarioOrNull($funcionarioRepository, $funcionarioNome, $franqueada_id);

            $parametros         = [
                'codigo'           => $xlsxHelper->getValorCelulaIndice('A', $i),
                'nome'             => $xlsxHelper->getValorCelulaIndice('B', $i),
                'data_inicio'      => $xlsxHelper->getValorCelulaIndice('C', $i),
                'data_termino'     => $xlsxHelper->getValorCelulaIndice('D', $i),
                'sala_nome'        => $salaNome,
                'horario'          => $xlsxHelper->getValorCelulaIndice('F', $i),
                'descricao'        => $xlsxHelper->getValorCelulaIndice('G', $i),
                'estagio_nome'     => $estagioNome,
                'funcionario_nome' => $funcionarioNome,
                'curso_nome'       => $cursoNome,
                'numero_alunos'    => $xlsxHelper->getValorCelulaIndice('K', $i),
                'duracao_aula'     => $xlsxHelper->getValorCelulaIndice('L', $i),
                'local_aula'       => $xlsxHelper->getValorCelulaIndice('M', $i),
                'observacao'       => $xlsxHelper->getValorCelulaIndice('N', $i),
                'ultima_licao'     => $xlsxHelper->getValorCelulaIndice('O', $i),
                'valor_turma'      => $xlsxHelper->getValorCelulaIndice('P', $i),
                'franqueada_id'    => $franqueada_id,
                'sala'             => $salaORM,
                'curso'            => $cursoORM,
                'estagio'          => $estagioORM,
                'funcionario'      => $funcionarioORM,
            ];
            $importacaoTurmaORM = GeneralORMFactory::criar(Turma::class, true, $parametros);
            $entityManager->persist($importacaoTurmaORM);
        }//end for

        $entityManager->flush();
    }


}
