<?php

declare(strict_types=1);

namespace Medas\RestRequestHandlerGenerator;

use Medas\Core\Attributes\Service;
use Medas\EntityGenerator\FileNameFinder;
use Medas\PhpClassAnalysis\ClassAnalyser;

#[Service]
readonly class VoteHandlerMethodInjector
{
    public function __construct(
        private ClassAnalyser  $classAnalyser,
        private FileNameFinder $fileNameFinder,
    )
    {
    }

    /**
     * Adds a method to an existing, already generated class file if it does not already
     * declare a method with the given name. No-ops if the method already exists, so this
     * is safe to call repeatedly (e.g., on every re-run of the generator).
     */
    public function addMethodIfMissing(
        string $handlerClassName,
        string $methodName,
        string $methodTemplate,
        array  $replacements,
    ): void
    {
        if (!class_exists($handlerClassName)) {
            return;
        }

        // confirms the file actually declares a class at this name, as opposed to e.g.,
        // a stale autoloader entry
        $this->classAnalyser->analyseClassByName($handlerClassName);

        $reflection = new \ReflectionClass($handlerClassName);

        if ($reflection->hasMethod($methodName)) {
            return;
        }

        $fileName = $this->fileNameFinder->find($handlerClassName);
        $code = file_get_contents($fileName);

        $method = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $methodTemplate
        );

        $code = $this->insertBeforeFinalClosingBrace($code, $method);

        file_put_contents($fileName, $code);
    }

    private function insertBeforeFinalClosingBrace(string $code, string $method): string
    {
        $position = strrpos($code, '}');

        if ($position === false) {
            throw new Exceptions\NoClosingBraceFound();
        }

        return substr($code, 0, $position) . $method . "\n" . substr($code, $position);
    }
}
