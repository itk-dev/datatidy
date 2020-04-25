<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * A quick 'n dirty test controller that can return test data sources.
 *
 * @Route("/test/data/source", name="test_data_source_")
 */
class DataSourceController extends AbstractController
{
    private $resourcePath = __DIR__.'/../Resources/data/source';

    public function __construct()
    {
        $this->resourcePath = realpath($this->resourcePath);
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
        $finder = new Finder();
        $finder->files()->in($this->resourcePath);
        $paths = [];
        foreach ($finder as $fileInfo) {
            $paths[] = substr($fileInfo->getRealPath(), \strlen($this->resourcePath) + 1);
        }
        $urls = array_map(function ($path) {
            return $this->generateUrl('test_data_source_get', ['path' => $path], UrlGeneratorInterface::ABSOLUTE_URL);
        }, $paths);

        return new JsonResponse($urls);
    }

    /**
     * @Route("/{path}", requirements={"path"=".+"}, name="get", methods={"GET"})
     */
    public function getData(string $path)
    {
        $filename = $this->resourcePath.'/'.$path;

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
}
