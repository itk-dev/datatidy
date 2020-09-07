<?php

/*
 * This file is part of itk-dev/datatidy.
 *
 * (c) 2019–2020 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace App\Service;

use App\Tests\ContainerTestCase;

class DataHelperTest extends ContainerTestCase
{
    /** @var DataHelper */
    private $dataHelper;

    protected function setUp(): void
    {
        $this->dataHelper = $this->getContainer()->get(DataHelper::class);
    }

    public function testExpand()
    {
        $data = json_decode(<<<'JSON'
[
    {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:25832"
            }
        },
        "bbox": [560430.0327407509, 6207103.841963952, 583839.4899465791, 6240651.54342634],
        "features": [
            {
                "type": "Feature",
                "geometry": {
                    "type": "Point",
                    "coordinates": [102.0, 0.5]
                },
                "properties": {
                    "prop0": "value0"
                }
            },
            {
                "type": "Feature",
                "geometry": {
                    "type": "LineString",
                    "coordinates": [
                        [102.0, 0.0], [103.0, 1.0], [104.0, 0.0], [105.0, 1.0]
                    ]
                },
                "properties": {
                    "prop0": "value0",
                    "prop1": 0.0
                }
            },
            {
                "type": "Feature",
                "geometry": {
                    "type": "Polygon",
                    "coordinates": [
                        [
                            [100.0, 0.0], [101.0, 0.0], [101.0, 1.0],
                            [100.0, 1.0], [100.0, 0.0]
                        ]
                    ]
                },
                "properties": {
                    "prop0": "value0",
                    "prop1": { "this": "that" }
                }
            }
        ]
    }
]
JSON, true, 512, JSON_THROW_ON_ERROR);

        $actual = $this->dataHelper->expand($data, 'features');
        $expected = json_decode(<<<'JSON'
[
    {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:25832"
            }
        },
        "bbox": [560430.0327407509, 6207103.841963952, 583839.4899465791, 6240651.54342634],
        "features": {
            "type": "Feature",
            "geometry": {
                "type": "Point",
                "coordinates": [102.0, 0.5]
            },
            "properties": {
                "prop0": "value0"
            }
        }
    },
    {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:25832"
            }
        },
        "bbox": [560430.0327407509, 6207103.841963952, 583839.4899465791, 6240651.54342634],
        "features": {
            "type": "Feature",
            "geometry": {
                "type": "LineString",
                "coordinates": [
                    [102.0, 0.0], [103.0, 1.0], [104.0, 0.0], [105.0, 1.0]
                ]
            },
            "properties": {
                "prop0": "value0",
                "prop1": 0.0
            }
        }
    },
    {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:25832"
            }
        },
        "bbox": [560430.0327407509, 6207103.841963952, 583839.4899465791, 6240651.54342634],
        "features": {
            "type": "Feature",
            "geometry": {
                "type": "Polygon",
                "coordinates": [
                    [
                        [100.0, 0.0], [101.0, 0.0], [101.0, 1.0],
                        [100.0, 1.0], [100.0, 0.0]
                    ]
                ]
            },
            "properties": {
                "prop0": "value0",
                "prop1": { "this": "that" }
            }
        }
    }
]
JSON, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($expected, $actual);

        $data = $actual;
        $actual = $this->dataHelper->expand($data, 'features');
        $expected = json_decode(<<<'JSON'
[
    {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:25832"
            }
        },
        "bbox": [560430.0327407509, 6207103.841963952, 583839.4899465791, 6240651.54342634],
        "features.type": "Feature",
        "features.geometry": {
            "type": "Point",
            "coordinates": [102.0, 0.5]
        },
        "features.properties": {
            "prop0": "value0"
        }
    },
    {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:25832"
            }
        },
        "bbox": [560430.0327407509, 6207103.841963952, 583839.4899465791, 6240651.54342634],
        "features.type": "Feature",
        "features.geometry": {
            "type": "LineString",
            "coordinates": [
                [102.0, 0.0], [103.0, 1.0], [104.0, 0.0], [105.0, 1.0]
            ]
        },
        "features.properties": {
            "prop0": "value0",
            "prop1": 0.0
        }
    },
    {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:25832"
            }
        },
        "bbox": [560430.0327407509, 6207103.841963952, 583839.4899465791, 6240651.54342634],
        "features.type": "Feature",
        "features.geometry": {
            "type": "Polygon",
            "coordinates": [
                [
                    [100.0, 0.0], [101.0, 0.0], [101.0, 1.0],
                    [100.0, 1.0], [100.0, 0.0]
                ]
            ]
        },
        "features.properties": {
            "prop0": "value0",
            "prop1": { "this": "that" }
        }
    }
]
JSON, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($expected, $actual);

        $data = $actual;
        $actual = $this->dataHelper->expand($data, 'features.properties');
        $expected = json_decode(<<<'JSON'
[
    {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:25832"
            }
        },
        "bbox": [560430.0327407509, 6207103.841963952, 583839.4899465791, 6240651.54342634],
        "features.type": "Feature",
        "features.geometry": {
            "type": "Point",
            "coordinates": [102.0, 0.5]
        },
        "features.properties.prop0": "value0",
        "features.properties.prop1": null
    },
    {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:25832"
            }
        },
        "bbox": [560430.0327407509, 6207103.841963952, 583839.4899465791, 6240651.54342634],
        "features.type": "Feature",
        "features.geometry": {
            "type": "LineString",
            "coordinates": [
                [102.0, 0.0], [103.0, 1.0], [104.0, 0.0], [105.0, 1.0]
            ]
        },
        "features.properties.prop0": "value0",
        "features.properties.prop1": 0.0
    },
    {
        "type": "FeatureCollection",
        "crs": {
            "type": "name",
            "properties": {
                "name": "EPSG:25832"
            }
        },
        "bbox": [560430.0327407509, 6207103.841963952, 583839.4899465791, 6240651.54342634],
        "features.type": "Feature",
        "features.geometry": {
            "type": "Polygon",
            "coordinates": [
                [
                    [100.0, 0.0], [101.0, 0.0], [101.0, 1.0],
                    [100.0, 1.0], [100.0, 0.0]
                ]
            ]
        },
        "features.properties.prop0": "value0",
        "features.properties.prop1": { "this": "that" }
    }
]
JSON, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame($expected, $actual);
    }

    public function testExpandAndCollapse()
    {
        $expected = json_decode(<<<'JSON'
[
{
    "type": "FeatureCollection",
    "crs": {
        "type": "name",
        "properties": {
            "name": "EPSG:25832"
        }
    },
    "bbox": [560430.0327407509, 6207103.841963952, 583839.4899465791, 6240651.54342634],
    "features": [
        {
            "type": "Feature",
            "geometry": {
                "type": "Point",
                "coordinates": [102.0, 0.5]
            },
            "properties": {
                "prop0": "value0"
            }
        },
        {
            "type": "Feature",
            "geometry": {
                "type": "LineString",
                "coordinates": [
                    [102.0, 0.0], [103.0, 1.0], [104.0, 0.0], [105.0, 1.0]
                ]
            },
            "properties": {
                "prop0": "value0",
                "prop1": 0.0
            }
        },
        {
            "type": "Feature",
            "geometry": {
                "type": "Polygon",
                "coordinates": [
                    [
                        [100.0, 0.0], [101.0, 0.0], [101.0, 1.0],
                        [100.0, 1.0], [100.0, 0.0]
                    ]
                ]
            },
            "properties": {
                "prop0": "value0",
                "prop1": { "this": "that" }
            }
        }
    ]
}
]
JSON, true, 512, JSON_THROW_ON_ERROR);

        $steps = [
            'crs',
            // 'crs.properties',
            'features',
            'features',
            'features.properties',
            // 'bbox',
        ];
        $actual = $expected;

        foreach ($steps as $step) {
            $actual = $this->dataHelper->expand($actual, $step);
        }

        foreach (array_reverse($steps) as $step) {
            $actual = $this->dataHelper->collapse($actual, $step);
        }

        $this->assertEquals($expected, $actual);
    }
}
