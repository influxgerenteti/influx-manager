<?php

namespace App\Factory;

use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;

/**
 * Fabrica de criacoes de objeto Response com base nos dados e headers passados.<br>
 * Exemplo de se chamar: ResponseFactory::metodo(["indice"=>"valor"]).<br>
 * Caso desejar passar cabecalho customizado: ResponseFactory::metodo(["indice"=>"valor"],["indiceheader"=>"valorHeader"]);
 * Abaixo esta a referencia das nomenclaturas utilizadas
 *
 * @author Luiz Antonio Costa
 *
 * @see https://developer.mozilla.org/pt-BR/docs/Web/HTTP/Status
 */
class ResponseFactory
{

    private static $data = [
        "erro"  => [],
        "corpo" => [],
    ];

    /**
     * Cria a Resposta
     *
     * @param int $httpCodigo Numero do erro
     * @param array $data Dados a serem enviados
     * @param array $customHeaders Cabecalhos customizados
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private static function criarResponse($httpCodigo, $data=[], $customHeaders=[])
    {
        return View::create($data, $httpCodigo, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 200
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function ok($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = false;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        if ($corpo === null) {
            $corpo = [];
        }

        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_OK, $retorno, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 201
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function created($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = false;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_CREATED, $retorno, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 202
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function accepted($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = false;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_ACCEPTED, $retorno, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 204
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function noContent($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = false;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_NO_CONTENT, $retorno, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 400
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function badRequest($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = true;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_BAD_REQUEST, $retorno, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 401
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function unauthorized($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = true;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_UNAUTHORIZED, $retorno, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 403
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function forbidden($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = true;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_FORBIDDEN, $retorno, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 404
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function notFound($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = true;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_NOT_FOUND, $retorno, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 409
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function conflict($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = true;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_CONFLICT, $retorno, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 415
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function unsupportedMediaType($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = true;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $retorno, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 500
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function internalServerError($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = true;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $retorno, $customHeaders);
    }

    /**
     * Cria uma resposta do Tipo 502
     *
     * @param array $corpo Dados a serem enviados no body
     * @param string $msg Msg de retorno
     * @param array $customHeaders
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function badGateway($corpo, $msg="", $customHeaders=[])
    {
        $retorno         = self::$data;
        $retorno["erro"] = true;
        if (empty($msg) === false) $retorno["mensagem"] = $msg;
        $retorno["corpo"] = $corpo;
        return self::criarResponse(Response::HTTP_BAD_GATEWAY, $retorno, $customHeaders);
    }


}
