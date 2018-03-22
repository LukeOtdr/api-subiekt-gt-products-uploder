# Uploader produktów do subiekta

Prosty uploader produktów z dokumentu csv w formacie:

Kod SKU;

EAN;

Nazwa produktu;

cena;symbol dostawy z subiekta;

czas dostawy;

Przed użyciem należy skonfigurować plik config/uploader.ini ustawić parametry:

```
endpoint = "http://10.20.30.205/dev-api-subiekt-gt/public/api"
api_key = "XXXXXXXXXXXXXXXXXXX"
```

endpoint - adres bramki subiekta

api_key - api key do subiekta

Przykładowey plik z danymi w katalogu ```test```