<?php

declare(strict_types=1);

namespace Formidable\Helper;

use DOMDocument;
use Formidable\Field;

final class Textarea
{
    use AttributeTrait;

    public function __invoke(Field $field, array $htmlAttributes = []): string
    {
        $htmlAttributes['id']   = 'input.' . $field->key;
        $htmlAttributes['name'] = $field->key;

        $document = new DOMDocument('1.0', 'utf-8');
        $textarea = $document->createElement('textarea');
        $textarea->appendChild($document->createTextNode($field->value));
        $document->appendChild($textarea);
        $this->addAttributes($textarea, $htmlAttributes);

        return $document->saveHTML($textarea);
    }
}
