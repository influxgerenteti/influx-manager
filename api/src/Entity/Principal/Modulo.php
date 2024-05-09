<?php

namespace App\Entity\Principal;

use InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\Principal\ModuloRepository")
 */
class Modulo
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
     * @ORM\Column(type="string", length=80)
     */
    private $nome;

    /**
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Modulo", fetch="LAZY")
     */
    private $modulo_pai;

    /**
     *
     * @ORM\Column(type="string", options={"comment": "A - Ativo, I- Inativo, R - Removido", "default": "A"})
     */
    private $situacao = "A";

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ModuloPapelAcao", mappedBy="modulo", fetch="LAZY")
     */
    private $modulo_papel_acao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\ModuloUsuarioAcao", mappedBy="modulo", fetch="LAZY")
     */
    private $moduloUsuarioAcaos;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\UrlSistema", mappedBy="modulos")
     */
    private $urlSistemas;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ordem;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\AcaoSistema", mappedBy="modulo")
     */
    private $acaoSistemas;

    /**
     * @ORM\Column(type="boolean")
     */
    private $exibir_no_menu = true;

     /**
     * @ORM\Column(type="boolean")
     */
    private $exibir_como_relatorio = true;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $entity;

    /**
     * @ORM\Column(type="boolean" , options={"default": "0"})
     */
    private $apenas_franqueadora = false;

    public function __construct()
    {
        $this->modulo_papel_acao  = new ArrayCollection();
        $this->moduloUsuarioAcaos = new ArrayCollection();
        $this->urlSistemas        = new ArrayCollection();
        $this->acaoSistemas       = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getModuloPai(): ?self
    {
        return $this->modulo_pai;
    }

    public function setModuloPai(?self $modulo_pai): self
    {
        $this->modulo_pai = $modulo_pai;

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
     * @return Collection|ModuloPapelAcao[]
     */
    public function getModuloPapelAcao(): Collection
    {
        return $this->modulo_papel_acao;
    }

    public function addModuloPapelAcao(ModuloPapelAcao $moduloPapelAcao): self
    {
        if ($this->modulo_papel_acao->contains($moduloPapelAcao) === false) {
            $this->modulo_papel_acao[] = $moduloPapelAcao;
            $moduloPapelAcao->setModulo($this);
        }

        return $this;
    }

    public function removeModuloPapelAcao(ModuloPapelAcao $moduloPapelAcao): self
    {
        if ($this->modulo_papel_acao->contains($moduloPapelAcao) === true) {
            $this->modulo_papel_acao->removeElement($moduloPapelAcao);
            // set the owning side to null (unless already changed)
            if ($moduloPapelAcao->getModulo() === $this) {
                $moduloPapelAcao->setModulo(null);
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
            $moduloUsuarioAcao->setModulo($this);
        }

        return $this;
    }

    public function removeModuloUsuarioAcao(ModuloUsuarioAcao $moduloUsuarioAcao): self
    {
        if ($this->moduloUsuarioAcaos->contains($moduloUsuarioAcao) === true) {
            $this->moduloUsuarioAcaos->removeElement($moduloUsuarioAcao);
            // set the owning side to null (unless already changed)
            if ($moduloUsuarioAcao->getModulo() === $this) {
                $moduloUsuarioAcao->setModulo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UrlSistema[]
     */
    public function getUrlSistemas(): Collection
    {
        return $this->urlSistemas;
    }

    public function addUrlSistema(UrlSistema $urlSistema): self
    {
        if ($this->urlSistemas->contains($urlSistema) === false) {
            $this->urlSistemas[] = $urlSistema;
            $urlSistema->addModulo($this);
        }

        return $this;
    }

    public function removeUrlSistema(UrlSistema $urlSistema): self
    {
        if ($this->urlSistemas->contains($urlSistema) === true) {
            $this->urlSistemas->removeElement($urlSistema);
            $urlSistema->removeModulo($this);
        }

        return $this;
    }

    public function getOrdem(): ?string
    {
        return $this->ordem;
    }

    public function setOrdem(?string $ordem): self
    {
        $this->ordem = $ordem;
        return $this;
    }

    /**
     * @return Collection|AcaoSistema[]
     */
    public function getAcaoSistemas(): Collection
    {
        return $this->acaoSistemas;
    }

    public function addAcaoSistema(AcaoSistema $acaoSistema): self
    {
        if ($this->acaoSistemas->contains($acaoSistema) === false) {
            $this->acaoSistemas[] = $acaoSistema;
            $acaoSistema->addModulo($this);
        }

        return $this;
    }

    public function removeAcaoSistema(AcaoSistema $acaoSistema): self
    {
        if ($this->acaoSistemas->contains($acaoSistema) === true) {
            $this->acaoSistemas->removeElement($acaoSistema);
            $acaoSistema->removeModulo($this);
        }

        return $this;
    }

    public function getExibirNoMenu(): ?bool
    {
        return $this->exibir_no_menu;
    }

    public function getExibirComoRelatorio(): ?bool
    {
        return $this->exibir_como_relatorio;
    }

    public function setExibiComoRelatorio(bool $exibir_como_relatorio): self
    {
        $this->exibir_como_relatorio = $exibir_como_relatorio;

        return $this;
    }

    public function setExibirNoMenu(bool $exibir_no_menu): self
    {
        $this->exibir_no_menu = $exibir_no_menu;

        return $this;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(?string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getApenasFranqueadora(): ?bool
    {
        return $this->apenas_franqueadora;
    }

    public function setApenasFranqueadora(bool $apenas_franqueadora): self
    {
        $this->apenas_franqueadora = $apenas_franqueadora;

        return $this;
    }


}
