<?php


namespace App\DataSource;


use App\Annotation\Option;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractDataSource implements DataSourceInterface
{
    protected $httpClient;

    /**
     * @Option(name="URL", description="Endpoint URL", type="string")
     */
    protected $url;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    abstract function pull();
}
