<?php

namespace App\Helper\RemessaPHP\Cnab\Retorno\Cnab240;

class Detalhe extends \App\Helper\RemessaPHP\Cnab\Format\Linha implements \App\Helper\RemessaPHP\Cnab\Retorno\IDetalhe
{
    public $codigo_banco;
    public $arquivo;

    public $segmento_t;
    public $segmento_u;
    public $segmento_w;

    private $tipo_baixa           = [
        6,
        9,
        17,
        25,
    ];
    private $tipo_baixa_rejeitada = [
        3,
        26,
        30,
    ];

    public function __construct(\App\Helper\RemessaPHP\Cnab\Retorno\IArquivo $arquivo)
    {
        $this->codigo_banco = $arquivo->codigo_banco;
        $this->arquivo      = $arquivo;
    }

    /**
     * Retorno se é para dar baixa no boleto.
     *
     * @return bool
     */
    public function isBaixa()
    {
        return in_array($this->getCodigo(), $this->tipo_baixa);
    }

    /**
     * Retorno se é uma baixa rejeitada.
     *
     * @return bool
     */
    public function isBaixaRejeitada()
    {
        return in_array($this->getCodigo(), $this->tipo_baixa_rejeitada);
    }

    /**
     * Identifica o tipo de detalhe, se por exemplo uma taxa de manutenção.
     *
     * @return int
     */
    public function getCodigo()
    {
        return (int) $this->segmento_t->codigo_movimento;
    }

    /**
     * Retorna o valor recebido em conta.
     *
     * @return float
     */
    public function getValorRecebido()
    {
        return $this->segmento_u->valor_liquido;
    }

    /**
     * Retorna o valor do título.
     *
     * @return float
     */
    public function getValorTitulo()
    {
        return $this->segmento_t->valor_titulo;
    }

    /**
     * Retorna o valor do pago.
     *
     * @return float
     */
    public function getValorPago()
    {
        return $this->segmento_u->valor_pago;
    }

    /**
     * Retorna o valor da tarifa.
     *
     * @return float
     */
    public function getValorTarifa()
    {
        return $this->segmento_t->valor_tarifa;
    }

    /**
     * Retorna o valor do Imposto sobre operações financeiras.
     *
     * @return float
     */
    public function getValorIOF()
    {
        return $this->segmento_u->valor_iof;
    }

    /**
     * Retorna o valor dos descontos concedido (antes da emissão).
     *
     * @return Double;
     */
    public function getValorDesconto()
    {
        return $this->segmento_u->valor_desconto;
    }

    /**
     * Retorna o valor dos abatimentos concedidos (depois da emissão).
     *
     * @return float
     */
    public function getValorAbatimento()
    {
        return $this->segmento_u->valor_abatimento;
    }

    /**
     * Retorna o valor de outras despesas.
     *
     * @return float
     */
    public function getValorOutrasDespesas()
    {
        return $this->segmento_u->valor_outras_despesas;
    }

    /**
     * Retorna o valor de outros creditos.
     *
     * @return float
     */
    public function getValorOutrosCreditos()
    {
        return $this->segmento_u->valor_outros_creditos;
    }

    /**
     * Retorna o número do documento do boleto.
     *
     * @return string
     */
    public function getNumeroDocumento()
    {
        $numero_documento = $this->segmento_t->numero_documento;
        if (trim($numero_documento, '0') === '') {
            return;
        }

        return $numero_documento;
    }

    /**
     * Retorna o nosso número do boleto.
     *
     * @return string
     */
    public function getNossoNumero()
    {
        $nossoNumero = $this->segmento_t->nosso_numero;

        if ($this->codigo_banco === 1) {
            $nossoNumero = preg_replace(
                '/^' . strval($this->arquivo->getCodigoConvenio()) . '/',
                '',
                $nossoNumero
            );
        }

        if (in_array($this->codigo_banco, [\App\Helper\RemessaPHP\Cnab\Banco::SANTANDER]) === true) {
            // retira o dv
            $nossoNumero = substr($nossoNumero, 0, -1);
        }

        return $nossoNumero;
    }

    /**
     * Retorna o objeto \DateTime da data de vencimento do boleto.
     *
     * @return \DateTime
     */
    public function getDataVencimento()
    {
        $data = false;
        if (empty($this->segmento_t->data_vencimento) === false) {
            $data = \DateTime::createFromFormat('dmY', sprintf('%08d', $this->segmento_t->data_vencimento));
            $data->setTime(0, 0, 0);
        }

        return $data;
    }

    /**
     * Retorna a data em que o dinheiro caiu na conta.
     *
     * @return \DateTime
     */
    public function getDataCredito()
    {
        $data = false;
        if (empty($this->segmento_u->data_credito) === false) {
            $data = \DateTime::createFromFormat('dmY', sprintf('%08d', $this->segmento_u->data_credito));
            $data->setTime(0, 0, 0);
        }

        return $data;
    }

    /**
     * Retorna o valor de juros e mora.
     */
    public function getValorMoraMulta()
    {
        return $this->segmento_u->valor_acrescimos;
    }

    /**
     * Retorna a data da ocorrencia, o dia do pagamento.
     *
     * @return \DateTime
     */
    public function getDataOcorrencia()
    {
        $data = false;
        if (empty($this->segmento_u->data_ocorrencia) === false) {
            $data = \DateTime::createFromFormat('dmY', sprintf('%08d', $this->segmento_u->data_ocorrencia));
            $data->setTime(0, 0, 0);
        }

        return $data;
    }

    /**
     * Retorna o número da carteira do boleto.
     *
     * @return string
     */
    public function getCarteira()
    {
        if ($this->segmento_t->existField('carteira') === true) {
            return $this->segmento_t->carteira;
        } else {
            return '';
        }
    }

    /**
     * Retorna o número da agencia do boleto.
     *
     * @return string
     */
    public function getAgencia()
    {
        return $this->segmento_t->agencia_mantenedora;
    }

    /**
     * Retorna o número da agencia do boleto.
     *
     * @return string
     */
    public function getAgenciaDv()
    {
        return $this->segmento_t->agencia_dv;
    }

    /**
     * Retorna a agencia cobradora.
     *
     * @return string
     */
    public function getAgenciaCobradora()
    {
        return $this->segmento_t->agencia_cobradora;
    }

    /**
     * Retorna a o dac da agencia cobradora.
     *
     * @return string
     */
    public function getAgenciaCobradoraDac()
    {
        return $this->segmento_t->agencia_cobradora_dac;
    }

    /**
     * Retorna o numero sequencial.
     *
     * @return Integer;
     */
    public function getNumeroSequencial()
    {
        return $this->segmento_t->numero_sequencial_lote;
    }

    /**
     * Retorna o nome do código.
     *
     * @return string
     */
    public function getCodigoNome()
    {
        $codigo = (int) $this->getCodigo();

        $table = [
            2  => 'Entrada Confirmada',
            3  => 'Entrada Rejeitada',
            4  => 'Transferência de Carteira/Entrada',
            5  => 'Transferência de Carteira/Baixa',
            6  => 'Liquidação',
            9  => 'Baixa',
            12 => 'Confirmação Recebimento Instrução de Abatimento',
            13 => 'Confirmação Recebimento Instrução de Cancelamento Abatimento',
            14 => 'Confirmação Recebimento Instrução Alteração de Vencimento',
            17 => 'Liquidação Após Baixa ou Liquidação Título Não Registrado',
            19 => 'Confirmação Recebimento Instrução de Protesto',
            20 => 'Confirmação Recebimento Instrução de Sustação/Cancelamento de Protesto',
            23 => 'Remessa a Cartório (Aponte em Cartório)',
            24 => 'Retirada de Cartório e Manutenção em Carteira',
            25 => 'Protestado e Baixado (Baixa por Ter Sido Protestado)',
            26 => 'Instrução Rejeitada',
            27 => 'Confirmação do Pedido de Alteração de Outros Dados',
            28 => 'Débito de Tarifas/Custas',
            30 => 'Alteração de Dados Rejeitada',
            36 => 'Confirmação de envio de e-mail/SMS',
            37 => 'Envio de e-mail/SMS rejeitado',
            43 => 'Estorno de Protesto/Sustação',
            44 => 'Estorno de Baixa/Liquidação',
            45 => 'Alteração de dados',
            51 => 'Título DDA reconhecido pelo sacado',
            52 => 'Título DDA não reconhecido pelo sacado',
            53 => 'Título DDA recusado pela CIP',
        ];

        if (array_key_exists($codigo, $table) === true) {
            return $table[$codigo];
        } else {
            return 'Desconhecido';
        }
    }

    /**
     * Retorna o código de liquidação, normalmente usado para
     * saber onde o cliente efetuou o pagamento.
     *
     * @return string
     */
    public function getCodigoLiquidacao()
    {
        // @TODO: Resgatar o código de liquidação
        return;
    }

    /**
     * Retorna a descrição do código de liquidação, normalmente usado para
     * saber onde o cliente efetuou o pagamento.
     *
     * @return string
     */
    public function getDescricaoLiquidacao()
    {
        // @TODO: Resgator descrição do código de liquidação
        return;
    }

    public function dump()
    {
        $dump  = PHP_EOL;
        $dump .= '== SEGMENTO T ==';
        $dump .= PHP_EOL;
        $dump .= $this->segmento_t->dump();
        $dump .= '== SEGMENTO U ==';
        $dump .= PHP_EOL;
        $dump .= $this->segmento_u->dump();

        if (empty($this->segmento_w) === false) {
            $dump .= '== SEGMENTO W ==';
            $dump .= PHP_EOL;
            $dump .= $this->segmento_w->dump();
        }

        return $dump;
    }

    public function isDDA()
    {
        // @TODO: implementar funçao isDDA no Cnab240
    }

    public function getAlegacaoPagador()
    {
        // @TODO: implementar funçao getAlegacaoPagador no Cnab240
    }


}