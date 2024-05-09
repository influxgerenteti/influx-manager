<?php

namespace App\Facade\Principal;

use App\Entity\Principal\UsuarioAcesso;

use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Marcelo Andre Naegeler
 */
class UsuarioAcessoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\UsuarioAcessoRepository
     */
    private $usuarioAcessoRepository;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->usuarioAcessoRepository = self::getEntityManager()->getRepository(UsuarioAcesso::class);
    }

    /**
     * Criar token de acesso para o usuário
     *
     * @param string $mensagem Mensagem de erro ao criar token
     * @param \App\Entity\Principal\Usuario $usuario
     *
     * @return boolean
     */
    public function criar (&$mensagem, \App\Entity\Principal\Usuario &$usuario)
    {
        $tokenString = bin2hex(openssl_random_pseudo_bytes(60));
        $tokenAcesso = new UsuarioAcesso();
        $tokenAcesso->setUsuario($usuario);
        $tokenAcesso->setTokenAcesso($tokenString);
        self::criarRegistro($tokenAcesso, $mensagem);
        if (empty($mensagem) === true) {
            self::getEntityManager()->refresh($usuario);
        }

        return $tokenString;
    }

    /**
     * Valida o token de acesso, retorna se o login é válido ou não.
     *
     * @param string $mensagem
     * @param string $tokenAcesso
     *
     * @return boolean
     */
    public function validarTokenAcesso (&$mensagem="", $tokenAcesso="")
    {
        $tokenAcesso   = str_replace('Bearer ', '', $tokenAcesso);
        $usuarioAcesso = $this->usuarioAcessoRepository->findOneBy(['token_acesso' => $tokenAcesso]);

        if ($usuarioAcesso === null) {
            $mensagem = "Você precisa fazer login para ter acesso a esta área.";
            return false;
        }

        return $usuarioAcesso;
    }


}
