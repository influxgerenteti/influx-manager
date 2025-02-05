<?php

namespace App\Facade\Importacao;


use App\Facade\Principal\GenericFacade;
use App\Entity\Importacao\BibliotecaObraReserva;
use App\Entity\Importacao\BibliotecaObra;
use App\BO\Principal\FranqueadaBO;
use App\Entity\Principal\Franqueada;
use App\BO\Importacao\BibliotecaObraReservaBO;
use App\Entity\Importacao\Aluno;
use App\Entity\Importacao\Funcionario;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 *
 * @author Luiz A Costa
 */
class BibliotecaObraReservaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Importacao\BibliotecaObraReservaRepository
     */
    private $bibliotecaObraReservaRepository;

    /**
     *
     * @var \App\Repository\Importacao\BibliotecaObraRepository
     */
    private $bibliotecaObraRepository;

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
        $this->foundationEntityManager         = $managerRegistry->getManager($connection);
        $this->bibliotecaObraReservaRepository = self::getEntityManager()->getRepository(BibliotecaObraReserva::class);
        $this->bibliotecaObraRepository        = self::getEntityManager()->getRepository(BibliotecaObra::class);
        $this->alunoRepository       = self::getEntityManager()->getRepository(Aluno::class);
        $this->funcionarioRepository = self::getEntityManager()->getRepository(Funcionario::class);
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
            BibliotecaObraReservaBO::excluirRegistrosPorFranqueada($this->bibliotecaObraReservaRepository, self::getEntityManager(), $franqueada_id);
            BibliotecaObraReservaBO::criarRegistrosPorFranqueada($xlsxHelper, self::getEntityManager(), $franqueada_id, $this->alunoRepository, $this->funcionarioRepository, $this->bibliotecaObraRepository);
        } else {
            $mensagemErro = "Franqueada não encontrada na base de dados.";
        }
    }


}
