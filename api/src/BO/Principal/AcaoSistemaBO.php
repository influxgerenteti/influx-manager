<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class AcaoSistemaBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\AcaoSistemaRepository
     */
    private $acaoSistemaRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->acaoSistemaRepository = $entityManager->getRepository(\App\Entity\Principal\AcaoSistema::class);
        parent::configuraGenericBO(
            [
                "moduloRepository" => $entityManager->getRepository(\App\Entity\Principal\Modulo::class),
            ]
        );
    }

    /**
     * Verifica a existencia dos modulos para poder ser adicionado na AcaoSistem
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaParametrosRelacionaisOpcionais(&$parametros, &$mensagemErro)
    {
        $bRetorno   = true;
        $modulosORM = [];
        if ((isset($parametros[ConstanteParametros::CHAVE_MODULOS]) === true) && (count($parametros[ConstanteParametros::CHAVE_MODULOS]) > 0)) {
            foreach ($parametros[ConstanteParametros::CHAVE_MODULOS] as $moduloId) {
                $moduloORM = null;
                if (self::verificaModuloExisteBO([ConstanteParametros::CHAVE_MODULO => $moduloId], $mensagemErro, $moduloORM) === false) {
                    $mensagemErro = "Modulo com a ID: " . $moduloId . ", não encontrado!\n" . $mensagemErro;
                    $bRetorno     = false;
                    break;
                } else {
                    $modulosORM[] = $moduloORM;
                }
            }

            $parametros[ConstanteParametros::CHAVE_MODULOS] = $modulosORM;
        }

        return $bRetorno;
    }

    /**
     * Verifica se podemos prosseguir com o processo de salvar
     *
     * @param array $parametros Ponteiro de array de parametros para realizar a validacao
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro)
    {
        if ($this->verificaParametrosRelacionaisOpcionais($parametros, $mensagemErro) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se a AcaoSistema existe atraves do campo ID
     *
     * @param \App\Repository\Principal\AcaoSistemaRepository $acaoSistemaRepository Repositorio da AcaoSistema
     * @param int $id Chave primaria a ser consultada
     * @param string $mensagemErro Mensagem de erro a ser retornado ao front-end
     * @param null|\App\Entity\Principal\AcaoSistema $acaoSistemaORM Ponteiro para retornar o objeto
     *
     * @return boolean
     */
    public static function verificaAcaoSistemaExiste(\App\Repository\Principal\AcaoSistemaRepository $acaoSistemaRepository, $id, &$mensagemErro, &$acaoSistemaORM=null)
    {
        $acaoSistemaORM = $acaoSistemaRepository->find($id);
        if (is_null($acaoSistemaORM) === true) {
            $mensagemErro = "AcaoSistema não encontrada na base de dados.";
            return false;
        }

        return true;
    }


}
