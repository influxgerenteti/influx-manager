<?php
namespace App\BO\Principal;

/**
 *
 * @author Luiz Antonio Costa
 */
class ValorHoraLinhasBO
{


    /**
     * Verifica se o valor hora linhas ja existe no banco
     *
     * @param \App\Repository\Principal\ValorHoraLinhasRepository $valorHoraLinhasRepository Repositorio do Valor Hora Linhas
     * @param integer $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ValorHoraLinhas $valorHoraLinhasORM retorno objeto
     *
     * @return boolean
     */
    public static function valorHoraLinhasExisteBanco($valorHoraLinhasRepository, $id, &$mensagemErro, &$valorHoraLinhasORM=null)
    {
        $valorHoraLinhasORM = $valorHoraLinhasRepository->find($id);
        if (is_null($valorHoraLinhasORM) === true) {
            $mensagemErro = 'Tipo de pagamento não encontrado.';
            return false;
        }

        return true;
    }

    /**
     * Verifica se valor por hora existe e está ativo
     *
     * @param \App\Repository\Principal\ValorHoraLinhasRepository $repository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\ValorHoraLinhas $valorHoraLinhas
     *
     * @return boolean
     */
    public static function valorHoraLinhasExisteEAtivo (\App\Repository\Principal\ValorHoraLinhasRepository $repository, $id, &$mensagemErro, &$valorHoraLinhas)
    {
        if (self::valorHoraLinhasExisteBanco($repository, $id, $mensagemErro, $valorHoraLinhas) === false) {
            return false;
        }

        if ($valorHoraLinhas->getSituacao() !== 'A') {
            $mensagemErro = 'O tipo de pagamento selecionado está inativo.';
            return false;
        }

        return true;
    }


}
