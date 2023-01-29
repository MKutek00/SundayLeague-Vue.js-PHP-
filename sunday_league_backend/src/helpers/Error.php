<?php

namespace Helper;

class Error
{

    private $error;

    private $errorno;

    private $header = true;

    private $params = [];

    /**
     *
     * @param string $error
     * @param string $errorno
     */
    public function __construct($error, $errorno = null)
    {
        $this->error = $error;
        $this->errorno = $errorno;
    }

    public function setParams(...$params)
    {
        $this->params = $params;
        return $this;
    }

    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    public function isHeader()
    {
        return $this->header;
    }

    public function toString()
    {
        $error = "";
        $cnt_percent = \substr_count(\str_replace("%%", "%", $this->error), "%");
        $cnt_params = count($this->params);

        if ($cnt_percent > $cnt_params) {
            for ($i = 0; $i < $cnt_percent - $cnt_params; $i++) {
                $this->params[] = "";
            }
        }

        $error = (!empty($this->errorno)) ? "[" . $this->errorno . "] " : "";
        $error .= sprintf($this->error, ...$this->params);
        return $error;
    }
}
