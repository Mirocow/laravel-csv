<?php

namespace Rahul900day\Csv;

use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use SplFileObject;

class Factory
{
    public function fromDisk(string $disk, string $path, string $open_mode = 'r', string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): Csv
    {
        $reader = Reader::createFromPath(Storage::disk($disk)->path($path), $open_mode)
            ->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEscape($escape);

        return new Csv($reader);
    }

    public function fromFileObject(SplFileObject $file, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): Csv
    {
        $reader = Reader::createFromFileObject($file)
            ->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEscape($escape);

        return new Csv($reader);
    }

    public function fromPath(string $path, string $open_mode = 'r', string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): Csv
    {
        $reader = Reader::createFromPath($path, $open_mode)
            ->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEscape($escape);

        return new Csv($reader);
    }

    public function fromStream($stream, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): Csv
    {
        $reader = Reader::createFromStream($stream)
            ->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEscape($escape);

        return new Csv($reader);
    }

    public function fromString(string $content, string $delimiter = ',', string $enclosure = '"', string $escape = '\\'): Csv
    {
        $reader = Reader::createFromString($content)
            ->setDelimiter($delimiter)
            ->setEnclosure($enclosure)
            ->setEscape($escape);

        return new Csv($reader);
    }
}
