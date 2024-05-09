<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\PlanejamentoLicaoRepository")
 * @ORM\Table(name="planejamento_licao")
 */
class PlanejamentoLicao
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
     * @ORM\Column(type="string", length=1,options={"default":"A","comment":"A - ATIVO, I - INATIVO"})
     */
    private $situacao = 'A';

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Licao", mappedBy="planejamento_licao")
     */
    private $licaos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\Livro", mappedBy="planejamento_licao")
     */
    private $livros;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portal_level_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $portal_level_reference_code;

    public function __construct()
    {
        $this->licaos = new ArrayCollection();
        $this->livros = new ArrayCollection();
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

    public function getSituacao(): ?string
    {
        return $this->situacao;
    }

    public function setSituacao(string $situacao): self
    {
        $this->situacao = $situacao;

        return $this;
    }

    /**
     * @return Collection|Licao[]
     */
    public function getLicaos(): Collection
    {
        return $this->licaos;
    }

    public function addLicao(Licao $licao): self
    {
        if ($this->licaos->contains($licao) === false) {
            $this->licaos[] = $licao;
            $licao->setPlanejamentoLicao($this);
        }

        return $this;
    }

    public function removeLicao(Licao $licao): self
    {
        if ($this->licaos->contains($licao) === true) {
            $this->licaos->removeElement($licao);
            // set the owning side to null (unless already changed)
            if ($licao->getPlanejamentoLicao() === $this) {
                $licao->setPlanejamentoLicao(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Livro[]
     */
    public function getLivros(): Collection
    {
        return $this->livros;
    }

    public function addLivros(Livro $livro): self
    {
        if ($this->livros->contains($livro) === false) {
            $this->livros[] = $livro;
            $livro->setPlanejamentoLicao($this);
        }

        return $this;
    }

    public function removeLivros(Licao $livro): self
    {
        if ($this->livros->contains($livro) === true) {
            $this->livros->removeElement($livro);
            // set the owning side to null (unless already changed)
            if ($livro->getPlanejamentoLicao() === $this) {
                $livro->setPlanejamentoLicao(null);
            }
        }

        return $this;
    }



    /**
     * Get the value of portal_level_id
     */
    public function getPortal_level_id()
    {
        return $this->portal_level_id;
    }

    /**
     * Set the value of portal_level_id
     *
     * @return self
     */
    public function setPortal_level_id($portal_level_id)
    {
        $this->portal_level_id = $portal_level_id;

        return $this;
    }

    /**
     * Get the value of portal_level_reference_code
     */
    public function getPortal_level_reference_code()
    {
        return $this->portal_level_reference_code;
    }

    /**
     * Set the value of portal_level_reference_code
     *
     * @return self
     */
    public function setPortal_level_reference_code($portal_level_reference_code)
    {
        $this->portal_level_reference_code = $portal_level_reference_code;

        return $this;
    }


}
