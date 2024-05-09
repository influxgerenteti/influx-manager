<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class ModeloTemplateBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
            ]
        );
    }

    /**
     * Verifica os campos de relacionamentos que são obrigatorios
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica as regras para verificar se pode salvar ou nao
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se existe a ModeloTemplate no banco de dados, se existir, ele retorna no ultimo parametro na funcao, caso contrario, ele preenchera a msg de erro e retornara falso
     *
     * @param \App\Repository\Principal\ModeloTemplateRepository $modeloTemplateRepository Repositorio de ModeloTemplate
     * @param integer $id Chave primaria da modeloTemplate
     * @param string $mensagemErro Msg de erro para ser retornada ao front-end
     * @param \App\Entity\Principal\ModeloTemplate|null $modeloTemplateORM Retorno no caso de sucesso
     *
     * @return boolean true|false
     */
    public static function verificaModeloTemplateExiste(\App\Repository\Principal\ModeloTemplateRepository $modeloTemplateRepository, $id, &$mensagemErro, &$modeloTemplateORM)
    {
        $modeloTemplateORM = $modeloTemplateRepository->find($id);
        if (is_null($modeloTemplateORM) === true) {
            $mensagemErro = "ModeloTemplate não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
