# Brugervejledning

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

[Læs mere om datatransformationer](data-transforms.md)

@TODO Transformer options

## Datamål

Når et datasæt har været udsat for alle transformationerne i en dataflow kan det
endelige datasæt sendes til et datamål. Hvordan datasættet sendes afhænger af datamålet.

## Eksempler

Se [Det store eksempel](TUTORIAL.md) for en gennemgang af brug af Datatidy.

Eksempler på transformationer:

* [Filter](examples/filter.md)
* [Beregning](examples/calculation.md)
