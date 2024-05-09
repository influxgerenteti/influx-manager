<?php

namespace App\Entity\Principal;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Principal\LivroBibliotecaRepository")
 */
class LivroBiblioteca
{


    /**
     * Joins in the browse view
     */
    public static $browseJoins = [
        'idioma',
        'livroBibliotecaExemplars',
    ];

    /**
     * Joins in the update view
     */
    public static $updateJoins = [
        'franqueada',
        'idioma',
        'livroBibliotecaExemplars',
    ];

    /**
     * Filters in browse view
     */
    public static $filters = [
        'quick'    => [
            [
                'field'    => 'nome',
                'criteria' => 'LIKE',
                'pattern'  => '^&value^',
            ],
            [
                'field'    => 'autor',
                'criteria' => 'LIKE',
                'pattern'  => '^&value^',
            ],
            [
                'field'    => 'idioma',
                'criteria' => '=',
            ],
            [
                'field'    => 'nivel',
                'criteria' => '=',
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
        return self::validarCodigosExemplares($instance, $manager);
    }

    /**
     * Custom onUpdate function
     * Is called right before flush
     */
    public static function onUpdate ($instance, $manager)
    {
        return self::validarCodigosExemplares($instance, $manager);
    }

    /**
     * Função para validar se os códigos de exemplares já são usados
     */
    private static function validarCodigosExemplares ($instance, $manager)
    {
        $result = [ 'errors' => [] ];

        $exemplares        = $instance->getLivroBibliotecaExemplars();
        $codigosDesteLivro = [];

        foreach ($exemplares as $exemplar) {
            $codigo   = $exemplar->getCodigo();
            $findCopy = $manager->getRepository('\App\Entity\Principal\LivroBibliotecaExemplar')->buscarExemplarPorCodigo($codigo);

            if (in_array($codigo, $codigosDesteLivro) === true || (is_null($findCopy) === false && $findCopy->getLivroBiblioteca() !== $exemplar->getLivroBiblioteca())) {
                $result['errors'][] = "O código de exemplar \"$codigo\" já existe";
            }

            $codigosDesteLivro[] = $codigo;
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
     * @ORM\Column(type="string", length=255)
     * (label="Título da Obra", showOnBrowse="true", showOnCreate="true", showOnUpdate="true", formViewOrder="1", required="true", canOrderBy="true")
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=255)
     * (label="Nome do Autor", showOnBrowse="true", showOnCreate="true", showOnUpdate="true", formViewOrder="2", required="true", canOrderBy="true")
     */
    private $autor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Idioma")
     * @ORM\JoinColumn(nullable=false)
     * (label="Idioma", showOnBrowse="true", descriptionColumn="descricao", valueColumn="id", showOnCreate="true", showOnUpdate="true", findType="select", formViewOrder="3", required="true", canOrderBy="true", orderColumn="idioma.descricao")
     */
    private $idioma;

    /**
     * @ORM\Column(type="string", length=3, options={"comment": "(INI)ciante, (INT)ermediário e (AVA)nçado"})
     * (label="Nível", showOnBrowse="true", showOnCreate="true", showOnUpdate="true", findType="select", selectOptions="[{'value': 'INI', 'text': 'Iniciante'}, {'value': 'INT', 'text': 'Intermediário'}, {'value': 'AVA', 'text': 'Avançado'}]", valueColumn="value", descriptionColumn="text", formViewOrder="4", required="true", canOrderBy="true")
     */
    private $nivel;

    /**
     * @ORM\Column(type="text", nullable=true)
     * (label="Observação", showOnCreate="true", showOnUpdate="true", formViewClass="col-md-12", formViewOrder="6")
     */
    private $observacao;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_criacao;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Principal\LivroBibliotecaExemplar", mappedBy="livro_biblioteca", orphanRemoval=true, cascade={"persist"})
     * (label="Exemplares", showOnBrowse="true", descriptionColumn="codigo", showOnUpdate="true", showOnCreate="true", formViewOrder="5", view="form", oneToManyTableColumns="codigo")
     */
    private $livroBibliotecaExemplars;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Principal\Franqueada", inversedBy="livroBibliotecas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $franqueada;

    public function __construct()
    {
        $this->data_criacao = new \DateTime();
        $this->livroBibliotecaExemplars = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getAutor(): ?string
    {
        return $this->autor;
    }

    public function setAutor(string $autor): self
    {
        $this->autor = $autor;

        return $this;
    }

    public function getIdioma(): ?Idioma
    {
        return $this->idioma;
    }

    public function setIdioma(?Idioma $idioma): self
    {
        $this->idioma = $idioma;

        return $this;
    }

    public function getNivel(): ?string
    {
        return $this->nivel;
    }

    public function setNivel(string $nivel): self
    {
        $this->nivel = $nivel;

        return $this;
    }

    public function getObservacao(): ?string
    {
        return $this->observacao;
    }

    public function setObservacao(?string $observacao): self
    {
        $this->observacao = $observacao;

        return $this;
    }

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->data_criacao;
    }

    public function setDataCriacao(?\DateTimeInterface $data_criacao): self
    {
        $this->data_criacao = $data_criacao;

        return $this;
    }

    /**
     * @return Collection|LivroBibliotecaExemplar[]
     */
    public function getLivroBibliotecaExemplars(): Collection
    {
        return $this->livroBibliotecaExemplars;
    }

    public function addLivroBibliotecaExemplar(LivroBibliotecaExemplar $livroBibliotecaExemplar): self
    {
        if ($this->livroBibliotecaExemplars->contains($livroBibliotecaExemplar) === false) {
            $this->livroBibliotecaExemplars[] = $livroBibliotecaExemplar;
            $livroBibliotecaExemplar->setLivroBiblioteca($this);
        }

        return $this;
    }

    public function removeLivroBibliotecaExemplar(LivroBibliotecaExemplar $livroBibliotecaExemplar): self
    {
        if ($this->livroBibliotecaExemplars->contains($livroBibliotecaExemplar) === true) {
            $this->livroBibliotecaExemplars->removeElement($livroBibliotecaExemplar);
            // set the owning side to null (unless already changed)
            if ($livroBibliotecaExemplar->getLivroBiblioteca() === $this) {
                $livroBibliotecaExemplar->setLivroBiblioteca(null);
            }
        }

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
