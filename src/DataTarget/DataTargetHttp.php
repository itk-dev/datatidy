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

/**
 * @DataTarget(
 *     name="Http",
 *     description="Send data to an HTTP endpoint.")
 * )
 */
class DataTargetHttp extends AbstractDataTarget
{
    /**
     * @Option(name="Url", type="string", description="The data target url.")
     */
    private $url;

    /**
     * @Option(name="Format", type="choice", choices={"json","csv"})
     */
    private $format;

    /**
     * @Option(name="Username", type="string", required=false, description="Username")
     */
    private $username;

    /**
     * @Option(name="Password", type="string", required=false, description="Password")
     */
    private $password;

    /** @var HttpClientInterface */
    private $httpClient;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(HttpClientInterface $httpClient, SerializerInterface $serializer)
    {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
    }

    public function publish(array $rows, Collection $columns, array &$data)
    {
        $payload = $this->serializer->encode($data, $this->format);
        // @TODO: https://symfony.com/doc/current/components/http_client.html#authentication

        $options = [];
        if ('json' === $this->format) {
            $options['json'] = $rows;
        } elseif ('csv' === $this->format) {
            $options['body'] = $this->serializer->encode($rows, $this->format);
            $options['headers']['content-type'] = 'text/csv';
        } else {
            throw new \RuntimeException(sprintf('Unknown format: %s', $this->format));
        }

        $response = $this->httpClient->request('POST', $this->url, $options);

        // @TODO: Check response code.
        return \in_array($response->getStatusCode(), [200, 201], true);
    }
}
