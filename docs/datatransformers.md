# Data transformers

A *Data transformer* is a service that can transform a data set into another data set.

## Implementing a data transformer

In the example below a data transformer that adds a prefix to all columns is implemented.

First, create a class extending [`AbstractDataTransformer`](../src/DataTransformer/AbstractDataTransformer.php):

```php
<?php

namespace Examples\DataTransformer;

use App\DataTransformer\AbstractDataTransformer;

class AddPrefixDataTransform extends AbstractDataTransformer {
    /**
     * {@inheritdoc}
     */
    public function transform(DataSet $input): DataSet
    {
        // …
    }

    /**
     * {@inheritdoc}
     */
    public function transformColumns(ArrayCollection $columns): ArrayCollection
    {
        // …
    }
}
```

Then, add a `DataTransformer` annotation to mark the class as being i data transformer:

```php
<?php
…
use App\Annotation\DataTransformer;

/**
 * @DataTransformer(
 *     name="Add prefix",
 *     description="Adds a prefix to all column names"
 * )
 */
class AddPrefixDataTransform extends AbstractDataTransformer {
    …
}
```

Finally, add and annotate options in the data transformer:

```php
<?php
…
use App\Annotation\DataTransformer;
use App\Annotation\DataTransformer\Option;

…
class AddPrefixDataTransform extends AbstractDataTransformer {
    /**
     * @Option(type="string", name="Prefix", description="The prefix added to column names.")
     *
     * @var string
     */
    private $prefix;

    …

    /**
     * {@inheritdoc}
     */
    public function transformColumns(ArrayCollection $columns): ArrayCollection
    {
        $transformedColumns = new ArrayCollection();

        foreach ($columns as $name => $column) {
            $name = $this->prefix.$name;
            $column = $this->renameColumn($column, $name);
            $transformedColumns[$name] = $column;
        }

        return $transformedColumns;
    }
}
```

### Tagging the data transformer

The final step is to add the `datatidy.data_transformer` tag to the service
(this may be done automatically, but how to do it is shown here for
completeness):

```yaml
services:
    App\DataTransformer\AddPrefixDataTransform:
        tags: ['datatidy.data_transformer']
```

After clearing the cache (`bin/console cache:clear`), the new data transformer should be available.

Use

```sh
bin/console datatidy:data-transformer:list 'Add prefix' --show-options
```

to list all data transformers.

### The final result

```php
<?php

namespace App\Examples\DataTransformer;

use App\Annotation\DataTransformer;
use App\Annotation\DataTransformer\Option;
use App\DataSet\DataSet;
use App\DataTransformer\AbstractDataTransformer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @DataTransformer(
 *     name="Add prefix",
 *     description="Adds a prefix to all column names"
 * )
 */
class AddPrefixDataTransform extends AbstractDataTransformer
{
    /**
     * @Option(type="string", name="Prefix", description="The prefix added to column names.")
     *
     * @var string
     */
    private $prefix;

    /**
     * {@inheritdoc}
     */
    public function transform(DataSet $input): DataSet
    {
        $columns = $input->getColumns();
        $transformedColumns = $this->transformColumns($input);

        // …
    }

    /**
     * {@inheritdoc}
     */
    public function transformColumns(ArrayCollection $columns): ArrayCollection
    {
        $transformedColumns = new ArrayCollection();

        foreach ($columns as $name => $column) {
            $name = $this->prefix.$name;
            $column = $this->renameColumn($column, $name);
            $transformedColumns[$name] = $column;
        }

        return $transformedColumns;
    }
}
```
