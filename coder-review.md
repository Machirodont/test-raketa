По функционалу
=
AddToCartController\
    - Это, по идее, должен быть не get, а post\
    - Отсутствует, собственно, сохранение Cart в Redis (где-то потерялся вызов $this->cartManager->saveCart())\
    - Если корзины еще нет - она должна тут создаваться, а не отдаваться 404
    - Возможно добавить неактивный продукт в корзину    

GetCartController
    Всегда возвращает 404, хотя при успешном результате должен возвращать 200

ProductRepository::getByCategory
ProductRepository::getByUuid
    Строка в SQL запроса вставляется без обрамления кавычками. Лучше использовать запрос с передачей параметров

ProductRepository::getByCategory
    SQL-запрос получает только id, а там нужны все колонки

ProductRepository
    Пропущен импорт Exception;

CartManager::saveCart
    В вызове $this->connector->set() перепутан порядок аргументов

Connector::get
    Неверное объявление типа аргумента - там должен быть string вместо Cart

CartManager::getCart
    При вызове new Cart() некорректные аргументы, но он там и в принципе не нужен.
    Крайне нежелательно чтобы метод get... что-то создавал.
    Пусть возвращает null если данных нет, а не создает новый объект.

По архитектуре
=
По логике формирования JsonResponse
    В нескольких местах повторяется довольно громоздкая логика формирования JsonResponse из массива. Желательно соблюсти DRY и вынести ее в отдельный метод

\Raketa\BackendTestTask\Repository\CartManager
    ConnectorFacade нужно внедрять как сервис через DI, а не наследовать
    $logger тоже через DI, а то он сейчас нигде не инициализируется
    Соответственно, настроить DI-контейнер так, чтобы настройки пробрасывались в ConnectorFacade

CartView
    - SQL запросы в цикле для отображения продуктов корзины. Нужно вытаскивать все данные одним запросом.
    - total в items - это промежуточный общий итог, а подразумевалась, видимо, цена конкретного item (price*quantity) 

ProductView
    Это, скорее, ProductCategoryView.
    ProductView должен возвращать View для сущности Product

Для представления финансовой информации лучше использовать не float, а decimal и работать через BCMath или аналогичный пакет с десятеричной арифметикой
    
Всякие мелочи
=
\Raketa\BackendTestTask\Repository\CartManager::getCart
    Тип возвращаемого значения лучше указать в контракте метода, а из doc-блока это убрать

\Raketa\BackendTestTask\Controller\GetCartController::get
    Ненужный else

\Raketa\BackendTestTask\Controller\GetCartController::__construct и др.
    Все что можно, особенно сервисы, внедряемые через DI, должно быть private. И везде где можно - использовать объявление в конструкторе.

Использовать session_id даже без префикса в качестве ключа для Cart я бы не стал, использовать какой-нибудь синтетический ключ

\Raketa\BackendTestTask\Infrastructure\ConnectorFacade::$connector
    Нет объявления типа (Connector)

\Raketa\BackendTestTask\Repository\CartManager::saveCart
    В PHPDoc использован @inheritdoc, хотя родительский метод отсутствует.

\Raketa\BackendTestTask\Infrastructure\Connector::set
    Connector не должен зависеть от Cart

ConnectorFacade
    нет никакого смысла делать build отдельно от конструктора