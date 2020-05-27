# Datatransformationer

## Struktureret data

[Som nævnt arbejder Datatidy med datasæt som indeholder data i rækker og
kolonner](README.md#datatransformationer), men ofte indeholder en datakilde
struktureret (eller hierarkisk) data, fx JSON eller XML.

For at kunne behandle JSON i Datatidy er det indledningsvist nødvendigt at
udfolde strukturen så man får data opsat i rækker og kolonner. Når man har
forædlet data via sin opskrift skal man kunne folde data sammen til struktureret
data med samme struktur som oprindeligt.

Til dette formål, ud- og sammenfoldning af data, findes to datatransformationer:
`Expand columns` og `Collapse columns`.

### Eksempel: GeoJSON

I følgende
[GeoJSON-datakilde](https://data.datatidy.srvitkphp73stg.itkdev.dk/data.datatidy.srvitkphp73stg.itkdev.dk/examples/wind_direction.geojson.json)
ønsker vi at oversætte vindretning (`wind_direction`) til kompaspunkter (1 → N,
2 → E, 3 → S, 4 → W):

```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "properties": {
        "name": "Dokk1",
        "wind_direction": 2
      },
      "geometry": {
        "type": "Point",
        "coordinates": [
          10.2108272,
          56.1539574
        ]
      }
    },
    {
      "type": "Feature",
      "properties": {
        "name": "Aarhus Rådhus",
        "wind_direction": 3
      },
      "geometry": {
        "type": "Point",
        "coordinates": [
          10.2008397,
          56.1525791
        ]
      }
    }
  ]
}
```

Det første skridt er at udfolde `features` så vi får to rækker:

| type              | features                                                                                                                                                  |
|-------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------|
| FeatureCollection | {"type": "Feature", "properties": {"name": "Dokk1", "wind_direction": 2}, "geometry": {"type": "Point", "coordinates": [10.2108272, 56.1539574]}}         |
| FeatureCollection | {"type": "Feature", "properties": {"name": "Aarhus Rådhus", "wind_direction": 3}, "geometry": {"type": "Point", "coordinates": [10.2008397, 56.1525791]}} |

Bemærk at værdien i `type` gentages i begge rækker – det er der en god teknisk forklaring på som kommer lidt senere.
