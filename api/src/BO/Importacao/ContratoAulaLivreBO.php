<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\ContratoAulaLivre;

/**
 *
 * @author Luiz A. Costa
 */
class ContratoAulaLivreBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\ContratoAulaLivreRepository $repository
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
     * @param \App\Repository\Importacao\ContratoRepository $contratoRepository
     * @param \App\Repository\Importacao\FuncionarioRepository $funcionarioRepository
     * @param \App\Repository\Importacao\AlunoRepository $alunoRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $contratoRepository, $funcionarioRepository, $alunoRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $numeroContrato  = $xlsxHelper->getValorCelulaIndice('A', $i);
            $alunoNome       = $xlsxHelper->getValorCelulaIndice('B', $i);
            $funcionarioNome = $xlsxHelper->getValorCelulaIndice('C', $i);

            $alunoORM       = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);
            $funcionarioORM = self::retornaFuncionarioOrNull($funcionarioRepository, $funcionarioNome, $franqueada_id);
            $contratoORM    = self::retornaContratoOrNull($contratoRepository, $numeroContrato, $franqueada_id);

            $parametros        = [
                "franqueada_id"    => $franqueada_id,
                "contrato"         => $contratoORM,
                "funcionario"      => $funcionarioORM,
                "aluno"            => $alunoORM,
                "numero_contrato"  => $numeroContrato,
                "aluno_nome"       => $alunoNome,
                "funcionario_nome" => $funcionarioNome,
                "usuario"          => $xlsxHelper->getValorCelulaIndice('E', $i),
                "descricao"        => $xlsxHelper->getValorCelulaIndice('D', $i),
                "data_inicio"      => $xlsxHelper->getValorCelulaIndice('F', $i),
                "data_termino"     => $xlsxHelper->getValorCelulaIndice('G', $i),
                "data_contrato"    => $xlsxHelper->getValorCelulaIndice('H', $i),
                "observacao"       => $xlsxHelper->getValorCelulaIndice('I', $i),
            ];
            $importacaoItemORM = GeneralORMFactory::criar(ContratoAulaLivre::class, true, $parametros);
            $entityManager->persist($importacaoItemORM);
        }//end for

        $entityManager->flush();
    }


}
