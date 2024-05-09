<?php

namespace App\Facade\Principal;

use App\Facade\Principal\GenericFacade;
use Doctrine\Common\Persistence\ManagerRegistry;
use App\Helper\ConstanteParametros;
use App\Facade\Principal\BoletoFacade;

/**
 *
 * @author Rodrigo de Souza Fernandes (GATI labs)
 */
class RemessaFacade extends GenericFacade
{

    /**
     *
     * @var \App\Repository\Principal\FranqueadaRepository
     */
    private $franqueadaRepository;

    /**
     *
     * @var \App\Facade\Principal\BoletoFacade
     */
    private $boletoFacade;

    /**
     * {@inheritDoc}
     *
     * @see \App\Facade\Principal\GenericFacade::__construct()
     */
    function __construct (ManagerRegistry $managerRegistry, $connection="base_principal")
    {
        parent::__construct($managerRegistry);
        $this->franqueadaRepository = self::getEntityManager()->getRepository(\App\Entity\Principal\Franqueada::class);
        $this->boletoFacade         = new BoletoFacade($managerRegistry);
    }

    /**
     * Imprime o arquivo de remressa
     *
     * @param [] $parametros
     * @param string $mensagemErro
     *
     * @return string arquivo de remessa gerado
     */
    public function imprimirRemessa ($parametros, &$mensagemErro)
    {
        $filename = null;
        $boletos  = $this->boletoFacade->buscarBoletosORM($parametros[ConstanteParametros::CHAVE_BOLETOS]);
        if (count($boletos) === 0) {
            $mensagemErro = "Boletos não encontrados na base de dados.";
            return null;
        }

        $contaSelecionadaORM = null;
        foreach ($boletos as $boletoORM) {
            if ($boletoORM->getConta()->getBancoEmiteBoleto() === true) {
                $contaSelecionadaORM = $boletoORM->getConta();
                break;
            }
        }

        if (is_null($contaSelecionadaORM) === true) {
            $mensagemErro = "A Franqueada não possui conta com emissão de boleto.";
            return null;
        }

        $franqueadaORM = $contaSelecionadaORM->getFranqueada();
        if ((is_null($franqueadaORM->getCidade()) === true) || (is_null($franqueadaORM->getCidade()) === true)) {
            $mensagemErro = "A Franqueada não possui estado ou cidade cadastrada para geração do arquivo de remessa.";
            return null;
        }

        if ($this->boletoFacade->buscarBancoPorCodigo($contaSelecionadaORM->getBanco()->getCodigo(), $mensagemErro) === null) {
            $mensagemErro = "Este banco \"" . $contaSelecionadaORM->getBanco()->getDescricao() . "\" não está configurado no sistema para geração de arquivo de remessa.";
            return null;
        }

        $filename = $this->imprimirRemessaBanco($franqueadaORM, $contaSelecionadaORM, $boletos);
        return $filename;
    }

    /**
     * Monta o arquivo de remessa para impressão
     *
     * @param \App\Entity\Principal\Franqueada $franqueadaORM
     * @param \App\Entity\Principal\Conta $contaORM
     * @param \App\Entity\Principal\Boleto[] $boletos
     *
     * @return string $filename caminho do arquivo de remessa gerado
     */
    protected function imprimirRemessaBanco ($franqueadaORM, $contaORM, $boletos)
    {
        $codigo_banco = (int) $contaORM->getBanco()->getCodigo();

        $contaORM->setNumeroSequenciaArquivoCobranca((int) $contaORM->getNumeroSequenciaArquivoCobranca() + 1);

        $parametros = [
            "data_geracao"              => new \DateTime(),
            "data_gravacao"             => new \DateTime(),
            "nome_fantasia"             => $franqueadaORM->getNome(),
            "razao_social"              => $franqueadaORM->getRazaoSocial(),
            "cnpj"                      => $franqueadaORM->getCnpj(),
            "banco"                     => $codigo_banco,
            "logradouro"                => $franqueadaORM->getEndereco(),
            "numero"                    => $franqueadaORM->getNumeroEndereco(),
            "bairro"                    => $franqueadaORM->getBairroEndereco(),
            "cidade"                    => $franqueadaORM->getCidade()->getNome(),
            "uf"                        => $franqueadaORM->getEstado()->getSigla(),
            "cep"                       => $franqueadaORM->getCepEndereco(),
            "agencia"                   => $contaORM->getNumeroAgencia(),
            "agencia_dv"                => $contaORM->getDigitoAgencia(),
            "conta"                     => $contaORM->getContaCorrente(),
            "conta_dv"                  => $contaORM->getDigitoContaCorrente(),
            "conta_dac"                 => $contaORM->getDigitoContaCorrente(),
            "codigo_cedente_dv"         => $contaORM->getDigitoContaCorrente(),
            "codigo_carteira"           => $contaORM->getCarteira(),
            "codigo_cedente"            => $contaORM->getEmpresaNoBanco(),
            "codigo_cedente_dac"        => $contaORM->getDigitoContaCorrente(),
            "sequencial_remessa"        => $contaORM->getNumeroSequenciaArquivoCobranca(),
            "agencia_dac"               => $contaORM->getDigitoAgencia(),
            "numero_sequencial"         => $contaORM->getNumeroSequenciaArquivoCobranca(),
            "numero_sequencial_arquivo" => $contaORM->getNumeroSequenciaArquivoCobranca(),
        ];

        switch ($codigo_banco) {
            case \App\Helper\RemessaPHP\Cnab\Banco::CEF:
            $arquivo = new \App\Helper\RemessaPHP\Cnab\Remessa\Cnab400\Arquivo($codigo_banco);
            $parametros["operacao"] = "";
                break;
            case \App\Helper\RemessaPHP\Cnab\Banco::BRADESCO:
            $arquivo = new \App\Helper\RemessaPHP\Cnab\Remessa\Cnab400\Arquivo($codigo_banco);
                break;
            case \App\Helper\RemessaPHP\Cnab\Banco::BANCO_DO_BRASIL:
            $arquivo = new \App\Helper\RemessaPHP\Cnab\Remessa\Cnab400\Arquivo($codigo_banco);
            $parametros["numero_convenio"] = "";
                break;
            case \App\Helper\RemessaPHP\Cnab\Banco::SICREDI:
            $arquivo = new \App\Helper\RemessaPHP\Cnab\Remessa\Cnab240\Arquivo($codigo_banco);
            $parametros["agencia_dv"]      = "0";
            $parametros["codigo_convenio"] = "0";
            $parametros["codigo_cedente"]  = str_pad($parametros["conta"], 12, '0', STR_PAD_LEFT);
            $parametros["agencia_mais_cedente_dv"] = "";
                break;
            default:
                break;
        }//end switch

        $arquivo->configure($parametros);
        $erros = [];

        foreach ($boletos as $boletoORM) {
            $tituloReceberORM = $boletoORM->getTituloReceber();
            $sacadoPessoaORM  = $tituloReceberORM->getSacadoPessoa();
            $dataMulta        = clone $boletoORM->getDataVencimento();
            $dataMulta->modify("+1 day");

            $dataDesconto = clone $boletoORM->getDataVencimento();
            if ((int) $contaORM->getNumeroDiasDescontoAntecipado() > 0) {
                $dataDesconto->modify("-" . $contaORM->getNumeroDiasDescontoAntecipado() . " day");
            }

            $sacado_tipo = "cpf";
            if ($sacadoPessoaORM->getTipoPessoa() === "J") {
                $sacado_tipo = "cnpj";
            }

            $erroGerarBoleto = false;
            // Início das checagens do cadastro do sacado
            $sacado_nome = $sacadoPessoaORM->getNomeContato();

            $sacado_logradouro = $sacadoPessoaORM->getEndereco();
            if (is_null($sacado_logradouro) === true || empty($sacado_logradouro) === true) {
                $erroGerarBoleto = true;
                $erros[]         = [
                    "campo"  => 'Logradouro',
                    "sacado" => $sacado_nome,
                ];
            }

            $sacado_bairro = $sacadoPessoaORM->getBairroEndereco();
            if (is_null($sacado_bairro) === true || empty($sacado_bairro) === true) {
                $erroGerarBoleto = true;
                $erros[]         = [
                    "campo"  => 'Bairro',
                    "sacado" => $sacado_nome,
                ];
            }

            $sacado_cep = $sacadoPessoaORM->getCepEndereco();
            if (is_null($sacado_cep) === true || empty($sacado_cep) === true) {
                $erroGerarBoleto = true;
                $erros[]         = [
                    "campo"  => 'CEP',
                    "sacado" => $sacado_nome,
                ];
            }

            $sacado_cidade = $sacadoPessoaORM->getCidade();
            if (is_null($sacado_cidade) === true) {
                $erroGerarBoleto = true;
                $erros[]         = [
                    "campo"  => 'Cidade',
                    "sacado" => $sacado_nome,
                ];
            } else {
                $sacado_cidade = $sacado_cidade->getNome();
                if (is_null($sacado_cidade) === true || empty($sacado_cidade) === true) {
                    $erroGerarBoleto = true;
                    $erros[]         = [
                        "campo"  => 'Cidade',
                        "sacado" => $sacado_nome,
                    ];
                }
            }

            $sacado_uf = $sacadoPessoaORM->getEstado();
            if (is_null($sacado_uf) === true) {
                $erroGerarBoleto = true;
                $erros[]         = [
                    "campo"  => 'Estado',
                    "sacado" => $sacado_nome,
                ];
            } else {
                $sacado_uf = $sacado_uf->getSigla();
                if (is_null($sacado_uf) === true || empty($sacado_uf) === true) {
                    $erroGerarBoleto = true;
                    $erros[]         = [
                        "campo"  => 'Estado',
                        "sacado" => $sacado_nome,
                    ];
                }
            }

            $sacado_cpf = $this->formatCnpjCpf($sacadoPessoaORM->getCnpjCpf());
            if (is_null($sacado_cpf) === true || empty($sacado_cpf) === true) {
                $erroGerarBoleto = true;
                $erros[]         = [
                    "campo"  => 'CPF',
                    "sacado" => $sacado_nome,
                ];
            }

            if ($erroGerarBoleto === true) {
                continue;
            }

            $paramDetalhes = [
                "codigo_de_ocorrencia" => 1,
            // 1 = Entrada de título, futuramente poderemos ter uma constante
                "nosso_numero"         => $boletoORM->getNossoNumero(),
                "numero_documento"     => $boletoORM->getNossoNumero(),
                "carteira"             => $contaORM->getCarteira(),
                "especie"              => "99",
            // Outros
            // Você pode consultar as especies Cnab\Especie
                "valor"                => $boletoORM->getValor(),
                "instrucao1"           => $contaORM->getPrimeiraInstrucao(),
                "instrucao2"           => $contaORM->getSegundaInstrucao(),
                "sacado_nome"          => $sacado_nome,
                "sacado_tipo"          => $sacado_tipo,
                "sacado_cpf"           => $sacado_cpf,
                "sacado_logradouro"    => $sacado_logradouro,
                "sacado_bairro"        => $sacado_bairro,
                "sacado_cep"           => $sacado_cep,
                "sacado_cidade"        => $sacado_cidade,
                "sacado_uf"            => $sacado_uf,
                "data_vencimento"      => $boletoORM->getDataVencimento(),
                "data_cadastro"        => $tituloReceberORM->getDataEmissao(),
                "juros_de_um_dia"      => ($boletoORM->getValor() * (float) $tituloReceberORM->getTaxaJuroDia()) / 100,
                "data_desconto"        => $dataDesconto,
                "valor_desconto"       => ($boletoORM->getValor() * (float) $contaORM->getPercentualDescontoAntecipado()) / 100,
                "prazo"                => (int) $contaORM->getNumeroDiasMaxPagamentoAposVencimento(),
            // prazo de dias para o cliente pagar após o vencimento
                "taxa_de_permanencia"  => "0",
            // 00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
                "mensagem"             => $contaORM->getObservacaoBoleto(),
                "data_multa"           => $dataMulta,
                "valor_multa"          => ($boletoORM->getValor() * (float) $tituloReceberORM->getTaxaMulta()) / 100,
            ];

            $nosso_numero_compl = str_pad($contaORM->getCarteira(), 2, "0", STR_PAD_LEFT) . str_pad($boletoORM->getNossoNumero(), 11, "0", STR_PAD_LEFT);

            switch ($codigo_banco) {
                case \App\Helper\RemessaPHP\Cnab\Banco::BRADESCO:
                $paramDetalhes["digito_nosso_numero"] = $this->digitoVerificadorNossoNumeroBradesco($nosso_numero_compl);
                    break;
                case \App\Helper\RemessaPHP\Cnab\Banco::SICREDI:
                $paramDetalhes["digito_nosso_numero"] = $this->digitoVerificadorNossoNumeroSicredi($nosso_numero_compl);
                // Conforme PDF enviado com correções em 26/05/21, a Sicredi só aceita o valor da taxa de multa em percentuais
                $paramDetalhes["valor_multa"] = $tituloReceberORM->getTaxaMulta();
                $paramDetalhes["agencia"]     = $parametros["agencia"];
                $paramDetalhes["conta"]       = $parametros["conta"];
                    break;
                default:
                    break;
            }

            // você pode adicionar vários boletos em uma remessa
            $arquivo->insertDetalhe($paramDetalhes);
            $boletoORM->setSituacaoCobranca('ENV');
        }//end foreach

        /*
         * TODO: A confirmar com o Juscelino e o pessoal da influx onde deveria barrar a questão da validação do endereço,
         *       é bom ter aqui p/ garantir mas acho que tem que ter também na geração de boletos pra mostrar o endereço no boleto
         */

        if (count($erros) > 0) {
            echo "Erro ao gerar remessa. Após corrigir os erros abaixo, esta tela pode ser atualizada para gerar novamente a remessa.<br><br>";

            $erros_filtrados = [];
            foreach ($erros as $erro) {
                $ja_esta_no_array = false;
                foreach ($erros_filtrados as $filtrado) {
                    if ($erro["campo"] === $filtrado["campo"] && $erro["sacado"] === $filtrado["sacado"]) {
                        $ja_esta_no_array = true;
                    }
                }

                if ($ja_esta_no_array === false) {
                    $erros_filtrados[] = $erro;
                }
            }

            foreach ($erros_filtrados as $erro) {
                echo $erro["campo"] . " do sacado " . $erro["sacado"] . " não está cadastrado.<br>";
            }

            die();
        }//end if

        self::flushSeguro($mensagemErro);

        // Geração do nome do arquivo de acordo com o banco
        if ($codigo_banco === \App\Helper\RemessaPHP\Cnab\Banco::SICREDI) {
            $caminho = "uploads/";

            $dia = date('d');
            $mes = date('n');
            switch ($mes) {
                case '10':
                $mes = 'O';
                    break;
                case '11':
                $mes = 'N';
                    break;
                case '12':
                $mes = 'D';
                    break;
            }

            $filename = $contaORM->getContaCorrente() . $mes . $dia;
            $extensao = 'crm';

            $filename = "$caminho$filename.$extensao";
        } else {
            $filename = "uploads/CB" . date("md") . "01.REM";
        }//end if

        $arquivo->save($filename);
        return $filename;
    }

    /**
     * Formata o CNPJ/CPF
     *
     * @param string $cnpj_cpf
     *
     * @return string Cnpj/Cpf formatado
     */
    protected function formatCnpjCpf ($cnpj_cpf)
    {
        $cnpj_cpf = preg_replace("/\D/", '', $cnpj_cpf);
        if (strlen($cnpj_cpf) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }

    /**
     * Calcular digito nosso número
     *
     * @param string $numero Nosso número
     *
     * @return string $dv Digitado verificador Nosso Número
     */
    protected function digitoVerificadorNossoNumeroBradesco($numero)
    {
        $resto  = $this->modulo11Bradesco($numero, 7, 1);
        $digito = 11 - $resto;
        $dv     = "";
        if ($digito === 10) {
            $dv = "P";
        } else if ($digito === 11) {
            $dv = 0;
        } else {
            $dv = $digito;
        }

        return $dv;
    }

    /**
     * Formata o CNPJ/CPF
     *
     * @param string $numero Nosso número
     * @param string $base Base de cálculo do digito
     * @param string $vResto Verifica e retorna o Resto da divisão
     *
     * @return string $digito Retorna o resto da visão
     */
    protected function modulo11Bradesco($numero, $base=9, $vResto=0)
    {
        $soma    = 0;
        $fator   = 2;
        $numeros = [];
        $parcial = [];
        $digito  = "";

        for ($i = strlen($numero); $i > 0; $i--) {
            $numeros[$i] = substr($numero, $i - 1, 1);
            $parcial[$i] = $numeros[$i] * $fator;
            $soma       += $parcial[$i];
            if ($fator === $base) {
                $fator = 1;
            }

            $fator++;
        }

        if ($vResto === 0) {
            $soma  *= 10;
            $digito = $soma % 11;
            if ($digito === 10) {
                $digito = 0;
            }
        } else {
            $digito = $soma % 11;
        }

        return $digito;
    }



    /**
     * Calcular digito nosso número
     *
     * @param string $numero Nosso número
     *
     * @return string $dv Digitado verificador Nosso Número
     */
    protected function digitoVerificadorNossoNumeroSicredi($numero)
    {
        // TODO: Fazer de acordo com a documentação
        $resto  = $this->modulo11Sicredi($numero, 7, 1);
        $digito = 11 - $resto;
        $dv     = "";
        if ($digito === 10) {
            $dv = "P";
        } else if ($digito === 11) {
            $dv = 0;
        } else {
            $dv = $digito;
        }

        return $dv;
    }

    /**
     * Calcula o valor do módulo 11
     *
     * @param string $numero Nosso número
     * @param string $base Base de cálculo do digito
     * @param string $vResto Verifica e retorna o Resto da divisão
     *
     * @return string $digito Retorna o resto da visão
     */
    protected function modulo11Sicredi($numero, $base=9, $vResto=0)
    {
        // TODO: Fazer de acordo com a documentação
        $soma    = 0;
        $fator   = 2;
        $numeros = [];
        $parcial = [];
        $digito  = "";

        for ($i = strlen($numero); $i > 0; $i--) {
            $numeros[$i] = substr($numero, $i - 1, 1);
            $parcial[$i] = $numeros[$i] * $fator;
            $soma       += $parcial[$i];
            if ($fator === $base) {
                $fator = 1;
            }

            $fator++;
        }

        if ($vResto === 0) {
            $soma  *= 10;
            $digito = $soma % 11;
            if ($digito === 10) {
                $digito = 0;
            }
        } else {
            $digito = $soma % 11;
        }

        return $digito;
    }


}
