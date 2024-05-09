<?php
namespace App\Helper;

/**
 *
 * @author Luiz Antonio Costa
 */
class CurlHelper
{


    function __construct()
    {
        $this->limparRequisicaoAnterior();
    }

    /**
     *
     * @var resource
     */
    private $curl;

    /**
     *
     * @var array
     */
    private $arrHeaderOption;

    /**
     *
     * @var string
     */
    private $errorCurl;

    /**
     *
     * @var integer
     */
    private $statusCode;

    /**
     *
     * @var array
     */
    private $rawHeader;

    /**
     *
     * @var array
     */
    private $rawBody;

    /**
     * Formata o cabeçalho para que seja apresentado de uma maneira "amigavel"
     *
     * @param mixed $header Cabeçalho
     *
     * @return array
     */
    protected function formataHeader($header)
    {
        $headers = [];

        $data = explode("\n", $header);
        $headers['status'] = $data[0];

        array_shift($data);
        foreach ($data as $part) {
            $dataHeader = explode(":", $part);
            if (empty(trim($dataHeader[0])) === false) {
                $headers[trim($dataHeader[0])] = trim($dataHeader[1]);
            }
        }

        return $headers;
    }

    /**
     * Monta as opcoes conforme passado no array de $opcoes, caso contrario ele montara com com as opcoes padrao
     *
     * @param string $url URL para qual devera ser montada as opcoes
     * @param string $metodo Metodo de requisicao POST, GET, PUT
     * @param array $opcoesHeader Array de opcoes exemplo:
     *                            <br>array(
     *                            <br>CURLOPT_URL => "www.google.com.br"
     *                            <br>)
     */
    protected function montaOpcoes($url, $metodo, $opcoesHeader)
    {
        if (count($opcoesHeader) > 0) {
            curl_setopt_array($this->curl, $opcoesHeader);
        } else if (count($this->arrHeaderOption) > 0) {
            curl_setopt_array($this->curl, $this->arrHeaderOption);
        } else {
            curl_setopt_array(
                $this->curl,
                [
                    CURLOPT_URL            => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER         => true,
                    CURLOPT_ENCODING       => "",
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_TIMEOUT        => 30,
                    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST  => $metodo,
                    CURLOPT_HTTPHEADER     => $this->arrHeaderOption,
                ]
            );
        }
    }

    /**
     * Funcao que monta os parametros para requisicao POST ou PUT
     *
     * @param array $parametros Chave e valor
     */
    protected function montaParametros($parametros)
    {
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $parametros);
    }

    /**
     * Monta a resposta da requisição assim que teve sucesso ou falha
     */
    protected function montaResposta()
    {
        $resposta         = curl_exec($this->curl);
        $tamanho_header   = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        $this->errorCurl  = curl_error($this->curl);
        $this->statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $this->rawHeader  = $this->formataHeader(substr($resposta, 0, $tamanho_header));
        $this->rawBody    = substr($resposta, $tamanho_header);
    }

    /**
     * Fecha o ponteiro do objeto cURL
     */
    protected function fecharRequisicao()
    {
        curl_close($this->curl);
    }

    /**
     * @return array
     */
    public function getArrHeaderOption()
    {
        return $this->arrHeaderOption;
    }

    /**
     * @return string
     */
    public function getErrorCurl()
    {
        return $this->errorCurl;
    }

    /**
     * @return number
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getRawHeader()
    {
        return $this->rawHeader;
    }

    /**
     * @return array
     */
    public function getRawBody()
    {
        return $this->rawBody;
    }

    /**
     * @param array $arrHeaderOption
     */
    public function setArrHeaderOption($arrHeaderOption)
    {
        $this->arrHeaderOption = $arrHeaderOption;
    }

    /**
     * Executa uma requisição cURL na URL solicitada
     *
     * @param string $url URL a ser executada
     * @param string $metodo Metodo da requisição GET, POST ou PUT
     * @param array $parametros Parametros que irá ser executado na requisição
     * @param array $opcoesHeader Opções que irão no header
     *
     * @return boolean
     */
    public function executarUrl($url, $metodo="GET", $parametros=[], $opcoesHeader=[])
    {
        $this->curl = curl_init();
        $this->montaOpcoes($url, $metodo, $opcoesHeader);
        if (($metodo === "POST") || ($metodo === "PUT")) {
            $this->montaParametros($parametros);
        }

        $this->montaResposta();
        $this->fecharRequisicao();
        return empty($this->errorCurl);
    }

    /**
     * Limpa o resultado cacheado da execução anterior
     */
    public function limparRequisicaoAnterior()
    {
        $this->errorCurl       = "";
        $this->statusCode      = null;
        $this->curl            = null;
        $this->arrHeaderOption = [];
        $this->rawBody         = [];
        $this->rawHeader       = [];
    }


}
