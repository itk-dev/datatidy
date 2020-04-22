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
use DOMDocument;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * @DataTarget(
 *     name="XML",
 *     description="Send data flow result to an HTTP endpoint.",
 * )
 */
class XmlHttpDataTarget extends AbstractHttpDataTarget
{
    /**
     * @Option(name="Document element name", description="Name of the document (root) element", type="string")
     */
    protected $documentElementName;

    protected function getPostOptions(array $rows, Collection $columns): array
    {
        $context = [
            XmlEncoder::ROOT_NODE_NAME => $this->documentElementName,
            XmlEncoder::AS_COLLECTION => false,
        ];
        $xml = $this->serializer->encode($rows, 'xml', $context);

        // The root element now has an <item key="…"/> child for each row and this child must be removed.
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        foreach ($dom->documentElement->childNodes as $item) {
            foreach (iterator_to_array($item->childNodes) as $child) {
                $item->parentNode->insertBefore($child, $item);
            }
            $dom->documentElement->removeChild($item);
        }

        $body = $dom->saveXML();

        return [
            'body' => $body,
        ];
    }
}
