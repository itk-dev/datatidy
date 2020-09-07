<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\DataSource;

use App\Annotation\DataSource;
use App\Annotation\DataSource\Option;
use App\DataSource\Exception\DataSourceRuntimeException;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @DataSource(name="XML", description="Pulls from a XML data source")
 */
class XmlDataSource extends AbstractHttpDataSource implements DataSourceInterface
{
    /**
     * @Option(name="root", description="Root node XPath, e.g. “/data/results” – beware of namespaces!", type="string", required=false)
     */
    private $root;

    public function pull()
    {
        try {
            $response = $this->getResponse();

            $data = $this->serializer->decode($response->getContent(), 'xml', [
                XmlEncoder::AS_COLLECTION => false,
            ]);

            // @TODO Handle root
            // @TODO Make sure we have data in rows
            $data = [$data];

            return $data;
        } catch (\Exception $exception) {
            throw new DataSourceRuntimeException($exception->getMessage());
        }
    }
}
