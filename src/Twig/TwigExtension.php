<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Twig;

use App\DataTransformer\DataTransformerManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    /** @var DataTransformerManager */
    private $dataTransformerManager;

    public function __construct(DataTransformerManager $dataTransformerManager)
    {
        $this->dataTransformerManager = $dataTransformerManager;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('transformer_name', [$this, 'getTransformerName']),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('iconClass', [$this, 'getIconClass']),
        ];
    }

    public function getIconClass(string $name)
    {
        switch ($name) {
            case 'dashboard':
                return 'fa-tachometer-alt';
            case 'datasource':
                return 'fa-database';
            case 'job':
                return 'fa-bolt';
            case 'users':
                return 'fa-users';
            case 'search':
                return 'fa-search';
            case 'envelope':
                return 'fa-envelope';
            case 'lock':
                return 'fa-lock';
            case 'dataflow':
                return 'fa-wave-square';
            case 'edit':
                return 'fa-edit';
            case 'help':
                return 'fa-question-circle';
            case 'recipe':
                return 'fa-cogs';
            case 'previous':
                return 'fa-chevron-left';
            case 'next':
                return 'fa-chevron-right';
            case 'settings':
                return 'fa-cog';
            case 'target':
                return 'fa-file-export';
            case 'delete':
                return 'fa-trash-alt';
            case 'steps':
                return 'fa-list-ul';
            case 'preview':
                return 'fa-table';
            case 'play':
                return 'fa-play-circle';
            default:
                return '';
        }
    }

    public function getTransformerName(string $name)
    {
        try {
            $transformer = $this->dataTransformerManager->getTransformer($name);
            $metadata = $transformer->getMetadata();

            return $metadata['name'];
        } catch (\Exception $exception) {
        }

        return $name;
    }
}
