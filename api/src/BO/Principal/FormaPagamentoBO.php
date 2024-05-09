<?php
namespace App\BO\Principal;

use App\Entity\Principal\FormaPagamento;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Marcelo André Naegeler
 */
class FormaPagamentoBO extends GenericBO
{


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->configuraGenericBO(
            [
                "formaPagamentoRepository" => $entityManager->getRepository(\App\Entity\Principal\FormaPagamento::class),
            ]
        );
    }

    /**
     * Verifica se existe a forma de pagamento no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\FormaPagamentoRepository $formaPagamentoRepository Repositorio do FormaPagamenot
     * @param integer $id Chave primaria da categoria
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\FormaPagamento|null $formaPagamentoORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaFormaPagamentoExiste(\App\Repository\Principal\FormaPagamentoRepository $formaPagamentoRepository, $id, &$mensagemErro, &$formaPagamentoORM)
    {
        $formaPagamentoORM = $formaPagamentoRepository->find($id);

        if ($formaPagamentoORM === null) {
            $mensagemErro = "FormaPagamenot não encontrado na base de dados.";
            return false;
        }

        return true;
    }


}
