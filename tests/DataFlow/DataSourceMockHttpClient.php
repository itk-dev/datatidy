<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataFlow;

use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DataSourceMockHttpClient extends MockHttpClient
{
    public function __construct()
    {
        parent::__construct([$this, 'callback']);
    }

    public function callback($method, $url, $options)
    {
        if ('GET' === $method) {
            return $this->get($url, $options);
        }

        throw new BadRequestHttpException();
    }

    private function get($url, $options)
    {
        $filename = __DIR__.'/tests/'.parse_url($url, PHP_URL_PATH);

        if (!file_exists($filename)) {
            throw new NotFoundHttpException($url.' '.$filename);
        }

        return new MockResponse(file_get_contents($filename));
    }
}
