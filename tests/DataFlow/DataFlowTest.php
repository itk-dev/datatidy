<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataFlow;

use App\DataSource\JsonDataSource;
use App\DataTarget\CsvHttpDataTarget;
use App\DataTarget\JsonHttpDataTarget;
use App\Entity\DataFlow;
use App\Tests\ContainerTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\Alice\DataLoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class DataFlowTest extends ContainerTestCase
{
    protected function setUp(): void
    {
        // Break open data sources and targets and inject our mock http client.
        $httpClient = new DataSourceMockHttpClient();
        foreach ([JsonDataSource::class, JsonHttpDataTarget::class, CsvHttpDataTarget::class] as $serviceClass) {
            $service = $this->getContainer()->get($serviceClass);
            $property = new \ReflectionProperty($service, 'httpClient');
            $property->setAccessible(true);
            $property->setValue($service, $httpClient);
        }
    }

    /**
     * @dataProvider dataProvider
     *
     * @param $filename
     */
    public function test($filename): void
    {
        $content = file_get_contents($filename);
        $data = Yaml::parse($content);

        $expected = $this->buildExpected($filename, $data['expected'] ?? []);

        // Clean up before run.
        if (isset($data['expected']['actual_filename'])) {
            $filename = $this->getFilename($data['expected']['actual_filename']);
            $this->filesystem()->remove($filename);
        }

        $dataFlow = $this->buildDataFlow($data['fixtures']);

        $result = $expectedData = $this->dataFlowManager()->run($dataFlow, [
            'publish' => $dataFlow->getDataTargets()->count() > 0,
        ]);

        if ($result->hasTransformException()) {
            throw $result->getException();
        }

        $this->assertTrue($result->isSuccess());

        if (isset($data['expected']['filename'], $data['expected']['actual_filename'])) {
            $this->assertJsonFileEqualsJsonFile(
                $this->getFilename($data['expected']['filename']),
                $this->getFilename($data['expected']['actual_filename'])
            );
        } else {
            $actual = $result->getLastTransformResult()->getRows();

            $this->assertEquals($expected, $actual);
        }
    }

    private function buildExpected(string $testFilename, array $data)
    {
        $expectedFilename = null;
        if (isset($data['filename'])) {
            $expectedFilename = __DIR__.'/tests/'.$data['filename'];
        } else {
            $pattern = preg_replace('/(\.[^.]+)*$/', '', $testFilename).'/expected.*';
            $filenames = glob($pattern);
            $expectedFilename = reset($filenames);
            if (!is_file($expectedFilename)) {
                throw new \RuntimeException(sprintf('Cannot find expected file matching patterns "%s"', $pattern));
            }
        }
        if (!is_file($expectedFilename)) {
            throw new \RuntimeException(sprintf('Expected file "%s" does not exist', $expectedFilename));
        }

        $content = file_get_contents($expectedFilename);
        $extension = pathinfo($expectedFilename, PATHINFO_EXTENSION);

        switch ($extension) {
            case 'json':
                return json_decode($content, true);
            case 'yaml':
                return Yaml::parse($content);
            case 'csv':
                $serializer = $this->get('serializer');

                return $serializer->decode($content, 'csv', [
                    'as_collection' => true,
                ]);
            default:
                throw new \RuntimeException(sprintf('Cannot yet handle extension "%s"', $extension));
        }
    }

    private function buildDataFlow(array $data)
    {
        /** @var DataLoaderInterface $loader */
        $loader = $this->get('nelmio_alice.data_loader');

        $set = $loader->loadData($data);
        $dataFlow = null;
        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine.orm.entity_manager');
        foreach ($set->getObjects() as $id => $object) {
            $em->persist($object);
            if ($object instanceof DataFlow && 'data_flow' === $id) {
                $dataFlow = $object;
            }
        }
        $em->flush();
        if (null === $dataFlow) {
            throw new \RuntimeException('Cannot find data flow with id "data_flow"');
        }

        // Refresh data flow to set relations correctly.
        $em->refresh($dataFlow);

        return $dataFlow;
    }

    public function dataProvider()
    {
        $dir = __DIR__.'/tests';
        $filenames = glob($dir.'/test*.yaml');

        $filter = getenv('DATATIDY_TEST_FILTER');
        if ($filter) {
            // Check for regex delimiter and add if not found.
            if (0 !== strpos($filter, '/')) {
                $filter = '/'.$filter.'/';
            }
            $filenames = array_filter($filenames, static function ($filename) use ($filter) {
                return preg_match($filter, $filename);
            });
        }

        return array_map(static function ($filename) {
            return [$filename];
        }, $filenames);
    }

    private function filesystem(): Filesystem
    {
        return $this->get('filesystem');
    }

    private function getFilename(string $filename)
    {
        return $this->filesystem()->isAbsolutePath($filename) ? $filename : __DIR__.'/tests/'.$filename;
    }
}
