<?php

namespace Helper;

class Json
{

    /**
     *
     * @var array
     */
    public $dane;

    /**
     *
     * @param array $dane
     */
    public function __construct($dane)
    {
        $this->dane = $dane;
    }

    /**
     * Metoda zamienia tablicę z danymi na json
     *
     * @param  Array $dane
     * @return Json
     */
    public static function toJson($dane)
    {
        return new self($dane);
    }


    public static function callError(Error $error)
    {
        if ($error->isHeader()) {
            header("HTTP/1.0 460 Aplication Error");
            header("Content-Type: application/json");
        }

        echo json_encode($error->toString());
    }

    public static function ExceptionHandle(\Throwable $err, $header = true)
    {
        $err = new Error($err->getMessage(), $err->getCode());
        $err->setHeader($header);
        self::callError($err);
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        // header("Content-Type: application/json");
        return json_encode($this->dane);
    }
}
