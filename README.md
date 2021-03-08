* GET **/movies** — получить коллекцию фильмов
> https://flag-rest-api.herokuapp.com/api/movies  
> 
genre - фильтр по жанру  
actor - фильтр по актеру  
order[title] =  asc(default) | desc - сортировка по названию
> https://flag-rest-api.herokuapp.com/api/movies?order[title]&genre=crime&actor=marlon%20brando
>

#  
* POST **/movies** — добавить фильм
```
curl -X POST https://flag-rest-api.herokuapp.com/api/movies -H 'Content-Type: application/json' -d '{"title":"Fight Club","year":1999,"genre":"drama", "runtime":139}'
```
title, year — required fields

#  
* GET **/movie/{id}** — получить фильм по id
> https://flag-rest-api.herokuapp.com/api/movie/3
>

#  
* PUT **/movie{id}** — заменить фильм по id
```
curl -X PUT https://flag-rest-api.herokuapp.com/api/movie/3 -H 'Content-Type: application/json' -d '{"title":"Forrest Gump","year":1994,"genre":"drama"}'
```
title, year — required fields

#  
* PATCH **/movie/{id}** — обновить фильм по id
```
curl -X PATCH https://flag-rest-api.herokuapp.com/api/movie/3 -H 'Content-Type: application/json' -d '{"year":2021}'
```

#  
* DELETE **/movie/{id}** — удалить фильм по id
```
curl -X DELETE https://flag-rest-api.herokuapp.com/api/movie/3
```