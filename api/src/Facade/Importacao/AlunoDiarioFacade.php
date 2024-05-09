<?php

namespace App\Facade\Importacao;


use App\Facade\Principal\GenericFacade;
use App\Entity\Importacao\AlunoDiario;
use App\Entity\Importacao\Aluno;
use App\Entity\Importacao\Estagio;
use App\Entity\Importacao\Funcionario;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Principal\Franqueada;
use App\BO\Importacao\AlunoDiarioBO;
use App\Entity\Importacao\Turma;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class AlunoDiarioFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Importacao\AlunoDiarioRepository
     */
    private $alunoDiarioRepository;

    /**
     *
     * @var \App\Repository\Importacao\AlunoRepository
     */
    private $alunoRepository;

    /**
     *
     * @var \App\Repository\Importacao\TurmaRepository
     */
    private $turmaRepository;

    /**
     *
     * @var \App\Repository\Importacao\EstagioRepository
     */
    private $estagioRepository;

    /**
     *
     * @var \App\Repository\Importacao\FuncionarioRepository
     */
    private $funcionarioRepository;

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
        $this->alunoDiarioRepository   = self::getEntityManager()->getRepository(AlunoDiario::class);
        $this->turmaRepository         = self::getEntityManager()->getRepository(Turma::class);
        $this->alunoRepository         = self::getEntityManager()->getRepository(Aluno::class);
        $this->estagioRepository       = self::getEntityManager()->getRepository(Estagio::class);
        $this->funcionarioRepository   = self::getEntityManager()->getRepository(Funcionario::class);
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
            AlunoDiarioBO::excluirRegistrosPorFranqueada($this->alunoDiarioRepository, self::getEntityManager(), $franqueada_id);
            AlunoDiarioBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->alunoRepository, $this->turmaRepository, $this->estagioRepository, $this->funcionarioRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
