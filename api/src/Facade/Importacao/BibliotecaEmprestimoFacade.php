<?php

namespace App\Facade\Importacao;


use App\Facade\Principal\GenericFacade;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Principal\Franqueada;
use App\BO\Importacao\BibliotecaEmprestimoBO;
use App\Entity\Importacao\Aluno;
use App\Entity\Importacao\BibliotecaEmprestimo;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class BibliotecaEmprestimoFacade extends GenericFacade
{
    /**
     *
     * @var \App\Repository\Importacao\BibliotecaEmprestimoRepository
     */
    private $bibliotecaEmprestimoRepository;

    /**
     *
     * @var \App\Repository\Importacao\AlunoRepository
     */
    private $alunoRepository;
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
        $this->bibliotecaEmprestimoRepository = self::getEntityManager()->getRepository(BibliotecaEmprestimo::class);
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
            BibliotecaEmprestimoBO::excluirRegistrosPorFranqueada($this->bibliotecaEmprestimoRepository, self::getEntityManager(), $franqueada_id);
            BibliotecaEmprestimoBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->alunoRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
