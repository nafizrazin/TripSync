<?php
namespace App\Exceptions;
use RuntimeException;
final class ApiProblem extends RuntimeException {
    public function __construct(public readonly string $errorCode, string $message, public readonly int $status=400, public readonly array $context=[]) { parent::__construct($message); }
}
