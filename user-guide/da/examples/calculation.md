# Beregning

Disse eksempler tager udgangspunkt i [datakilden defineret i Det store
eksempel](../tutorial#datakilden).

## Vindhastighed i m/s

I datakilden er vindhastigheden angivet i km/t, men vi vil gerne have den i m/s.

Brug først en [filter](filter)-transformation til kun at vise rækker hvor
“type” er “wind_speed”.

Opret derefter en datatransformation med type “Calculate” og følgende parametre:

| Felt       | Værdi      | Kommentar                                                               |
|------------|------------|-------------------------------------------------------------------------|
| Name       | km/t → m/s | Bemærk at der er to “Name”-felter – dette er navnet på transformationen |
| Name       | wind_speed | Navnet på den nye kolonne til resultatet af beregningen                 |
| Expression | value/3.6  | Værdien i “value” divideres med 3.6                                     |
| Type       | float      | Resultatet er et decimaltal                                             |

![calculate_000][calculate_000]

Tryk “Save step” og kontroller at beregningen fungerer som forventet.

![calculate_001][calculate_001]

I det overnstående tilføjes en ny kolonne med resultatet af beregningen, men man
kan også overskrive værdien i en allerede eksisterende kolonne. Rediger
“Calculate”-transformationen, ændr (det rigtige) “Name” til “value” og gem
transformationen.

![calculate_002][calculate_002]

Nu overskriver vindhastigheden i m/s i den oprindelige værdi i “value”-kolonnen.

![calculate_003][calculate_003]

[calculate_000]: images/Screenshot_2019-12-17%20Add%20transform%20Datatidy.png
[calculate_001]: images/Screenshot_2019-12-17%20Data%20flow%20recipe%20step%20Datatidy.png
[calculate_002]: images/Screenshot_2019-12-17%20Edit%20transform%20Datatidy(1).png
[calculate_003]: images/Screenshot_2019-12-17%20Data%20flow%20recipe%20step%20Datatidy(1).png
