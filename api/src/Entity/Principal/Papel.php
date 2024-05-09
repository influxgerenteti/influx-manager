<?php
namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\PapelRepository")
 */
class Papel
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
     * @ORM\Column(type="string", length=255)
     */
    private $descricao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ModuloPapelAcao", mappedBy="papel")
     */
    private $moduloPapelAcao;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\Usuario", mappedBy="papels")
     */
    private $usuariosPapeis;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\ChecklistAtividade", mappedBy="papel")
     */
    private $checklistAtividades;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $oculto = 0;

    public function __construct()
    {
        $this->moduloPapelAcao     = new ArrayCollection();
        $this->usuariosPapeis      = new ArrayCollection();
        $this->checklistAtividades = new ArrayCollection();
    }

    public function getId()
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
            $moduloPapelAcao->setPapel($this);
        }

        return $this;
    }

    public function removeModuloPapelAcao(ModuloPapelAcao $moduloPapelAcao): self
    {
        if ($this->moduloPapelAcao->contains($moduloPapelAcao) === true) {
            $this->moduloPapelAcao->removeElement($moduloPapelAcao);
            // set the owning side to null (unless already changed)
            if ($moduloPapelAcao->getPapel() === $this) {
                $moduloPapelAcao->setPapel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Usuario[]
     */
    public function getUsuariosPapeis(): Collection
    {
        return $this->usuariosPapeis;
    }

    public function addUsuariosPapeis(Usuario $usuariosPapeis): self
    {
        if ($this->usuariosPapeis->contains($usuariosPapeis) === false) {
            $this->usuariosPapeis[] = $usuariosPapeis;
            $usuariosPapeis->addPapel($this);
        }

        return $this;
    }

    public function removeUsuariosPapeis(Usuario $usuariosPapeis): self
    {
        if ($this->usuariosPapeis->contains($usuariosPapeis) === true) {
            $this->usuariosPapeis->removeElement($usuariosPapeis);
            $usuariosPapeis->removePapel($this);
        }

        return $this;
    }

    /**
     * @return Collection|ChecklistAtividade[]
     */
    public function getChecklistAtividades(): Collection
    {
        return $this->checklistAtividades;
    }

    public function addChecklistAtividade(ChecklistAtividade $checklistAtividade): self
    {
        if ($this->checklistAtividades->contains($checklistAtividade) === false) {
            $this->checklistAtividades[] = $checklistAtividade;
            $checklistAtividade->addPapel($this);
        }

        return $this;
    }

    public function removeChecklistAtividade(ChecklistAtividade $checklistAtividade): self
    {
        if ($this->checklistAtividades->contains($checklistAtividade) === true) {
            $this->checklistAtividades->removeElement($checklistAtividade);
            $checklistAtividade->removePapel($this);
        }

        return $this;
    }

    public function getOculto(): ?bool
    {
        return $this->oculto;
    }

    public function setOculto(bool $oculto): self
    {
        $this->oculto = $oculto;

        return $this;
    }


}
