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
use Symfony\Contracts\Translation\TranslatorInterface;
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

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(DataTransformerManager $dataTransformerManager, RouterInterface $router, RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->dataTransformerManager = $dataTransformerManager;
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('transformer_name', [$this, 'getTransformerName']),
            new TwigFilter('time_elapsed', [$this, 'getTimeElapsed']),
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

    public function getTimeElapsed(\DateTimeInterface $time, array $options = [])
    {
        $now = new \DateTimeImmutable($options['now'] ?? 'now');
        $seconds = $now->getTimestamp() - $time->getTimestamp();

        $sections = [
            'day' => 24 * 60 * 60,
            'hour' => 60 * 60,
            'minute' => 60,
        ];

        if ($seconds < 60) {
            $sections['second'] = 1;
        }

        $parts = [];
        foreach ($sections as $name => $value) {
            $parts[$name] = floor($seconds / $value);
            $seconds %= $value;
        }
        $parts = array_filter($parts);

        array_walk($parts, function (&$value, $name) {
            $value = $this->translator->trans('one:1 '.$name.'|%count% '.$name.'s', ['%count%' => $value]);
        });

        $result = '';
        $parts = array_values($parts);
        foreach ($parts as $index => $part) {
            if ($index > 0) {
                $result .= ($index < \count($parts) - 1)
                    ? ($options['delimiter'] ?? ', ')
                    : ($options['connector'] ?? ' '.$this->translator->trans('and').' ');
            }
            $result .= $part;
        }

        return $result;
    }
}
