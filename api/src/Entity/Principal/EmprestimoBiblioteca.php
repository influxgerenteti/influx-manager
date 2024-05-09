<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\EmprestimoBibliotecaRepository")
 */
class EmprestimoBiblioteca
{


    /**
     * Joins in the browse view
     */
    public static $browseJoins = [
        'aluno.pessoa',
        'livro_biblioteca_exemplar.livro_biblioteca',
    ];

    /**
     * Joins in the update view
     */
    public static $updateJoins = [
        'franqueada',
        'aluno.pessoa',
        'livro_biblioteca_exemplar.livro_biblioteca',
    ];

    /**
     * Filters in browse view
     */
    public static $filters = [
        'quick'    => [
            [
                'field'         => 'livro_biblioteca_exemplar.codigo',
                'criteria'      => 'LIKE',
                'pattern'       => '^&value^',
                'formViewClass' => 'col-md-2',
            ],
            [
                'field'         => 'aluno',
                'criteria'      => '=',
                'formViewClass' => 'col-md-3',
                'with'          => [ 'pessoa.franqueadas' ],
                'where'         => [
                    [
                        'field'    => 'franqueadas.id',
                        'criteria' => '=',
                        'value'    => '$CURRENT_FRANCHISE',
                    ],
                ],
            ],
            [
                'field'         => 'livro_biblioteca_exemplar.livro_biblioteca.nome',
                'criteria'      => 'LIKE',
                'pattern'       => '^&value^',
                'formViewClass' => 'col-md-3',
            ],
            [
                'field'         => 'data_devolucao',
                'criteria'      => '=',
                'formViewClass' => 'col-md-2',
            ],
            [
                'field'    => 'devolvido',
                'criteria' => 'IN',
            ],
        ],
        'advanced' => [],
    ];

    /**
     * Custom onCreate function
     * Is called right before flush
     */
    public static function onCreate ($instance, $manager)
    {
        return self::validarCriacaoEAtualizacao($instance, $manager);
    }

    /**
     * Custom onUpdate function
     * Is called right before flush
     */
    public static function onUpdate ($instance, $manager)
    {
        return self::validarCriacaoEAtualizacao($instance, $manager);
    }

    /**
     * Função para validar se os códigos de exemplares já são usados
     */
    private static function validarCriacaoEAtualizacao ($instance, $manager)
    {
        $result = [ 'errors' => [] ];

        $exemplares = $instance->getLivroBibliotecaExemplar();
        $exemplaresNesteEmprestimo = [];

        foreach ($exemplares as $exemplar) {
            $id       = $exemplar->getId();
            $codigo   = $exemplar->getCodigo();
            $findCopy = $manager->getRepository('\App\Entity\Principal\EmprestimoBiblioteca')->buscarEmprestimosAbertosComOExemplar($exemplar);

            if (in_array($id, $exemplaresNesteEmprestimo) === true || (is_null($findCopy) === false && $instance->getDevolvido() === false && $findCopy !== $instance)) {
                $result['errors'][] = "O exemplar \"$codigo\" está emprestado";
            }

            $exemplaresNesteEmprestimo[] = $id;
        }

        if (is_null($instance->getDataDevolucao()) === false && $instance->getDataEmprestimo() > $instance->getDataDevolucao()) {
            $result['errors'][] = "A data de devolução não pode ser maior que a de empréstimo";
        }

        return $result;
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Aluno", inversedBy="emprestimoBibliotecas")
     * @ORM\JoinColumn(nullable=false)
     * (label="Aluno", showOnBrowse="true", showOnCreate="true", showOnUpdate="true", listViewOrder="1", formViewClass="col-md-6", formViewOrder="1", required="true", findType="typeahead", queryColumn="pessoa.nome_contato", valueColumn="id", descriptionColumn="pessoa.nome_contato")
     */
    private $aluno;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Principal\LivroBibliotecaExemplar", inversedBy="emprestimoBibliotecas")
     * (label="Exemplares", showOnBrowse="true", showOnCreate="true", showOnUpdate="true", listViewOrder="2", formViewOrder="1", formViewBreakAfter="true", required="true", valueColumn="id", descriptionColumn="codigo,livro_biblioteca.nome", queryColumn="codigo,livro_biblioteca.nome", manyToManyTableColumns="codigo,livro_biblioteca.nome")
     */
    private $livro_biblioteca_exemplar;

    /**
     * @ORM\Column(type="datetime")
     * (label="Data de Empréstimo", showOnCreate="true", showOnUpdate="true", formViewOrder="3", required="true", format="date", formViewClass="col-md-2")
     */
    private $data_emprestimo;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * (label="Data de Devolução", showOnBrowse="true", showOnCreate="true", showOnUpdate="true", listViewOrder="3", formViewOrder="3", format="date", formViewClass="col-md-2")
     */
    private $data_devolucao;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     * (label="Devolvido", showOnBrowse="true", showOnCreate="true", showOnUpdate="true", listViewOrder="4", formViewOrder="4", formViewClass="col-auto", selectOptions="[{ 'value': true, 'text': 'Sim' }, { 'value': false, 'text': 'Não' }]")
     */
    private $devolvido = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="emprestimoBibliotecas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    public function __construct()
    {
        $this->livro_biblioteca_exemplar = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAluno(): ?Aluno
    {
        return $this->aluno;
    }

    public function setAluno(?Aluno $aluno): self
    {
        $this->aluno = $aluno;

        return $this;
    }

    /**
     * @return Collection|LivroBibliotecaExemplar[]
     */
    public function getLivroBibliotecaExemplar(): Collection
    {
        return $this->livro_biblioteca_exemplar;
    }

    public function addLivroBibliotecaExemplar(LivroBibliotecaExemplar $livroBibliotecaExemplar): self
    {
        if ($this->livro_biblioteca_exemplar->contains($livroBibliotecaExemplar) === false) {
            $this->livro_biblioteca_exemplar[] = $livroBibliotecaExemplar;
        }

        return $this;
    }

    public function removeLivroBibliotecaExemplar(LivroBibliotecaExemplar $livroBibliotecaExemplar): self
    {
        if ($this->livro_biblioteca_exemplar->contains($livroBibliotecaExemplar) === true) {
            $this->livro_biblioteca_exemplar->removeElement($livroBibliotecaExemplar);
        }

        return $this;
    }

    public function getDataEmprestimo(): ?\DateTimeInterface
    {
        return $this->data_emprestimo;
    }

    public function setDataEmprestimo(\DateTimeInterface $data_emprestimo): self
    {
        $this->data_emprestimo = $data_emprestimo;

        return $this;
    }

    public function getDataDevolucao(): ?\DateTimeInterface
    {
        return $this->data_devolucao;
    }

    public function setDataDevolucao(?\DateTimeInterface $data_devolucao): self
    {
        $this->data_devolucao = $data_devolucao;

        return $this;
    }

    public function getDevolvido(): ?bool
    {
        return $this->devolvido;
    }

    public function setDevolvido(bool $devolvido): self
    {
        $this->devolvido = $devolvido;

        return $this;
    }

    public function getFranqueada(): ?Franqueada
    {
        return $this->franqueada;
    }

    public function setFranqueada(?Franqueada $franqueada): self
    {
        $this->franqueada = $franqueada;

        return $this;
    }


}
