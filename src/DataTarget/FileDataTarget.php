<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTarget;

use App\Annotation\DataTarget;
use App\Annotation\DataTarget\Option;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @DataTarget(
 *     name="File",
 *     description="Write data flow result to a file.",
 * )
 */
class FileDataTarget extends AbstractDataTarget
{
    /**
     * @Option(name="File name", description="Absolute path the output file", type="string")
     */
    private $filename;

    /**
     * @Option(name="Format", description="", type="choice", choices={"JSON": "json", "Comma Separated Values (CSV)": "csv"})
     */
    private $format;

    /** @var SerializerInterface */
    private $serializer;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(SerializerInterface $serializer, Filesystem $filesystem)
    {
        $this->serializer = $serializer;
        $this->filesystem = $filesystem;
    }

    public function publish(array $rows, Collection $columns, array &$data)
    {
        $content = $this->serializer->serialize($rows, $this->format);
        $this->filesystem->dumpFile($this->filename, $content);
        $this->info(sprintf('%d row(s) written to %s', \count($rows), $this->filename));
    }
}
