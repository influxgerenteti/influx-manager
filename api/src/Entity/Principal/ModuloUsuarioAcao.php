<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ModuloUsuarioAcaoRepository")
 * @ORM\Table(name="modulo_usuario_acao",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="indice_relacional",
 *              columns={"modulo_id", "usuario_id", "acao_sistema_id"}
 *          )
 *      }
 * )
 */
class ModuloUsuarioAcao
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Modulo", inversedBy="moduloUsuarioAcaos", fetch="LAZY")
     * @ORM\JoinColumn(name="modulo_id",                          referencedColumnName="id", nullable=false)
     */
    private $modulo;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Usuario", inversedBy="moduloUsuarioAcaos", fetch="LAZY")
     * @ORM\JoinColumn(name="usuario_id",                          referencedColumnName="id", nullable=false)
     */
    private $usuario;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\AcaoSistema", inversedBy="moduloUsuarioAcaos", fetch="LAZY")
     * @ORM\JoinColumn(name="acao_sistema_id",                         referencedColumnName="id", nullable=false)
     */
    private $acao_sistema;

    public function getModulo(): ?Modulo
    {
        return $this->modulo;
    }

    public function setModulo(?Modulo $modulo): self
    {
        $this->modulo = $modulo;

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

    public function getAcaoSistema(): ?AcaoSistema
    {
        return $this->acao_sistema;
    }

    public function setAcaoSistema(?AcaoSistema $acao_sistema): self
    {
        $this->acao_sistema = $acao_sistema;

        return $this;
    }


}
