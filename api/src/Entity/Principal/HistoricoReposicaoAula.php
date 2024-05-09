<?php

namespace App\Entity\Principal;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\HistoricoReposicaoAulaRepository")
 * @ORM\Table(name="historico_reposicao_aula")
 */
class HistoricoReposicaoAula
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Principal\ReposicaoAula", inversedBy="historicoReposicaoAula", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $reposicao_aula;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $mid_term_escrita_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $mid_term_escrita_atual = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $mid_term_test_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $mid_term_test_atual = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $mid_term_composition_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $mid_term_composition_atual = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $final_escrita_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $final_escrita_atual = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $final_test_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $final_test_atual = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $final_composition_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $final_composition_atual = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_mid_term_escrita_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_mid_term_escrita_atual = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_mid_term_test_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_mid_term_test_atual = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_mid_term_composition_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_mid_term_composition_atual = null;


    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_final_escrita_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_final_escrita_atual = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_final_test_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_final_test_atual = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_final_composition_anterior = null;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $retake_final_composition_atual = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="historicoReposicaoAulasMtoa")
     */
    private $mid_term_oral_anterior;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="historicoReposicaoAulasMtoat")
     */
    private $mid_term_oral_atual;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="historicoReposicaoAulasFoa")
     */
    private $final_oral_anterior;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="historicoReposicaoAulasRmtoa")
     */
    private $retake_mid_term_oral_anterior;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="historicoReposicaoAulasRmtoat")
     */
    private $retake_mid_term_oral_atual;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="historicoReposicaoAulasRfoa")
     */
    private $retake_final_oral_anterior;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="historicoReposicaoAulasRfoat")
     */
    private $retake_final_oral_atual;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\ConceitoAvaliacao", inversedBy="historicoReposicaoAulasFoat")
     */
    private $final_oral_atual;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReposicaoAula(): ?ReposicaoAula
    {
        return $this->reposicao_aula;
    }

    public function setReposicaoAula(ReposicaoAula $reposicao_aula): self
    {
        $this->reposicao_aula = $reposicao_aula;

        return $this;
    }

    public function getMidTermEscritaAnterior()
    {
        return $this->mid_term_escrita_anterior;
    }

    public function setMidTermEscritaAnterior($mid_term_escrita_anterior): self
    {
        $this->mid_term_escrita_anterior = $mid_term_escrita_anterior;

        return $this;
    }

    public function getMidTermEscritaAtual()
    {
        return $this->mid_term_escrita_atual;
    }

    public function setMidTermEscritaAtual($mid_term_escrita_atual): self
    {
        $this->mid_term_escrita_atual = $mid_term_escrita_atual;

        return $this;
    }

    public function getMidTermTestAnterior()
    {
        return $this->mid_term_test_anterior;
    }

    public function setMidTermTestAnterior($mid_term_test_anterior): self
    {
        $this->mid_term_test_anterior = $mid_term_test_anterior;

        return $this;
    }

    public function getMidTermTestAtual()
    {
        return $this->mid_term_test_atual;
    }

    public function setMidTermTestAtual($mid_term_test_atual): self
    {
        $this->mid_term_test_atual = $mid_term_test_atual;

        return $this;
    }

    public function getMidTermCompositionAnterior()
    {
        return $this->mid_term_composition_anterior;
    }

    public function setMidTermCompositionAnterior($mid_term_composition_anterior): self
    {
        $this->mid_term_composition_anterior = $mid_term_composition_anterior;

        return $this;
    }

    public function getMidTermCompositionAtual()
    {
        return $this->mid_term_composition_atual;
    }

    public function setMidTermCompositionAtual($mid_term_composition_atual): self
    {
        $this->mid_term_composition_atual = $mid_term_composition_atual;

        return $this;
    }


    public function getFinalEscritaAnterior()
    {
        return $this->final_escrita_anterior;
    }

    public function setFinalEscritaAnterior($final_escrita_anterior): self
    {
        $this->final_escrita_anterior = $final_escrita_anterior;

        return $this;
    }

    public function getFinalEscritaAtual()
    {
        return $this->final_escrita_atual;
    }

    public function setFinalEscritaAtual($final_escrita_atual): self
    {
        $this->final_escrita_atual = $final_escrita_atual;

        return $this;
    }

    public function getFinalTestAnterior()
    {
        return $this->final_test_anterior;
    }

    public function setFinalTestAnterior($final_test_anterior): self
    {
        $this->final_test_anterior = $final_test_anterior;

        return $this;
    }

    public function getFinalTestAtual()
    {
        return $this->final_test_atual;
    }

    public function setFinalTestAtual($final_test_atual): self
    {
        $this->final_test_atual = $final_test_atual;

        return $this;
    }

    public function getFinalCompositionAnterior()
    {
        return $this->final_composition_anterior;
    }

    public function setFinalCompositionAnterior($final_composition_anterior): self
    {
        $this->final_composition_anterior = $final_composition_anterior;

        return $this;
    }

    public function getFinalCompositionAtual()
    {
        return $this->final_composition_atual;
    }

    public function setFinalCompositionAtual($final_composition_atual): self
    {
        $this->final_composition_atual = $final_composition_atual;

        return $this;
    }

    public function getRetakeMidTermEscritaAnterior()
    {
        return $this->retake_mid_term_escrita_anterior;
    }

    public function setRetakeMidTermEscritaAnterior($retake_mid_term_escrita_anterior): self
    {
        $this->retake_mid_term_escrita_anterior = $retake_mid_term_escrita_anterior;

        return $this;
    }

    public function getRetakeMidTermEscritaAtual()
    {
        return $this->retake_mid_term_escrita_atual;
    }

    public function setRetakeMidTermEscritaAtual($retake_mid_term_escrita_atual): self
    {
        $this->retake_mid_term_escrita_atual = $retake_mid_term_escrita_atual;

        return $this;
    }

    public function getRetakeMidTermTestAnterior()
    {
        return $this->retake_mid_term_test_anterior;
    }

    public function setRetakeMidTermTestAnterior($retake_mid_term_test_anterior): self
    {
        $this->retake_mid_term_test_anterior = $retake_mid_term_test_anterior;

        return $this;
    }

    public function getRetakeMidTermTestAtual()
    {
        return $this->retake_mid_term_test_atual;
    }

    public function setRetakeMidTermTestAtual($retake_mid_term_test_atual): self
    {
        $this->retake_mid_term_test_atual = $retake_mid_term_test_atual;

        return $this;
    }

    public function getRetakeMidTermCompositionAnterior()
    {
        return $this->retake_mid_term_composition_anterior;
    }

    public function setRetakeMidTermCompositionAnterior($retake_mid_term_composition_anterior): self
    {
        $this->retake_mid_term_composition_anterior = $retake_mid_term_composition_anterior;

        return $this;
    }

    public function getRetakeMidTermCompositionAtual()
    {
        return $this->retake_mid_term_composition_atual;
    }

    public function setRetakeMidTermCompositionAtual($retake_mid_term_composition_atual): self
    {
        $this->retake_mid_term_composition_atual = $retake_mid_term_composition_atual;

        return $this;
    }

    public function getRetakeFinalEscritaAnterior()
    {
        return $this->retake_final_escrita_anterior;
    }

    public function setRetakeFinalEscritaAnterior($retake_final_escrita_anterior): self
    {
        $this->retake_final_escrita_anterior = $retake_final_escrita_anterior;

        return $this;
    }

    public function getRetakeFinalEscritaAtual()
    {
        return $this->retake_final_escrita_atual;
    }

    public function setRetakeFinalEscritaAtual($retake_final_escrita_atual): self
    {
        $this->retake_final_escrita_atual = $retake_final_escrita_atual;

        return $this;
    }

    public function getRetakeFinalTestAnterior()
    {
        return $this->retake_final_test_anterior;
    }

    public function setRetakeFinalTestAnterior($retake_final_test_anterior): self
    {
        $this->retake_final_test_anterior = $retake_final_test_anterior;

        return $this;
    }

    public function getRetakeFinalTestAtual()
    {
        return $this->retake_final_test_atual;
    }

    public function setRetakeFinalTestAtual($retake_final_test_atual): self
    {
        $this->retake_final_test_atual = $retake_final_test_atual;

        return $this;
    }

    public function getRetakeFinalCompositionAnterior()
    {
        return $this->retake_final_composition_anterior;
    }

    public function setRetakeFinalCompositionAnterior($retake_final_composition_anterior): self
    {
        $this->retake_final_composition_anterior = $retake_final_composition_anterior;

        return $this;
    }

    public function getRetakeFinalCompositionAtual()
    {
        return $this->retake_final_composition_atual;
    }

    public function setRetakeFinalCompositionAtual($retake_final_composition_atual): self
    {
        $this->retake_final_composition_atual = $retake_final_composition_atual;

        return $this;
    }

    public function getMidTermOralAnterior(): ?ConceitoAvaliacao
    {
        return $this->mid_term_oral_anterior;
    }

    public function setMidTermOralAnterior(?ConceitoAvaliacao $mid_term_oral_anterior): self
    {
        $this->mid_term_oral_anterior = $mid_term_oral_anterior;

        return $this;
    }

    public function getMidTermOralAtual(): ?ConceitoAvaliacao
    {
        return $this->mid_term_oral_atual;
    }

    public function setMidTermOralAtual(?ConceitoAvaliacao $mid_term_oral_atual): self
    {
        $this->mid_term_oral_atual = $mid_term_oral_atual;

        return $this;
    }

    public function getFinalOralAnterior(): ?ConceitoAvaliacao
    {
        return $this->final_oral_anterior;
    }

    public function setFinalOralAnterior(?ConceitoAvaliacao $final_oral_anterior): self
    {
        $this->final_oral_anterior = $final_oral_anterior;

        return $this;
    }

    public function getRetakeMidTermOralAnterior(): ?ConceitoAvaliacao
    {
        return $this->retake_mid_term_oral_anterior;
    }

    public function setRetakeMidTermOralAnterior(?ConceitoAvaliacao $retake_mid_term_oral_anterior): self
    {
        $this->retake_mid_term_oral_anterior = $retake_mid_term_oral_anterior;

        return $this;
    }

    public function getRetakeMidTermOralAtual(): ?ConceitoAvaliacao
    {
        return $this->retake_mid_term_oral_atual;
    }

    public function setRetakeMidTermOralAtual(?ConceitoAvaliacao $retake_mid_term_oral_atual): self
    {
        $this->retake_mid_term_oral_atual = $retake_mid_term_oral_atual;

        return $this;
    }

    public function getRetakeFinalOralAnterior(): ?ConceitoAvaliacao
    {
        return $this->retake_final_oral_anterior;
    }

    public function setRetakeFinalOralAnterior(?ConceitoAvaliacao $retake_final_oral_anterior): self
    {
        $this->retake_final_oral_anterior = $retake_final_oral_anterior;

        return $this;
    }

    public function getRetakeFinalOralAtual(): ?ConceitoAvaliacao
    {
        return $this->retake_final_oral_atual;
    }

    public function setRetakeFinalOralAtual(?ConceitoAvaliacao $retake_final_oral_atual): self
    {
        $this->retake_final_oral_atual = $retake_final_oral_atual;

        return $this;
    }

    public function getFinalOralAtual(): ?ConceitoAvaliacao
    {
        return $this->final_oral_atual;
    }

    public function setFinalOralAtual(?ConceitoAvaliacao $final_oral_atual): self
    {
        $this->final_oral_atual = $final_oral_atual;

        return $this;
    }


}
