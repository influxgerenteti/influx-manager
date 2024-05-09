<?php

namespace App\Facade\Principal;

use App\Entity\Principal\Token;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Marcelo Andre Naegeler
 */
class TokenFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\TokenRepository $tokenRepository
     */
    private $tokenRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct(ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->tokenRepository = self::getEntityManager()->getRepository(Token::class);
    }

    /**
     * Busca o token de identificação do usuário
     *
     * @param string $mensagem Mensagem de retorno ao backend
     * @param string $tokenString String do token a ser buscado
     * @param bool   $asArray retornar como array
     *
     * @return \App\Entity\Principal\Token $token
     */
    public function buscarToken(&$mensagem, $tokenString, $asArray=false)
    {
        if ($asArray === true) {
            $token = $this->tokenRepository->findByToken($tokenString);
        } else {
            $token = $this->tokenRepository->findOneByToken($tokenString);
        }

        if ($token === null) {
            $mensagem = "Token não encontrado.";
        }

        return $token;
    }

    /**
     * Remove o token pela hash
     *
     * @param string $mensagem Mensagem de retorno ao backend
     * @param \App\Entity\Principal\Token $token
     */
    public function removerToken(&$mensagem, Token $token)
    {
        self::removerRegistro($token, $mensagem);
    }

    /**
     * Atualiza o usuario com as informações passadas por parametros
     *
     * @param string $mensagem Retorno pro front-end
     * @param \App\Entity\Principal\Usuario $usuario Usuário a atualizar a senha
     *
     * @return boolean TRUE | FALSE
     */
    public function atualizarSenha (&$mensagem, $usuario=null)
    {
        $senha = password_hash(substr($usuario->getCpf(), 8), PASSWORD_DEFAULT);
        ;
        $usuario->setForcaTrocaSenha(true);
        $usuario->setSenha($senha);
        self::flushSeguro($mensagem);
        return empty($mensagem);
    }


}
