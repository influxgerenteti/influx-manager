<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class FuncionarioValorHoraBO extends GenericBO
{
    /**
     *
     * @var \App\Repository\Principal\FuncionarioValorHoraRepository
     */
    private $funcionarioValorHoraRepository;

    function __construct(EntityManagerInterface $entityManager)
    {
        $this->funcionarioValorHoraRepository = $entityManager->getRepository(\App\Entity\Principal\SalaFranqueada::class);
        parent::configuraGenericBO(
            [
                "funcionarioRepository" => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
                "valorHoraRepository"   => $entityManager->getRepository(\App\Entity\Principal\ValorHora::class),
            ]
        );
    }

    /**
     * Verifica campos relacionados a entidade
     *
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro)
    {
        if (self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_FUNCIONARIO]) === true) {
            if (self::verificaValorHoraExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_VALOR_HORA]) === true) {
                return true;
            }
        }

        return false;
    }


    /**
     * Buscao objeto através da id informada
     *
     * @param \App\Repository\Principal\FuncionarioValorHoraRepository $funcionarioValorHoraRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\FuncionarioValorHora $funcionarioValorHoraORM
     *
     * @return boolean
     */
    public static function verificaFuncionarioValorHoraExiste(\App\Repository\Principal\FuncionarioValorHoraRepository $funcionarioValorHoraRepository, $id, &$mensagemErro, &$funcionarioValorHoraORM)
    {
        $funcionarioValorHoraORM = $funcionarioValorHoraRepository->find($id);
        if (is_null($funcionarioValorHoraORM) === true) {
            $mensagemErro = "Funcionario Valor Hora não encontrado.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se os campos relacionais estao validos e indica se pode salvar ou não
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
     * Configura os parametros para a parte de atualização
     *
     * @param array $parametros
     * @param \App\Entity\Principal\FuncionarioValorHora $objetoORM
     */
    public function configuraParametros($parametros, &$objetoORM)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_VALOR]) === true) && (empty($parametros[ConstanteParametros::CHAVE_VALOR]) === false)) {
            $objetoORM->setValor($parametros[ConstanteParametros::CHAVE_VALOR]);
        }
    }


}
