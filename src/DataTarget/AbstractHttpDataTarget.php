<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataTarget;

use App\Annotation\DataTarget;
use App\Annotation\DataTarget\Option;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @DataTarget(
 *     name="HTTP",
 *     description="Send data flow result to an HTTP endpoint.",
 * )
 */
abstract class AbstractHttpDataTarget extends AbstractDataTarget
{
    /**
     * @Option(name="URL", description="The URL of the data target", type="string")
     */
    protected $url;

    /**
     * @Option(name="Username", description="Username for basic access authentication", type="string", required=false)
     */
    protected $username;

    /**
     * @Option(name="Password", description="Password for basic access authentication", type="string", required=false)
     */
    protected $password;

    /** @var SerializerInterface */
    protected $serializer;

    /** @var HttpClientInterface */
    protected $httpClient;

    public function __construct(SerializerInterface $serializer, HttpClientInterface $httpClient)
    {
        $this->serializer = $serializer;
        $this->httpClient = $httpClient;
    }

    public function publish(array $rows, Collection $columns, array &$data)
    {
        $options = $this->getPostOptions($rows, $columns);
        $response = $this->post($options);

        $this->info(sprintf('%d row(s) sent to %s', \count($rows), $this->url));

        return $this->isValidResponse($response);
    }

    protected function getPostOptions(array $rows, Collection $columns): array
    {
        throw new \RuntimeException(sprintf('%s::%s not implemented', static::class, __FUNCTION__));
    }

    public function isValidResponse(ResponseInterface $response)
    {
        // @TODO: Check response code.
        return \in_array($response->getStatusCode(), [200, 201], true);
    }

    protected function post(array $options)
    {
        // @TODO: Add authentication
        $response = $this->httpClient->request('POST', $this->url, $options);

        return $response;
    }
}
