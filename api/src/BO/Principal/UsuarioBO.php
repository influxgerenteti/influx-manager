<?php
namespace App\BO\Principal;

use App\Helper\ConstanteParametros;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * @author Luiz Antonio Costa
 */
class UsuarioBO extends GenericBO
{


    function __construct(EntityManagerInterface $entityManager)
    {
        parent::configuraGenericBO(
            [
                "franqueadaRepository" => $entityManager->getRepository(\App\Entity\Principal\Franqueada::class),
                "papelRepository"      => $entityManager->getRepository(\App\Entity\Principal\Papel::class),
            ]
        );
    }

    /**
     * Configura franqueadas
     *
     * @param array $arrFranqueada
     * @param \App\Entity\Principal\Usuario $objetoUsuario
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function configuraFranqueadaXUsuario($arrFranqueada, &$objetoUsuario, &$mensagemErro)
    {
        $bRetorno = true;
        for ($i = 0; $i < count($arrFranqueada); $i++) {
            $franqueadaORM = self::getFranqueadaRepository()->find($arrFranqueada[$i]);
            if (is_null($franqueadaORM) === true) {
                $bRetorno     = false;
                $mensagemErro = "Franqueada informada não encontrada: " . $arrFranqueada[$i];
                break;
            }

            $franqueadaORM->addUsuario($objetoUsuario);
        }

        return $bRetorno;
    }


    /**
     * Configura os papeis para o usuario
     *
     * @param \App\Entity\Principal\Usuario $usuarioORM
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public function configuraPapeis(&$usuarioORM, &$parametros, &$mensagemErro)
    {
        $bReturn  = true;
        $papeisId = $parametros[ConstanteParametros::CHAVE_PAPELS];
        for ($i = 0; $i < count($papeisId); $i++) {
            $papelORM = self::getPapelRepository()->find($papeisId[$i]);
            if (is_null($papelORM) === true) {
                $mensagemErro .= "Papel com id: " . $papeisId[$i] . " não encontrado.\n";
                $bReturn       = false;
                break;
            }

            $usuarioORM->addPapel($papelORM);
        }

        return $bReturn;
    }

    /**
     * Verifica se o usuario existe no banco de dados atraves dos campos das entidades passadas por parametros
     *
     * @param \App\Repository\Principal\UsuarioRepository $usuarioRepositorio
     * @param array $parametros A serem pesquisados
     * @param null|\App\Entity\Principal\Usuario $retornoORM
     *
     * @return boolean
     */
    public static function usuarioExisteBanco($usuarioRepositorio, $parametros, &$retornoORM=null)
    {
        $retornoORM = $usuarioRepositorio->findOneBy($parametros);

        return is_null($retornoORM) === false;
    }

    /**
     * Cria token único (60 caracteres) a ser enviado por e-mail ao usuário para criar ou alterar senha.
     *
     * @return string
     */
    public static function gerarToken ()
    {
        $length = 60;
        return bin2hex(openssl_random_pseudo_bytes($length));
    }

    /**
     * Executa as validações para verificar se pode prosseguir com a criação de usuario
     *
     * @param \App\Repository\Principal\UsuarioRepository $usuarioRepositorio
     * @param \App\Repository\Principal\FranqueadaRepository $franqueadaRepository
     * @param array $parametros
     * @param string $mensagemErro
     *
     * @return boolean
     */
    public static function podeCriar($usuarioRepositorio, $franqueadaRepository, &$parametros, &$mensagemErro)
    {
        $bRetorno = false;
        $parametros[ConstanteParametros::CHAVE_CPF] = str_pad($parametros[ConstanteParametros::CHAVE_CPF], 11, "0", STR_PAD_LEFT);

        $usuarioExisteEmail = false;
        if (empty($parametros[ConstanteParametros::CHAVE_EMAIL]) === false) {
            $usuarioExisteEmail = self::usuarioExisteBanco($usuarioRepositorio, ["email" => $parametros[ConstanteParametros::CHAVE_EMAIL]]);
        }

        $usuarioExisteCpf = false;
        if (empty($parametros[ConstanteParametros::CHAVE_CPF]) === false) {
            $usuarioExisteCpf = self::usuarioExisteBanco($usuarioRepositorio, ["cpf" => $parametros[ConstanteParametros::CHAVE_CPF]]);
        }

        if (\App\BO\Principal\FranqueadaBO::franqueadaExisteBanco($franqueadaRepository, ["id" => $parametros[ConstanteParametros::CHAVE_FRANQUEADA_PADRAO]], $parametros[ConstanteParametros::CHAVE_FRANQUEADA_PADRAO]) === false) {
            $mensagemErro = "Franqueada padrão, não encontrada na base de dados";
        } else if (\App\BO\Principal\PessoaBO::isCgcValido($parametros[ConstanteParametros::CHAVE_CPF], $mensagemErro, true) === false) {
            $mensagemErro = "Ocorreu um erro:" . $mensagemErro;
        } else {
            if ($usuarioExisteEmail === true) {
                $mensagemErro = "Não foi possivel criar o usuario com o e-mail informado, pois o mesmo se encontra já cadastrado no banco";
            } else if ($usuarioExisteCpf === true) {
                $mensagemErro = "Não foi possivel criar o usuario com o CPF informado, pois o mesmo se encontra já cadastrado no banco";
            } else {
                $bRetorno = true;
            }
        }

        return $bRetorno;
    }

    /**
     * Monta o array para realizar o login pro front-end
     *
     * @param \App\Entity\Principal\Usuario $usuario
     *
     * @return array
     */
    public static function montaArrayLogin($usuario)
    {
        $arrUsuario = [
            "id"                   => $usuario->getId(),
            "nome"                 => $usuario->getNome(),
            "situacao"             => $usuario->getSituacao(),
            "usuario_acesso"       => [
                "id"           => $usuario->getUsuarioAcesso()->getId(),
                "token_acesso" => $usuario->getUsuarioAcesso()->getTokenAcesso(),
            ],
            "franqueada_padrao"    => [
                "id"       => $usuario->getFranqueadaPadrao()->getId(),
                "nome"     => $usuario->getFranqueadaPadrao()->getNome(),
                "cnpj"     => $usuario->getFranqueadaPadrao()->getCnpj(),
                "situacao" => $usuario->getFranqueadaPadrao()->getSituacao(),
            ],
            "pertenceFranqueadora" => $usuario->isUsuarioPertenceFranqueadora(),
        ];
        foreach ($usuario->getFuncionarios() as $funcionarioORM) {
            $arrUsuario["funcionarios"][] = [
                "id"               => $funcionarioORM->getId(),
                "apelido"          => $funcionarioORM->getApelido(),
                "gestor_comercial" => $funcionarioORM->getGestorComercial(),
            ];
        };

        foreach ($usuario->getFranqueadas() as $franqueada) {
            if ($franqueada->getExcluido() === true) {
                continue;
            }

            $estadoFranqueada = $franqueada->getEstado();
            if (is_null($estadoFranqueada) === true) {
                $estadoFranqueadaId = '';
            } else {
                $estadoFranqueadaId = $estadoFranqueada->getId();
            }

            $arrUsuario["franqueadas"][] = [
                "id"                  => $franqueada->getId(),
                "nome"                => $franqueada->getNome(),
                "cnpj"                => $franqueada->getCnpj(),
                "situacao"            => $franqueada->getSituacao(),
                "franqueadora"        => $franqueada->getFranqueadora(),
                "estado"              => ["id" => $estadoFranqueadaId],
                "percentual_multa"    => $franqueada->getPercentualMulta(),
                "percentual_juro_dia" => $franqueada->getPercentualJuroDia(),
            ];
        }//end foreach

        // função para ordenar por ordem alfabética os nomes das franqueadas e fixa em primeiro a inFlux Franqueadora.
        usort($arrUsuario["franqueadas"], fn($a, $b) => $a['id'] <=> $b['id']);
        $usuarioFranqueadora = array_shift($arrUsuario["franqueadas"]);
        
        usort($arrUsuario["franqueadas"], fn($a, $b) => $a['nome'] <=> $b['nome']);
        array_unshift($arrUsuario["franqueadas"],$usuarioFranqueadora);

        return $arrUsuario;
    }
}
