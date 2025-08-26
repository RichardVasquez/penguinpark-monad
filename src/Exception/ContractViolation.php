<?php declare(strict_types=1);

namespace PenguinPark\Monad\Exception;

use LogicException;

// Programmer mistakes / API contract violations (wrong return type, ap needs callable, etc.)
class ContractViolation extends LogicException implements MonadException {}