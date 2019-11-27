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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    /** @var DataTransformerManager */
    private $dataTransformerManager;

    /** @var RouterInterface */
    private $router;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(DataTransformerManager $dataTransformerManager, RouterInterface $router, RequestStack $requestStack)
    {
        $this->dataTransformerManager = $dataTransformerManager;
        $this->router = $router;
        $this->requestStack = $requestStack;
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
            new TwigFunction('path_with_referer', [$this, 'getPathWithReferer']),
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

    public function getPathWithReferer(string $route, array $parameters = [])
    {
        if (!isset($parameters['referer'])) {
            $request = $this->requestStack->getCurrentRequest();
            $parameters['referer'] = $this->router->generate(
                $request->get('_route'),
                $request->get('_route_params')
            );
        }

        return $this->router->generate($route, $parameters);
    }
}
