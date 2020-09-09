# Data Sources

A *Data Source* is a service that a data flow uses to pull data from.

## Implementing a data source

When adding a new Data Source, there are a few rules you should follow:

- The new Data Source class should implement the [`DataSourceInterface`](../src/DataSource/DataSourceInterface.php).
- The new Data Source class should be annotated with the [`DataSource`](../src/Annotation/DataSource.php) annotation.
- The new Data Source should be added as a service and tagged with datatidy.data_source.

### Step by step - CsvDataSource example

Implement the DataSourceInterface:

```php
<?php

namespace Examples\DataSource;

use App\DataSource\AbstractHttpDataSource;
use League\Csv\Reader;

class CsvDataSource extends AbstractHttpDataSource implements DataSourceInterface
{
    public function pull()
    {
        $response = $this->getResponse();
        $reader = Reader::createFromString($response);

        return $reader->getRecords();
    }
}
```
Then add a `DataSource` annotation:

```php
<?php

...
use App\Annotation\DataSource;

/**
 * @DataSource(name="CSV", description="Pulls from a CSV data source")
 */
class CsvDataSource extends AbstractHttpDataSource implements DataSourceInterface
{
    ...
}
```

Tag the new Data Source with datatidy.date_source:

```yaml
# config/services.yaml
services:
    Examples\DataSource\CsvDataSource:
      tags: ['datatidy.data_source']
```
