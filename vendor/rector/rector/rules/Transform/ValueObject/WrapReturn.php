<?php

declare (strict_types=1);
namespace Rector\Transform\ValueObject;

use PHPStan\Type\ObjectType;
use Rector\Validation\RectorAssert;
final class WrapReturn
{
    /**
     * @readonly
     */
    private string $type;
    /**
     * @readonly
     */
    private string $method;
    /**
     * @readonly
     */
    private bool $isArrayWrap;
    public function __construct(string $type, string $method, bool $isArrayWrap)
    {
        $this->type = $type;
        $this->method = $method;
        $this->isArrayWrap = $isArrayWrap;
        RectorAssert::className($type);
    }
    public function getObjectType() : ObjectType
    {
        return new ObjectType($this->type);
    }
    public function getMethod() : string
    {
        return $this->method;
    }
    public function isArrayWrap() : bool
    {
        return $this->isArrayWrap;
    }
}
