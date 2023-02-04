<?php

namespace Pyz\Zed\Pdf\Business\Exception;

class PdfSaveDirCouldNotBeCreatedException extends PdfException
{
    const MESSAGE = 'Das Verzeichnis zum Speichern von PDF-Dokumenten konnte nicht angelegt werden: %s';
}
