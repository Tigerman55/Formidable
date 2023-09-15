<?php

declare(strict_types=1);

namespace Formidable\Helper;

use DOMDocument;
use DOMNode;
use Formidable\Field;
use Formidable\Helper\Exception\InvalidSelectLabelException;

use function array_key_exists;
use function in_array;
use function is_array;
use function is_int;
use function is_string;

final class Select
{
    use AttributeTrait;

    public function __invoke(Field $field, array $options, array $htmlAttributes = []): string
    {
        $htmlAttributes['id'] = 'input.' . $field->getKey();

        if (array_key_exists('multiple', $htmlAttributes)) {
            $htmlAttributes['name'] = $field->getKey() . '[]';
            $selectedValues         = $field->getNestedValues();
        } else {
            $htmlAttributes['name'] = $field->getKey();
            $selectedValues         = [$field->getValue()];
        }

        $document = new DOMDocument('1.0', 'utf-8');
        $select   = $document->createElement('select');
        $document->appendChild($select);
        $this->addAttributes($select, $htmlAttributes);
        $this->addOptions($document, $select, $options, $selectedValues);

        return $document->saveHTML($select);
    }

    private function addOptions(DOMDocument $document, DOMNode $node, array $options, array $selectedValues): void
    {
        foreach ($options as $value => $label) {
            if (is_int($value)) {
                $value = (string) $value;
            }

            if (! is_string($label) && ! is_array($label)) {
                throw InvalidSelectLabelException::fromInvalidLabel($label);
            }

            if (is_array($label)) {
                $optgroup = $document->createElement('optgroup');
                $this->addAttributes($optgroup, ['label' => $value]);
                $this->addOptions($document, $optgroup, $label, $selectedValues);
                $node->appendChild($optgroup);
                continue;
            }

            $option = $document->createElement('option');
            $option->appendChild($document->createTextNode($label));
            $htmlAttributes = ['value' => $value];

            if (in_array($value, $selectedValues, true)) {
                $htmlAttributes['selected'] = 'selected';
            }

            $this->addAttributes($option, $htmlAttributes);
            $node->appendChild($option);
        }
    }
}
