<?php
spl_autoload_register(function ($className) {

    $className = ltrim($className, '\\');

    $namespaceToDir = '';

    // wykryj namespace
    $lastPos = strrpos($className, '\\');
    if ($lastPos) {
        $namespace = substr($className, 0, $lastPos);
        $className = substr($className, $lastPos + 1);
        $namespaceToDir = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    // obsługa podkreśleń w nazwie klasy
    $classNameToDir = str_replace('_', DIRECTORY_SEPARATOR, $className);

    $fileName = $namespaceToDir . $classNameToDir . '.php';

    require $fileName;
});