<?php

declare(strict_types=1);

namespace Formidable\Helper;

use DOMDocument;
use Formidable\Field;

use function array_key_exists;

final class InputText
{
    use AttributeTrait;

    public function __invoke(Field $field, array $htmlAttributes = []): string
    {
        if (! array_key_exists('type', $htmlAttributes)) {
            $htmlAttributes['type'] = 'text';
        }

        $htmlAttributes['id']    = 'input.' . $field->key;
        $htmlAttributes['name']  = $field->key;
        $htmlAttributes['value'] = $field->value;

        $document = new DOMDocument('1.0', 'utf-8');
        $input    = $document->createElement('input');
        $document->appendChild($input);
        $this->addAttributes($input, $htmlAttributes);

        return $document->saveHTML($input);
    }
}
