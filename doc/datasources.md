# Data Sources

When adding a new Data Source entity, there are a few rules you should follow:

- The new class should be placed in the src/Entity folder.
- The name of the class should end in DataSource e.g. JsonDataSource.
- The class should extend the AbstractDataSource class.
- The new class should be added to the discriminator map in the AbstractDataSource class.
- A new form type should be created in the src/Form folder
  - The name of the new form type should be the entity class name followed by type e.g. JsonDataSourceType

## Step by step - XmlDataSource example

Create the entity:

```bash
docker-compose exec phpfpm bin/console make:entity XmlDataSource
```

Make sure it extends the AbstractDataSource class:

```php
<?php

namespace App\Entity;

class XmlDataSource extends AbstractDataSource
{
    ...
}
```

Add the new DataSource class to the discriminator map in the AbstractDataSource class:

```php
<?php 

namespace App\Entity;

 ...
 * @ORM\DiscriminatorMap({"json" = "JsonDataSource", "csv" = "CsvDataSource", "xml" = "XmlDataSource"})
 */
abstract class AbstractDataSource
{
    ...
}

```

Create and run the migrations:

```bash
docker-compose exec phpfpm bin/console make:migration
docker-compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

Create the form type for the new DataSource entity:

```php
<?php

namespace App\Form;

class XmlDataSourceType extends AbstractDataSourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Reuse the form already built in the parent:
        parent::buildForm($builder, $options);

        // Add your own elements:
        $builder->add(...);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => XmlDataSource::class,
        ]);
    }
}
```

You are all done.
