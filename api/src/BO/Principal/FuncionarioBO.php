<?php
namespace App\BO\Principal;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\ConstanteParametros;
use DoctrineExtensions\Query\Mysql\Cos;

/**
 *
 * @author Luiz Antonio Costa
 */
class FuncionarioBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "pessoaRepository"         => $entityManager->getRepository(\App\Entity\Principal\Pessoa::class),
                "franqueadaRepository"     => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "cargoRepository"          => $entityManager->getRepository(\App\Entity\Principal\Cargo::class),
                "usuarioRepository"        => $entityManager->getRepository(\App\Entity\Principal\Usuario::class),
                "nivelInstrutorRepository" => $entityManager->getRepository(\App\Entity\Principal\NivelInstrutor::class),
                "bancoRepository"          => $entityManager->getRepository(\App\Entity\Principal\Banco::class),
                "funcionarioRepository"    => $entityManager->getRepository(\App\Entity\Principal\Funcionario::class),
            ]
        );
    }

    /**
     * Verifica campos relacionados a entidade
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Funcionario $objetoORM
     *
     * @return boolean
     */
    protected function verificaCamposRelacionaisObrigatorios(&$parametros, &$mensagemErro, &$objetoORM)
    {
        if (self::verificaPessoaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_PESSOA], ConstanteParametros::CHAVE_PESSOA, true) === true) {
            if (self::verificaFranqueadaExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_FRANQUEADA]) === true) {
                if (self::verificaCargoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CARGO]) === true) {
                    if (self::verificaBancoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_BANCO]) === true) {
                        if ($this->configuraCamposRelacionaisOpcionais($parametros, $mensagemErro, $objetoORM) === true) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Verifica e configura os campos que necessitam de relacionamento que sao opcionais na edicao
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Funcionario $objetoORM
     *
     * @return boolean
     */
    protected function configuraCamposRelacionaisOpcionais(&$parametros, &$mensagemErro, &$objetoORM=null)
    {
        $bRetornoUsuario        = true;
        $bRetornoNivelInstrutor = true;
        $bRetornoGestorComercialFuncionario = true;
        $bRetornoCargo = true;
        $bRetornoBanco = true;

        if ((isset($parametros[ConstanteParametros::CHAVE_CARGO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_CARGO]) === false)) {
            if (self::verificaCargoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_CARGO]) === true) {
                if (is_null($objetoORM) === false) {
                    $objetoORM->setCargo($parametros[ConstanteParametros::CHAVE_CARGO]);
                }
            } else {
                $bRetornoCargo = false;
            }

            unset($parametros[ConstanteParametros::CHAVE_CARGO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_BANCO]) === true)&&(empty($parametros[ConstanteParametros::CHAVE_BANCO]) === false)) {
            if (self::verificaBancoExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_BANCO]) === true) {
                if (is_null($objetoORM) === false) {
                    $objetoORM->setBanco($parametros[ConstanteParametros::CHAVE_BANCO]);
                }
            } else {
                $bRetornoBanco = false;
            }

            unset($parametros[ConstanteParametros::CHAVE_BANCO]);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_USUARIO]) === true) {
            if (empty($parametros[ConstanteParametros::CHAVE_USUARIO]) === false) {
                $bRetornoUsuario = self::verificaUsuarioExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_USUARIO]);
                if ($bRetornoUsuario === true) {
                    if (is_null($objetoORM) === false) {
                        $usuario = $parametros[ConstanteParametros::CHAVE_USUARIO];
                    }
                }
            }

            if (isset($usuario) === false) {
                $usuario = null;
            }

            unset($parametros[ConstanteParametros::CHAVE_USUARIO]);
            $objetoORM->setUsuario($usuario);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]) === true) {
            if (empty($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]) === false) {
                $bRetornoNivelInstrutor = self::verificaNivelInstrutorExisteBO($parametros, $mensagemErro, $parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]);
                if ($bRetornoNivelInstrutor === true) {
                    if (is_null($objetoORM) === false) {
                        $nivelInstrutor = $parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR];
                    }
                }
            }

            if (isset($nivelInstrutor) === false) {
                $nivelInstrutor = null;
            }

            unset($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]);
            $objetoORM->setNivelInstrutor($nivelInstrutor);
        }

        if (isset($parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FUNCIONARIO]) === true) {
            if (empty($parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FUNCIONARIO]) === false) {
                $bRetornoGestorComercialFuncionario = self::verificaFuncionarioExisteBO($parametros, $mensagemErro, ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FUNCIONARIO, $parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FUNCIONARIO]);
                if ($bRetornoGestorComercialFuncionario === true) {
                    if (is_null($objetoORM) === false) {
                        $gestor = $parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FUNCIONARIO];
                    }
                }
            }

            if (isset($gestor) === false) {
                $gestor = null;
            }

            unset($parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FUNCIONARIO]);
            $objetoORM->setGestorComercialFuncionario($gestor);
        }

        return ($bRetornoUsuario && $bRetornoBanco && $bRetornoCargo && $bRetornoNivelInstrutor && $bRetornoGestorComercialFuncionario);
    }

    /**
     * Buscao objeto através da id informada
     *
     * @param \App\Repository\Principal\FuncionarioRepository $funcionarioRepository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Funcionario $funcionarioORM
     *
     * @return boolean
     */
    public static function verificaFuncionarioExiste(\App\Repository\Principal\FuncionarioRepository $funcionarioRepository, $id, &$mensagemErro, &$funcionarioORM)
    {
        $funcionarioORM = $funcionarioRepository->find($id);
        if (is_null($funcionarioORM) === true) {
            $mensagemErro = "Funcionario não encontrado.";
            return false;
        }

        return true;
    }

    /**
     * Verifica se os campos relacionais estao validos e indica se pode salvar ou não
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Funcionario $objetoORM
     *
     * @return boolean
     */
    public function podeSalvar(&$parametros, &$mensagemErro, &$objetoORM)
    {
        if ($this->verificaCamposRelacionaisObrigatorios($parametros, $mensagemErro, $objetoORM) === true) {
            return true;
        }

        return false;
    }

    /**
     * Realiza as verificacoes nos campos relacionaveis e configura os indices com os objetos
     * Se algum valor nao existir ele retornara false
     *
     * @param array $parametros
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Funcionario $objetoORM
     *
     * @return boolean
     */
    public function podeAlterar(&$parametros, &$mensagemErro, &$objetoORM)
    {
        if ($this->configuraCamposRelacionaisOpcionais($parametros, $mensagemErro, $objetoORM) === true) {
            return true;
        }//end if

        return false;
    }

    /**
     * Configura os parametros para a parte de atualização
     *
     * @param array $parametros
     * @param \App\Entity\Principal\Funcionario $objetoORM
     */
    public function configuraParametros($parametros, &$objetoORM, &$mensagemErro)
    {
        if ((isset($parametros[ConstanteParametros::CHAVE_TIPO_PAGAMENTO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_TIPO_PAGAMENTO]) === false)) {
            $objetoORM->setTipoPagamento($parametros[ConstanteParametros::CHAVE_TIPO_PAGAMENTO]);
            unset($parametros[ConstanteParametros::CHAVE_TIPO_PAGAMENTO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_APELIDO]) === true) && ($parametros[ConstanteParametros::CHAVE_APELIDO] !== "")) {
            $objetoORM->setApelido($parametros[ConstanteParametros::CHAVE_APELIDO]);
            unset($parametros[ConstanteParametros::CHAVE_APELIDO]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO]) === false)) {
            $dataAdmissao = $parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataAdmissao, $dataAdmissao);
            if ($dataAdmissao !== false) {
                $objetoORM->setDataAdmissao($dataAdmissao);
                unset($parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO]);
            } else {
                $mensagemErro .= "Ocorreu um erro na conversão de data de admissao.\n Formato Invalido! Dado recebido:" . $parametros[ConstanteParametros::CHAVE_DATA_ADMISSAO];
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO]) === false)) {
            $dataDemissao = $parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO];
            \App\Helper\FunctionHelper::formataCampoDateTimeJS($dataDemissao, $dataDemissao);
            if ($dataDemissao !== false) {
                $objetoORM->setDataDemissao($dataDemissao);
                $parametros[ConstanteParametros::CHAVE_SITUACAO] = 'I';
                unset($parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO]);
            } else {
                $mensagemErro .= "Ocorreu um erro na conversão de data de demissão.\n Formato Invalido! Dado recebido:" . $parametros[ConstanteParametros::CHAVE_DATA_DEMISSAO];
            }
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_RECEBE_EMAILS_FLAG]) === true) && ($parametros[ConstanteParametros::CHAVE_RECEBE_EMAILS_FLAG] !== "")) {
            $objetoORM->setRecebeEmails($parametros[ConstanteParametros::CHAVE_RECEBE_EMAILS_FLAG]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]) === true) && ($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG] !== "")) {
            $objetoORM->setInstrutor($parametros[ConstanteParametros::CHAVE_INSTRUTOR_FLAG]);
        }

        if (($objetoORM->getInstrutor() === false) && ($objetoORM->getInstrutorPersonal() === false)) {
            $objetoORM->setNivelInstrutor(null);
        } else if (isset($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]) === true) {
            $objetoORM->setNivelInstrutor($parametros[ConstanteParametros::CHAVE_NIVEL_INSTRUTOR]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_INSTRUTOR_PERSONAL_FLAG]) === true) && ($parametros[ConstanteParametros::CHAVE_INSTRUTOR_PERSONAL_FLAG] !== "")) {
            $objetoORM->setInstrutorPersonal($parametros[ConstanteParametros::CHAVE_INSTRUTOR_PERSONAL_FLAG]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FLAG]) === true) && ($parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FLAG] !== "")) {
            $objetoORM->setGestorComercial($parametros[ConstanteParametros::CHAVE_GESTOR_COMERCIAL_FLAG]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_CONSULTOR_FLAG]) === true) && ($parametros[ConstanteParametros::CHAVE_CONSULTOR_FLAG] !== "")) {
            $objetoORM->setConsultor($parametros[ConstanteParametros::CHAVE_CONSULTOR_FLAG]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_ATENDENTE_FLAG]) === true) && ($parametros[ConstanteParametros::CHAVE_ATENDENTE_FLAG] !== "")) {
            $objetoORM->setAtendente($parametros[ConstanteParametros::CHAVE_ATENDENTE_FLAG]);
        }

        if ((isset($parametros[ConstanteParametros::CHAVE_SITUACAO]) === true) && (empty($parametros[ConstanteParametros::CHAVE_SITUACAO]) === false)) {
            $objetoORM->setSituacao($parametros[ConstanteParametros::CHAVE_SITUACAO]);
        }

        \App\Helper\FunctionHelper::setParams($parametros, $objetoORM);
    }

    /**
     * Verifica se o funcionário existe e está ativo
     *
     * @param \App\Repository\Principal\FuncionarioRepository $repository
     * @param int $id
     * @param string $mensagemErro
     * @param \App\Entity\Principal\Funcionario $funcionario
     *
     * @return boolean
     */
    public static function funcionarioExisteEAtivo(\App\Repository\Principal\FuncionarioRepository $repository, $id, &$mensagemErro, &$funcionario)
    {
        if (self::verificaFuncionarioExiste($repository, $id, $mensagemErro, $funcionario) === false) {
            return false;
        }

        if ($funcionario->getSituacao() !== 'A') {
            $mensagemErro = 'O funcionário selecionado está inativo.';
            return false;
        }

        return true;
    }


}
