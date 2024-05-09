<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\UsuarioAcessoRepository")
 * @ORM\Table(name="usuario_acesso")
 */
class UsuarioAcesso
{
    /**
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="usuarioAcesso", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private $token_acesso;

    public function getId()
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getTokenAcesso(): ?string
    {
        return $this->token_acesso;
    }

    public function setTokenAcesso(string $token_acesso): self
    {
        $this->token_acesso = $token_acesso;

        return $this;
    }


}
