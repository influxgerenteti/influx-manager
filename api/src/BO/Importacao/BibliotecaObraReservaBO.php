<?php
namespace App\BO\Importacao;

use App\Factory\GeneralORMFactory;
use App\Entity\Importacao\BibliotecaObraReserva;

/**
 *
 * @author Luiz A. Costa
 */
class BibliotecaObraReservaBO extends GenericImportacaoBO
{


    /**
     * Excluir Registros por Franqueada
     *
     * @param \App\Repository\Importacao\BibliotecaObraReservaRepository $repository
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
     * @param \App\Repository\Importacao\BibliotecaObraRepository $bibliotecaObraRepository
     */
    public static function criarRegistrosPorFranqueada(&$xlsxHelper, $entityManager, $franqueada_id, $alunoRepository, $funcionarioRepository, $bibliotecaObraRepository)
    {
        $contador = $xlsxHelper->getQuantidadeMaxLinhasColuna();
        for ($i = 2; $i <= $contador; $i++) {
            $alunoNome          = $xlsxHelper->getValorCelulaIndice('B', $i);
            $funcionarioNome    = $xlsxHelper->getValorCelulaIndice('C', $i);
            $bibliotecaObraNome = $xlsxHelper->getValorCelulaIndice('D', $i);
            $alunoORM           = self::retornaAlunoOrNull($alunoRepository, $alunoNome, $franqueada_id);
            $funcionarioORM     = self::retornaFuncionarioOrNull($funcionarioRepository, $funcionarioNome, $franqueada_id);
            $bibliotecaObraORM  = self::retornaBibliotecaObraOrNull($bibliotecaObraRepository, $bibliotecaObraNome, $franqueada_id);

            $parametros = [
                'codigo'               => $xlsxHelper->getValorCelulaIndice('A', $i),
                'aluno_nome'           => $alunoNome,
                'funcionario_nome'     => $funcionarioNome,
                'biblioteca_obra_nome' => $bibliotecaObraNome,
                'numero_exemplares'    => $xlsxHelper->getValorCelulaIndice('E', $i),
                'nome'                 => $xlsxHelper->getValorCelulaIndice('F', $i),
                'data_reserva'         => $xlsxHelper->getValorCelulaIndice('G', $i),
                'aluno'                => $alunoORM,
                'funcionario'          => $funcionarioORM,
                'biblioteca_obra'      => $bibliotecaObraORM,
                'franqueada_id'        => $franqueada_id,
            ];
            $importacaoBibliotecaObraReservaORM = GeneralORMFactory::criar(BibliotecaObraReserva::class, true, $parametros);
            $entityManager->persist($importacaoBibliotecaObraReservaORM);
        }//end for

        $entityManager->flush();
    }


}
