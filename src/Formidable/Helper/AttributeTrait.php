<?php

declare(strict_types=1);

namespace Formidable\Helper;

use DOMElement;
use Formidable\Helper\Exception\InvalidHtmlAttributeKeyException;
use Formidable\Helper\Exception\InvalidHtmlAttributeValueException;

use function is_string;

trait AttributeTrait
{
    protected function addAttributes(DOMElement $node, array $htmlAttributes): void
    {
        foreach ($htmlAttributes as $key => $value) {
            if (! is_string($key)) {
                throw InvalidHtmlAttributeKeyException::fromInvalidKey();
            }

            if (! is_string($value)) {
                throw InvalidHtmlAttributeValueException::fromInvalidValue($value);
            }

            $node->setAttribute($key, $value);
        }
    }
}
