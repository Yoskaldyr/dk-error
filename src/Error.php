<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 4/24/2016
 * Time: 5:49 PM
 */

namespace N3vrax\DkError;

class Error
{
    protected $code;

    protected $title;

    protected $type;

    protected $message;

    protected $extra;

    /**
     * Error constructor.
     * @param int $code
     * @param string|null $title
     * @param string|null $type
     * @param string|null $message
     * @param mixed|null $extra
     */
    public function __construct($code, $title = null, $type = null, $message = null, $extra = null)
    {
        $this->code = (int)$code;
        $this->message = $message;
        $this->extra = $extra;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getExtra()
    {
        return $this->extra;
    }
}