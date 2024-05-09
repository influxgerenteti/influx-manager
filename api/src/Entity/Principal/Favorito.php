<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\FavoritoRepository")
 * @ORM\Table(
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="indice_relacional",
 *              columns={"usuario_id", "modulo_id", "franqueada_id"}
 *          )
 *      }
 * )
 */
class Favorito
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Modulo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $modulo;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    public function getId()
    {
        return $this->id;
    }

    public function getModulo() : ?Modulo
    {
        return $this->modulo;
    }

    public function setModulo(?Modulo $modulo) : self
    {
        $this->modulo = $modulo;

        return $this;
    }

    public function getFranqueada() : ?Franqueada
    {
        return $this->franqueada;
    }

    public function setFranqueada(?Franqueada $franqueada) : self
    {
        $this->franqueada = $franqueada;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }


}
