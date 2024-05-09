<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\AcaoSistemaRepository")
 * @ORM\Table(name="acao_sistema")
 */
class AcaoSistema
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $descricao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ModuloPapelAcao", mappedBy="acao_sistema")
     */
    private $moduloPapelAcao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ModuloUsuarioAcao", mappedBy="acao_sistema")
     */
    private $moduloUsuarioAcaos;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Modulo", inversedBy="acaoSistemas")
     */
    private $modulo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $permissao_descricao;

    /**
     * @ORM\Column(type="boolean", options={"comment":"Flag criada, para verificar se para a permissão mencionada, necessita colocar um login com privilégios superiores, exemplo: admin, supervisor, gestor"})
     */
    private $solicita_login_superior = false;

    public function __construct()
    {
        $this->moduloPapelAcao    = new ArrayCollection();
        $this->moduloUsuarioAcaos = new ArrayCollection();
        $this->modulo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    /**
     * @return Collection|ModuloPapelAcao[]
     */
    public function getModuloPapelAcao(): Collection
    {
        return $this->moduloPapelAcao;
    }

    public function addModuloPapelAcao(ModuloPapelAcao $moduloPapelAcao): self
    {
        if ($this->moduloPapelAcao->contains($moduloPapelAcao) === false) {
            $this->moduloPapelAcao[] = $moduloPapelAcao;
            $moduloPapelAcao->setAcaoSistema($this);
        }

        return $this;
    }

    public function removeModuloPapelAcao(ModuloPapelAcao $moduloPapelAcao): self
    {
        if ($this->moduloPapelAcao->contains($moduloPapelAcao) === true) {
            $this->moduloPapelAcao->removeElement($moduloPapelAcao);
            // set the owning side to null (unless already changed)
            if ($moduloPapelAcao->getAcaoSistema() === $this) {
                $moduloPapelAcao->setAcaoSistema(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ModuloUsuarioAcao[]
     */
    public function getModuloUsuarioAcaos(): Collection
    {
        return $this->moduloUsuarioAcaos;
    }

    public function addModuloUsuarioAcao(ModuloUsuarioAcao $moduloUsuarioAcao): self
    {
        if ($this->moduloUsuarioAcaos->contains($moduloUsuarioAcao) === false) {
            $this->moduloUsuarioAcaos[] = $moduloUsuarioAcao;
            $moduloUsuarioAcao->setAcaoSistema($this);
        }

        return $this;
    }

    public function removeModuloUsuarioAcao(ModuloUsuarioAcao $moduloUsuarioAcao): self
    {
        if ($this->moduloUsuarioAcaos->contains($moduloUsuarioAcao) === true) {
            $this->moduloUsuarioAcaos->removeElement($moduloUsuarioAcao);
            // set the owning side to null (unless already changed)
            if ($moduloUsuarioAcao->getAcaoSistema() === $this) {
                $moduloUsuarioAcao->setAcaoSistema(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Modulo[]
     */
    public function getModulo(): Collection
    {
        return $this->modulo;
    }

    public function addModulo(Modulo $modulo): self
    {
        if ($this->modulo->contains($modulo) === false) {
            $this->modulo[] = $modulo;
        }

        return $this;
    }

    public function removeModulo(Modulo $modulo): self
    {
        if ($this->modulo->contains($modulo) === true) {
            $this->modulo->removeElement($modulo);
        }

        return $this;
    }

    public function getPermissaoDescricao(): ?string
    {
        return $this->permissao_descricao;
    }

    public function setPermissaoDescricao(string $permissao_descricao): self
    {
        $this->permissao_descricao = $permissao_descricao;

        return $this;
    }

    public function getSolicitaLoginSuperior(): ?bool
    {
        return $this->solicita_login_superior;
    }

    public function setSolicitaLoginSuperior(bool $solicita_login_superior): self
    {
        $this->solicita_login_superior = $solicita_login_superior;

        return $this;
    }


}
