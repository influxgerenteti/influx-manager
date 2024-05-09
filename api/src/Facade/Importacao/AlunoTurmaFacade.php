<?php

namespace App\Facade\Importacao;


use App\BO\Principal\FranqueadaBO;
use App\Entity\Principal\Franqueada;
use App\Facade\Principal\GenericFacade;
use App\BO\Importacao\AlunoTurmaBO;
use App\Entity\Importacao\AlunoTurma;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class AlunoTurmaFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Importacao\AlunoTurmaRepository
     */
    private $alunoTurmaRepository;

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
        $this->alunoTurmaRepository    = self::getEntityManager()->getRepository(AlunoTurma::class);
        $this->alunoRepository         = self::getEntityManager()->getRepository(\App\Entity\Importacao\Aluno::class);
        $this->turmaRepository         = self::getEntityManager()->getRepository(\App\Entity\Importacao\Turma::class);
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
            AlunoTurmaBO::excluirRegistrosPorFranqueada($this->alunoTurmaRepository, self::getEntityManager(), $franqueada_id);
            AlunoTurmaBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->alunoRepository, $this->turmaRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
