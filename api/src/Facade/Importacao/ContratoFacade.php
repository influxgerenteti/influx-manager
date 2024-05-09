<?php

namespace App\Facade\Importacao;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Entity\Importacao\Aluno;
use App\Entity\Importacao\Responsavel;
use App\Entity\Importacao\Turma;
use App\Entity\Importacao\Funcionario;
use App\Entity\Importacao\Curso;
use App\Entity\Importacao\Estagio;
use App\Entity\Principal\Franqueada;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Importacao\Contrato;
use App\BO\Importacao\ContratoBO;

/**
 *
 * @author Luiz A Costa
 */
class ContratoFacade extends GenericFacade
{

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
            ContratoBO::excluirRegistrosPorFranqueada($this->contratoRepository, self::getEntityManager(), $franqueada_id);
            ContratoBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->alunoRepository, $this->responsavelRepository, $this->funcionarioRepository, $this->turmaRepository, $this->cursoRepository, $this->estagioRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
