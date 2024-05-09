<?php
namespace App\Tests\BO;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\BO\Principal\PessoaBO;
use App\Helper\FunctionHelper;
use App\Helper\ConstanteParametros;

/**
 *
 * @author Luiz Antonio Costa
 */
class PessoaBOTest extends KernelTestCase
{

    /**
     *
     * @var \App\BO\Principal\PessoaBO
     */
    private $pessoaBO;

    protected function setUp()
    {
        $kernel         = self::bootKernel();
        $this->pessoaBO = new PessoaBO($kernel->getContainer()->get('doctrine')->getManager());
    }

    public function testCpfValido()
    {
        $cpfFormatado = "519.661.627-66";
        $cpf          = "51966162766";
        $mensagemErro = "";
        $this->assertTrue($this->pessoaBO->isCgcValido($cpfFormatado, $mensagemErro, true) === true, FunctionHelper::mostrarTextoUnitVermelho("O CPF(FORMATADO) NAO EH VALIDO\nMSG DO BO:" . $mensagemErro));
        $this->assertTrue($this->pessoaBO->isCgcValido($cpf, $mensagemErro, true) === true, FunctionHelper::mostrarTextoUnitVermelho("O CPF(NUMERO) NAO EH VALIDO\nMSG DO BO:" . $mensagemErro));
    }

    public function testCpfInvalido()
    {
        $cpfFormatado = "519.661.627-00";
        $cpf          = "51966162700";
        $mensagemErro = "";
        $this->assertFalse($this->pessoaBO->isCgcValido($cpfFormatado, $mensagemErro, true) === true, FunctionHelper::mostrarTextoUnitVermelho("O CPF(FORMATADO) EH VALIDO\nMSG DO BO:" . $mensagemErro));
        $this->assertFalse($this->pessoaBO->isCgcValido($cpf, $mensagemErro, true) === true, FunctionHelper::mostrarTextoUnitVermelho("O CPF(NUMERO) EH VALIDO\nMSG DO BO:" . $mensagemErro));
    }

    public function testCpfSequencialDigitado()
    {
        $cpfFormatado = "111.111.111-11";
        $cpf          = "11111111111";
        $mensagemErro = "";
        $this->assertFalse($this->pessoaBO->isCgcValido($cpfFormatado, $mensagemErro, true) === true, FunctionHelper::mostrarTextoUnitVermelho("O SEQUENCIAL EH VALIDO\nMSG DO BO:" . $mensagemErro));
        $this->assertFalse($this->pessoaBO->isCgcValido($cpf, $mensagemErro, true) === true, FunctionHelper::mostrarTextoUnitVermelho("O SEQUENCIAL NAO EH VALIDO\nMSG DO BO:" . $mensagemErro));
    }

    public function testIsCpf()
    {
        $cpf = "11111111111";
        $this->assertTrue($this->pessoaBO->isCpf($cpf) === true, FunctionHelper::mostrarTextoUnitVermelho("Falha no teste de verificacao se eh CPF(O VALOR INFORMADO NAO EH UM CPF)"));
    }

    public function testIsNotCpf()
    {
        $cpf = "111111111111";
        $this->assertFalse($this->pessoaBO->isCpf($cpf) === true, FunctionHelper::mostrarTextoUnitVermelho("Falha no teste de verificacao se eh CPF(O VALOR INFORMADO EH UM CPF)"));
    }

    public function testCnpjValido()
    {
        $cnpjFormatado = "29.289.173/0001-53";
        $cnpj          = "29289173000153";
        $mensagemErro  = "";
        $this->assertTrue($this->pessoaBO->isCgcValido($cnpjFormatado, $mensagemErro) === true, FunctionHelper::mostrarTextoUnitVermelho("O CNPJ(FORMATADO) NAO EH VALIDO\nMSG DO BO:" . $mensagemErro));
        $this->assertTrue($this->pessoaBO->isCgcValido($cnpj, $mensagemErro) === true, FunctionHelper::mostrarTextoUnitVermelho("O CNPJ(NUMERO) NAO EH VALIDO\nMSG DO BO:" . $mensagemErro));
    }

    public function testCnpjInvalido()
    {
        $cnpjFormatado = "29.289.173/0001-52";
        $cnpj          = "29289173000152";
        $mensagemErro  = "";
        $this->assertFalse($this->pessoaBO->isCgcValido($cnpjFormatado, $mensagemErro) === true, FunctionHelper::mostrarTextoUnitVermelho("O CNPJ(FORMATADO) EH VALIDO\nMSG DO BO:" . $mensagemErro));
        $this->assertFalse($this->pessoaBO->isCgcValido($cnpj, $mensagemErro) === true, FunctionHelper::mostrarTextoUnitVermelho("O CNPJ(NUMERO) EH VALIDO\nMSG DO BO:" . $mensagemErro));
    }

    public function testCnpjSequencialDigitado()
    {
        $cnpjFormatado = "11.111.111/1111-11";
        $cnpj          = "11111111111111";
        $mensagemErro  = "";
        $this->assertFalse($this->pessoaBO->isCgcValido($cnpjFormatado, $mensagemErro) === true, FunctionHelper::mostrarTextoUnitVermelho("O SEQUENCIAL EH VALIDO\nMSG DO BO:" . $mensagemErro));
        $this->assertFalse($this->pessoaBO->isCgcValido($cnpj, $mensagemErro) === true, FunctionHelper::mostrarTextoUnitVermelho("O SEQUENCIAL NAO EH VALIDO\nMSG DO BO:" . $mensagemErro));
    }

    public function testPodeSalvarValido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_FRANQUEADA      => 1,
            ConstanteParametros::CHAVE_BANCO           => 1,
            ConstanteParametros::CHAVE_PLANO_CONTA     => 3,
            ConstanteParametros::CHAVE_DATA_NASCIMENTO => "1992-10-10T12:00:00.000Z",
        ];
        $mensagemErro = "";
        $retorno      = $this->pessoaBO->podeSalvar($parametros, $mensagemErro);

        $this->assertTrue($retorno, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste: " . $mensagemErro));
    }

    public function testPodeSalvarInvalido()
    {
        $parametros   = [
            ConstanteParametros::CHAVE_FRANQUEADA      => 999,
            ConstanteParametros::CHAVE_BANCO           => 1,
            ConstanteParametros::CHAVE_PLANO_CONTA     => 3,
            ConstanteParametros::CHAVE_DATA_NASCIMENTO => "1992-10-10T12:00:00.000Z",
        ];
        $mensagemErro = "";
        $retorno      = $this->pessoaBO->podeSalvar($parametros, $mensagemErro);

        $this->assertFalse($retorno === true, FunctionHelper::mostrarTextoUnitVermelho("Ocorreu um erro no teste"));
    }

    protected function tearDown()
    {
        $this->pessoaBO = null;
    }


}
