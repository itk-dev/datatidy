<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * A quick 'n dirty test controller that can act as a data target.
 *
 * @Route("/test/data/target", name="test_data_target_")
 */
class DataTargetController extends AbstractController
{
    private $targetPath = __DIR__.'/../Resources/data/target';

    public function __construct()
    {
        $this->targetPath = realpath($this->targetPath);
    }

    /**
     * @Route("/{path}", requirements={"path"=".+"}, name="get", methods={"GET"})
     */
    public function get(string $path)
    {
        $filename = $this->targetPath.'/'.$path;

        if (!file_exists($filename)) {
            throw new NotFoundHttpException(sprintf('Invalid path: %s', $path));
        }
        $content = file_get_contents($filename);

        $contentType = [
                'csv' => 'text/csv',
                'json' => 'application/json',
                'xml' => 'application/xml',
            ][pathinfo($filename, PATHINFO_EXTENSION)] ?? 'text/plain';

        return new Response($content, 200, ['content-type' => $contentType]);
    }

    /**
     * @Route("/{path}", requirements={"path"=".+"}, name="post", methods={"POST"})
     */
    public function post(Request $request, string $path, Filesystem $filesystem)
    {
        $filename = $this->targetPath.'/'.$path;

        $content = $request->getContent();

        $filesystem->mkdir(\dirname($filename));
        $filesystem->dumpFile($filename, $content);

        return new Response('', 201);
    }
}
