---
layout: page
title: "Brugervejledning"
permalink: /user-guide/da
---

Datatidy er et værktøj til at “forbedre” åbne datasæt så de bliver nemmere at
bruge for slutbrugere fx til at bygge apps eller lave visualiseringer af
data. Hvad “forbedre” faktisk betyder afhænger af hvilken situation data bruges
i, men et eksempel på en forbedring kan være at datasættet

| id | measured_at | value |
|---:|-------------|------:|
|  1 | 2019-03-01  |     0 |
| 42 | 2019-05-23  |     2 |
| 87 | 2019-12-18  |     1 |

transformeres til

| id | measured_at | wind_direction |
|---:|-------------|----------------|
|  1 | 2019-03-01  | north          |
| 42 | 2019-05-23  | south          |
| 87 | 2019-12-18  | east           |

Efter transformationen står det pludselig klart at datasættet (formentlig)
indeholder oplysninger om vindretninger og i dette tilfælde betyder at
“forbedre” altså at gøre data lettere at forstå.

## Datatidy

Datatidy består grundlæggende af tre ting: datakilder, datatransformationer og
datamål. En *datakilde* leverer data, en *datatransformation* transformerer data
og et *datamål* modtager (transformeret) data.

Et *dataflow* består af en datakilde, én eller flere datatransformationer,
kaldet en *opskrift*, og ét eller flere datamål, og en *kørsel af et dataflow*
består i at hente data fra datakilder, transformere data via opskriften og til
sidst sende det transformerede datasæt til hvert datamål.

Datatidy lever i en åben verden hvor datakilder skal være frit tilgængelige for
alle (fx ikke beskyttet af adgangskoder eller lignende) og tilsvarende skal
transformeret data være frit tilgængeligt. Der skal naturligvis ofte bruges en
adgangskode når data sendes til et datamål, men det endelige datasæt skal være
frit tilgængeligt fra datamålet (som nu spiller rollen som datakilde).

## Datakilder

En datakilde skal kunne levere data som svar på en simpel
[HTTP](https://da.wikipedia.org/wiki/HTTP)-forespørgsel og pt. understøtter
Datatidy formaterne [JSON](https://en.wikipedia.org/wiki/JSON) og
[CSV](https://en.wikipedia.org/wiki/Comma-separated_values).

En datakilde består af en url plus nogle indstillinger der styrer hvordan data
hentes fra kilden, fx hvilket format data har (JSON, CSV, …).

**Bemærk**: Det er afgørende at urlen til en datakilde ikke ændrer sig idet
Datatidy løbende vil hente og behandle data fra kilden.

Hvis man finder datakilder på [opendata.dk](https://www.opendata.dk/) skal man
af og til holde tungen lige i munden for at få fat i den rigtige datakildeurl.

På https://www.opendata.dk/city-of-frederiksberg/dagtilbud_frederiksberg peger
“Download”-knappen på urlen
https://admin.opendata.dk/dataset/8e0c004c-fa81-4cd3-84ca-20db643fff29/resource/2f142bbc-2517-416f-8748-69fc68a6a0d4/download/dagtilbud.geojson
som viderestiller til
https://cc-p-minio.ckan.io/ckan/opendatadenmark-prod/resources/2f142bbc-2517-416f-8748-69fc68a6a0d4/dagtilbud.geojson?AWSAccessKeyId=2effdd1004072cb9&Expires=1590566362&Signature=6%2FrZvQb%2F%2Bb34nsgXYLNIgzqam3Q%3D
(ved redaktionens afslutning). Den endelige url virker kun midlertidigt og
Datatidy kan derfor ikke bruge den som datakilde.

Hvis man vil bruge “[Dagtilbud
Frederiksberg](https://www.opendata.dk/city-of-frederiksberg/dagtilbud_frederiksberg)”
som datakilde er det derfor afgørende at man bruger urlen
https://admin.opendata.dk/dataset/8e0c004c-fa81-4cd3-84ca-20db643fff29/resource/2f142bbc-2517-416f-8748-69fc68a6a0d4/download/dagtilbud.geojson
i sin datakilde. Denne url kan nemt findes ved at højreklikke på
“Download”-knappen og vælge “Copy link address”.


## Datatransformationer

I Datatidy består *et datasæt* af rækker og kolonner, populært kaldet *en
tabel*, hvor kolonnerne indeholder *navne* or rækkerne indeholder *værdier* for
hvert navn i rækken.

En datatransformation laver en simpel transformering af et datasæt og leverer et
nyt datasæt som resultat. En transformering kan ændre værdier, tilføje eller
fjerne kolonner, filtere rækker og meget andet.

Der er principielt ingen grænser for hvad en enkelt datatransformation kan gøre
ved et datasæt, men det tilstræbes at de kun gør én ting, fx enten ændrer
værdier eller tilføjer kolonner, så det er let at forstå hvad der sker med data.

I en opskrift køres datatransformationerne i en veldefineret rækkefølge og en
transformation skal derfor kunne behandle resultatet af den foregående
transformation.

[Læs mere om datatransformationer](data-transforms)

@TODO Transformer options

## Datamål

Når et datasæt har været udsat for alle transformationerne i en dataflow kan det
endelige datasæt sendes til et datamål. Hvordan datasættet sendes afhænger af datamålet.

## Eksempler

Se [Det store eksempel](tutorial) for en gennemgang af brug af Datatidy.

Eksempler på transformationer:

* [Filter](examples/filter)
* [Beregning](examples/calculation)
