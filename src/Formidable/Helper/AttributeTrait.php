<?php

declare(strict_types=1);

namespace Formidable\Helper;

use DOMNode;
use Formidable\Helper\Exception\InvalidHtmlAttributeKeyException;
use Formidable\Helper\Exception\InvalidHtmlAttributeValueException;

use function is_string;

trait AttributeTrait
{
    protected function addAttributes(DOMNode $node, array $htmlAttributes): void
    {
        foreach ($htmlAttributes as $key => $value) {
            if (! is_string($key)) {
                throw InvalidHtmlAttributeKeyException::fromInvalidKey($key);
            }

            if (! is_string($value)) {
                throw InvalidHtmlAttributeValueException::fromInvalidValue($value);
            }

            $node->setAttribute($key, $value);
        }
    }
}
