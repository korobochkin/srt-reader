<?php

declare(strict_types=1);

namespace Korobochkin\SrtReader\Ast;

class SrtDocumentNodeFactory
{
    public static function create(array $children): SrtDocumentNode
    {
        return new SrtDocumentNode($children);
    }
}
