

-- РЕФАКТОРИНГ ТАБЛИЦЫ ORDERS
-- Разбил ее на связанные таблицы
-- orders / users / managers / orders_delivery / order_statuses / countries / regions / cities


CREATE TABLE orders (
    id                         INT AUTO_INCREMENT PRIMARY KEY,
    hash                       VARCHAR(32)              NOT NULL UNIQUE COMMENT 'hash заказа',
    user_id                    INT                      NULL,
    manager_id                 INT                      NOT NULL COMMENT 'Ссылка на менеджера',
    token                      VARCHAR(64)              NOT NULL UNIQUE COMMENT 'уникальный хеш пользователя',
    number                     VARCHAR(15)              NULL UNIQUE COMMENT 'Номер заказа',
    status_id                  INT                      NOT NULL COMMENT 'Статус заказа из справочника статусов',
    name                       VARCHAR(200)             NOT NULL COMMENT 'Название заказа',
    description                TEXT                     NULL COMMENT 'Дополнительная информация',
    pay_type                   TINYINT                  NOT NULL COMMENT 'Тип оплаты например: 1-карта, 2-перевод, 3-наличные',
    discount                   DECIMAL(5,2)             NULL COMMENT 'Процент скидки (0-100)',
    cur_rate                   DECIMAL(10,6) DEFAULT 1  NULL COMMENT 'Курс на момент оплаты',
    spec_price                 BOOLEAN    DEFAULT FALSE NULL COMMENT 'Установлена спец цена',
    locale                     VARCHAR(5)               NOT NULL COMMENT 'правильно же понял что локаль: en, de, fr и т.д.',
    currency                   VARCHAR(3) DEFAULT 'EUR' NOT NULL COMMENT 'валюта: EUR, USD, GBP',
    measure                    VARCHAR(3) DEFAULT 'm'   NOT NULL COMMENT 'ед. изм.: m, cm, mm',
    weight_gross               DECIMAL(10,3)            NULL COMMENT 'Общий вес брутто (кг)',
    step                       TINYINT    DEFAULT 1     NOT NULL COMMENT 'Этап: 1-черновик, 2-оформлен, 3-подтвержден',
    address_equal              BOOLEAN    DEFAULT TRUE  NULL COMMENT 'Адреса плательщика и получателя совпадают',
    bank_transfer_requested    BOOLEAN    DEFAULT FALSE NULL COMMENT 'Запрашивался банковский перевод',
    accept_pay                 BOOLEAN    DEFAULT FALSE NULL COMMENT 'Заказ отправлен в работу',
    product_review             BOOLEAN    DEFAULT FALSE NULL COMMENT 'Оставлен отзыв',
    process                    BOOLEAN    DEFAULT FALSE NULL COMMENT 'Метка массовой обработки',
    show_msg                   BOOLEAN    DEFAULT FALSE NULL COMMENT 'Показывать спец. сообщение',
    full_payment_date          DATE                     NULL COMMENT 'Дата полной оплаты',
    mirror                     SMALLINT                 NULL COMMENT 'Метка зеркала',
    entrance_review            SMALLINT    DEFAULT 0    NULL COMMENT 'Счетчик просмотров отзывов',
    address_payer              INT                      NULL COMMENT 'ID адреса плательщика',
    bank_details               TEXT                     NULL COMMENT 'Реквизиты банка',
    pay_date_execution         DATETIME                 NULL COMMENT 'Дата действия текущей цены',
    fact_date                  DATETIME                 NULL COMMENT 'Фактическая дата поставки',
    sending_date               DATETIME                 NULL COMMENT 'Расчетная дата поставки',
    canceled_at                TIMESTAMP                NULL COMMENT 'Дата отмены',
    updated_at                 TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at                 TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_orders_manager FOREIGN KEY (manager_id) REFERENCES managers(id),
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_orders_status FOREIGN KEY (status_id) REFERENCES order_statuses(id)
) COMMENT 'Хранит информацию о заказах';
CREATE INDEX idx_orders_status_id ON orders (status_id);
CREATE INDEX idx_orders_create_date ON orders (created_at);
CREATE INDEX idx_orders_user_id ON orders (user_id);
CREATE INDEX idx_orders_manager_id ON orders (manager_id);

CREATE TABLE users (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    email            VARCHAR(100) NOT NULL UNIQUE COMMENT 'Контактный E-mail',
    phone            VARCHAR(20)  NOT NULL COMMENT 'Номер телефона',
    country_code     VARCHAR(3) NULL COMMENT 'Код страны если наш сервис работает с разными странами',
    vat_type         TINYINT DEFAULT 0 NOT NULL COMMENT 'Тип плательщика: 0-частное лицо, 1-плательщик НДС',
    vat_number       VARCHAR(100) NULL COMMENT 'НДС-номер',
    tax_number       VARCHAR(50) NULL COMMENT 'Индивидуальный налоговый номер',
    sex              TINYINT NULL COMMENT 'Пол: 0-не указан, 1-мужской, 2-женский',
    client_name      VARCHAR(255) NULL COMMENT 'Имя клиента',
    client_surname   VARCHAR(255) NULL COMMENT 'Фамилия клиента',
    company_name     VARCHAR(255) NULL COMMENT 'Название компании',
    password_hash    VARCHAR(255) NOT NULL COMMENT 'Хеш пароля',
    reset_token      VARCHAR(100) NULL COMMENT 'Токен для сброса пароля',
    reset_token_expires_at DATETIME NULL COMMENT 'Срок действия токена сброса',
    is_active        BOOLEAN DEFAULT TRUE COMMENT 'Активен ли пользователь',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) COMMENT 'Таблица пользователей';
CREATE INDEX idx_users_email ON users (email);
CREATE INDEX idx_users_phone ON users (phone);
CREATE INDEX idx_users_active ON users (is_active);
CREATE INDEX idx_users_full_name ON users (client_name, client_surname);

CREATE TABLE managers (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    manager_name     VARCHAR(50)  NOT NULL COMMENT 'Имя менеджера',
    manager_email    VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email менеджера',
    manager_phone    VARCHAR(20)  NOT NULL COMMENT 'Телефон менеджера',
    password_hash    VARCHAR(255) NOT NULL COMMENT 'Хеш пароля',
    username         VARCHAR(50)  NOT NULL UNIQUE COMMENT 'Логин для входа',
    role             TINYINT DEFAULT 1 NOT NULL COMMENT 'Роли',
    is_active        BOOLEAN DEFAULT TRUE COMMENT 'Активен ли менеджер',
    reset_token      VARCHAR(100) NULL COMMENT 'Токен для сброса пароля',
    reset_token_expires_at DATETIME NULL COMMENT 'Срок действия токена сброса',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) COMMENT 'Таблица менеджеров';
CREATE INDEX idx_managers_email ON managers (manager_email);
CREATE INDEX idx_managers_phone ON managers (manager_phone);
CREATE INDEX idx_managers_username ON managers (username);
CREATE INDEX idx_managers_active ON managers (is_active);

CREATE TABLE orders_delivery (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    order_id          INT NOT NULL,
    -- Валюту доставки можно посмотреть в самом заказе
    amount            DECIMAL(10,2) NULL COMMENT 'Стоимость доставки в валюте заказа',
    type_id           TINYINT DEFAULT 0 NOT NULL COMMENT 'Например: 0 - адрес клиента, 1 - адрес склада, 2 - пункт выдачи',
    calculate_type_id TINYINT DEFAULT 0 NOT NULL COMMENT 'Например: 0 - ручной, 1 - автоматический',
    -- Сроки доставки
    time_min         DATE NULL COMMENT 'Минимальный срок доставки',
    time_max         DATE NULL COMMENT 'Максимальный срок доставки',
    -- Куда доставлять
    full_address     VARCHAR(500) NOT NULL COMMENT 'Полный адрес страна/город/улица/дом/квартира и тд',
    country_id       INT NULL COMMENT 'ID страны доставки (FK на справочник стран)',
    region_id        INT NULL COMMENT 'ID Региона/области доставки (FK на справочник регионов)',
    city_id          INT NULL COMMENT 'Город',
    address          VARCHAR(500) NULL COMMENT 'Улица, дом',
    building         VARCHAR(50) NULL COMMENT 'Строение/корпус',
    apartment_office VARCHAR(30) NULL COMMENT 'Квартира/офис',
    postal_code      VARCHAR(20) NULL COMMENT 'Почтовый индекс',
    tracking_number  varchar(50)     null comment 'Номер треккинга',
    carrier_id       INT             NULL COMMENT 'ID транспортной компании (выносим в справочник транспортные компании)',
    offset_reason    TINYINT         null comment 'тип причина сдвига сроков 1 - каникулы на фабрике, 2 - фабрика уточняет сроки пр-ва, 3 - другое',
    offset_date      datetime        null comment 'Дата сдвига предполагаемого расчета доставки',
    proposed_date    datetime        null comment 'Предполагаемая дата поставки',
    ship_date        datetime        null comment 'Предполагаемая дата отгрузки',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    -- Внешние ключи
    CONSTRAINT fk_delivery_order FOREIGN KEY (order_id) REFERENCES orders(id),
    CONSTRAINT fk_delivery_country FOREIGN KEY (country_id) REFERENCES countries(id),
    CONSTRAINT fk_delivery_region FOREIGN KEY (region_id) REFERENCES regions(id),
    CONSTRAINT fk_delivery_city FOREIGN KEY (city_id) REFERENCES cities(id)
);
CREATE INDEX idx_delivery_order_id ON orders_delivery (order_id);
CREATE INDEX idx_delivery_country_id ON orders_delivery (country_id);
CREATE INDEX idx_delivery_city ON orders_delivery (city_id);
CREATE INDEX idx_delivery_region_city ON orders_delivery (region_id, city_id);
CREATE INDEX idx_delivery_location ON orders_delivery (country_id, region_id, city_id);

CREATE TABLE order_statuses (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(20) NOT NULL UNIQUE COMMENT 'Код статуса',
    name        VARCHAR(50) NOT NULL COMMENT 'Название статуса',
    description VARCHAR(200) NULL COMMENT 'Описание',
    sort_order  INT DEFAULT 0 COMMENT 'Порядок сортировки',
    is_active   BOOLEAN DEFAULT TRUE COMMENT 'Активен ли статус'
);

-- Примерные статусы которые могли бы быть
INSERT INTO order_statuses (code, name, description, sort_order) VALUES
('draft', 'Черновик', 'Заказ в процссе оформления', 1),
('pending', 'Ожидает оплаты', 'Ожидает подтверждения оплаты', 2),
('paid', 'Оплачен', 'Заказ оплачен', 3),
('processing', 'в обработке', 'Заказ в работе', 4),
('shipped', 'Отправлен', 'Заказ отправлен клиенту', 5),
('delivered', 'Доставлен', 'Заказ доставлен', 6),
('cancelled', 'Отмнен', 'Заказ отменен', 7);

CREATE TABLE countries (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE COMMENT 'Название страны',
    code        VARCHAR(3)   NOT NULL UNIQUE COMMENT 'Код страны (ISO)',
    is_active   BOOLEAN DEFAULT TRUE COMMENT 'Активна ли страна'
) COMMENT 'Справочник стран';

CREATE TABLE regions (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    country_id  INT NOT NULL COMMENT 'Ссылка на страну',
    name        VARCHAR(100) NOT NULL COMMENT 'Название региона',
    code        VARCHAR(10)  NULL COMMENT 'Код региона',
    is_active   BOOLEAN DEFAULT TRUE COMMENT 'Активен ли регион',
    CONSTRAINT fk_regions_country FOREIGN KEY (country_id) REFERENCES countries(id)
) COMMENT 'Справочник регионов';

CREATE TABLE cities (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    region_id   INT NOT NULL COMMENT 'Ссылка на регион',
    name        VARCHAR(100) NOT NULL COMMENT 'Название города',
    is_active   BOOLEAN DEFAULT TRUE COMMENT 'Активен ли город',
    CONSTRAINT fk_cities_region FOREIGN KEY (region_id) REFERENCES regions(id)
) COMMENT 'Справочник городов';



-- РЕФАКТОРИНГ ТАБЛИЦЫ ORDERS_ARTICLE
-- Разбил ее на связанные таблицы
-- orders_article / articles



CREATE TABLE orders_article (
    id                        INT AUTO_INCREMENT PRIMARY KEY,
    order_id                  INT          NOT NULL COMMENT 'Ссылка на заказ',
    article_id                INT          NOT NULL COMMENT 'ID товара/артикула',
    amount                    DECIMAL(10,3) NOT NULL COMMENT 'Количество в базовой единице измерения',
    price                     DECIMAL(10,2) NOT NULL COMMENT 'Цена за единицу в валюте заказа',
    display_measure           VARCHAR(3)    NULL COMMENT 'Ед. изм. для отображения',
    conversion_rate           DECIMAL(10,6) DEFAULT 1 NULL COMMENT 'Коэффициент конвертации между единицами',
    weight                    DECIMAL(8,3)  NOT NULL COMMENT 'Вес упаковки (кг)',
    weight_total              DECIMAL(10,3) NULL COMMENT 'Общий вес позиции (amount * weight)',
    special_notes             VARCHAR(500)  NULL COMMENT 'Дополнительные примечания',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_article_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_orders_article_article FOREIGN KEY (article_id) REFERENCES articles(id)

) COMMENT 'Состав заказа - товарные позиции (если правильно понял елементы заказа как например order_items могло бы быть)';
CREATE INDEX idx_orders_article_orders_id ON orders_article (order_id);
CREATE INDEX idx_orders_article_article_id ON orders_article (article_id);
CREATE INDEX idx_orders_article_order_article ON orders_article (order_id, article_id);
CREATE INDEX idx_orders_article_prices ON orders_article (price);

CREATE TABLE articles (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    sku              VARCHAR(50) NOT NULL UNIQUE COMMENT 'Артикул',
    name             VARCHAR(255) NOT NULL COMMENT 'Название товара',
    description      TEXT NULL COMMENT 'Описание',
    factory          VARCHAR(100) NOT NULL,
    collection       VARCHAR(100) NOT NULL,
    pallet           INT NOT NULL COMMENT 'Количество на палете',
    packaging        INT NOT NULL COMMENT 'Количество в упаковке',
    packaging_count  INT NOT NULL comment 'Количество кратно которому можно добавлять товар в заказ',
    multiple_pallet  TINYINT null comment 'Кратность палете, 1 - кратно упаковке, 2 - кратно палете, 3 - не меньше палеты',
    is_swimming_pool BOOLEAN DEFAULT FALSE,
    base_price       DECIMAL(10,2) NOT NULL COMMENT 'Базовая цена',
    base_measure     VARCHAR(3) DEFAULT 'pcs' NOT NULL COMMENT 'Базовая ед. измерения',
    weight           DECIMAL(8,3) NOT NULL COMMENT 'Вес единицы',
    is_active        BOOLEAN DEFAULT TRUE,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) COMMENT 'Сами по себе товарные позиции';
CREATE INDEX idx_articles_sku ON articles (sku);
CREATE INDEX idx_articles_name ON articles (name);
CREATE INDEX idx_articles_factory ON articles (factory);
CREATE INDEX idx_articles_collection ON articles (collection);
CREATE INDEX idx_articles_base_price ON articles (base_price);
