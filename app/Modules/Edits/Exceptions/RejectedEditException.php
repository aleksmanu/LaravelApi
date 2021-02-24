<?php
namespace App\Modules\Edits\Exceptions;

use Exception;
use Throwable;

class RejectedEditException extends Exception
{
    /**
     * @var array
     */
    protected $context;
    public function __construct(
        string $message = "",
        array $context = [],
        int $code = 0,
        Throwable $previous = null
    ) {

        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }
}
