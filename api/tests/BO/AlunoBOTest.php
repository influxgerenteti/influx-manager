<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Helper\ConstanteParametros;
use App\Helper\FunctionHelper;
use App\BO\Principal\AlunoBO;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class AlunoBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\Repository\Principal\AlunoRepository
     */
    private $alunoRepository;

    /**
     *
     * @var \App\BO\Principal\AlunoBO
     */
    private $alunoBO;

    protected function setUp()
    {
        $kernel = self::bootKernel();
        $this->alunoRepository = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(\App\Entity\Principal\Aluno::class);
        $this->alunoBO         = new AlunoBO($kernel->getContainer()->get('doctrine')->getManager());
    }

    protected function tearDown()
    {
        $this->alunoRepository = null;
        $this->alunoBO         = null;
    }

    public function testVerificaAlunoExiste()
    {
        $mensagemErro = "";
        $alunoORM     = null;
        $retorno      = $this->alunoBO->verificaAlunoExiste($this->alunoRepository, 1, $mensagemErro, $alunoORM, true);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Aluno nao encontrado na base de dados.\n" . $mensagemErro));
        $this->assertEquals(\App\Entity\Principal\Aluno::class, get_class($alunoORM), FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu algum erro no ORM, segue msg de erro:\n" . $mensagemErro));
    }

    public function testVerificaAlunoNaoExiste()
    {
        $mensagemErro = "";
        $alunoORM     = null;
        $retorno      = $this->alunoBO->verificaAlunoExiste($this->alunoRepository, 999, $mensagemErro, $alunoORM, true);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Aluno foi encontrado na base, e isto nao poderia acontecer."));
        $this->assertEquals(null, $alunoORM, FunctionHelper::mostrarTextoUnitVermelho("O Retorno de objetos nao eh o mesmo que o esperado"));
        $this->assertNotEmpty($mensagemErro, FunctionHelper::mostrarTextoUnitVermelho("O retorno esperado deste teste eh para que tenha vindo um erro de base de dados"));
    }

    public function testPodeCriarValido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_PESSOA              => 1,
            ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO => 1,
            ConstanteParametros::CHAVE_EMANCIPADO          => 0,
        ];
        $mensagemErro = "";
        \App\Helper\VariaveisCompartilhadas::$franqueadaID = 1;

        $retorno = $this->alunoBO->podeCriar($parametros, $mensagemErro);

        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Nao foi possivel prosseguir com o teste devido ao erro da funcao podeSalvar()(Error:)" . $mensagemErro));
    }

    public function testPodeCriarInvalido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_PESSOA              => 999,
            ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO => 1,
            ConstanteParametros::CHAVE_EMANCIPADO          => 0,
        ];
        $mensagemErro = "";
        $retorno      = $this->alunoBO->podeCriar($parametros, $mensagemErro);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste onde nao deveria salvar."));
    }

    public function testPodeAtualizarValido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_PESSOA              => 1,
            ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO => 1,
            ConstanteParametros::CHAVE_EMANCIPADO          => 0,
        ];
        $mensagemErro = "";
        $alunoORM     = $this->alunoRepository->find(1);
        $retorno      = $this->alunoBO->podeAtualizar($parametros, $mensagemErro, $alunoORM);
        $this->assertTrue($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Nao foi possivel prosseguir com o teste devido ao erro da funcao podeAtualizar()(Error:)" . $mensagemErro));
    }

    public function testPodeAtualizarInvalido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_PESSOA              => 999,
            ConstanteParametros::CHAVE_CLASSIFICACAO_ALUNO => 999,
            ConstanteParametros::CHAVE_EMANCIPADO          => 0,
        ];
        $mensagemErro = "";
        $alunoORM     = $this->alunoRepository->find(1);
        $retorno      = $this->alunoBO->podeAtualizar($parametros, $mensagemErro, $alunoORM);
        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste onde nao deveria salvar."));
    }


}
