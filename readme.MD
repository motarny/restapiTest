Rekrutacja Home.pl - RestAPI
===================

----------

W akcji
-------------
Aplikację można obejrzeć w akcji pod adresem: http://nessun.ovh/home/front/

Instalacja
-------------
Rozpakowane repozytorium jest gotowe do instalacji na dowolnym hostingu z obsługą PHP 5.4
Zrzut bazy załączony. Konfiguracja bazy danych znajduje się w pliki \app\api.php w tablicy $pdoConfig.


Wywołanie API
-------------
/app/api.php/[kolekcja]/[id]

 - **kolekcja** - aktualnie obsługiwane **locations**
 - **id** - opcjonalny id zasobu


Metody http i przykłady:

 - **GET**
/app/api.php/locations?[params]
Pobiera listę lokalizacji, opcjonalne parametry:
	 - text=*string* - poszukuje lokalizacji ze słowem *string* w nazwie lub adresie
	 - distance_from_hq=*int* - poszukuje lokalizacji znajdujących się w odległości nie większej niż *int* metrów
	 - order_by=*[id/description/distance_from_hq]* - sortowanie wyników według wskazanego parametru
	 - order=*[asc/desc]* - rosnąco czy malejąco

		**Przykład**
		/app/api.php/locations?text=szczecin&distance_from_hq=4500

	/app/api.php/locations/[id]
	Pobiera informacje o wskazanej po id lokalizacji

 - **POST**
/app/api.php/locations
Utworzy nową lokalizację w bazie

	/app/api.php/locations/[id]
	Zaktualizuje lokalizację o wskazanym id

	Parametry posta:
		description, address, latitude, longitude

 - **DELETE**
/app/api.php/locations/[id]
Usunie z bazy wskazaną lokalizację.

