<?php

declare(strict_types=1);

namespace Formidable\Helper;

use DOMDocument;
use Formidable\FormError\FormErrorSequence;

use function htmlspecialchars;

final class ErrorList
{
    use AttributeTrait;

    private ErrorFormatter $errorFormatter;

    public function __construct(?ErrorFormatter $errorFormatter = null)
    {
        $this->errorFormatter = $errorFormatter ?? new ErrorFormatter();
    }

    public function __invoke(FormErrorSequence $errors, array $htmlAttributes = []): string
    {
        if ($errors->isEmpty()) {
            return '';
        }

        $errorFormatter = $this->errorFormatter;
        $document       = new DOMDocument('1.0', 'utf-8');
        $list           = $document->createElement('ul');
        $document->appendChild($list);
        $this->addAttributes($list, $htmlAttributes);

        foreach ($errors as $error) {
            $list->appendChild($document->createElement(
                'li',
                htmlspecialchars($errorFormatter($error->getMessage(), $error->getArguments()))
            ));
        }

        return $document->saveHTML($list);
    }
}
