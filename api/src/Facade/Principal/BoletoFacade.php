<?php

namespace App\Facade\Principal;

use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Helper\SituacoesSistema;
use App\BO\Principal\BoletoBO;

/**
 *
 * @author Luiz A Costa
 */
class BoletoFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\BoletoRepository
     */
    private $boletoRepository;

    /**
     *
     * @var \App\BO\Principal\BoletoBO
     */
    private $boletoBO;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->boletoRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Boleto::class);
        $this->boletoBO         = new BoletoBO(self::getEntityManager());
    }


    /**
     * Lista todos os registros do banco de dados
     *
     * @param array $parametros Parametros da requisicao
     *
     * @return array
     */
    public function listar($parametros)
    {
        $retornoRepositorio = $this->boletoRepository->filtrarBoletoPorPagina($parametros);
        $retorno            = [
            ConstanteParametros::CHAVE_TOTAL => $retornoRepositorio->getTotalItemCount(),
            ConstanteParametros::CHAVE_ITENS => $retornoRepositorio->getItems(),
        ];
        return $retorno;
    }

    /**
     * Busca um boleto conforme propriedades
     *
     * @param array $propriedades
     *
     * @return \App\Entity\Principal\Boleto
     */
    public function buscarPorPropriedades ($propriedades)
    {
        return $this->boletoRepository->findOneBy($propriedades);
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     *
     * @return array
     */
    public function buscarPorId(&$mensagemErro, $id)
    {
        $objetoORM = $this->boletoRepository->buscarRegistroPorId($id);
        if (empty($objetoORM) === true) {
            $mensagemErro = "Boleto não encontrado na base de dados.";
        }

        return $objetoORM;
    }

    /**
     * Busca o registro pela chave primaria
     *
     * @param array $ids Chaves primarias dos registros
     *
     * @return array
     */
    public function buscarBoletosPorIds($ids)
    {
        $objetos = $this->boletoRepository->buscarBoletosPorIds($ids);
        if (empty($objetos) === true) {
            $mensagemErro = "Boletos não encontrado na base de dados.";
        }

        return $objetos;
    }

    /**
     * Cria um objeto no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param array $parametros Parametros a serem inclusos no objeto
     * @param boolean $persistFlush Flag para realizar o persist e flush
     *
     * @return mixed|null|\App\Entity\Principal\Boleto
     */
    public function criar(&$mensagemErro, $parametros=[], $persistFlush=true)
    {
        $objetoORM = null;
        $parametros[ConstanteParametros::CHAVE_SITUACAO_COBRANCA] = SituacoesSistema::SITUACAO_PENDENTE;

        if ($this->boletoBO->podeSalvar($parametros, $mensagemErro) === true) {
            $objetoORM = \App\Factory\GeneralORMFactory::criar(\App\Entity\Principal\Boleto::class, true, $parametros);
            if ($persistFlush === true) {
                self::criarRegistro($objetoORM, $mensagemErro);
            } else {
                self::persistSeguro($objetoORM, $mensagemErro);
            }
        }

        return $objetoORM;
    }

    /**
     * Atualiza um registro no banco de dados
     *
     * @param string $mensagemErro Mensagem que ira retornar para front-end
     * @param int $id Chave primaria do registro
     * @param array $parametros Campos e valores que iram ser atualizados
     *
     * @return boolean
     */
    public function atualizar(&$mensagemErro, $id, $parametros=[])
    {
        $objetoORM = $this->boletoRepository->find($id);
        if (is_null($objetoORM) === true) {
            $mensagemErro = "Boleto não encontrado na base de dados.";
        } else {
            $objetoORM->setSituacaoCobranca($parametros[ConstanteParametros::CHAVE_SITUACAO_COBRANCA]);
            self::flushSeguro($mensagemErro);
        }

        return empty($mensagemErro);
    }

    /**
     * Imprime o boleto
     *
     * @param int $boleto
     * @param string $mensagemErro
     *
     * @return string HTML do boleto gerado
     */
    public function imprimirBoleto ($boleto, &$mensagemErro, &$host='')
    {
        $boleto = $this->boletoRepository->find($boleto);

        if (is_null($boleto) === true) {
            $mensagemErro = 'Boleto não encontrado na base de dados.';
            return false;
        }

        $boletoHTML = '';
        $parametros = [
            'data_documento'         => null,
            'data_vencimento'        => null,
            'valor_saldo'            => null,
            'nosso_numero'           => null,
            'carteira'               => null,
            'variacao_carteira'      => null,
            'conta'                  => null,
            'digito_conta'           => null,
            'agencia'                => null,
            'digito_agencia'         => null,
            'convenio'               => null,
            'numero_documento'       => null,
            'sacado_nome'            => null,
            'sacado_cpf_cnpj'        => null,
            'sacado_endereco'        => null,
            'sacado_endereco_numero' => null,
            'sacado_bairro'          => null,
            'sacado_cidade'          => null,
            'sacado_estado'          => null,
            'sacado_cep'             => null,
            'cedente_nome'           => null,
            'cedente_cnpj'           => null,
            'cedente_endereco'       => null,
            'instrucoes'             => null,
            'aceite'                 => null,
            'especie'                => null,
            'especie_doc'            => null,
            'especie_doc2'           => null,
            'codigo_barras'          => null,
            'linha_digitavel'        => null,
            'agencia_conta'          => null,
            'codigo_banco_com_dv'    => null,
        ];

        $conta         = $boleto->getConta();
        $codigoBanco   = $conta->getBanco()->getCodigo();
        $tituloReceber = $boleto->getTituloReceber();
        $aluno         = $tituloReceber->getAluno();
        $franqueada    = $boleto->getFranqueada();
        $contrato      = $tituloReceber->getContaReceber()->getContrato();
        if (is_null($aluno) === true) {
            $responsavelFinanceiro = $tituloReceber->getSacadoPessoa();
        } else {
            $responsavelFinanceiro = $aluno->getResponsavelFinanceiroPessoa();
            if (is_null($responsavelFinanceiro) === true) {
                $responsavelFinanceiro = $aluno->getPessoa();
            }
        }

        $cidade = null;
        if (is_null($responsavelFinanceiro->getCidade()) === false) {
            $cidade = $responsavelFinanceiro->getCidade()->getNome();
        }

        $estado = null;
        if (is_null($responsavelFinanceiro->getEstado()) === false) {
            $estado = $responsavelFinanceiro->getEstado()->getSigla();
        }

        $data_documento = $tituloReceber->getDataEmissao();
        if (is_null($data_documento) === true) {
            $data_documento = $tituloReceber->getContaReceber()->getDataEmissao();
        }

        $parametros['carteira']          = $conta->getCarteira();
        $parametros['variacao_carteira'] = $conta->getVariacaoCarteira();
        $parametros['conta']            = $conta->getContaCorrente();
        $parametros['digito_conta']     = $conta->getDigitoContaCorrente();
        $parametros['agencia']          = $conta->getNumeroAgencia();
        $parametros['digito_agencia']   = $conta->getDigitoAgencia();
        $parametros['convenio']         = $conta->getConvenio();
        $parametros['data_documento']   = $data_documento;
        $parametros['data_vencimento']  = $tituloReceber->getDataVencimento();
        $parametros['valor_saldo']      = str_replace(".", ",", $tituloReceber->getValorSaldoDevedor());
        $parametros['nosso_numero']     = $boleto->getNossoNumero();
        $parametros['numero_documento'] = $boleto->getNossoNumero();
        $parametros['sacado_nome']      = $responsavelFinanceiro->getNomeContato();
        $parametros['sacado_cpf_cnpj']  = $responsavelFinanceiro->getCnpjCpf();
        $parametros['sacado_endereco']  = $responsavelFinanceiro->getEndereco();
        $parametros['sacado_endereco_numero'] = $responsavelFinanceiro->getNumeroEndereco();
        $parametros['sacado_bairro']          = $responsavelFinanceiro->getBairroEndereco();
        $parametros['sacado_cidade']          = $cidade;
        $parametros['sacado_estado']          = $estado;
        $parametros['sacado_cep']       = $responsavelFinanceiro->getCepEndereco();
        $parametros['cedente_nome']     = $franqueada->getNome();
        $parametros['cedente_cnpj']     = $franqueada->getCnpj();
        $parametros['cedente_endereco'] = $franqueada->getEndereco();
        $instrucoes = $conta->getObservacaoBoleto();
        $parametros['instrucoes1']  = $conta->getTextoMoraDiaria() . ' ' . str_replace(".", ",", $conta->getTaxaJuroDia()) . '%';
        $parametros['instrucoes2']  = $conta->getTextoMultaAtraso() . ' ' . str_replace(".", ",", $conta->getTaxaMulta()) . '%';
        $parametros['instrucoes3']  = "";
        $parametros['aceite']       = "N";
        $parametros['especie']      = "R$";
        $parametros['especie_doc']  = "DM";
        $parametros['especie_doc2'] = "DMI";
        $parametros['instrucoesDescontoAntecipacao'] = "";
        $parametros['maxDiasAposVencimento']         = "";
        $parametros['observacao'] = "";
        $parametros['instrucoes'] = '';
        $maxDiasAposVencimento    = $conta->getNumeroDiasMaxPagamentoAposVencimento();
        if (empty($maxDiasAposVencimento) === false) {
            $parametros['maxDiasAposVencimento'] = "Não aceitar o pagamento após " . $maxDiasAposVencimento . " dias do vencimento.<br>";
        }

        $descontoAntecipacao = $tituloReceber->getDescontoAntecipacao();
        if (empty($descontoAntecipacao) === true) {
            $descontoAntecipacao = 0;
        }

        $descontoSuperAmigo = $tituloReceber->getValorDescontoSuperAmigo();
        if (empty($descontoSuperAmigo) === true) {
            $descontoSuperAmigo = 0;
        }

        $descontos = $descontoSuperAmigo + $descontoAntecipacao;
        $dataVencimentoFormatada = $parametros["data_vencimento"]->format('d/m/Y');
        if ($descontos > 0) {
            $stringDesconto = 'R$ ' . str_replace(".", ",", number_format($descontos, 2));
            $parametros['instrucoesDescontoAntecipacao'] = "Conceder desconto de $stringDesconto se pago até o dia $dataVencimentoFormatada.<br>";
        }

        if (empty($instrucoes) === false && empty(trim($instrucoes)) === false) {
            $parametros['instrucoes'] = $instrucoes . "<br>";
        }

        $observacao = $tituloReceber->getObservacao();
        if (empty($observacao) === false) {
            $parametros["observacao"] = $observacao . "<br>";
        }

        $banco = $this->buscarBancoPorCodigo($codigoBanco, $mensagemErro);

        if ($banco !== null) {
            $boletoHTML = $this->imprimirBoletoBanco($parametros, $banco, $host);
        }

        return $boletoHTML;
    }

    /**
     * Compara o número do banco para identificar o layout
     *
     * @param string $codigoBanco
     * @param string $mensagemErro
     *
     * @return string|null
     */
    public function buscarBancoPorCodigo ($codigoBanco, &$mensagemErro)
    {
        $banco = null;

        switch ($codigoBanco) {
            case '001':
            $banco = 'bb';
                break;
            case '104':
            $banco = 'cef';
                break;
            case '237':
            $banco = 'bradesco';
                break;
            case '341':
            $banco = 'itau';
                break;
            case '748':
            $banco = 'sicredi';
                break;
            default:
            $mensagemErro = "Código " . $codigoBanco . " não representa um banco homologado para a geração de boletos.";
                break;
        }

        return $banco;
    }

    /**
     * Imprime o boleto do banco selecionado
     *
     * @param array $parametros
     * @param string $banco nome do banco
     *
     * @return string HTML do boleto gerado
     */
    private function imprimirBoletoBanco ($parametros, $banco, &$host='')
    {
        ob_start();
        $path = getcwd();

        // $headers = apache_request_headers();
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            }
        }

        if (isset($headers['X-Original-Host']) === true) {
            $host = $headers['X-Original-Host'];
        } else {
            $host = $_SERVER['HTTP_HOST'];
        }

        if (($this->detectOS() === 'LINUX') || $this->detectOS() === 'DARWIN') {
            $path = "$path/../src/Helper/BoletoPHP/boleto_$banco.php";
        } else {
            $path = "$path\\..\\src\\Helper\\BoletoPHP\\boleto_$banco.php";
        }

        include $path;
        return ob_get_clean();
    }

    /**
     * Detecta o sistema operacional do servidor para includes
     *
     * @return string nome do OS
     */
    private function detectOS ()
    {
        return strtoupper(PHP_OS);
    }

    /**
     * Retorna lista de boletos
     *
     * @param array $arrayId
     *
     * @return \App\Entity\Principal\Boleto[]
     */
    public function buscarBoletosORM($arrayId)
    {
        return $this->boletoRepository->buscarTodosBoletosORM($arrayId);
    }

    /**
     * Retorna nosso número do boleto da Sicredi
     *
     * @param string $agencia
     * @param string $conta
     * @param string $nosso_numero
     *
     * @return string
     */
    public static function geraNossoNumeroSicredi($agencia, $conta, $nosso_numero)
    {
        $byte_idt = \App\Helper\RemessaPHP\Cnab\Banco::BYTE_ID_SICREDI;
        $agencia  = self::formata_numero($agencia, 4, 0);
        $posto    = self::formata_numero(\App\Helper\RemessaPHP\Cnab\Banco::POSTO_SICREDI, 2, 0);
        $conta    = self::formata_numero($conta, 5, 0);
        // Byte de identificacao do cedente do bloqueto utilizado para compor o nosso numero.
                                          // 1 - Idtf emitente: Cooperativa | 2 a 9 - Idtf emitente: Cedente
        $inicio_nosso_numero = date("y");
        // Ano da geracao do titulo ex: 07 para 2007
        $nnum = $inicio_nosso_numero . $byte_idt . self::formata_numero($nosso_numero, 5, 0);

        // calculo do DV do nosso número
        $dv_nosso_numero = self::digitoVerificadorNossoNumeroSicredi("$agencia$posto$conta$nnum");

        $nossonumero = "$nnum$dv_nosso_numero";

        return $nossonumero;
    }

    private static function digitoVerificadorNossoNumeroSicredi($numero)
    {

        $resto2 = self::sicrediModulo11($numero, 9, 1);
        // esta rotina sofrer algumas alterações para ajustar no layout do SICREDI
        $digito = 11 - $resto2;
        if ($digito > 9) {
            $dv = 0;
        } else {
            $dv = $digito;
        }

        return $dv;
    }

    private static function sicrediModulo11($num, $base=9, $r=0)
    {
        {

            $soma  = 0;
            $fator = 2;

            for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num, $i - 1, 1);
            $parcial[$i] = $numeros[$i] * $fator;
            $soma       += $parcial[$i];
            if ($fator === $base) {
                $fator = 1;
            }

            $fator++;
            }

            if ($r === 0) {
            $soma  *= 10;
            $digito = $soma % 11;
            return $digito;
            } else if ($r === 1) {
            $r_div  = (int) ($soma / 11);
            $digito = ($soma - ($r_div * 11));
            return $digito;
            }

        }
    }//end if

    public static function formata_numero($numero, $loop, $insert, $tipo="geral")
    {
        if ($tipo === "geral") {
            $numero = str_replace(",", "", $numero);
            while (strlen($numero) < $loop) {
                $numero = $insert . $numero;
            }
        }

        if ($tipo === "valor") {
            $numero = str_replace(",", "", $numero);
            while (strlen($numero) < $loop) {
                $numero = $insert . $numero;
            }
        }

        if ($tipo === "convenio") {
            while (strlen($numero) < $loop) {
                $numero = $numero . $insert;
            }
        }

        return $numero;
    }

    /**
     * Retorna nosso número do boleto da Sicredi formatado
     *
     * @param string $nossoNumero
     *
     * @return string
     */
    public static function formataNossoNumeroSicredi($nossoNumero)
    {
        $nossoNumero = substr($nossoNumero, 0, 2) . '/' . substr($nossoNumero, 2, 6) . '-' . substr($nossoNumero, 8, 1);
        return $nossoNumero;
    }


    /**
     * Cancela o boleto passado
     *
     * @param string $mensagemErro
     * @param integer $id
     *
     * @return boolean
     */
    public function cancelar (&$mensagemErro, $id)
    {
        $boleto = $this->boletoRepository->find($id);

        if (is_null($boleto) === true) {
            $mensagemErro = "Boleto não encontrado.";
            return false;
        }

        if ($boleto->getSituacaoCobranca() === SituacoesSistema::SITUACAO_PENDENTE) {
            $boleto->setSituacaoCobranca(SituacoesSistema::SITUACAO_CANCELADO);
        }

        return empty($mensagemErro);
    }


}
