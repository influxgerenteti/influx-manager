<?php

namespace App\Facade\Importacao;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Importacao\Aluno;
use App\Entity\Importacao\Responsavel;
use App\Entity\Importacao\Funcionario;
use App\Entity\Importacao\Turma;
use App\Entity\Importacao\Curso;
use App\Entity\Importacao\Contrato;
use App\Entity\Importacao\Estagio;
use App\Entity\Importacao\ContratoAulaLivre;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Principal\Franqueada;
use App\BO\Importacao\ContratoAulaLivreBO;

/**
 *
 * @author Luiz A Costa
 */
class ContratoAulaLivreFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Importacao\ContratoAulaLivreRepository
     */
    private $contratoAulaLivreRepository;

    /**
     *
     * @var \App\Repository\Importacao\ContratoRepository
     */
    private $contratoRepository;

    /**
     *
     * @var \App\Repository\Importacao\AlunoRepository
     */
    private $alunoRepository;

    /**
     *
     * @var \App\Repository\Importacao\FuncionarioRepository
     */
    private $funcionarioRepository;

    /**
     *
     * @var \App\Repository\Importacao\ResponsavelRepository
     */
    private $responsavelRepository;

    /**
     *
     * @var \App\Repository\Importacao\TurmaRepository
     */
    private $turmaRepository;

    /**
     *
     * @var \App\Repository\Importacao\CursoRepository
     */
    private $cursoRepository;

    /**
     *
     * @var \App\Repository\Importacao\EstagioRepository
     */
    private $estagioRepository;

    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $foundationEntityManager;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry, "base_importacao");
        $this->foundationEntityManager = $managerRegistry->getManager($connection);
        $this->alunoRepository         = self::getEntityManager()->getRepository(Aluno::class);
        $this->responsavelRepository   = self::getEntityManager()->getRepository(Responsavel::class);
        $this->funcionarioRepository   = self::getEntityManager()->getRepository(Funcionario::class);
        $this->turmaRepository         = self::getEntityManager()->getRepository(Turma::class);
        $this->cursoRepository         = self::getEntityManager()->getRepository(Curso::class);
        $this->contratoRepository      = self::getEntityManager()->getRepository(Contrato::class);
        $this->estagioRepository       = self::getEntityManager()->getRepository(Estagio::class);
        $this->contratoAulaLivreRepository = self::getEntityManager()->getRepository(ContratoAulaLivre::class);
    }

    /**
     * Cria vários registros na base de dados
     *
     * @param string $mensagemErro Ponteiro de retorno de msg de erro para o front-end
     * @param integer $franqueada_id ID da franqueada
     * @param \App\Helper\XlsxHelper $xlsxHelper Helper do XLSX
     */
    public function criar(&$mensagemErro, $franqueada_id, &$xlsxHelper)
    {
        if (FranqueadaBO::franqueadaExisteBanco($this->foundationEntityManager->getRepository(Franqueada::class), ["id" => $franqueada_id]) === true) {
            ContratoAulaLivreBO::excluirRegistrosPorFranqueada($this->contratoAulaLivreRepository, self::getEntityManager(), $franqueada_id);
            ContratoAulaLivreBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->contratoRepository, $this->funcionarioRepository, $this->alunoRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
