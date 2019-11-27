# Data flow tests

## Creating a test

Create a new YAML document, `test-my-flow.yaml`, say (the filename must start
with `test`), in the `tests/DataFlow/tests` directory. The document must contain
a `fixture` key with `App\Entity\DataFlow` fixture value, e.g.

```yaml
fixture:
    App\Entity\DataFlow:
        data_flow:
            name: test
            dataSource: '@data_source'
            frequency: 60

    App\Entity\DataSource:
        data_source:
            name: test
            ttl: 3600
            dataSource: App\DataSource\JsonDataSource
            dataSourceOptions:
                url: http://test/test-my-flow/data.json
                root: data

    App\Entity\DataTransform:
        data_transform_0:
            data_flow: '@data_flow'
            name: Select columns
            transformer: App\DataTransformer\SelectColumnsDataTransformer
            transformerOptions:
                columns: ['_id', 'REPORT_ID']
                include: false
```

Data source data must be put in files with names matching the paths of the data
source urls in the fixtures (e.g. `test-my-flow/data.json`) in the
`tests/DataFlow/tests` directory:

```json
{
    "data": [
        {
            "id": 1,
            "name": "Mikkel",
            "birthday": "23-05-75"
        },
        {
            "id": 2,
            "name": "James Hetfield",
            "birthday": "03-08-63"
        }
    ]
}
```

Finally, define the expected result under the `expected` key:

```yaml
…

expected:
    filename: test-my-flow/expected.json
```

`filename` must be relative to the `tests/DataFlow/tests` directory.

Run the test

```sh
docker-compose exec phpfpm bin/phpunit tests/DataFlow/DataFlowTest
```

Use the `DATATIDY_TEST_FILTER` environment variable to filter which tests are
run:

```sh
docker-compose exec -e DATATIDY_TEST_FILTER='test-my-flow' phpfpm bin/phpunit tests/DataFlow/DataFlowTest
```
