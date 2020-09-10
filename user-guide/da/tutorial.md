---
layout: page
permalink: /user-guide/da/tutorial
---

# Det store eksempel

Dette eksempel viser hvordan [data fra Aarhus kommunes
Citylab-sensorer](https://portal.opendata.dk/dataset/sensordata/resource/c65b055d-a020-4871-ab51-bdbc3fd73fd8)
kan gøres lidt nemmere at forstå og arbejde med.

## Datakilden

Urlen til den faktiske datakilde er
[http://portal.opendata.dk/api/3/action/datastore_search?resource_id=c65b055d-a020-4871-ab51-bdbc3fd73fd8](http://portal.opendata.dk/api/3/action/datastore_search?resource_id=c65b055d-a020-4871-ab51-bdbc3fd73fd8)
og dataformatet er JSON.

Log ind i Datatidy, gå til “Data library” og tryk på “Add data source”.

Udfyld formularen med noget a la følgende

| Felt        | Værdi                         | Kommentar                                                                                                                                                                                 |
|-------------|-------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Name        | Sensordata fra Aarhus kommune | Brug et kort, præcist og sigende navn.                                                                                                                                                    |
| Description | Sensordata fra Aarhus kommune | Giv en god beskrivelse så andre – og du selv – kan forstå hvilke data datakilden leverer.                                                                                                 |
| Ttl         | 3600                          | **T**ime **T**o **L**ive. Antal minutter der skal gå melle at data hentes fra kilden. Hvis fx kilden kun opdateres én gang i døgnet, så er der ingen grund til at hente data hvert minut. |
| Data source | JSON                          | Data leveres som JSON.                                                                                                                                                                    |

Når “JSON” vælges som ud for “Data source” dukker en række ny felter op under “Data source options”. Udfyld disse:

| Felt | Værdi                                                                                                    | Kommentar                                                                  |
|------|----------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------|
| URL  | http://portal.opendata.dk/api/3/action/datastore_search?resource_id=c65b055d-a020-4871-ab51-bdbc3fd73fd8 | Den faktisk url til data.                                                  |
| root | result.records                                                                                           | Data ligger i ``result.records`` i JSON-dokumentet som datakilden leverer. |

![Create data source][create_data_source_002]

Når alt er som det skal være et det tid til at trykke på “Save”.

## Datatransformationerne

Nu skal selve dataflowet som transformerer datasættet fra datakilden (og på sigt
sender resultatet til et datamål) bygges.

Gå til “Flows”, tryk på “New data flow” og udfyld formularen:

| Felt        | Værdi                         | Kommentar                                   |
|-------------|-------------------------------|---------------------------------------------|
| Name        | Sensordata fra Aarhus kommune | Brug et kort, præcist og sigende navn.      |
| Data source | Sensordata fra Aarhus kommune | Vælg datakilden som blev defineret ovenfor. |

Tryk på “Create data flow”.

Nu ses en oversigt over dataflowet med datakilde, datatransformationer (opskrift) og datamål.

![Edit data flow][edit_data_flow]

Tryk på “Edit recipe”.

Vi er (for eksemplets skyld) kun interesserede i “type”, “time” og “value”, så den første transformation skal udvælge kun disse kolonner.

Vælg “Select columns” under “Add new step” og tryk på “Add transform”.

Udfyld formularen:

| Felt        | Værdi             | Kommentar                                |
|-------------|-------------------|------------------------------------------|
| Name        | Vælg kolonner     | Brug et kort, præcist og sigende navn.   |
| Transformer | Select columns    | Er allerede valgt.                       |
| Columns     | type, time, value | Vælg kolonner.                           |
| Include     | ✓                 | Vi ønsker at beholde de valgte kolonner. |

![Select columns][select_columns_flow]

Tryk på “Save step”.

Nu skulle datasættet kun indeholde de tre ønskede kolonner.

![Select columns][data_flow_step_1]


[create_data_source_000]: Screenshot_2019-12-16%20New%20data%20source%20Datatidy.png
[create_data_source_001]: Screenshot_2019-12-16%20New%20data%20source%20Datatidy(1).png
[create_data_source_002]: Screenshot_2019-12-16%20New%20data%20source%20Datatidy(2).png
[edit_data_flow]: Screenshot_2019-12-16%20Edit%20Data%20flow%20Datatidy.png
[select_columns_flow]: Screenshot_2019-12-16%20Add%20transform%20Datatidy(1).png
[data_flow_step_1]: Screenshot_2019-12-16%20Data%20flow%20recipe%20step%20Datatidy(2).png

[not_used_image]: Screenshot_2019-12-16%20Add%20transform%20Datatidy(2).png
[not_used_image]: Screenshot_2019-12-16%20Add%20transform%20Datatidy.png
[not_used_image]: Screenshot_2019-12-16%20Create%20new%20flow%20Datatidy(1).png
[not_used_image]: Screenshot_2019-12-16%20Create%20new%20flow%20Datatidy.png
[not_used_image]: Screenshot_2019-12-16%20Data%20flow%20recipe%20step%20Datatidy(1).png
[not_used_image]: Screenshot_2019-12-16%20Data%20flow%20recipe%20step%20Datatidy(2).png
[not_used_image]: Screenshot_2019-12-16%20Data%20flow%20recipe%20step%20Datatidy(3).png
[not_used_image]: Screenshot_2019-12-16%20Data%20flow%20recipe%20step%20Datatidy(4).png
[not_used_image]: Screenshot_2019-12-16%20Data%20flow%20recipe%20step%20Datatidy(5).png
[not_used_image]: Screenshot_2019-12-16%20Data%20flow%20recipe%20step%20Datatidy.png
[not_used_image]: Screenshot_2019-12-16%20Edit%20Data%20flow%20Datatidy.png
[not_used_image]: Screenshot_2019-12-16%20New%20data%20source%20Datatidy(1).png
[not_used_image]: Screenshot_2019-12-16%20New%20data%20source%20Datatidy(2).png
[not_used_image]: Screenshot_2019-12-16%20New%20data%20source%20Datatidy.png
