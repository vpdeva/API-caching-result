# API caching result/response

This caching system has been built to be able to cache API requests from any website that often returns json results or XML results.

It can also be used to cache PHP data (like big SQL results, arrays, json, etc).

This item can use 3 types of different storages: SQLite, file system and MySQL.

- Speed up your website by caching strategic data.

- Avoid being rejected by API providers by limiting requests.

- Can cache XMLs, json, PHP arrays, variables and even HTML .

- No configuration if used with the file system or SQLite.

- Define for how long a data should be cached.

###Cache a data for 120 seconds:
$c1 = new Yp_cache();
$c1->cache_set(array(‘key’=>‘123’, ‘data’=>$data, ‘expire’=>‘120’));
Get a cached data:
$data = $c1->cache_get(array(‘key’=>‘123’));
print_r($data);

###Delete a cached data:
$c1->cache_delete(array(‘key’=>‘123’));

###Delete all cached data:
$c1->cache_flush();


