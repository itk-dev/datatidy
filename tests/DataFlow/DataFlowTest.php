<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Tests\DataFlow;

use App\DataSource\CsvDataSource;
use App\DataSource\JsonDataSource;
use App\DataSource\XmlDataSource;
use App\DataTarget\CsvHttpDataTarget;
use App\DataTarget\JsonHttpDataTarget;
use App\DataTarget\XmlHttpDataTarget;
use App\Entity\DataFlow;
use App\Tests\ContainerTestCase;
use App\Traits\LogTrait;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Nelmio\Alice\DataLoaderInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\SerializerInterface;

class DataFlowTest extends ContainerTestCase
{
    use LogTrait;

    protected function setUp(): void
    {
        // Break open data sources and targets and inject our mock http client.
        $httpClient = new DataSourceMockHttpClient();
        foreach ([
                     CsvDataSource::class,
                     JsonDataSource::class,
                     XmlDataSource::class,
                     CsvHttpDataTarget::class,
                     JsonHttpDataTarget::class,
                     XmlHttpDataTarget::class,
                 ] as $serviceClass) {
            $service = $this->getContainer()->get($serviceClass);
            $property = new \ReflectionProperty($service, 'httpClient');
            $property->setAccessible(true);
            $property->setValue($service, $httpClient);
        }

        $output = new ConsoleOutput();
        // @TODO Get verbosity from command line arguments or PHPUnit configuration.
        $output->setVerbosity($output::VERBOSITY_DEBUG);
        $this->setLogger(new ConsoleLogger($output));
    }

    /**
     * @dataProvider dataProvider
     *
     * @param $filename
     *
     * @throws Exception
     */
    public function test($filename): void
    {
        $this->debug('Running test in {filename}', ['filename' => $filename]);

        $this->debug('Loading test data');
        $data = $this->loadData($filename);

        $expected = $this->buildExpected($filename, $data['expected'] ?? []);

        // Clean up before run.
        if (isset($data['expected']['actual_filename'])) {
            $filename = $this->getFilename($data['expected']['actual_filename']);
            $this->filesystem()->remove($filename);
        }

        $dataFlow = $this->buildDataFlow($data);
        $publish = $dataFlow->getDataTargets()->count() > 0;

        $this->debug('Running data flow (publish: {publish})', ['publish' => $publish ? 'yes' : 'no']);
        $result = $this->dataFlowManager()->run($dataFlow, [
            'publish' => $publish,
        ]);

        if ($result->hasTransformException()) {
            throw $result->getTransformException();
        }

        $this->assertTrue($result->isSuccess());

        if ($publish) {
            if ($result->hasPublishException()) {
                throw $result->getPublishException();
            }

            $this->assertTrue($result->isPublished());
        }

        if (isset($data['expected']['actual_filename'])) {
            $actual = $this->loadData($this->getFilename($data['expected']['actual_filename']));
            $this->assertEquals($expected, $actual);
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
        $this->debug('Loading expected data from {expectedFilename}', ['expectedFilename' => $expectedFilename]);
        if (!is_file($expectedFilename)) {
            throw new \RuntimeException(sprintf('Expected file "%s" does not exist', $expectedFilename));
        }

        return $this->loadData($expectedFilename);
    }

    private function loadData(string $filename): array
    {
        $content = file_get_contents($filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        /** @var SerializerInterface $serializer */
        $serializer = $this->get('serializer');

        switch ($extension) {
            case 'geojson':
            case 'json':
                return $serializer->decode($content, 'json');
            case 'yaml':
                return $serializer->decode($content, 'yaml');
            case 'xml':
                return $serializer->decode($content, 'xml');
            case 'csv':
                return $serializer->decode($content, 'csv', [
                    'as_collection' => true,
                ]);
            default:
                throw new \RuntimeException(sprintf('Cannot yet handle extension "%s"', $extension));
        }
    }

    private function buildDataFlow(array $data)
    {
        $this->debug('Building data flow');
        /** @var DataLoaderInterface $loader */
        $loader = $this->get('nelmio_alice.data_loader');

        $set = $loader->loadData($data['fixtures']);
        $dataFlow = null;
        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine.orm.entity_manager');
        $dataFlowId = $data['data_flow_id'] ?? 'data_flow';
        foreach ($set->getObjects() as $id => $object) {
            $em->persist($object);
            if ($object instanceof DataFlow && $dataFlowId === $id) {
                $dataFlow = $object;
            }
        }
        $em->flush();
        if (null === $dataFlow) {
            throw new \RuntimeException(sprintf('Cannot find data flow with id "%s"', $dataFlowId));
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
